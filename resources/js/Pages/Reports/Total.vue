<template>
    <AppLayout :title="pageTitle">
        <!-- Header & Filter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 print:hidden">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Rekapitulasi Total</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Periode: <span class="font-medium text-slate-700 dark:text-slate-300">{{ formattedMonth }}</span></p>
            </div>
            <div class="flex gap-2">
                <input 
                    type="month" 
                    v-model="selectedMonth" 
                    class="rounded-xl border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow px-4 py-2" 
                />
                <button @click="printData" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / PDF
                </button>
            </div>
        </div>

        <!-- Kop Surat (Hanya Tampil Saat Print) -->
        <div class="hidden print:block mb-8 border-b-2 border-slate-800 pb-4 text-center">
            <h1 class="text-3xl font-bold uppercase text-black">{{ tenant?.company_profile?.name || tenant?.name || 'LadaPala Bill' }}</h1>
            <p class="text-base text-black mt-2">{{ tenant?.company_profile?.address || 'Alamat Perusahaan Belum Diatur' }}</p>
            <p class="text-sm text-black mt-1">Telp: {{ tenant?.company_profile?.phone || '-' }} | Email: {{ tenant?.company_profile?.email || '-' }}</p>
            <h2 class="text-xl font-bold text-black mt-6">Laporan Total Keuangan</h2>
            <p class="text-sm text-black">Periode: {{ formattedMonth }}</p>
        </div>

        <!-- Glassmorphism Card (Panel Utama) -->
        <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl overflow-hidden mb-6 print:shadow-none print:border-none print:bg-white">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800 print:bg-none print:border-b-2 print:border-black">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white print:text-black">Ringkasan Keuangan</h3>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-transparent print:bg-white">
                <!-- Grid untuk KPI Cards: 4 Columns base -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-3">
                    
                    <!-- KPI 1: Total Pendapatan (Blue Tone) -->
                    <Link :href="route('reports.detail-pendapatan', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Tagihan Bulanan</p>
                                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 print:text-black">Rp {{ formatNumber(pendapatan) }}</h3>
                                <p class="text-xs text-slate-400 mt-2 print:text-black">Dari <span class="font-semibold text-slate-600 dark:text-slate-300 print:text-black">{{ txnCount }}</span> Transaksi</p>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 2: Pendapatan Lain (Cyan Tone) -->
                    <Link :href="route('reports.other-incomes.index', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-cyan-500/10 dark:bg-cyan-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Pendapatan Lain</p>
                                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 print:text-black">Rp {{ formatNumber(pendapatanLain) }}</h3>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 3: Total Pengeluaran (Rose/Red Tone) -->
                    <Link :href="route('reports.pengeluaran', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-red-500/10 dark:bg-red-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Total Pengeluaran</p>
                                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 print:text-black">Rp {{ formatNumber(pengeluaran) }}</h3>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 4: PPN (Amber Tone) -->
                    <Link :href="route('reports.tax', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-20 h-20 bg-amber-500/10 dark:bg-amber-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Total TAX</p>
                                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 print:text-black">Rp {{ formatNumber(ppn) }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 5: Total Fee (Indigo Tone) -->
                    <Link :href="route('sales.fee', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-20 h-20 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Total Fee</p>
                                <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 print:text-black">Rp {{ formatNumber(total_fee) }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 dark:text-indigo-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI NEW: Wajib Setor (Fuchsia Tone) -->
                    <Link :href="route('sales.setoran', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-fuchsia-500/10 dark:bg-fuchsia-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Wajib Setor</p>
                                <h3 class="text-2xl font-extrabold text-fuchsia-600 dark:text-fuchsia-400 print:text-black">Rp {{ formatNumber(setoran + belum_disetor) }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-fuchsia-50 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-500 dark:text-fuchsia-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 6: Sudah Disetor (Teal Tone) -->
                    <Link :href="route('sales.setoran', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-20 h-20 bg-teal-500/10 dark:bg-teal-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Sudah Disetor</p>
                                <h3 class="text-xl font-extrabold text-teal-600 dark:text-teal-400 print:text-black">Rp {{ formatNumber(setoran) }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-500 dark:text-teal-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 7: Belum Disetor (Orange Tone) -->
                    <Link :href="route('sales.setoran', { month: selectedMonth })" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-orange-500/10 dark:bg-orange-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 print:text-black">Belum Disetor</p>
                                <h3 class="text-2xl font-extrabold text-orange-600 dark:text-orange-400 print:text-black">Rp {{ formatNumber(belum_disetor) }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center text-orange-500 dark:text-orange-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- KPI 8: Revenue / Laba Bersih (Emerald/Green Tone) - Full Width -->
                    <Link :href="route('reports.cashflow', { month: selectedMonth })" class="block sm:col-span-2 lg:col-span-4 print:col-span-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-slate-800 dark:to-slate-800 rounded-xl border border-emerald-200 dark:border-slate-700 p-6 relative overflow-hidden group shadow-sm transition-all hover:from-emerald-100 hover:to-teal-100 dark:hover:bg-slate-700/50 hover:-translate-y-1 hover:shadow-md print:border-slate-400 print:shadow-none print:bg-white">
                        <div class="absolute right-0 top-0 w-36 h-36 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 print:hidden"></div>
                        <div class="flex justify-between items-center relative z-10">
                            <div>
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1 print:text-black">Revenue (Laba Bersih)</p>
                                <h3 class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-300 print:text-black">Rp {{ formatNumber((pendapatan + pendapatanLain) - pengeluaran - total_fee) }}</h3>
                                <p class="text-xs text-emerald-500/80 dark:text-emerald-400/60 mt-1 print:text-black">(Tagihan Bulanan + Pendapatan Lain) − Pengeluaran − Fee</p>
                            </div>
                            <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm print:hidden transition-transform group-hover:scale-110">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

const props = defineProps({
    capabilities: Object,
    pendapatan: Number,
    pendapatanLain: Number,
    ppn: Number,
    txnCount: Number,
    pengeluaran: Number,
    setoran: Number,
    month: String,
    tenant: Object,
    total_fee: Number,
    belum_disetor: Number,
});

const pageTitle = computed(() => 'Rekapitulasi Total');

const formatNumber = (value) => {
    if (!value) return '0';
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const selectedMonth = ref(props.month || dayjs().format('YYYY-MM'));
const formattedMonth = computed(() => {
    return selectedMonth.value ? dayjs(selectedMonth.value + '-01').format('MMMM YYYY') : '-';
});

// Print helper
const printData = () => {
    window.print();
};

// Watch month change and reload page
watch(selectedMonth, (newVal) => {
    router.get(usePage().url.split('?')[0], { month: newVal }, {
        preserveState: true,
        preserveScroll: true,
        only: ['pendapatan', 'pendapatanLain', 'ppn', 'txnCount', 'pengeluaran', 'setoran', 'month', 'total_fee', 'belum_disetor']
    });
});
</script>

<style>
@media print {
    body {
        background-color: white !important;
    }
    .sidebar, .header, footer {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .page-content {
        padding: 0 !important;
    }
}
</style>
