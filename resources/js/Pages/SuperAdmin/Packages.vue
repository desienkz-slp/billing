<template>
    <SuperadminLayout :title="pageTitle">
        <div class="p-2">
            
            <!-- Page Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Master Paket SaaS</h1>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button v-if="!showForm" @click="showCreateForm" type="button" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Buat Paket Baru
                    </button>
                    <button v-else @click="showForm = false" type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white dark:bg-slate-800 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:w-auto transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Daftar
                    </button>
                </div>
            </div>

            <!-- List / Table View -->
            <div v-if="!showForm" class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Nama Paket</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Harga & Siklus</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Limit User</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Limit Router</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">WA Addon</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            
                            <!-- Row 1 -->
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">S</div>
                                        <div class="ml-4">
                                            <div class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Starter</div>
                                            <div class="text-xs text-slate-500">Cocok untuk ISP rintisan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white">Rp 150.000</div>
                                    <div class="text-xs text-slate-500">per Bulan</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                        100 Pelanggan
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                                    1 Router
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">
                                        Tidak Aktif
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <button @click="editPackage('Starter')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                    <span class="text-slate-300 mx-2">|</span>
                                    <button @click="deletePackage('Starter')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-500 text-white flex items-center justify-center font-bold text-lg relative">
                                            P
                                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white flex items-center">
                                                Pro <span class="ml-2 px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded">POPULER</span>
                                            </div>
                                            <div class="text-xs text-slate-500">Skala menengah</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white">Rp 350.000</div>
                                    <div class="text-xs text-slate-500">per Bulan</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                        500 Pelanggan
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                                    3 Router
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <button @click="editPackage('Pro')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                    <span class="text-slate-300 mx-2">|</span>
                                    <button @click="deletePackage('Pro')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-lg">E</div>
                                        <div class="ml-4">
                                            <div class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Enterprise</div>
                                            <div class="text-xs text-slate-500">Skala besar</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white">Custom</div>
                                    <div class="text-xs text-slate-500">Kontak Sales</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-400">
                                        Unlimited
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                                    Unlimited
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <button @click="editPackage('Enterprise')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                    <span class="text-slate-300 mx-2">|</span>
                                    <button @click="deletePackage('Enterprise')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form View -->
            <div v-else class="max-w-full h-full flex flex-col min-h-0 w-full mx-auto">
                <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ isEditing ? 'Edit Paket Langganan' : 'Buat Paket Langganan Baru' }}
                        </h3>
                    </div>
                    
                    <form @submit.prevent="submitForm" class="p-6 space-y-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Paket</label>
                                <input v-model="formPackageName" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Premium">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Harga Bulanan</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="250000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Siklus Penagihan</label>
                                    <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option>Bulanan (Bulan)</option>
                                        <option>Tahunan (Tahun)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Singkat</label>
                                <input type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Cocok untuk ...">
                            </div>
                        </div>

                        <!-- Limits -->
                        <div class="pt-6 border-t border-slate-200 dark:border-slate-700 space-y-4">
                            <h4 class="text-sm font-medium text-slate-900 dark:text-white uppercase tracking-wider">Batasan (Limits)</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Batas Maksimal Pelanggan (User ISP)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pr-16 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Maksimal">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-slate-500 sm:text-sm">User</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Kosongkan untuk unlimited.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Batas Maksimal Router</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pr-16 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Maksimal">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-slate-500 sm:text-sm">Router</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center">
                                <input id="enable_wa" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700" checked>
                                <label for="enable_wa" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">
                                    Berikan Akses Modul WA Gateway
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex justify-end space-x-3">
                            <button type="button" @click="showForm = false" class="rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 py-2 px-4 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-gradient-to-r from-blue-600 to-purple-600 py-2 px-6 text-sm font-medium text-white shadow-md hover:from-blue-700 hover:to-purple-700 focus:outline-none transition-colors">
                                {{ isEditing ? 'Simpan Perubahan' : 'Simpan Paket' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </SuperadminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SuperadminLayout from '@/Layouts/SuperadminLayout.vue';

const pageTitle = computed(() => {
    return 'Master Paket SaaS - LadaPala-Bill';
});

// State for toggling form
const showForm = ref(false);
const isEditing = ref(false);
const formPackageName = ref('');

const showCreateForm = () => {
    isEditing.value = false;
    formPackageName.value = '';
    showForm.value = true;
};

const editPackage = (packageName) => {
    isEditing.value = true;
    formPackageName.value = packageName;
    showForm.value = true;
};

const deletePackage = (packageName) => {
    if (confirm(`Apakah Anda yakin ingin menghapus paket "${packageName}"? Paket yang sedang aktif di tenant mungkin akan terdampak.`)) {
        alert(`Simulasi: Paket "${packageName}" berhasil dihapus dari database!`);
    }
};

const submitForm = () => {
    if (isEditing.value) {
        alert('Fungsi update (edit) paket ke database saas_packages akan diimplementasikan nanti!');
    } else {
        alert('Fungsi submit paket baru ke database saas_packages akan diimplementasikan nanti!');
    }
};
</script>
