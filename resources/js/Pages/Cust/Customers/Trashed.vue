<template>
    <AppLayout title="Pelanggan Dihapus (Trash)">
        <div class="p-2 w-full max-w-full w-full mx-auto flex flex-col h-full">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col gap-4 px-4 sm:px-0">
                <!-- Top Row: Title & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-red-600 dark:text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Pelanggan Dihapus</h1>
                    </div>
                    
                    <div class="flex items-center gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                        <!-- Batch Restore Button -->
                        <button v-if="selectedCustomers.length > 0" @click="confirmBatchRestore" class="px-4 py-2 bg-emerald-100 dark:bg-emerald-900/50 hover:bg-emerald-200 dark:hover:bg-emerald-800/70 text-emerald-700 dark:text-emerald-400 text-sm font-medium rounded-xl transition-all flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            Restore ({{ selectedCustomers.length }})
                        </button>

                        <!-- Batch Delete Button -->
                        <button v-if="selectedCustomers.length > 0" @click="confirmBatchForceDelete" class="px-4 py-2 bg-red-100 dark:bg-red-900/50 hover:bg-red-200 dark:hover:bg-red-800/70 text-red-700 dark:text-red-400 text-sm font-medium rounded-xl transition-all flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Permanen ({{ selectedCustomers.length }})
                        </button>
                        
                        <!-- Kembali Button -->
                        <Link :href="route('cust.customers.index')" class="w-full sm:w-auto px-6 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium flex items-center justify-center whitespace-nowrap">
                            <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Kembali ke Pelanggan
                        </Link>
                    </div>
                </div>

                <!-- Bottom Row: Filters -->
                <div class="w-full">
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full overflow-x-auto overflow-y-hidden whitespace-nowrap scrollbar-hide">
                        <!-- Per Page -->
                        <select v-model="formFilters.per_page" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <!-- Area -->
                        <select v-model="formFilters.area_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                            <option value="">Semua Area</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1 hidden md:block"></div>
                        <!-- Koordinator -->
                        <select v-model="formFilters.sales_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max hidden md:block">
                            <option value="">Semua Mitra</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <!-- Search -->
                        <div class="relative min-w-[120px] w-full">
                            <input 
                                v-model="formFilters.search" 
                                type="text" 
                                placeholder="Cari pelanggan..." 
                                class="w-full bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 py-1 pl-8 pr-3"
                            />
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table List -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0">
                    <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-2 py-3 w-10 text-center whitespace-nowrap">
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">NAMA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">WA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">TGL DIHAPUS</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">PAKET</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AREA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">SALES</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="customer in customers.data" :key="customer.id" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-2 py-3 text-center whitespace-nowrap">
                                    <input type="checkbox" :value="customer.id" v-model="selectedCustomers" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </td>
                                <td class="px-3 py-3 font-medium text-xs sm:text-sm text-slate-900 dark:text-white whitespace-nowrap text-center">
                                    {{ customer.name }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ customer.phone || '-' }}</td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ new Date(customer.deleted_at).toLocaleString('id-ID') }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.package?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.area?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.sales?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap flex items-center justify-center gap-2">
                                    <button @click="confirmRestore(customer)" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400 dark:hover:bg-emerald-800/70 rounded-md transition-colors text-xs font-medium flex items-center" title="Restore">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        Restore
                                    </button>
                                    <button @click="confirmForceDelete(customer)" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/50 dark:text-red-400 dark:hover:bg-red-800/70 rounded-md transition-colors text-xs font-medium flex items-center" title="Hapus Permanen">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="customers.data.length === 0">
                                <td colspan="8" class="px-4 py-16 text-center text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-2xl">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Trash kosong. Tidak ada data pelanggan yang dihapus.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="customers.links && customers.links.length > 3" class="px-6 py-4 mt-2 mb-6 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-md rounded-2xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-medium text-slate-800 dark:text-slate-200">{{ customers.from || 0 }}</span> sampai <span class="font-medium text-slate-800 dark:text-slate-200">{{ customers.to || 0 }}</span> dari <span class="font-medium text-slate-800 dark:text-slate-200">{{ customers.total }}</span> data
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center">
                            <template v-for="(link, p) in customers.links" :key="p">
                                <component
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || '#'"
                                    class="px-3 py-1.5 rounded-lg text-sm transition-colors"
                                    :class="{
                                        'bg-blue-600 text-white font-medium shadow-sm': link.active,
                                        'text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700': !link.active && link.url,
                                        'text-slate-300 dark:text-slate-600 cursor-not-allowed': !link.url
                                    }"
                                    v-html="link.label"
                                ></component>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="isDeleteModalOpen" @close="closeDeleteModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">Konfirmasi Hapus Permanen</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin menghapus permanen <span v-if="deleteTarget">pelanggan <strong>{{ deleteTarget.name }}</strong></span><span v-else><strong>{{ selectedCustomers.length }} pelanggan</strong> yang dipilih</span>? Data yang dihapus permanen <strong>tidak dapat dikembalikan</strong>.
                </p>

                <div class="flex justify-end gap-3">
                    <button @click="closeDeleteModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button @click="executeForceDelete" :disabled="isDeleting" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Ya, Hapus Permanen
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Restore Confirmation Modal -->
        <Modal :show="isRestoreModalOpen" @close="closeRestoreModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mb-4">Konfirmasi Restore</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin mengembalikan <span v-if="restoreTarget">pelanggan <strong>{{ restoreTarget.name }}</strong></span><span v-else><strong>{{ selectedCustomers.length }} pelanggan</strong> yang dipilih</span> ke daftar aktif?
                </p>

                <div class="flex justify-end gap-3">
                    <button @click="closeRestoreModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button @click="executeRestore" :disabled="isRestoring" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isRestoring" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Ya, Restore
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    customers: Object,
    areas: Array,
    users: Array,
    filters: Object,
});

const formFilters = ref({
    search: props.filters?.search || '',
    area_id: props.filters?.area_id || '',
    sales_id: props.filters?.sales_id || '',
    per_page: props.filters?.per_page || '15',
});

let searchTimeout;
watch(formFilters, (newValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('cust.customers.trashed'), newValues, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

// Batch & Select logic
const selectedCustomers = ref([]);
const isAllSelected = computed(() => {
    return props.customers.data.length > 0 && selectedCustomers.value.length === props.customers.data.length;
});

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedCustomers.value = props.customers.data.map(c => c.id);
    } else {
        selectedCustomers.value = [];
    }
};

// Force Delete Logic
const isDeleteModalOpen = ref(false);
const deleteTarget = ref(null);
const isDeleting = ref(false);

const confirmForceDelete = (customer) => {
    deleteTarget.value = customer;
    isDeleteModalOpen.value = true;
};

const confirmBatchForceDelete = () => {
    deleteTarget.value = null; // null means batch
    isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    window.setTimeout(() => {
        deleteTarget.value = null;
        isDeleting.value = false;
    }, 200);
};

const executeForceDelete = () => {
    isDeleting.value = true;
    if (deleteTarget.value) {
        // Single force delete
        router.delete(route('cust.customers.force-delete', deleteTarget.value.id), {
            onSuccess: () => {
                closeDeleteModal();
                selectedCustomers.value = selectedCustomers.value.filter(id => id !== deleteTarget.value.id);
            },
            onError: () => isDeleting.value = false
        });
    } else {
        // Batch force delete
        router.post(route('cust.customers.batch-force-delete'), {
            ids: selectedCustomers.value,
        }, {
            onSuccess: () => {
                closeDeleteModal();
                selectedCustomers.value = [];
            },
            onError: () => isDeleting.value = false
        });
    }
};

// Restore Logic
const isRestoreModalOpen = ref(false);
const restoreTarget = ref(null);
const isRestoring = ref(false);

const confirmRestore = (customer) => {
    restoreTarget.value = customer;
    isRestoreModalOpen.value = true;
};

const confirmBatchRestore = () => {
    restoreTarget.value = null; // null means batch
    isRestoreModalOpen.value = true;
};

const closeRestoreModal = () => {
    isRestoreModalOpen.value = false;
    window.setTimeout(() => {
        restoreTarget.value = null;
        isRestoring.value = false;
    }, 200);
};

const executeRestore = () => {
    isRestoring.value = true;
    if (restoreTarget.value) {
        // Single restore
        router.post(route('cust.customers.restore', restoreTarget.value.id), {}, {
            onSuccess: () => {
                closeRestoreModal();
                selectedCustomers.value = selectedCustomers.value.filter(id => id !== restoreTarget.value.id);
            },
            onError: () => isRestoring.value = false
        });
    } else {
        // Batch restore
        router.post(route('cust.customers.batch-restore'), {
            ids: selectedCustomers.value,
        }, {
            onSuccess: () => {
                closeRestoreModal();
                selectedCustomers.value = [];
            },
            onError: () => isRestoring.value = false
        });
    }
};
</script>
