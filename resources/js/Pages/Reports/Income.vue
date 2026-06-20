<template>
    <AppLayout title="Data Pendapatan (Income)">
        <div class="p-2 w-full max-w-full w-full mx-auto flex flex-col h-full">
            <!-- Header Section -->
            <div class="mb-4 flex flex-col gap-4 px-4 sm:px-0">
                <!-- Top Row: Title & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 dark:text-emerald-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Pendapatan (Income)</h1>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 mb-2">
                    <div v-for="(val, label) in kpiData" :key="label" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 relative overflow-hidden group shadow-sm hover:shadow-md transition-shadow">
                        <div class="absolute right-0 top-0 w-16 h-16 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110" :class="kpiColors[label]?.bg || 'bg-slate-500/5 dark:bg-slate-500/10'"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1" :class="kpiColors[label]?.text || 'text-slate-500 dark:text-slate-400'">{{ label }}</p>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">{{ formatRupiah(val) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 items-center mt-2">
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full overflow-x-auto whitespace-nowrap scrollbar-hide">
                        <select v-model="formFilters.per_page" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                            <option value="15">15</option>
                            <option value="50">50</option>
                            <option value="all">Semua</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <div class="flex items-center px-2 min-w-max">
                            <span class="text-xs text-slate-500 mr-2">Bulan:</span>
                            <input type="month" v-model="formFilters.month" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 py-1" />
                        </div>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <select v-model="formFilters.area_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-2 pr-7 min-w-max">
                            <option value="">Semua Area</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <select v-model="formFilters.sales_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-2 pr-7 min-w-max">
                            <option value="">Semua Sales</option>
                            <option v-for="sales in salesList" :key="sales.id" :value="sales.id">{{ sales.name }}</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
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

            <!-- Table Container -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0 relative">
                    <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">WAKTU</th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">PERIODE</th>
                                <th scope="col" class="px-3 py-3 whitespace-nowrap">PELANGGAN</th>
                                <th scope="col" class="px-3 py-3 whitespace-nowrap">PAKET</th>
                                <th scope="col" class="px-3 py-3 whitespace-nowrap">SALES</th>
                                <th scope="col" class="px-3 py-3 whitespace-nowrap">OLEH</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap">HARGA</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">DISKON</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">TAMBAHAN</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">PPN</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">BHP USO</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap">ADMIN</th>
                                <th scope="col" class="px-3 py-3 text-right whitespace-nowrap font-bold text-slate-800 dark:text-slate-200">TERIMA</th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="pay in payments.data" :key="pay.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-3 py-3 text-center whitespace-nowrap text-xs">
                                    <span class="block font-medium text-slate-700 dark:text-slate-300">{{ pay.payment_date.split(' ')[0] }}</span>
                                    <span class="block text-[10px] text-slate-500">{{ pay.payment_date.split(' ')[1] }}</span>
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap font-medium text-slate-600 dark:text-slate-400">
                                    {{ pay.period }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap font-medium text-slate-900 dark:text-white">
                                    {{ pay.customer_name }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-xs">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-md">
                                        {{ pay.package_name }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">{{ pay.sales_name }}</td>
                                <td class="px-3 py-3 whitespace-nowrap text-xs"><span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-slate-600 dark:text-slate-300">{{ pay.created_by_name }}</span></td>
                                <td class="px-3 py-3 text-right whitespace-nowrap">{{ formatRupiah(pay.harga) }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap text-red-600 dark:text-red-400 hidden xl:table-cell">{{ pay.diskon > 0 ? '-' + formatRupiah(pay.diskon) : '-' }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">{{ pay.tambahan > 0 ? formatRupiah(pay.tambahan) : '-' }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">{{ pay.ppn > 0 ? formatRupiah(pay.ppn) : '-' }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap hidden xl:table-cell">{{ pay.bhp_uso > 0 ? formatRupiah(pay.bhp_uso) : '-' }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap">{{ pay.admin > 0 ? formatRupiah(pay.admin) : '-' }}</td>
                                <td class="px-3 py-3 text-right whitespace-nowrap font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ formatRupiah(pay.terima) }}
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="route('reports.income.print', pay.id)" target="_blank" class="p-1.5 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded transition-colors" title="Cetak Invoice">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </a>
                                        <button @click="resend(pay)" class="p-1.5 text-emerald-600 hover:bg-emerald-100 dark:text-emerald-400 dark:hover:bg-emerald-900/50 rounded transition-colors" title="Kirim WA">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        </button>
                                        <button @click="confirmRefund(pay)" class="p-1.5 text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900/50 rounded transition-colors" title="Batal (Refund)">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="payments.data.length === 0">
                                <td colspan="14" class="px-4 py-16 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tidak ada data pendapatan pada filter ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="payments.links && payments.links.length > 3" class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-medium">{{ payments.from || 0 }}</span> sampai <span class="font-medium">{{ payments.to || 0 }}</span> dari <span class="font-medium">{{ payments.total }}</span> data
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center">
                            <template v-for="(link, p) in payments.links" :key="p">
                                <component :is="link.url ? Link : 'span'" :href="link.url || '#'" class="px-3 py-1.5 rounded-lg text-sm transition-colors" :class="{'bg-blue-600 text-white font-medium': link.active, 'text-slate-500 hover:bg-slate-200': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url}" v-html="link.label"></component>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Refund Modal -->
        <Modal :show="isRefundModalOpen" @close="closeRefundModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">Konfirmasi Pembatalan (Refund)</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin membatalkan pembayaran sebesar <strong v-if="refundTarget">{{ formatRupiah(refundTarget.terima) }}</strong> dari pelanggan <strong v-if="refundTarget">{{ refundTarget.customer_name }}</strong>?
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeRefundModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Tutup</button>
                    <button @click="executeRefund" :disabled="isProcessing" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Ya, Batalkan
                    </button>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    payments: Object,
    filters: Object,
    capabilities: Object,
    tenant: Object,
    areas: Array,
    salesList: Array,
    kpiData: Object,
});

const kpiColors = {
    'Total Harga': { text: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-500/10 dark:bg-blue-500/20' },
    'Total Diskon': { text: 'text-red-600 dark:text-red-400', bg: 'bg-red-500/10 dark:bg-red-500/20' },
    'Total Tambahan': { text: 'text-yellow-600 dark:text-yellow-400', bg: 'bg-yellow-500/10 dark:bg-yellow-500/20' },
    'Total PPN': { text: 'text-purple-600 dark:text-purple-400', bg: 'bg-purple-500/10 dark:bg-purple-500/20' },
    'Total BHP USO': { text: 'text-orange-600 dark:text-orange-400', bg: 'bg-orange-500/10 dark:bg-orange-500/20' },
    'Total Admin': { text: 'text-cyan-600 dark:text-cyan-400', bg: 'bg-cyan-500/10 dark:bg-cyan-500/20' },
    'Total Terima': { text: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-500/10 dark:bg-emerald-500/20' },
};

const formFilters = ref({
    search: props.filters?.search || '',
    month: props.filters?.month || '',
    area_id: props.filters?.area_id || '',
    sales_id: props.filters?.sales_id || '',
    per_page: props.filters?.per_page || '15',
});

let searchTimeout;
watch(formFilters, (newValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('reports.income.index'), newValues, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

// Resend WA
const resend = (pay) => {
    router.post(route('reports.income.resend', pay.id), {}, { preserveScroll: true });
};

// Refund Logic
const isRefundModalOpen = ref(false);
const refundTarget = ref(null);
const isProcessing = ref(false);

const confirmRefund = (pay) => {
    refundTarget.value = pay;
    isRefundModalOpen.value = true;
};

const closeRefundModal = () => {
    isRefundModalOpen.value = false;
    setTimeout(() => { refundTarget.value = null; isProcessing.value = false; }, 200);
};

const executeRefund = () => {
    if (!refundTarget.value) return;
    isProcessing.value = true;
    router.post(route('reports.income.refund', refundTarget.value.id), {}, {
        onSuccess: () => closeRefundModal(),
        onError: () => { isProcessing.value = false; }
    });
};
</script>
