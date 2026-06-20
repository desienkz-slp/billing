<template>
    <AppLayout :title="pageTitle">
        <div class="px-2 py-1 w-full max-w-full h-full flex flex-col min-h-[calc(100vh-100px)] mx-auto">
            
            <!-- Header & Filter -->
            <div class="mb-6 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">Periode</div>
                    <div class="flex items-center">
                        <input type="month" v-model="filterMonth" 
                            class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow text-sm py-2" />
                    </div>
                    <button @click="applyFilter" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Tampilkan
                    </button>
                </div>
                <button @click="exportPdf" class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print PDF
                </button>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Arus Masuk -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Arus Masuk</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatRupiah(reportData.summary.total_masuk) }}</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-500 mt-1">Penerimaan Pelanggan</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Total Arus Keluar -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-rose-500/10 dark:bg-rose-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Arus Keluar</p>
                            <h3 class="text-2xl font-extrabold text-rose-600 dark:text-rose-400">{{ formatRupiah(reportData.summary.total_keluar) }}</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-500 mt-1">Pengeluaran, Fee, PPN</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-500 dark:text-rose-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Arus Kas Bersih -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-blue-500/10 dark:bg-blue-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Arus Kas Bersih</p>
                            <h3 class="text-2xl font-extrabold" :class="reportData.summary.bersih_bulan_ini >= 0 ? 'text-slate-800 dark:text-slate-100' : 'text-rose-600 dark:text-rose-400'">
                                {{ formatRupiah(reportData.summary.bersih_bulan_ini) }}
                            </h3>
                            <div class="text-[11px] mt-1 flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                <span>{{ reportData.summary.last_month_label }}: {{ formatRupiah(reportData.summary.bersih_bulan_lalu) }}</span>
                                <span :class="reportData.summary.growth >= 0 ? 'text-emerald-500 font-bold' : 'text-rose-500 font-bold'">
                                    <span v-if="reportData.summary.growth >= 0">▲</span>
                                    <span v-else>▼</span>
                                    {{ reportData.summary.growth }}%
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LAPORAN ARUS KAS (Board) -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl flex flex-col flex-1">
                <!-- Header Laporan -->
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800 flex justify-between items-center rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white uppercase">Laporan Arus Kas</h3>
                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                        Per {{ formatPeriodeLabel(filterMonth) }}
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto">
                    <!-- SECTION A -->
                    <h4 class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-6 uppercase tracking-wider">A. Arus Kas Dari Aktivitas Operasional</h4>

                    <!-- ARUS MASUK -->
                    <div class="mb-8">
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 bg-slate-50 dark:bg-[#1E293B] px-3 py-1.5 rounded inline-block">Arus Masuk</div>
                        
                        <!-- Penerimaan -->
                        <div class="flex justify-between items-center py-2.5 text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="font-medium">Penerimaan Pembayaran Pelanggan</div>
                            <div class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatRupiah(reportData.arus_masuk.penerimaan_pelanggan.total) }}</div>
                        </div>
                        <!-- Rincian Metode -->
                        <div v-for="metode in reportData.arus_masuk.penerimaan_pelanggan.metode" :key="metode.nama" class="flex justify-between items-center py-2 text-[13px] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/30 pl-8 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                                via {{ metode.nama }} <span class="text-xs text-slate-400 dark:text-slate-500">({{ metode.jumlah_trx }} transaksi)</span>
                            </div>
                            <div class="font-medium text-emerald-600/80 dark:text-emerald-400/80">{{ formatRupiah(metode.total) }}</div>
                        </div>

                        <!-- Pendapatan Lain (if any) -->
                        <div v-if="reportData.arus_masuk.pendapatan_lain.total > 0" class="mt-4">
                            <div class="flex justify-between items-center py-2.5 text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="font-medium">Pendapatan Lain-lain</div>
                                <div class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatRupiah(reportData.arus_masuk.pendapatan_lain.total) }}</div>
                            </div>
                            <div v-for="lain in reportData.arus_masuk.pendapatan_lain.items" :key="lain.nama" class="flex justify-between items-center py-2 text-[13px] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/30 pl-8 transition-colors">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                    {{ lain.nama }} <span class="text-xs text-slate-400 dark:text-slate-500">({{ lain.jumlah_trx }} transaksi)</span>
                                </div>
                                <div class="font-medium text-emerald-600/80 dark:text-emerald-400/80">{{ formatRupiah(lain.total) }}</div>
                            </div>
                        </div>

                        <!-- TOTAL MASUK -->
                        <div class="flex justify-between items-center py-4 mt-3 border-t-2 border-slate-200 dark:border-slate-600 text-sm">
                            <div class="font-extrabold text-slate-800 dark:text-slate-200 uppercase">Total Arus Masuk</div>
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 text-base">{{ formatRupiah(reportData.arus_masuk.total) }}</div>
                        </div>
                    </div>

                    <!-- ARUS KELUAR -->
                    <div class="mb-4">
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4 bg-slate-50 dark:bg-[#1E293B] px-3 py-1.5 rounded inline-block">Arus Keluar</div>
                        
                        <!-- Fee -->
                        <div class="flex justify-between items-center py-2.5 text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="font-medium">Pembayaran Fee per User</div>
                            <div class="font-semibold text-rose-600 dark:text-rose-400">{{ formatRupiah(reportData.arus_keluar.fee_per_user.total) }}</div>
                        </div>
                        <div v-for="user in reportData.arus_keluar.fee_per_user.users" :key="'fee_'+user.user" class="flex justify-between items-center py-2 text-[13px] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/30 pl-8 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                                {{ user.user }}
                            </div>
                            <div class="font-medium text-rose-600/80 dark:text-rose-400/80">{{ formatRupiah(user.total) }}</div>
                        </div>

                        <!-- Pengeluaran Operasional -->
                        <div class="flex justify-between items-center py-2.5 mt-4 text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="font-medium">Pengeluaran per User</div>
                            <div class="font-semibold text-rose-600 dark:text-rose-400">{{ formatRupiah(reportData.arus_keluar.pengeluaran_per_user.total) }}</div>
                        </div>
                        <div v-for="user in reportData.arus_keluar.pengeluaran_per_user.users" :key="'exp_'+user.user" class="flex justify-between items-center py-2 text-[13px] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/30 pl-8 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 dark:text-slate-500">—</span>
                                {{ user.user }} <span class="text-xs text-slate-400 dark:text-slate-500">({{ user.jumlah_item }} item)</span>
                            </div>
                            <div class="font-medium text-rose-600/80 dark:text-rose-400/80">{{ formatRupiah(user.total) }}</div>
                        </div>

                        <!-- PPN -->
                        <div class="flex justify-between items-center py-2.5 mt-4 text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <div class="font-medium">PPN Tercatat</div>
                            <div class="font-semibold text-rose-600 dark:text-rose-400">{{ formatRupiah(reportData.arus_keluar.ppn) }}</div>
                        </div>

                        <!-- TOTAL KELUAR -->
                        <div class="flex justify-between items-center py-4 mt-3 border-t-2 border-slate-200 dark:border-slate-600 text-sm">
                            <div class="font-extrabold text-slate-800 dark:text-slate-200 uppercase">Total Arus Keluar</div>
                            <div class="font-bold text-rose-600 dark:text-rose-400 text-base">{{ formatRupiah(reportData.arus_keluar.total) }}</div>
                        </div>
                    </div>
                </div>

                <!-- TOTAL BERSIH -->
                <div class="px-6 py-6 bg-slate-50 dark:bg-[#1E293B] border-t border-slate-200 dark:border-slate-700 rounded-b-2xl flex justify-between items-center">
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-white uppercase">Arus Kas Bersih Periode Ini</h3>
                    <div class="text-xl font-bold" :class="reportData.summary.bersih_bulan_ini >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500'">
                        {{ formatRupiah(reportData.summary.bersih_bulan_ini) }}
                    </div>
                </div>
            </div>

            <!-- RINCIAN TRANSAKSI (LEDGER) -->
            <div class="mt-6 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl flex flex-col">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800 flex justify-between items-center rounded-t-2xl">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white uppercase">Rincian Transaksi Historikal</h3>

                </div>
                
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-slate-600 dark:text-slate-400 min-w-[700px]">
                        <thead class="text-xs uppercase bg-slate-50 dark:bg-[#1E293B] text-slate-500 dark:text-slate-400 sticky top-0 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-2 font-semibold">Waktu / Tanggal</th>
                                <th class="p-2 font-semibold">Keterangan</th>
                                <th class="p-2 font-semibold text-right">Masuk</th>
                                <th class="p-2 font-semibold text-right">Keluar</th>
                                <th class="p-2 font-semibold text-right">Saldo Berjalan</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm">
                            <tr v-if="reportData.riwayat_transaksi.data.length === 0">
                                <td colspan="5" class="p-2 text-center text-slate-500">Tidak ada transaksi pada periode ini.</td>
                            </tr>
                            <tr v-for="(trx, index) in reportData.riwayat_transaksi.data" :key="index" class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="p-2 whitespace-nowrap">{{ formatDateTime(trx.tanggal) }}</td>
                                <td class="p-2">
                                    <div class="font-medium text-slate-700 dark:text-slate-300">{{ trx.keterangan }}</div>
                                    <div v-if="trx.referensi !== '-'" class="text-[11px] text-slate-500 mt-0.5">Ref: {{ trx.referensi }}</div>
                                </td>
                                <td class="p-2 text-right font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ trx.masuk > 0 ? formatRupiah(trx.masuk) : '-' }}
                                </td>
                                <td class="p-2 text-right font-medium text-rose-600 dark:text-rose-400">
                                    {{ trx.keluar > 0 ? formatRupiah(trx.keluar) : '-' }}
                                </td>
                                <td class="p-2 text-right font-bold text-slate-700 dark:text-slate-300">
                                    {{ formatRupiah(trx.saldo) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50/80 dark:bg-[#1E293B] border-t border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <tr>
                                <td colspan="2" class="p-2 text-right uppercase tracking-wider">Total Transaksi</td>
                                <td class="p-2 text-right text-emerald-600 dark:text-emerald-400">
                                    {{ formatRupiah(reportData.arus_masuk.total) }}
                                </td>
                                <td class="p-2 text-right text-rose-600 dark:text-rose-400">
                                    {{ formatRupiah(reportData.arus_keluar.total) }}
                                </td>
                                <td class="p-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    capabilities: Object,
    reportData: Object,
    month: String,
});

const pageTitle = computed(() => 'Laporan Arus Kas');

const filterMonth = ref(props.month);

const applyFilter = () => {
    router.get(route('reports.cashflow'), { month: filterMonth.value }, {
        preserveState: true,
        replace: true
    });
};

const formatPeriodeLabel = (monthStr) => {
    if (!monthStr) return '';
    const [year, month] = monthStr.split('-');
    const date = new Date(year, parseInt(month) - 1, 1);
    const lastDay = new Date(year, parseInt(month), 0).getDate();
    const monthName = date.toLocaleString('en-US', { month: 'long' });
    return `01 ${monthName} ${year} s/d ${lastDay} ${monthName} ${year}`;
};

const formatRupiah = (number) => {
    if (number === null || number === undefined) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const exportPdf = () => {
    window.print();
};
</script>

<style scoped>
@media print {
    button, input, select { display: none !important; }
    nav, aside { display: none !important; }
    .max-w-7xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body, html { background: white !important; }
}
</style>