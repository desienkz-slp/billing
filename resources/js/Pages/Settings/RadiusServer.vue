<template>
    <AppLayout :title="pageTitle">
        <div class="h-full">
            
            <!-- Page Header -->
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Radius Server Configuration</h1>
                </div>
                <div>
                    <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Server RADIUS
                    </button>
                </div>
            </div>

            <!-- Server List -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                <th class="p-4">Nama Server</th>
                                <th class="p-4">Tipe</th>
                                <th class="p-4">Host / Endpoint</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-if="servers.length === 0">
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    Belum ada server RADIUS yang ditambahkan.
                                </td>
                            </tr>
                            <tr v-for="server in servers" :key="server.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ server.name }}</div>
                                            <div class="text-xs text-slate-500">{{ server.routers_count }} Router terhubung</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        {{ getTypeLabel(server.type) }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono text-sm text-slate-600 dark:text-slate-400">
                                    <div>
                                        {{ server.api_endpoint }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <button @click="toggleStatus(server)" :class="server.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors hover:opacity-80">
                                        {{ server.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="testConnection(server)" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 rounded-lg transition-colors tooltip" title="Test Connection">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </button>
                                        <button @click="openEditModal(server)" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="deleteServer(server)" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal" :maxWidth="'2xl'">
            <div class="bg-white dark:bg-slate-800 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                        {{ form.id ? 'Edit Server RADIUS' : 'Tambah Server RADIUS' }}
                    </h2>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Server -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Server</label>
                            <input v-model="form.name" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20" required placeholder="Contoh: RADIUS Pusat">
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
                        </div>

                        <!-- Tipe -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Integrasi</label>
                            <select v-model="form.type" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20" required>
                                <option value="upluk_upluk_api">Upluk Upluk RADIUS API</option>
                            </select>
                            <div v-if="form.errors.type" class="mt-1 text-sm text-red-500">{{ form.errors.type }}</div>
                        </div>

                        <!-- API Specific Fields -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">API Endpoint URL</label>
                            <input v-model="form.api_endpoint" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 shadow-sm" placeholder="Contoh: http://10.0.0.3:3001">
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">API Token / Secret</label>
                            <input v-model="form.api_token" type="password" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 shadow-sm" :placeholder="form.id && hasApiToken ? 'Biarkan kosong jika tidak ingin mengubah' : 'Token API'">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="closeModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Server' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Test Connection Result Modal -->
        <Modal :show="showTestModal" @close="showTestModal = false" :maxWidth="'md'">
            <div class="p-6 text-center">
                <div v-if="isTesting" class="py-8">
                    <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Menguji Koneksi...</h3>
                    <p class="text-sm text-slate-500 mt-1">Harap tunggu sebentar</p>
                </div>
                <div v-else>
                    <div v-if="testResult?.status === 'success'" class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div v-else class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
                        {{ testResult?.status === 'success' ? 'Koneksi Berhasil' : 'Koneksi Gagal' }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 break-words">
                        {{ testResult?.message }}
                        <span v-if="testResult?.identity" class="font-bold text-blue-600 dark:text-blue-400">
                            "{{ testResult.identity }}"
                        </span>
                    </p>
                    
                    <button @click="showTestModal = false" class="px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white rounded-xl font-medium hover:bg-slate-700 dark:hover:bg-slate-600 transition-colors w-full">
                        Tutup
                    </button>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({
    servers: {
        type: Array,
        default: () => []
    }
});

const pageTitle = computed(() => 'Radius Server Configuration');
const { confirm } = useConfirm();

const showModal = ref(false);
const hasApiToken = ref(false);

const form = useForm({
    id: null,
    name: '',
    type: 'upluk_upluk_api',
    api_endpoint: '',
    api_token: '',
});

const getTypeLabel = (type) => {
    switch(type) {
        case 'upluk_upluk_api': return 'Upluk Upluk API';
        default: return type;
    }
};

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.id = null;
    form.type = 'upluk_upluk_api';
    hasApiToken.value = false;
    showModal.value = true;
};

const openEditModal = (server) => {
    form.reset();
    form.clearErrors();
    form.id = server.id;
    form.name = server.name;
    form.type = server.type;
    form.api_endpoint = server.api_endpoint;
    form.api_token = '';
    hasApiToken.value = server.has_api_token;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (form.id) {
        form.put(route('config.radius-server.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('config.radius-server.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteServer = (server) => {
    confirm({
        title: 'Hapus Server?',
        message: `Anda yakin ingin menghapus server ${server.name}? Aksi ini tidak dapat dibatalkan.`,
        type: 'danger',
        confirmText: 'Ya, Hapus'
    }).then((confirmed) => {
        if (confirmed) {
            router.delete(route('config.radius-server.destroy', server.id), {
                preserveScroll: true
            });
        }
    });
};

const toggleStatus = (server) => {
    router.post(route('config.radius-server.toggle', server.id), {}, {
        preserveScroll: true
    });
};

// Test Connection
const showTestModal = ref(false);
const isTesting = ref(false);
const testResult = ref(null);

const testConnection = async (server) => {
    showTestModal.value = true;
    isTesting.value = true;
    testResult.value = null;
    
    try {
        const response = await axios.post(route('config.radius-server.test', server.id));
        testResult.value = response.data;
    } catch (error) {
        testResult.value = {
            status: 'error',
            message: error.response?.data?.message || 'Terjadi kesalahan saat menguji koneksi.',
            details: error.response?.data || error.message
        };
    } finally {
        isTesting.value = false;
    }
};
</script>