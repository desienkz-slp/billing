<template>
    <AppLayout title="Pengeluaran (Expenses)">
        <div class="p-2 w-full max-w-full w-full mx-auto flex flex-col h-full">
            
            <!-- Header Section -->
            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-rose-600 dark:text-rose-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Data Pengeluaran</h1>
                </div>

                <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_manage_expenses" @click="openAddModal" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto transition-all">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengeluaran
                </button>
            </div>

            <!-- Stats & Filters -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                <button v-for="(val, cat) in kpiData" :key="cat" @click="filterKpi(cat)" 
                    class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all"
                    :class="formFilters.category === cat ? 'border-indigo-500 dark:border-indigo-500 ring-1 ring-indigo-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"
                        :class="[
                            cat === 'Total' ? 'bg-indigo-500/10 dark:bg-indigo-500/20' : 
                            cat === 'Operasional' ? 'bg-blue-500/10 dark:bg-blue-500/20' : 
                            cat === 'Marketing' ? 'bg-purple-500/10 dark:bg-purple-500/20' : 
                            cat === 'Alat' ? 'bg-amber-500/10 dark:bg-amber-500/20' : 
                            cat === 'Deviden' ? 'bg-emerald-500/10 dark:bg-emerald-500/20' : 
                            cat === 'Konsumsi' ? 'bg-rose-500/10 dark:bg-rose-500/20' : 'bg-slate-500/10 dark:bg-slate-500/20'
                        ]"></div>
                    <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center relative z-10 gap-2">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ cat }}</p>
                            <h3 class="text-xl font-extrabold"
                                :class="[
                                    cat === 'Total' ? 'text-indigo-600 dark:text-indigo-400' : 
                                    cat === 'Operasional' ? 'text-blue-600 dark:text-blue-400' : 
                                    cat === 'Marketing' ? 'text-purple-600 dark:text-purple-400' : 
                                    cat === 'Alat' ? 'text-amber-600 dark:text-amber-400' : 
                                    cat === 'Deviden' ? 'text-emerald-600 dark:text-emerald-400' : 
                                    cat === 'Konsumsi' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400'
                                ]">{{ formatRupiah(val) }}</h3>
                        </div>
                    </div>
                </button>
            </div>

            <div class="md:col-span-2 flex flex-col justify-center mb-4">
                <div class="flex flex-col sm:flex-row gap-3 items-center">
                    <!-- Date Filter -->
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full sm:max-w-[200px]">
                        <div class="flex items-center px-3 min-w-max w-full">
                            <input type="month" v-model="formFilters.bulan" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 py-1 w-full" />
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="w-full relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" v-model="formFilters.search" placeholder="Cari keterangan..." class="block w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition-colors" />
                        </div>
                    </div>
                </div>

            <!-- Table Container -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0 relative">
                    <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-4 py-3 whitespace-nowrap w-32">Tanggal</th>
                                <th scope="col" class="px-4 py-3 whitespace-nowrap w-40">Kategori</th>
                                <th scope="col" class="px-4 py-3 min-w-[250px]">Keterangan</th>
                                <th scope="col" class="px-4 py-3 whitespace-nowrap">Oleh</th>
                                <th scope="col" class="px-4 py-3 text-right whitespace-nowrap w-40">Nominal</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="block font-medium text-slate-700 dark:text-slate-300">{{ formatDate(expense.expense_date) }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-[11px] font-semibold tracking-wide" 
                                          :class="{
                                              'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300': expense.category === 'Operasional',
                                              'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300': expense.category === 'Marketing',
                                              'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300': expense.category === 'Alat/Material',
                                              'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300': expense.category === 'Pajak',
                                              'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300': expense.category === 'Deviden',
                                              'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300': expense.category === 'Lainnya'
                                          }">
                                        {{ expense.category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-700 dark:text-slate-300">{{ expense.description }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-1 rounded-md">{{ expense.creator?.name || '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap font-bold text-rose-600 dark:text-rose-400">
                                    {{ formatRupiah(expense.amount) }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_delete_finance" @click="confirmDelete(expense)" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/50 rounded transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="expenses.data.length === 0">
                                <td colspan="6" class="px-4 py-16 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tidak ada data pengeluaran pada filter ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="expenses.links && expenses.links.length > 3" class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-medium">{{ expenses.from || 0 }}</span> sampai <span class="font-medium">{{ expenses.to || 0 }}</span> dari <span class="font-medium">{{ expenses.total }}</span> data
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center">
                            <template v-for="(link, p) in expenses.links" :key="p">
                                <component :is="link.url ? Link : 'span'" :href="link.url || '#'" class="px-3 py-1.5 rounded-lg text-sm transition-colors" :class="{'bg-indigo-600 text-white font-medium': link.active, 'text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700': !link.active && link.url, 'text-slate-300 dark:text-slate-600 cursor-not-allowed': !link.url}" v-html="link.label"></component>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Modal -->
        <Modal :show="isAddModalOpen" @close="closeAddModal" maxWidth="md">
            <form @submit.prevent="submitForm" class="p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Tambah Pengeluaran</h3>
                    <button type="button" @click="closeAddModal" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" v-model="form.expense_date" required class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        <div v-if="form.errors.expense_date" class="text-red-500 text-xs mt-1">{{ form.errors.expense_date }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select v-model="form.category" required class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="" disabled>Pilih Kategori</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                        <div v-if="form.errors.category" class="text-red-500 text-xs mt-1">{{ form.errors.category }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" v-model="form.amount" min="0" required placeholder="Contoh: 150000" class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan / Deskripsi <span class="text-red-500">*</span></label>
                        <textarea v-model="form.description" rows="3" required placeholder="Tuliskan detail pengeluaran..." class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="closeAddModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Batal</button>
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="isDeleteModalOpen" @close="closeDeleteModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">Hapus Pengeluaran</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin menghapus catatan pengeluaran <strong v-if="deleteTarget">"{{ deleteTarget.description }}" ({{ formatRupiah(deleteTarget.amount) }})</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeDeleteModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Batal</button>
                    <button @click="executeDelete" :disabled="isDeleting" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    expenses: Object,
    total: Number,
    bulan: String,
    categories: Array,
    kpiData: Object,
});

const formFilters = ref({
    bulan: props.bulan || '',
    category: new URLSearchParams(window.location.search).get('category') || 'Total',
    search: new URLSearchParams(window.location.search).get('search') || '',
});

const filterKpi = (cat) => {
    formFilters.value.category = formFilters.value.category === cat ? 'Total' : cat;
};

let searchTimeout;
watch(formFilters, (newValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('reports.expenses.index'), newValues, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Add Form
const isAddModalOpen = ref(false);
const form = useForm({
    expense_date: new Date().toISOString().split('T')[0],
    category: '',
    description: '',
    amount: '',
});

const openAddModal = () => {
    form.reset();
    form.expense_date = new Date().toISOString().split('T')[0];
    isAddModalOpen.value = true;
};

const closeAddModal = () => {
    isAddModalOpen.value = false;
    form.clearErrors();
};

const submitForm = () => {
    form.post(route('reports.expenses.store'), {
        onSuccess: () => closeAddModal(),
    });
};

// Delete Logic
const isDeleteModalOpen = ref(false);
const deleteTarget = ref(null);
const isDeleting = ref(false);

const confirmDelete = (expense) => {
    deleteTarget.value = expense;
    isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    setTimeout(() => { deleteTarget.value = null; isDeleting.value = false; }, 200);
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    isDeleting.value = true;
    router.delete(route('reports.expenses.destroy', deleteTarget.value.id), {
        onSuccess: () => closeDeleteModal(),
        onError: () => { isDeleting.value = false; }
    });
};
</script>
