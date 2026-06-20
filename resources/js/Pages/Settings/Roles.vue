<template>
    <AppLayout title="Kelola Role">
        <div class="px-4 sm:px-6 lg:px-8 p-4 w-full max-w-full h-full flex flex-col min-h-0 w-full mx-auto">
            
            <!-- Page Header -->
            <div class="sm:flex sm:justify-between sm:items-center mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-slate-800 dark:text-slate-100 font-bold">Kelola Role & Hak Akses</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Atur tingkat otorisasi dan perizinan fitur untuk setiap posisi di perusahaan.</p>
                </div>
                
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Tambah Role</span>
                    </button>
                </div>
            </div>

            <!-- Role Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card -->
                <div v-for="role in roles" :key="role.id" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col transition-shadow hover:shadow-md">
                    <!-- Card Header -->
                    <div class="p-5 flex justify-between items-start border-b border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mr-4" :class="getRoleColor(role.name)">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path v-if="role.name === 'Superadmin'" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                    <path v-else d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle v-if="role.name !== 'Superadmin'" cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ role.name }}</h3>
                                <div class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2" :title="role.description">{{ role.description || 'Tidak ada deskripsi' }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="p-5 flex-grow">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pengguna</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ role.users_count }} Akun</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 mt-2">
                            <div class="bg-blue-500 h-1.5 rounded-full" :style="{ width: Math.min((role.users_count / totalUsers) * 100, 100) + '%' }"></div>
                        </div>
                    </div>
                    <!-- Card Footer -->
                    <div class="px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                        <button @click="openEditModal(role)" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 px-3 py-1.5 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
                            Lihat & Edit Akses
                        </button>
                        <button v-if="role.name !== 'Superadmin'" @click="confirmDelete(role)" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 px-3 py-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- NATIVE FULLSCREEN MODAL UNTUK FORM -->
        <teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col max-h-[90vh] overflow-hidden border border-slate-200 dark:border-slate-700" @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/80 shrink-0">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                            {{ isEditing ? (form.name === 'Superadmin' ? 'Lihat Hak Akses' : 'Edit Role & Hak Akses') : 'Tambah Role Baru' }}
                        </h3>
                        <button @click="closeModal" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Horizontal Tab Bar -->
                    <div class="flex overflow-x-auto bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 shrink-0 custom-scrollbar">
                        <button v-for="tab in permissionGroups" :key="tab.id" @click="activeTab = tab.id"
                            class="px-5 py-3 text-sm font-medium transition-colors whitespace-nowrap flex-shrink-0 border-b-2"
                            :class="activeTab === tab.id ? 'text-blue-600 dark:text-blue-400 border-blue-600 bg-white dark:bg-slate-700' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50'">
                            {{ tab.name }}
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 overflow-y-auto flex-grow custom-scrollbar bg-white dark:bg-slate-800">
                        
                        <!-- Tab: General Info -->
                        <div v-show="activeTab === 'general'" class="space-y-5 max-w-2xl">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Role <span class="text-rose-500">*</span></label>
                                <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" placeholder="Contoh: Staff Lapangan" required :disabled="isSuperadmin">
                                <div v-if="form.errors.name" class="text-sm text-rose-500 mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" placeholder="Jelaskan peran ini secara singkat..." :disabled="isSuperadmin"></textarea>
                            </div>
                            
                            <div v-if="isSuperadmin" class="p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl flex items-start">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="text-sm text-amber-800 dark:text-amber-200">
                                    <span class="font-bold">Info:</span> Role Superadmin memiliki akses sistem tanpa batas secara otomatis. Anda tidak dapat mengubah atau menghapus role ini untuk alasan keamanan.
                                </div>
                            </div>
                            
                            <!-- Platform Access -->
                            <div class="mt-6 border-t border-slate-200 dark:border-slate-700 pt-6">
                                <h4 class="text-md font-bold text-slate-800 dark:text-slate-100 mb-4">Akses Platform</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                    <label class="flex items-start justify-between cursor-pointer p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600" :class="{'opacity-50 pointer-events-none': isSuperadmin}">
                                        <div class="pr-4">
                                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Akses Desktop (Web)</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mengizinkan login melalui browser web di komputer.</div>
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 mt-1">
                                            <input type="checkbox" v-model="form.can_access_desktop" class="sr-only peer" :disabled="isSuperadmin">
                                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                                        </div>
                                    </label>

                                    <label class="flex items-start justify-between cursor-pointer p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600" :class="{'opacity-50 pointer-events-none': isSuperadmin}">
                                        <div class="pr-4">
                                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Akses Mobile (Aplikasi)</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mengizinkan login melalui aplikasi mobile (Android/iOS).</div>
                                        </div>
                                        <div class="relative inline-flex items-center shrink-0 mt-1">
                                            <input type="checkbox" v-model="form.can_access_mobile" class="sr-only peer" :disabled="isSuperadmin">
                                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs: Permission Groups -->
                        <div v-for="tab in permissionGroups.filter(t => t.id !== 'general' && t.id !== 'fee')" :key="'pane-'+tab.id" v-show="activeTab === tab.id" class="space-y-6">
                            <h4 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">{{ tab.name }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                <label v-for="perm in tab.permissions" :key="perm.key" class="flex items-start justify-between cursor-pointer p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600" :class="{'opacity-50 pointer-events-none': isSuperadmin}">
                                    <div class="pr-4">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ perm.label }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ perm.desc }}</div>
                                    </div>
                                    <div class="relative inline-flex items-center shrink-0 mt-1">
                                        <input type="checkbox" v-model="form[perm.key]" class="sr-only peer" :disabled="isSuperadmin">
                                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Tab: Fee / Komisi -->
                        <div v-show="activeTab === 'fee'" class="space-y-6 max-w-2xl">
                            <h4 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">Pengaturan Komisi</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Atur komisi (fee) yang akan diterima oleh pengguna dengan role ini (biasanya untuk Role Agen/Sales/Mitra).</p>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tipe Komisi</label>
                                <select v-model="form.fee_type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" :disabled="isSuperadmin">
                                    <option value="none">Tanpa Komisi (Karyawan Biasa)</option>
                                    <option value="persen">Berdasarkan Persentase (%)</option>
                                    <option value="fix">Nominal Tetap (Fix per Transaksi)</option>
                                </select>
                            </div>

                            <div v-if="form.fee_type === 'persen'">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Persentase Komisi (%)</label>
                                <input v-model="form.fee_persen" type="number" step="0.01" min="0" max="100" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" placeholder="Contoh: 10" :disabled="isSuperadmin">
                            </div>

                            <div v-if="form.fee_type === 'fix'">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nominal Komisi (Rp)</label>
                                <input v-model="form.fee_fix" type="number" step="1" min="0" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" placeholder="Contoh: 5000" :disabled="isSuperadmin">
                            </div>

                            <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-700">
                                <label class="flex items-start justify-between cursor-pointer p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600" :class="{'opacity-50 pointer-events-none': isSuperadmin}">
                                    <div class="pr-4">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Kunci Komisi (Lock Fee)</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Jika aktif, pelanggan dari user ini akan selalu memberikan komisi ke user ini secara otomatis, meskipun tagihannya dibayar/dilaporkan oleh user lain (misalnya lewat transfer bank/admin).</div>
                                    </div>
                                    <div class="relative inline-flex items-center shrink-0 mt-1">
                                        <input type="checkbox" v-model="form.fee_locked" class="sr-only peer" :disabled="isSuperadmin">
                                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                                    </div>
                                </label>
                                
                                <label class="mt-4 flex items-start justify-between cursor-pointer p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600" :class="{'opacity-50 pointer-events-none': isSuperadmin}">
                                    <div class="pr-4">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Pembatasan Saldo (Limit)</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Jika diaktifkan, user ini hanya bisa memproses pembayaran pelanggan jika nominal saldo miliknya masih mencukupi.</div>
                                    </div>
                                    <div class="relative inline-flex items-center shrink-0 mt-1">
                                        <input type="checkbox" v-model="form.is_saldo_limited" class="sr-only peer" :disabled="isSuperadmin">
                                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 flex justify-end gap-3 shrink-0">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                            Batal
                        </button>
                        <button v-if="!isSuperadmin" @click="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 flex items-center">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </div>
        </teleport>

        <!-- NATIVE DELETE CONFIRMATION MODAL -->
        <teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 border border-slate-200 dark:border-slate-700" @click.stop>
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-rose-600 dark:text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Hapus Role</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus role <strong>{{ roleToDelete?.name }}</strong>? Data yang dihapus tidak dapat dikembalikan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors" @click="showDeleteModal = false">Batal</button>
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-rose-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors flex justify-center items-center" @click="executeDelete" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </teleport>

    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    roles: Array,
});

const totalUsers = computed(() => {
    return props.roles?.reduce((acc, role) => acc + (role.users_count || 0), 0) || 1;
});

// UI State
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const activeTab = ref('general');
const roleToDelete = ref(null);

// Lock body scroll when modal opens
watch([showModal, showDeleteModal], ([newShowModal, newShowDeleteModal]) => {
    if (newShowModal || newShowDeleteModal) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
});

onUnmounted(() => {
    document.body.style.overflow = 'auto';
});

const isSuperadmin = computed(() => {
    return isEditing.value && form.name === 'Superadmin';
});

// Form State
const defaultForm = {
    id: null,
    name: '',
    description: '',
    fee_type: 'none',
    fee_persen: 0,
    fee_fix: 0,
    fee_locked: false,
    // Toggles mapping
    can_access_desktop: true,
    can_access_mobile: true,
    can_view_dashboard: false, can_view_dashboard_config: false, can_view_dashboard_map: false, can_view_dashboard_olt: false,
    can_input_customer: false, can_edit_customer: false, can_delete_customer: false,
    can_import_export_customer: false, can_view_all_customers: false, can_delete_customer_cuti: false,
    view_by_area: false, view_by_sales: false, view_own_only: false,
    can_process_payment: false, can_send_wa_invoice: false, can_cancel_payment: false, can_view_payment_history: false,
    can_manage_isolir: false, can_cuti: false, can_manage_cuti: false,
    can_view_reports: false, can_view_finance: false, can_manage_expenses: false,
    can_manage_deposits: false, can_manage_saldo: false, can_delete_finance: false,
    can_use_deposit: false,
    can_manage_packages: false, can_manage_areas: false, can_manage_odp: false,
    can_manage_servers: false,
    can_manage_users: false, can_manage_roles: false, can_view_audit_logs: false,
    can_backup_restore: false,
    can_view_radius: false, can_view_acs: false, can_manage_radius: false, can_manage_router: false, can_manage_olt: false, can_manage_acs: false, can_send_wa_blast: false,
    can_config_map: false, can_view_monitor: false, is_saldo_limited: false
};

const form = useForm({ ...defaultForm });

// Modal Handlers
const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    activeTab.value = 'general';
    showModal.value = true;
};

const openEditModal = (role) => {
    isEditing.value = true;
    form.reset();
    form.clearErrors();
    
    // Assign values safely
    Object.keys(form.data()).forEach(key => {
        if (role && role[key] !== undefined) {
            form[key] = role[key];
        }
    });
    
    // Superadmin override check
    if (role && role.name === 'Superadmin') {
        Object.keys(form.data()).forEach(key => {
            if (key !== 'id' && key !== 'name' && key !== 'description') {
                form[key] = true;
            }
        });
    }

    activeTab.value = 'general';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => {
        form.reset();
        form.clearErrors();
    }, 200); // Wait for transition
};

// CRUD Execution
const submit = () => {
    if (isEditing.value) {
        form.put(route('config.roles.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('config.roles.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (role) => {
    roleToDelete.value = role;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!roleToDelete.value) return;
    
    form.delete(route('config.roles.destroy', roleToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            roleToDelete.value = null;
        },
    });
};

const getRoleColor = (name) => {
    if (!name) return 'bg-slate-100 text-slate-600 dark:bg-slate-500/20 dark:text-slate-400';
    const nameLower = name.toLowerCase();
    if (nameLower === 'superadmin') return 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400';
    if (nameLower === 'admin') return 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400';
    if (nameLower === 'teknisi') return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400';
    if (nameLower === 'sales' || nameLower === 'agent') return 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400';
    if (nameLower === 'finance') return 'bg-fuchsia-100 text-fuchsia-600 dark:bg-fuchsia-500/20 dark:text-fuchsia-400';
    return 'bg-slate-100 text-slate-600 dark:bg-slate-500/20 dark:text-slate-400';
};

// Tabbed Groups
const permissionGroups = [
    {
        id: 'general',
        name: 'Informasi Umum',
        permissions: [] // handled separately in template
    },
    {
        id: 'dashboard',
        name: 'Dashboard & Tampilan',
        permissions: [
            { key: 'can_view_dashboard', label: 'Akses Modul Billing', desc: 'Bisa melihat halaman dashboard dan modul billing.' },
            { key: 'can_view_dashboard_config', label: 'Akses Modul Config', desc: 'Bisa masuk ke dalam modul konfigurasi.' },
            { key: 'can_view_dashboard_map', label: 'Akses Modul Peta', desc: 'Bisa masuk ke dalam modul peta jaringan.' },
            { key: 'can_view_dashboard_olt', label: 'Akses Modul OLT', desc: 'Akses modul integrasi OLT.' },
            { key: 'can_view_monitor', label: 'Akses Modul Monitoring', desc: 'Melihat grafik MRTG / aktivitas up-down jaringan.' },
            { key: 'can_view_radius', label: 'Akses Modul RADIUS', desc: 'Akses ke dalam modul RADIUS lokal/pusat.' },
            { key: 'can_view_acs', label: 'Akses Modul ACS', desc: 'Akses ke dalam modul ACS (Auto Configuration Server).' },
        ]
    },
    {
        id: 'customer',
        name: 'Pelanggan & Visibilitas',
        permissions: [
            { key: 'can_input_customer', label: 'Tambah Pelanggan', desc: 'Mendaftarkan pelanggan baru.' },
            { key: 'can_edit_customer', label: 'Edit Pelanggan', desc: 'Mengubah profil pelanggan.' },
            { key: 'can_delete_customer', label: 'Hapus Pelanggan', desc: 'Menghapus data pelanggan.' },
            { key: 'can_delete_customer_cuti', label: 'Hapus Pelanggan Cuti', desc: 'Menghapus data pelanggan yang berstatus cuti.' },
            { key: 'can_view_all_customers', label: 'Lihat Semua Pelanggan', desc: 'Bebas dari batasan area atau sales.' },
            { key: 'can_import_export_customer', label: 'Import & Export Excel', desc: 'Hak impor dan ekspor data pelanggan massal.' },
        ]
    },
    {
        id: 'billing',
        name: 'Tagihan & Keuangan',
        permissions: [
            { key: 'can_process_payment', label: 'Proses Pembayaran', desc: 'Menerima pembayaran tagihan masuk.' },
            { key: 'can_send_wa_invoice', label: 'WA Tagihan', desc: 'Kirim notifikasi tagihan/kuitansi via WA.' },
            { key: 'can_cancel_payment', label: 'Batal Pembayaran', desc: 'Membatalkan / void kuitansi.' },
            { key: 'can_view_payment_history', label: 'Riwayat Transaksi', desc: 'Melihat daftar histori bayar pelanggan.' },
            { key: 'can_manage_isolir', label: 'Aksi Isolir / Unisolir', desc: 'Blokir/Buka pelanggan secara manual.' },
            { key: 'can_cuti', label: 'Proses Cuti', desc: 'Mengajukan atau memproses cuti pelanggan.' },
            { key: 'can_view_finance', label: 'Menu Keuangan', desc: 'Bisa mengakses sub-menu keuangan.' },
            { key: 'can_view_reports', label: 'Laporan Keuangan', desc: 'Melihat laporan laba, rugi, rekap pendapatan.' },
            { key: 'can_manage_expenses', label: 'Pengeluaran', desc: 'Mencatat pengeluaran operasional.' },
            { key: 'can_manage_deposits', label: 'Setoran', desc: 'Manajemen uang setoran agen/kolektor.' },
            { key: 'can_manage_saldo', label: 'Kelola Saldo', desc: 'Kewenangan mengelola saldo deposit langsung.' },
            { key: 'can_delete_finance', label: 'Hapus Data Keuangan', desc: 'Akses menghapus entri finansial.' },
        ]
    },
    {
        id: 'fee',
        name: 'Komisi, Fee & Saldo',
        permissions: [] // handled separately
    },
    {
        id: 'network',
        name: 'Teknikal & Jaringan',
        permissions: [
            { key: 'can_manage_odp', label: 'Kelola ODP / ODC', desc: 'Manajemen tiang/kotak ODP di lapangan.' },
            { key: 'can_manage_packages', label: 'Kelola Paket', desc: 'Mengatur harga dan profil paket internet.' },
            { key: 'can_manage_areas', label: 'Kelola Area', desc: 'Pengaturan pembagian wilayah.' },
            { key: 'can_manage_router', label: 'Manajemen Router', desc: 'Sync, reset, ubah password rahasia PPPoE.' },
            { key: 'can_manage_olt', label: 'Kelola OLT', desc: 'Manajemen dan konfigurasi perangkat OLT.' },
            { key: 'can_manage_acs', label: 'Kelola ACS', desc: 'Edit device di ACS.' },
            { key: 'can_config_map', label: 'Config Peta', desc: 'Pengaturan dan pemetaan GIS ODP dan Router.' },
            { key: 'can_manage_radius', label: 'Config RADIUS', desc: 'Mengubah pengaturan inti server RADIUS.' },
        ]
    },
    {
        id: 'system',
        name: 'Sistem & Lanjutan',
        permissions: [
            { key: 'can_manage_users', label: 'Kelola User', desc: 'Tambah/Edit User yang bisa login.' },
            { key: 'can_manage_roles', label: 'Kelola Role Akses', desc: 'Halaman ini.' },
            { key: 'can_view_audit_logs', label: 'Lihat Audit Log', desc: 'Riwayat sistem perbaikan, hapus, dll.' },
            { key: 'can_backup_restore', label: 'Backup Data', desc: 'Backup database manual.' },
            { key: 'can_send_wa_blast', label: 'Kirim WA Blast', desc: 'Broadcast ke banyak pelanggan.' },
        ]
    }
];

</script>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

/* Custom scrollbar for permission list */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #475569; 
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #64748b; 
}
</style>