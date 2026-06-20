<template>
    <AppLayout :title="pageTitle">
        <div class="p-2">
            
            <!-- LIST MITRA/SALES -->
            <div v-if="!selectedMitra" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Manajemen Saldo Mitra</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-medium text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Mitra</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4 text-right">Saldo Saat Ini</th>
                                <th class="px-6 py-4 text-center">Aksi Saldo</th>
                                <th class="px-6 py-4 text-right">Histori</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="mitra in salesUsers" :key="mitra.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ mitra.name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-md border border-blue-200 dark:border-blue-800">
                                        {{ mitra.role_name || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold" :class="{'text-emerald-600 dark:text-emerald-400': mitra.deposit_balance > 0, 'text-red-500 dark:text-red-400': mitra.deposit_balance <= 0}">
                                    Rp {{ formatNumber(mitra.deposit_balance) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openModal('add', mitra)" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 flex items-center justify-center border border-emerald-200 dark:border-emerald-800 transition-colors" title="Tambah Saldo">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                        <button @click="openModal('deduct', mitra)" class="w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 flex items-center justify-center border border-red-200 dark:border-red-800 transition-colors" title="Kurangi Saldo">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="viewHistory(mitra)" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                        Lihat Histori &rarr;
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="salesUsers.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada mitra yang memiliki pembatasan saldo.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DETAIL HISTORY -->
            <div v-else>
                <!-- Header Control -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <button @click="closeMitra" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2 shadow-sm">
                        &larr; Kembali
                    </button>
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Histori Mutasi Saldo
                    </div>
                </div>

                <!-- Table Container -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <!-- Title -->
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Mutasi — {{ selectedMitra.name }}</h2>
                        </div>
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            Saldo Saat Ini: <span class="text-slate-900 dark:text-white">Rp {{ formatNumber(selectedMitra.deposit_balance) }}</span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase font-medium text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3 w-16">#</th>
                                    <th class="px-6 py-3">Waktu</th>
                                    <th class="px-6 py-3">Tipe</th>
                                    <th class="px-6 py-3 text-right">Nominal</th>
                                    <th class="px-6 py-3 text-right">Saldo Sebelum</th>
                                    <th class="px-6 py-3 text-right">Saldo Sesudah</th>
                                    <th class="px-6 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                <tr v-if="loading" class="animate-pulse">
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        Memuat histori mutasi...
                                    </td>
                                </tr>
                                <tr v-else-if="mutations.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada mutasi saldo.
                                    </td>
                                </tr>
                                <tr v-else v-for="(mut, index) in mutations" :key="mut.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">{{ index + 1 }}</td>
                                    <td class="px-6 py-4">{{ mut.created_at }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="mut.type === 'credit'" class="px-2 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-medium rounded border border-emerald-200 dark:border-emerald-800">Masuk</span>
                                        <span v-else class="px-2 py-1 bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 text-xs font-medium rounded border border-red-200 dark:border-red-800">Keluar</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium" :class="mut.type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'">
                                        {{ mut.type === 'credit' ? '+' : '-' }} Rp {{ formatNumber(mut.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-500">Rp {{ formatNumber(mut.balance_before) }}</td>
                                    <td class="px-6 py-4 text-right font-medium">Rp {{ formatNumber(mut.balance_after) }}</td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                        {{ mut.notes }}
                                        <div class="text-xs text-slate-400 mt-1">Oleh: {{ mut.creator_name }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- MODAL TAMBAH/KURANG SALDO -->
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/75 dark:bg-slate-900/90" @click="closeModal"></div>
                    <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                        <form @submit.prevent="submitSaldo">
                            <div class="px-6 pt-5 pb-4">
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full" :class="modalType === 'add' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400'">
                                        <svg v-if="modalType === 'add'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                            {{ modalType === 'add' ? 'Tambah Saldo' : 'Kurangi Saldo' }}
                                        </h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ modalTarget?.name }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nominal (Rp)</label>
                                        <input v-model="form.amount" type="number" min="1" required class="w-full bg-slate-50 dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5" placeholder="Masukkan jumlah nominal">
                                        <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                                        <textarea v-model="form.notes" rows="2" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5" placeholder="Keterangan opsional..."></textarea>
                                        <div v-if="form.errors.notes" class="text-red-500 text-xs mt-1">{{ form.errors.notes }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                                <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors flex items-center justify-center gap-2" :class="modalType === 'add' ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1' : 'bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-1'">
                                    <svg v-if="form.processing" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    {{ modalType === 'add' ? 'Tambahkan' : 'Kurangi' }}
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
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    capabilities: Array,
    salesUsers: Array,
});

const pageTitle = computed(() => 'Manajemen Saldo');

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};

// State for History View
const selectedMitra = ref(null);
const mutations = ref([]);
const loading = ref(false);

const viewHistory = (mitra) => {
    selectedMitra.value = mitra;
    fetchHistory();
};

const closeMitra = () => {
    selectedMitra.value = null;
    mutations.value = [];
};

const fetchHistory = async () => {
    if (!selectedMitra.value) return;
    loading.value = true;
    try {
        const res = await axios.get(route('sales.saldo.history', selectedMitra.value.id));
        mutations.value = res.data;
    } catch (e) {
        console.error("Gagal mengambil riwayat mutasi", e);
    } finally {
        loading.value = false;
    }
};

// Modal State
const showModal = ref(false);
const modalType = ref('add');
const modalTarget = ref(null);
const form = useForm({
    amount: '',
    notes: '',
});

const openModal = (type, mitra) => {
    modalType.value = type;
    modalTarget.value = mitra;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    modalTarget.value = null;
};

const submitSaldo = () => {
    if (!modalTarget.value) return;
    
    const url = modalType.value === 'add' 
        ? route('sales.saldo.add', modalTarget.value.id)
        : route('sales.saldo.deduct', modalTarget.value.id);
        
    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Saldo is automatically updated via Inertia props response
        }
    });
};
</script>
