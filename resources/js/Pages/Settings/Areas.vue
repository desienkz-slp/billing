<template>
    <AppLayout :title="pageTitle">
        <div class="p-2">
            
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Master Area</h1>
                </div>
                <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Area
                </button>
            </div>

            <!-- Daftar Area Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider w-16">#</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Area</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            <tr v-for="(area, index) in areas" :key="area.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    {{ index + 1 }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm font-medium text-slate-900 dark:text-white">
                                    {{ area.name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    {{ area.description || '-' }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(area)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">Edit</button>
                                        <button @click="deleteArea(area)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty state if no data -->
                            <tr v-if="!areas || areas.length === 0">
                                <td colspan="4" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        <p>Belum ada data area. Silakan tambahkan area baru.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700 flex flex-col" @click.stop>
                        
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/50">
                                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">{{ isEditing ? 'Edit Area' : 'Tambah Area' }}</h3>
                            </div>
                            <button @click="closeModal" type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-colors focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 py-5">
                            <form @submit.prevent="submitForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Area</label>
                                    <input v-model="form.name" type="text" required class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="contoh: Desa X">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Keterangan</label>
                                    <input v-model="form.description" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="opsional">
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 flex flex-row-reverse gap-3">
                            <button @click="submitForm" type="button" class="inline-flex justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 transition-all">
                                {{ isEditing ? 'Simpan Perubahan' : 'Tambahkan' }}
                            </button>
                            <button @click="closeModal" type="button" class="inline-flex justify-center rounded-xl bg-white dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    areas: {
        type: Array,
        default: () => []
    },
    capabilities: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = computed(() => {
    return 'Master Area';
});

// Modal state
const isModalOpen = ref(false);

// Form state
const form = ref({
    id: null,
    name: '',
    description: ''
});

const isEditing = computed(() => form.value.id !== null);

const openCreateModal = () => {
    form.value = { id: null, name: '', description: '' };
    isModalOpen.value = true;
};

const openEditModal = (area) => {
    form.value = {
        id: area.id,
        name: area.name,
        description: area.description || ''
    };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = () => {
    if (isEditing.value) {
        alert('Simulasi: Update area ' + form.value.name);
    } else {
        alert('Simulasi: Simpan area baru ' + form.value.name);
    }
    closeModal();
};

const deleteArea = (area) => {
    if (confirm(`Apakah Anda yakin ingin menghapus area "${area.name}"?`)) {
        alert('Simulasi: Area ' + area.name + ' berhasil dihapus.');
    }
};
</script>