<template>
    <AppLayout :title="pageTitle">
        <div class="p-2">
            
            <!-- LIST AGENT SETORAN -->
            <div v-if="!selectedSales" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden text-slate-700 dark:text-slate-200 transition-colors">
                
                <!-- TOP HEADER & FILTERS -->
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Laporan Setoran</h2>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col">
                            <label class="text-xs text-slate-500 dark:text-slate-400 mb-1">Periode (Bulan)</label>
                            <input type="month" v-model="selectedPeriod" class="bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-200 rounded-md py-1.5 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <button @click="fetchData" class="mt-5 px-4 py-1.5 bg-emerald-50 dark:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 text-sm font-medium rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-600/30 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Tampilkan
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 mt-4">
                    <!-- Total Uang Diterima -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Uang Diterima</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rp {{ formatNumber(totalUangDiterima) }}</h3>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">{{ totalTxn }} transaksi - {{ periodLabel }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Fee (Potongan) -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Fee (Potongan)</p>
                                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rp {{ formatNumber(totalFee) }}</h3>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">Sales % + Agent fix</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Yang Harus Disetor -->
                    <div class="bg-slate-800 dark:bg-slate-700 rounded-xl border border-slate-700 dark:border-slate-600 p-5 shadow-md relative overflow-hidden group text-white">
                        <div class="absolute right-0 top-0 w-24 h-24 bg-white/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1">Total Yang Harus Disetor</p>
                                <h3 class="text-2xl font-extrabold">Rp {{ formatNumber(totalHarusDisetor) }}</h3>
                                <p class="text-xs text-slate-400 mt-1">{{ totalSales }} sales/agent</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TABLE TITLE -->
                <div class="px-6 py-4 bg-slate-100/50 dark:bg-slate-800/50 border-t border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300">Rincian Setoran per Sales / Agent</h3>
                    <span class="text-xs text-slate-500 ml-2 font-normal hidden md:inline">Perhitungan: Uang diterima - Fee - Pengeluaran</span>
                </div>

                <!-- TABLE CONTENT -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-2 py-4 w-10 text-center">#</th>
                                <th class="px-2 py-4">Nama</th>
                                <th class="px-2 py-4">Tipe</th>
                                <th class="px-2 py-4 text-right">Uang Diterima</th>
                                <th class="px-2 py-4 text-right">Fee</th>
                                <th class="px-2 py-4 text-right">Harus Disetor</th>
                                <th class="px-2 py-4 text-right">Pengeluaran</th>
                                <th class="px-2 py-4 text-right">Sudah Disetor</th>
                                <th class="px-2 py-4 text-right">Sisa</th>
                                <th class="px-2 py-4 text-center">Txn</th>
                                <th class="px-2 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50">
                            <tr v-for="(sales, index) in salesUsers" :key="sales.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors">
                                <td class="px-2 py-3 text-center text-slate-500 align-top">{{ index + 1 }}</td>
                                <td class="px-2 py-3 font-medium text-slate-800 dark:text-white align-top">{{ sales.name }}</td>
                                <td class="px-2 py-3 align-top">
                                    <span class="px-2 py-0.5 rounded text-[10px] border" :class="getBadgeClass(sales.role_name)">
                                        {{ sales.role_name }}
                                    </span>
                                </td>
                                <td class="px-2 py-3 text-right align-top">Rp {{ formatNumber(sales.uang_diterima) }}</td>
                                <td class="px-2 py-3 text-right text-indigo-600 dark:text-indigo-300 align-top">Rp {{ formatNumber(sales.fee) }}</td>
                                <td class="px-2 py-3 text-right font-medium align-top">Rp {{ formatNumber(sales.harus_disetor) }}</td>
                                <td class="px-2 py-3 text-right text-rose-600 dark:text-rose-300 align-top">Rp {{ formatNumber(sales.pengeluaran) }}</td>
                                <td class="px-2 py-3 text-right text-emerald-600 dark:text-emerald-400 align-top">Rp {{ formatNumber(sales.sudah_disetor) }}</td>
                                <td class="px-2 py-3 text-right font-bold align-top" :class="sales.sisa > 0 ? 'text-sky-600 dark:text-sky-400' : 'text-slate-400'">Rp {{ formatNumber(sales.sisa) }}</td>
                                <td class="px-2 py-3 text-center align-top">{{ sales.txn }}</td>
                                <td class="px-2 py-3 text-center align-top">
                                    <div class="flex items-center justify-center gap-2">
                                        <button v-if="sales.sisa > 0" @click="openSetorModal(sales)" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 rounded hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors text-[10px] uppercase font-bold tracking-wide">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Setor
                                        </button>
                                        <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 text-emerald-600 dark:text-emerald-500 text-[10px] uppercase font-bold tracking-wide">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Lunas
                                        </span>
                                        <button @click="viewHistory(sales)" class="inline-flex items-center gap-1 px-2.5 py-1 bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/30 rounded hover:bg-sky-100 dark:hover:bg-sky-500/20 transition-colors text-[10px] uppercase font-bold tracking-wide whitespace-nowrap">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            History
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="salesUsers.length === 0">
                                <td colspan="11" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data setoran di periode ini.
                                </td>
                            </tr>
                            <!-- TOTAL FOOTER -->
                            <tr v-if="salesUsers.length > 0" class="bg-slate-100 dark:bg-slate-800 font-bold text-slate-800 dark:text-white border-t border-slate-300 dark:border-slate-700">
                                <td colspan="3" class="px-2 py-4 text-center">TOTAL KESELURUHAN</td>
                                <td class="px-2 py-4 text-right">Rp {{ formatNumber(totalUangDiterima) }}</td>
                                <td class="px-2 py-4 text-right text-indigo-600 dark:text-indigo-300">Rp {{ formatNumber(totalFee) }}</td>
                                <td class="px-2 py-4 text-right">Rp {{ formatNumber(totalHarusDisetor) }}</td>
                                <td class="px-2 py-4 text-right text-rose-600 dark:text-rose-300">Rp {{ formatNumber(salesUsers.reduce((sum, item) => sum + item.pengeluaran, 0)) }}</td>
                                <td class="px-2 py-4 text-right text-emerald-600 dark:text-emerald-400">Rp {{ formatNumber(salesUsers.reduce((sum, item) => sum + item.sudah_disetor, 0)) }}</td>
                                <td class="px-2 py-4 text-right text-sky-600 dark:text-sky-400">Rp {{ formatNumber(salesUsers.reduce((sum, item) => sum + item.sisa, 0)) }}</td>
                                <td class="px-2 py-4 text-center">{{ totalTxn }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DETAIL HISTORY SETORAN -->
            <div v-else>
                <!-- Header Control -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <button @click="closeHistory" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2 shadow-sm">
                        &larr; Kembali
                    </button>
                    <div class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Histori Setoran
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                        <h2 class="text-base font-semibold text-slate-800 dark:text-white">Riwayat Setoran — {{ selectedSales.name }} ({{ periodLabel }})</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase font-medium text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3 w-16">#</th>
                                    <th class="px-6 py-3">Tanggal Setor</th>
                                    <th class="px-6 py-3">Jumlah</th>
                                    <th class="px-6 py-3">Penerima</th>
                                    <th class="px-6 py-3">Keterangan</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                <tr v-if="loadingHistory" class="animate-pulse">
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Memuat riwayat setoran...</td>
                                </tr>
                                <tr v-else-if="deposits.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada riwayat setoran di bulan ini.</td>
                                </tr>
                                <tr v-else v-for="(dep, index) in deposits" :key="dep.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">{{ index + 1 }}</td>
                                    <td class="px-6 py-4">{{ dep.deposit_date }}</td>
                                    <td class="px-6 py-4 font-medium text-emerald-600 dark:text-emerald-400">Rp {{ formatNumber(dep.amount) }}</td>
                                    <td class="px-6 py-4">{{ dep.receiver_name }}</td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ dep.notes }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button v-if="dep.status === 'active' && capabilities.includes('billing.deposits.cancel')" @click="cancelDeposit(dep.id)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-xs font-medium rounded-full hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                            Cancel
                                        </button>
                                        <span v-else-if="dep.status !== 'active'" class="text-xs text-slate-500 italic">Dibatalkan</span>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODAL TAMBAH SETORAN -->
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/40 dark:bg-slate-900/80 backdrop-blur-sm" @click="closeSetorModal"></div>
                    <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <form @submit.prevent="submitSetoran">
                            <div class="px-6 pt-5 pb-4">
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Terima Setoran Baru</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Dari: {{ form.sales_name }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nominal Setoran (Rp)</label>
                                        <input v-model="form.amount" type="number" min="1" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5" placeholder="Masukkan jumlah setoran">
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Disarankan: Rp {{ formatNumber(suggestedAmount) }} (Sisa yang belum disetor)</div>
                                        <div v-if="form.errors.amount" class="text-red-500 dark:text-red-400 text-xs mt-1">{{ form.errors.amount }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Setor</label>
                                        <input v-model="form.deposit_date" type="date" required class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5">
                                        <div v-if="form.errors.deposit_date" class="text-red-500 dark:text-red-400 text-xs mt-1">{{ form.errors.deposit_date }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                                        <textarea v-model="form.notes" rows="2" class="w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5" placeholder="Catatan opsional..."></textarea>
                                        <div v-if="form.errors.notes" class="text-red-500 dark:text-red-400 text-xs mt-1">{{ form.errors.notes }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                                <button type="button" @click="closeSetorModal" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900">
                                    <svg v-if="form.processing" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Simpan Setoran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    capabilities: Array,
    salesUsers: Array,
    month: String,
    totalUangDiterima: Number,
    totalTxn: Number,
    totalFee: Number,
    totalHarusDisetor: Number,
    totalSales: Number
});

const pageTitle = computed(() => 'Laporan Setoran');

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};

const periodLabel = computed(() => {
    if (!props.month) return '-';
    let parts = props.month.split('-');
    let date = new Date(parts[0], parts[1] - 1, 1);
    return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(date);
});

// Period Filter
const selectedPeriod = ref(props.month);
const fetchData = () => {
    router.get(route('sales.setoran'), { month: selectedPeriod.value }, { preserveState: true });
};

// Styling Role Badge (Light & Dark mode support)
const getBadgeClass = (roleName) => {
    const role = (roleName || '').toLowerCase();
    if (role.includes('admin')) return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800';
    if (role.includes('sales')) return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
    if (role.includes('tagih')) return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800';
    return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
};

// State for History
const selectedSales = ref(null);
const deposits = ref([]);
const loadingHistory = ref(false);

const viewHistory = (sales) => {
    selectedSales.value = sales;
    fetchHistory();
};

const closeHistory = () => {
    selectedSales.value = null;
    deposits.value = [];
};

const fetchHistory = async () => {
    if (!selectedSales.value) return;
    loadingHistory.value = true;
    try {
        const res = await axios.get(route('sales.mitra.deposits', selectedSales.value.id), {
            params: { month: selectedPeriod.value }
        });
        deposits.value = res.data;
    } catch (e) {
        console.error("Gagal mengambil riwayat setoran", e);
    } finally {
        loadingHistory.value = false;
    }
};

const cancelDeposit = (id) => {
    if (confirm('Yakin ingin membatalkan setoran ini? Saldo Sisa Agen akan otomatis menyesuaikan.')) {
        router.delete(route('sales.setoran.destroy', id), {
            onSuccess: () => {
                fetchHistory();
            },
            preserveScroll: true
        });
    }
};

// State for Modal Setor
const showModal = ref(false);
const suggestedAmount = ref(0);
const form = useForm({
    sales_id: '',
    sales_name: '',
    amount: '',
    deposit_date: new Date().toISOString().slice(0, 10),
    notes: ''
});

const openSetorModal = (sales) => {
    suggestedAmount.value = sales.sisa > 0 ? sales.sisa : 0;
    form.sales_id = sales.id;
    form.sales_name = sales.name;
    form.amount = suggestedAmount.value;
    form.deposit_date = new Date().toISOString().slice(0, 10);
    form.notes = '';
    form.clearErrors();
    showModal.value = true;
};

const closeSetorModal = () => {
    showModal.value = false;
};

const submitSetoran = () => {
    form.post(route('sales.setoran.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeSetorModal();
        }
    });
};
</script>
