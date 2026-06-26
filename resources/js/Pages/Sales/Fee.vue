<template>
    <AppLayout title="Laporan Fee">
        <div class="p-2 w-full max-w-full flex flex-col w-full mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M15 9.5a3 3 0 0 0-6 0c0 2 6 3 6 6a3 3 0 0 1-6 0"/>
                        </svg>
                        Laporan Fee
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Laporan komisi Sales dan Kolektor berdasarkan bulan.</p>
                </div>
                
                <div class="flex items-center gap-3 bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300 ml-2">Bulan:</label>
                    <input 
                        type="month" 
                        v-model="selectedMonth" 
                        @change="filterData"
                        class="border-none bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-white text-sm rounded-md focus:ring-indigo-500 cursor-pointer"
                    >
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Total Sales Fee -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Fee Sales</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rp {{ formatNumber(totalSalesFee) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Total Collector Fee -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-purple-500/10 dark:bg-purple-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Fee Kolektor</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rp {{ formatNumber(totalCollectorFee) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Total Keseluruhan -->
                <div class="bg-indigo-600 dark:bg-indigo-500 rounded-xl border border-indigo-700 p-5 shadow-md relative overflow-hidden group text-white">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-white/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-indigo-200 uppercase tracking-wider mb-1">Total Keseluruhan</p>
                            <h3 class="text-2xl font-extrabold">Rp {{ formatNumber(totalKeseluruhan) }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M15 9.5a3 3 0 0 0-6 0c0 2 6 3 6 6a3 3 0 0 1-6 0"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800 dark:text-white">Rincian Hak Fee User</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-4 w-10 text-center">#</th>
                                <th class="px-4 py-4">Nama</th>
                                <th class="px-4 py-4">Tipe / Role</th>
                                <th class="px-4 py-4 text-right">Fee Sales</th>
                                <th class="px-4 py-4 text-right">Fee Kolektor</th>
                                <th class="px-4 py-4 text-right">Total Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50">
                            <tr v-for="(fee, index) in userFees" :key="fee.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ fee.name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] border" :class="getBadgeClass(fee.role_name)">
                                        {{ fee.role_name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-blue-600 dark:text-blue-400">Rp {{ formatNumber(fee.sales_fee) }}</td>
                                <td class="px-4 py-3 text-right text-purple-600 dark:text-purple-400">Rp {{ formatNumber(fee.collector_fee) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600 dark:text-indigo-400">Rp {{ formatNumber(fee.total_fee) }}</td>
                            </tr>
                            <tr v-if="userFees.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data fee di periode ini.
                                </td>
                            </tr>
                            <!-- TOTAL FOOTER -->
                            <tr v-if="userFees.length > 0" class="bg-slate-100 dark:bg-slate-800 font-bold text-slate-800 dark:text-white border-t border-slate-300 dark:border-slate-700">
                                <td colspan="3" class="px-4 py-4 text-center">TOTAL KESELURUHAN</td>
                                <td class="px-4 py-4 text-right text-blue-600 dark:text-blue-400">Rp {{ formatNumber(totalSalesFee) }}</td>
                                <td class="px-4 py-4 text-right text-purple-600 dark:text-purple-400">Rp {{ formatNumber(totalCollectorFee) }}</td>
                                <td class="px-4 py-4 text-right text-indigo-600 dark:text-indigo-400">Rp {{ formatNumber(totalKeseluruhan) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    capabilities: Object,
    userFees: Array,
    month: String,
    totalSalesFee: Number,
    totalCollectorFee: Number,
    totalKeseluruhan: Number
});

const selectedMonth = ref(props.month);

const filterData = () => {
    router.get(route('sales.fee'), { month: selectedMonth.value }, { preserveState: true, replace: true });
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(value || 0);
};

const getBadgeClass = (role) => {
    const roles = {
        'Superadmin': 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/30',
        'Administrator': 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/30',
        'Mitra': 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
        'Sales': 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
        'Kolektor': 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30',
    };
    return roles[role] || 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/30';
};
</script>
