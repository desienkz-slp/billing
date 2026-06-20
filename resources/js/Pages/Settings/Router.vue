<template>
    <AppLayout title="Kelola Router">
        <div class="max-w-full h-full flex flex-col min-h-0 w-full mx-auto p-4 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Kelola Router MikroTik</h1>
                </div>
                <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Router
                </button>
            </div>

            <!-- Routers Grid / Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Nama & Host</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Akses (Port)</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Backup Terakhir</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            <tr v-for="router in routers" :key="router.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-inner">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-xs sm:text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                                {{ router.name }}
                                                <span v-if="router.auto_backup" class="inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20" title="Auto Backup Aktif">Auto Bck</span>
                                            </div>
                                            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-mono">{{ router.host }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-8 font-medium">API:</span>
                                            <span class="font-mono bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[10px] sm:text-xs">{{ router.port }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-8 font-medium">FTP:</span>
                                            <span class="font-mono bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[10px] sm:text-xs">{{ router.ftp_port || 21 }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    <div v-if="router.last_backup_at" class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        {{ formatDate(router.last_backup_at) }}
                                    </div>
                                    <div v-else class="italic text-slate-400">Belum ada backup</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <button @click="toggleRouterStatus(router)" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2" :class="router.is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'" role="switch" :aria-checked="router.is_active">
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="router.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="testConnection(router)" :disabled="testingRouterId === router.id" class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors disabled:opacity-50" title="Test Koneksi">
                                            <svg v-if="testingRouterId === router.id" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        </button>
                                        
                                        <button @click="fetchConfig(router)" :disabled="fetchingConfigId === router.id" class="p-1.5 text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/30 rounded-lg transition-colors disabled:opacity-50" title="Lihat Config">
                                            <svg v-if="fetchingConfigId === router.id" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                                        </button>

                                        <button @click="openBackupModal(router)" class="p-1.5 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors" title="Backup & Restore">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                        </button>

                                        <button @click="openEditModal(router)" class="p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        
                                        <button @click="deleteRouter(router)" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="routers.length === 0">
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        <p>Belum ada router yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Router Modal -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200 dark:border-slate-700 flex flex-col" @click.stop>
                        
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/50">
                                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">{{ editingRouter ? 'Edit Router' : 'Tambah Router Baru' }}</h3>
                            </div>
                            <button @click="closeModal" type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-colors focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 py-5">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Label</label>
                                    <input v-model="form.name" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Misal: Router Utama">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Host / IP Address</label>
                                    <input v-model="form.host" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all font-mono" placeholder="192.168.1.1">
                                    <p v-if="form.errors.host" class="mt-1 text-sm text-red-500">{{ form.errors.host }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">API Port</label>
                                        <input v-model="form.port" type="number" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all font-mono" placeholder="8728">
                                        <p v-if="form.errors.port" class="mt-1 text-sm text-red-500">{{ form.errors.port }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">FTP Port</label>
                                        <input v-model="form.ftp_port" type="number" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all font-mono" placeholder="21">
                                        <p v-if="form.errors.ftp_port" class="mt-1 text-sm text-red-500">{{ form.errors.ftp_port }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
                                        <input v-model="form.username" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                                        <p v-if="form.errors.username" class="mt-1 text-sm text-red-500">{{ form.errors.username }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                                        <input v-model="form.password" type="password" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" :placeholder="editingRouter ? 'Kosongkan jika tidak diubah' : ''">
                                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                                    </div>
                                </div>
                                
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 mt-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-slate-900 dark:text-white">Auto Backup</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Jalankan backup otomatis (jika ada *cron*).</p>
                                        </div>
                                        <button @click="form.auto_backup = !form.auto_backup" type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2" :class="form.auto_backup ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'">
                                            <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="form.auto_backup ? 'translate-x-5' : 'translate-x-0'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-3">
                            <button @click="closeModal" type="button" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">Batal</button>
                            <button @click="saveRouter" :disabled="form.processing" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Config Modal -->
        <div v-if="isConfigModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="isConfigModalOpen = false"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-700 flex flex-col" @click.stop>
                        
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/60 bg-slate-800">
                            <h3 class="text-lg font-mono font-semibold text-white">Konfigurasi Router: {{ selectedRouter?.name }}</h3>
                            <button @click="isConfigModalOpen = false" class="text-slate-400 hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                            <pre class="text-sm font-mono text-emerald-400 whitespace-pre-wrap">{{ configData }}</pre>
                        </div>
                        
                        <div class="bg-slate-800 px-6 py-4 border-t border-slate-700/60 flex items-center justify-end">
                            <button @click="isConfigModalOpen = false" class="px-4 py-2 text-sm font-medium text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-colors">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Modal -->
        <div v-if="isBackupModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="isBackupModalOpen = false"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700 flex flex-col" @click.stop>
                        
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Kelola Backup: {{ selectedRouter?.name }}</h3>
                            <button @click="isBackupModalOpen = false" class="text-slate-400 hover:text-slate-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="px-6 py-5">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Daftar backup (Max 3).</p>
                                <button @click="createBackup(selectedRouter)" :disabled="isBackingUp" class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg text-emerald-700 bg-emerald-100 hover:bg-emerald-200 dark:text-emerald-400 dark:bg-emerald-900/30 transition-colors disabled:opacity-50">
                                    <svg v-if="isBackingUp" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <svg v-else class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                    Buat Backup Baru
                                </button>
                            </div>

                            <div v-if="backups.length > 0" class="border border-slate-200 dark:border-slate-700 rounded-lg divide-y divide-slate-200 dark:divide-slate-700">
                                <div v-for="bkp in backups" :key="bkp.id" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50">
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white text-sm">{{ bkp.config_name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ formatDate(bkp.created_at) }}</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="`/config/router/${selectedRouter.id}/backup/${bkp.id}/download`" target="_blank" class="p-1.5 text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-md transition-colors" title="Download">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                        <button @click="restoreBackup(selectedRouter, bkp)" class="p-1.5 text-orange-600 bg-orange-100 hover:bg-orange-200 rounded-md transition-colors" title="Info Restore">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-slate-500 dark:text-slate-400 text-sm border border-dashed border-slate-300 dark:border-slate-700 rounded-lg">
                                Belum ada riwayat backup
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    routers: {
        type: Array,
        default: () => [],
    }
});

// State
const isModalOpen = ref(false);
const editingRouter = ref(null);
const testingRouterId = ref(null);

const isConfigModalOpen = ref(false);
const fetchingConfigId = ref(null);
const configData = ref('');
const selectedRouter = ref(null);

const isBackupModalOpen = ref(false);
const isBackingUp = ref(false);
const backups = ref([]);

// Form
const form = useForm({
    name: '',
    host: '',
    port: 8728,
    ftp_port: 21,
    username: '',
    password: '',
    auto_backup: false,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const openCreateModal = () => {
    editingRouter.value = null;
    form.reset();
    form.clearErrors();
    form.port = 8728;
    form.ftp_port = 21;
    form.auto_backup = false;
    isModalOpen.value = true;
};

const openEditModal = (r) => {
    editingRouter.value = r;
    form.reset();
    form.clearErrors();
    form.name = r.name;
    form.host = r.host;
    form.port = r.port;
    form.ftp_port = r.ftp_port || 21;
    form.username = r.username;
    form.password = ''; // Don't show password
    form.auto_backup = r.auto_backup;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => form.reset(), 200);
};

const saveRouter = () => {
    if (editingRouter.value) {
        form.put(`/config/router/${editingRouter.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        form.post('/config/router', {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

const toggleRouterStatus = (r) => {
    axios.post(`/config/router/${r.id}/toggle`)
        .then(res => {
            if (res.data.status === 'success') {
                router.reload({ only: ['routers'] });
            }
        })
        .catch(() => alert('Terjadi kesalahan saat mengubah status router.'));
};

const deleteRouter = (r) => {
    if (confirm(`Apakah Anda yakin ingin menghapus router "${r.name}"? Semua data backup juga akan terhapus.`)) {
        router.delete(`/config/router/${r.id}`, {
            preserveScroll: true,
        });
    }
};

const testConnection = (r) => {
    testingRouterId.value = r.id;
    axios.post(`/config/router/${r.id}/test`)
        .then(res => {
            if (res.data.status === 'success') {
                alert(res.data.message);
            } else {
                alert("Error: " + res.data.message);
            }
        })
        .catch(err => {
            alert(err.response?.data?.message || 'Koneksi gagal/timeout.');
        })
        .finally(() => {
            testingRouterId.value = null;
        });
};

const fetchConfig = (r) => {
    fetchingConfigId.value = r.id;
    axios.get(`/config/router/${r.id}/config`)
        .then(res => {
            if (res.data.status === 'success') {
                selectedRouter.value = r;
                configData.value = res.data.config;
                isConfigModalOpen.value = true;
            } else {
                alert(res.data.message);
            }
        })
        .catch(err => {
            alert(err.response?.data?.message || 'Gagal mengambil config.');
        })
        .finally(() => {
            fetchingConfigId.value = null;
        });
};

const loadBackups = (r) => {
    axios.get(`/config/router/${r.id}/backups`)
        .then(res => {
            if (res.data.status === 'success') {
                backups.value = res.data.data;
            }
        });
};

const openBackupModal = (r) => {
    selectedRouter.value = r;
    backups.value = [];
    isBackupModalOpen.value = true;
    loadBackups(r);
};

const createBackup = (r) => {
    isBackingUp.value = true;
    axios.post(`/config/router/${r.id}/backup`)
        .then(res => {
            if (res.data.status === 'success') {
                alert(res.data.message);
                loadBackups(r);
                router.reload({ only: ['routers'] });
            } else {
                alert("Error: " + res.data.message);
            }
        })
        .catch(err => {
            alert(err.response?.data?.message || 'Gagal membuat backup');
        })
        .finally(() => {
            isBackingUp.value = false;
        });
};

const restoreBackup = (r, bkp) => {
    alert(`Untuk me-restore backup ini, silakan download file ${bkp.config_name}.rsc lalu jalankan "/import file=${bkp.config_name}.rsc" di terminal MikroTik.`);
};

</script>