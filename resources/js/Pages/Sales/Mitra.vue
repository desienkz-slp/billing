<template>
    <AppLayout :title="pageTitle">
        <div class="p-2">
            
            <!-- LIST MITRA -->
            <div v-if="!selectedMitra" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Daftar Mitra (Sales)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-medium text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Mitra</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Fee</th>
                                <th class="px-6 py-4 text-center">Pelanggan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="mitra in salesData" :key="mitra.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ mitra.name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-md border border-blue-200 dark:border-blue-800">
                                        {{ mitra.role_name || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ formatFee(mitra.fee_persen, mitra.fee_fix) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-medium text-xs border border-slate-200 dark:border-slate-700">
                                        {{ mitra.customers_count !== undefined && mitra.customers_count !== null ? mitra.customers_count : 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="viewMitra(mitra)" class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                                        Lihat Setoran &rarr;
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="salesData.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada user dengan hak akses bayar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DETAIL MITRA -->
            <div v-else>
                <!-- Header Control -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <button @click="closeMitra" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2 shadow-sm">
                        &larr; Semua Mitra
                    </button>
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        Laporan Setoran
                    </div>
                    <select v-model="selectedPeriod" @change="fetchDeposits" class="bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg py-2 pl-4 pr-8 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">Semua periode</option>
                        <option v-for="m in availablePeriods" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                </div>

                <!-- Table Container -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <!-- Title -->
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Riwayat Setoran — {{ selectedMitra.name }}</h2>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase font-medium text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3 w-16">#</th>
                                    <th class="px-6 py-3">Periode</th>
                                    <th class="px-6 py-3">Jumlah</th>
                                    <th class="px-6 py-3">Tgl Setor</th>
                                    <th class="px-6 py-3">Keterangan</th>
                                    <th class="px-6 py-3">Dibuat Oleh</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                <tr v-if="loading" class="animate-pulse">
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        Memuat riwayat setoran...
                                    </td>
                                </tr>
                                <tr v-else-if="deposits.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada riwayat setoran di periode ini.
                                    </td>
                                </tr>
                                <tr v-else v-for="(dep, index) in deposits" :key="dep.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">{{ index + 1 }}</td>
                                    <td class="px-6 py-4">{{ dep.period_label }}</td>
                                    <td class="px-6 py-4 font-medium text-emerald-600 dark:text-emerald-400">Rp {{ formatNumber(dep.amount) }}</td>
                                    <td class="px-6 py-4">{{ dep.deposit_date }}</td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ dep.notes }}</td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ dep.receiver_name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button v-if="dep.status === 'active' && capabilities.includes('billing.deposits.cancel')" @click="cancelDeposit(dep.id)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-slate-900 border border-red-500/30 text-red-500 dark:text-red-400 text-xs font-medium rounded-full hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Cancel
                                        </button>
                                        <span v-else-if="dep.status !== 'active'" class="text-xs text-slate-500 italic">Dibatalkan</span>
                                        <span v-else class="text-xs text-slate-500">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

const props = defineProps({
    capabilities: Array,
    salesData: Array,
});

const pageTitle = computed(() => 'Manajemen Mitra');

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};

const formatFee = (persen, fix) => {
    let parts = [];
    if (persen > 0) parts.push(`${persen}%`);
    if (fix > 0) parts.push(`Rp ${formatNumber(fix)}`);
    return parts.length > 0 ? parts.join(' + ') : '-';
};

// State for Detail View
const selectedMitra = ref(null);
const selectedPeriod = ref('');
const deposits = ref([]);
const loading = ref(false);

const availablePeriods = computed(() => {
    let periods = [];
    let date = new Date();
    for (let i = 0; i < 12; i++) {
        let val = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        let label = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(date);
        periods.push({ value: val, label });
        date.setMonth(date.getMonth() - 1);
    }
    return periods;
});

const viewMitra = (mitra) => {
    selectedMitra.value = mitra;
    selectedPeriod.value = '';
    fetchDeposits();
};

const closeMitra = () => {
    selectedMitra.value = null;
    deposits.value = [];
};

const fetchDeposits = async () => {
    if (!selectedMitra.value) return;
    loading.value = true;
    try {
        const res = await axios.get(route('sales.mitra.deposits', selectedMitra.value.id), {
            params: { month: selectedPeriod.value || null }
        });
        deposits.value = res.data;
    } catch (e) {
        console.error("Gagal mengambil riwayat setoran", e);
    } finally {
        loading.value = false;
    }
};

const cancelDeposit = async (id) => {
    const isConfirmed = await confirm({
        title: 'Batalkan Setoran',
        message: 'Yakin ingin membatalkan setoran ini?',
        confirmText: 'Ya, Batalkan',
        cancelText: 'Kembali',
        confirmColor: 'rose'
    });

    if (isConfirmed) {
        router.delete(route('sales.setoran.destroy', id), {
            onSuccess: () => fetchDeposits(),
            preserveScroll: true
        });
    }
};
</script>
