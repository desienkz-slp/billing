<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    capabilities: Object,
    payments: Array,
    totalPpn: Number,
    totalBhp: Number,
    totalAdmin: Number,
    month: String,
});

const formFilters = ref({
    month: props.month || '',
});

watch(formFilters, (newValues) => {
    router.get(route('tax'), newValues, { preserveState: true, replace: true });
}, { deep: true });

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Laporan Tax & Biaya" />

    <AppLayout title="Laporan Tax & Biaya">
        <div class="p-2 w-full max-w-full w-full mx-auto flex flex-col">
            
            <!-- Header Section -->
            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0 mt-1">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M14 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Laporan Tax & Biaya</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian PPN, BHP USO, dan Biaya Admin per Pelanggan.</p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
                <!-- Card PPN -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total PPN</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ formatRupiah(totalPpn) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Card BHP USO -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total BHP USO</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ formatRupiah(totalBhp) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Card Admin -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-rose-500/10 dark:bg-rose-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Biaya Admin</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ formatRupiah(totalAdmin) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-xl rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Filters -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                        <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full max-w-[250px]">
                            <div class="flex items-center px-3 w-full">
                                <input type="month" v-model="formFilters.month" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 w-full py-1" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th scope="col" class="px-6 py-4 whitespace-nowrap w-40">Waktu</th>
                                <th scope="col" class="px-6 py-4 min-w-[200px]">Pelanggan</th>
                                <th scope="col" class="px-6 py-4 whitespace-nowrap w-40 hidden md:table-cell">Paket</th>
                                <th scope="col" class="px-6 py-4 text-right whitespace-nowrap w-32 hidden xl:table-cell">BHP USO</th>
                                <th scope="col" class="px-6 py-4 text-right whitespace-nowrap w-32 hidden xl:table-cell">Admin</th>
                                <th scope="col" class="px-6 py-4 text-right whitespace-nowrap w-32">PPN</th>
                                <th scope="col" class="px-6 py-4 text-right whitespace-nowrap w-40">Total Tagihan (Semua)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="payment in payments" :key="payment.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ payment.payment_date }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ payment.customer_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                        {{ payment.package_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap hidden xl:table-cell">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ formatRupiah(payment.bhp_uso) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap hidden xl:table-cell">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ formatRupiah(payment.admin) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ formatRupiah(payment.ppn) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ formatRupiah(payment.ppn + payment.bhp_uso + payment.admin) }}</span>
                                </td>
                            </tr>
                            <tr v-if="payments.length === 0">
                                <td colspan="7" class="px-6 py-16 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tidak ada data pajak & biaya pada bulan ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
