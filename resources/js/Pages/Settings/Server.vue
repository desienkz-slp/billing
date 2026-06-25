<template>
    <AppLayout title="Kelola GenieACS Server">
        <div class="h-full flex flex-col min-h-0 w-full p-2">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Kelola Server GenieACS</h1>
                </div>
                <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Server
                </button>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashMessage" class="mb-4 rounded-xl px-4 py-3 type-body-sm font-medium flex items-center gap-2" :class="flashType === 'success' ? 'bg-success/10 text-success' : 'bg-critical/10 text-critical'">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="flashType === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ flashMessage }}
            </div>

            <!-- Servers Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Nama & Host</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Port API (NBI)</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Auth</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            <tr v-for="server in servers" :key="server.id" class="hover:bg-surface-soft dark:hover:bg-slate-700/50 transition-colors duration-150">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-inner">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2" ry="2" stroke-width="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2" stroke-width="2"/><line x1="6" y1="6" x2="6.01" y2="6" stroke-width="2"/><line x1="6" y1="18" x2="6.01" y2="18" stroke-width="2"/></svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-xs sm:text-sm text-slate-900 dark:text-white">{{ server.name }}</div>
                                            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-mono">{{ server.host }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="font-mono bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-lg text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-300">{{ server.port }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span v-if="server.username" class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        {{ server.username }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400 italic">Tanpa auth</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <button @click="toggleServerStatus(server)" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2" :class="server.is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'" role="switch" :aria-checked="server.is_active">
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="server.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Test Connection -->
                                        <button @click="testConnection(server)" :disabled="testingServerId === server.id" class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors disabled:opacity-50" title="Test Koneksi API">
                                            <svg v-if="testingServerId === server.id" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        </button>

                                        <!-- Edit -->
                                        <button @click="openEditModal(server)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>

                                        <!-- Delete -->
                                        <button @click="deleteServer(server)" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="servers.length === 0">
                                <td colspan="5" class="px-3 py-12 text-center text-steel dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-stone mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2" ry="2" stroke-width="1.5"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2" stroke-width="1.5"/><line x1="6" y1="6" x2="6.01" y2="6" stroke-width="1.5"/><line x1="6" y1="18" x2="6.01" y2="18" stroke-width="1.5"/></svg>
                                        <p class="type-body-md text-ink dark:text-slate-300 font-medium">Belum ada server GenieACS</p>
                                        <p class="type-caption text-stone mt-1">Tambahkan server untuk mulai mengelola perangkat CPE.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Test Result Banner -->
            <div v-if="testResult" class="mt-4 rounded-xl px-4 py-3 type-body-sm font-medium flex items-center gap-2 transition-all" :class="{
                'bg-success/10 text-success border border-success/20': testResult.status === 'success',
                'bg-warning/10 text-warning border border-warning/20': testResult.status === 'warning',
                'bg-critical/10 text-critical border border-critical/20': testResult.status === 'error',
            }">
                <svg v-if="testResult.status === 'success'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <svg v-else class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                {{ testResult.message }}
                <button @click="testResult = null" class="ml-auto p-1 hover:opacity-70">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- ACS Parameter Mapping Section -->
            <div class="mt-8">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Mapping Parameter ACS</h2>
                    <button @click="saveAcsParams" :disabled="isSavingParams" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all disabled:opacity-50 whitespace-nowrap flex-shrink-0">
                        <svg v-if="isSavingParams" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Simpan Mapping
                    </button>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <!-- Loading -->
                    <div v-if="isLoadingParams" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <!-- Table -->
                    <table v-else class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider sm:pl-6 w-12">#</th>
                                <th scope="col" class="py-3 px-3 text-left text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider">Parameter</th>
                                <th scope="col" class="py-3 px-3 text-left text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                                <th scope="col" class="py-3 px-3 text-left text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-wider min-w-[300px]">Path / Nama di GenieACS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="(param, idx) in acsParams" :key="param.key" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 pl-4 pr-3 sm:pl-6 text-xs sm:text-sm text-slate-500">{{ idx + 1 }}</td>
                                <td class="py-3 px-3">
                                    <div class="text-xs sm:text-sm font-medium text-slate-900 dark:text-white">{{ param.label }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ param.description }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold" :class="param.label.startsWith('PPPoE') || param.label.startsWith('RX') || param.label.startsWith('Temp') || param.label.startsWith('Active') || param.label.startsWith('PON') || param.label.startsWith('SSID') || param.label.startsWith('Pass') ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'">
                                        {{ param.label.startsWith('Product') || param.label.startsWith('Serial') || param.label.startsWith('Manuf') ? 'TR-069' : 'VParam' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <input v-model="param.value" type="text" class="block w-full h-[38px] px-3 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all placeholder:text-slate-400" :placeholder="getPlaceholder(param.key)">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Params Flash -->
                <div v-if="paramsFlash" class="mt-3 rounded-xl px-4 py-3 type-body-sm font-medium flex items-center gap-2" :class="paramsFlashType === 'success' ? 'bg-success/10 text-success' : 'bg-critical/10 text-critical'">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="paramsFlashType === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ paramsFlash }}
                </div>
            </div>
        </div>

        <!-- Create/Edit Server Modal -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-ink-deep/75 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-canvas dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-hairline-soft dark:border-slate-700 flex flex-col" @click.stop>

                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-hairline-soft dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-primary/10">
                                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2" ry="2" stroke-width="1.5"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2" stroke-width="1.5"/><line x1="6" y1="6" x2="6.01" y2="6" stroke-width="1.5"/><line x1="6" y1="18" x2="6.01" y2="18" stroke-width="1.5"/></svg>
                                </div>
                                <h3 class="type-subtitle-lg text-ink-deep dark:text-white" id="modal-title">{{ editingServer ? 'Edit Server' : 'Tambah Server GenieACS' }}</h3>
                            </div>
                            <button @click="closeModal" type="button" class="rounded-lg p-2 text-stone hover:bg-surface-soft hover:text-ink dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-colors focus:outline-none">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 py-5">
                            <div class="space-y-4">
                                <div>
                                    <label class="block type-body-sm font-medium text-charcoal dark:text-slate-300 mb-1.5">Nama Label</label>
                                    <input v-model="form.name" type="text" class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all" placeholder="Misal: GenieACS Utama">
                                    <p v-if="formErrors.name" class="mt-1 type-caption text-critical-strong">{{ formErrors.name }}</p>
                                </div>
                                <div>
                                    <label class="block type-body-sm font-medium text-charcoal dark:text-slate-300 mb-1.5">Host / IP Address</label>
                                    <input v-model="form.host" type="text" class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all font-mono" placeholder="192.168.1.100">
                                    <p v-if="formErrors.host" class="mt-1 type-caption text-critical-strong">{{ formErrors.host }}</p>
                                </div>
                                <div>
                                    <label class="block type-body-sm font-medium text-charcoal dark:text-slate-300 mb-1.5">Port NBI (API)</label>
                                    <input v-model="form.port" type="number" class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all font-mono" placeholder="7557">
                                    <p class="mt-1 type-caption text-stone">Default port NBI GenieACS adalah 7557.</p>
                                    <p v-if="formErrors.port" class="mt-1 type-caption text-critical-strong">{{ formErrors.port }}</p>
                                </div>

                                <div class="pt-3 border-t border-hairline-soft dark:border-slate-700/60">
                                    <p class="type-body-sm font-medium text-charcoal dark:text-slate-300 mb-3">Autentikasi (opsional)</p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block type-caption font-medium text-steel dark:text-slate-400 mb-1">Username</label>
                                            <input v-model="form.username" type="text" class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all" placeholder="admin">
                                        </div>
                                        <div>
                                            <label class="block type-caption font-medium text-steel dark:text-slate-400 mb-1">Password</label>
                                            <input v-model="form.password" type="password" class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent dark:text-white dark:bg-slate-900/50 dark:border-slate-600 transition-all" :placeholder="editingServer ? 'Kosongkan jika tidak diubah' : ''">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-surface-soft dark:bg-slate-800/50 px-6 py-4 border-t border-hairline-soft dark:border-slate-700/60 flex items-center justify-end gap-3">
                            <button @click="closeModal" type="button" class="px-5 py-2.5 type-button-md text-charcoal dark:text-slate-300 hover:text-ink dark:hover:text-white transition-colors rounded-full">Batal</button>
                            <button @click="saveServer" :disabled="isSaving" class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-2.5 type-button-md text-on-primary hover:bg-primary-deep transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

const props = defineProps({
    servers: {
        type: Array,
        default: () => [],
    }
});

const page = usePage();

// State
const isModalOpen = ref(false);
const editingServer = ref(null);
const testingServerId = ref(null);
const testResult = ref(null);
const flashMessage = ref(null);
const flashType = ref('success');
const isSaving = ref(false);
const formErrors = ref({});

// ACS Params state
const acsParams = ref([]);
const isLoadingParams = ref(true);
const isSavingParams = ref(false);
const paramsFlash = ref(null);
const paramsFlashType = ref('success');

const getPlaceholder = (key) => {
    const map = {
        'acs_param_pppoe_username': 'pppoeUsername',
        'acs_param_rx_power': 'rxPower',
        'acs_param_temperature': 'temperature',
        'acs_param_active_devices': 'activeDevices',
        'acs_param_pon_mode': 'ponMode',
        'acs_param_ssid_2ghz': 'ssid2ghz',
        'acs_param_pass_2ghz': 'pass2ghz',
        'acs_param_ssid_5ghz': 'ssid5ghz',
        'acs_param_pass_5ghz': 'pass5ghz',
        'acs_param_product_class': 'DeviceID.ProductClass',
        'acs_param_serial_number': 'DeviceID.SerialNumber',
        'acs_param_manufacturer': 'DeviceID.Manufacturer',
    };
    return map[key] || 'Masukkan nama parameter...';
};

const loadAcsParams = async () => {
    isLoadingParams.value = true;
    try {
        const res = await axios.get('/config/server/acs-params');
        if (res.data.status === 'success') {
            acsParams.value = res.data.data;
        }
    } catch (err) {
        console.error('Failed to load ACS params:', err);
    } finally {
        isLoadingParams.value = false;
    }
};

const saveAcsParams = async () => {
    isSavingParams.value = true;
    paramsFlash.value = null;
    try {
        const res = await axios.post('/config/server/acs-params', {
            params: acsParams.value.map(p => ({ key: p.key, value: p.value || '' }))
        });
        if (res.data.status === 'success') {
            paramsFlash.value = res.data.message;
            paramsFlashType.value = 'success';
            setTimeout(() => { paramsFlash.value = null; }, 4000);
        }
    } catch (err) {
        paramsFlash.value = err.response?.data?.message || 'Gagal menyimpan parameter.';
        paramsFlashType.value = 'error';
    } finally {
        isSavingParams.value = false;
    }
};

onMounted(() => {
    loadAcsParams();
});

// Form
const form = ref({
    name: '',
    host: '',
    port: 7557,
    username: '',
    password: '',
});

const resetForm = () => {
    form.value = {
        name: '',
        host: '',
        port: 7557,
        username: '',
        password: '',
    };
    formErrors.value = {};
};

const openCreateModal = () => {
    editingServer.value = null;
    resetForm();
    isModalOpen.value = true;
};

const openEditModal = (s) => {
    editingServer.value = s;
    form.value = {
        name: s.name,
        host: s.host,
        port: s.port,
        username: s.username || '',
        password: '',
    };
    formErrors.value = {};
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => resetForm(), 200);
};

const showFlash = (msg, type = 'success') => {
    flashMessage.value = msg;
    flashType.value = type;
    setTimeout(() => { flashMessage.value = null; }, 5000);
};

const saveServer = async () => {
    isSaving.value = true;
    formErrors.value = {};

    try {
        if (editingServer.value) {
            const res = await axios.put(`/config/server/${editingServer.value.id}`, form.value);
            if (res.data.status === 'success') {
                showFlash(res.data.message);
                closeModal();
                router.reload({ only: ['servers'] });
            }
        } else {
            const res = await axios.post('/config/server', form.value);
            if (res.data.status === 'success') {
                showFlash(res.data.message);
                closeModal();
                router.reload({ only: ['servers'] });
            }
        }
    } catch (err) {
        if (err.response?.status === 422 && err.response?.data?.errors) {
            formErrors.value = {};
            for (const [key, msgs] of Object.entries(err.response.data.errors)) {
                formErrors.value[key] = Array.isArray(msgs) ? msgs[0] : msgs;
            }
        } else {
            showFlash(err.response?.data?.message || 'Terjadi kesalahan.', 'error');
        }
    } finally {
        isSaving.value = false;
    }
};

const toggleServerStatus = (s) => {
    axios.post(`/config/server/${s.id}/toggle`)
        .then(res => {
            if (res.data.status === 'success') {
                showFlash(res.data.message);
                router.reload({ only: ['servers'] });
            }
        })
        .catch(() => showFlash('Gagal mengubah status server.', 'error'));
};

const deleteServer = async (s) => {
    const isConfirmed = await confirm({
        title: 'Hapus Server',
        message: `Apakah Anda yakin ingin menghapus server "${s.name}"?`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        confirmColor: 'rose'
    });
    
    if (isConfirmed) {
        axios.delete(`/config/server/${s.id}`)
            .then(res => {
                if (res.data.status === 'success') {
                    showFlash(res.data.message);
                    router.reload({ only: ['servers'] });
                } else {
                    showFlash(res.data.message, 'error');
                }
            })
            .catch(err => {
                showFlash(err.response?.data?.message || 'Gagal menghapus server.', 'error');
            });
    }
};

const testConnection = (s) => {
    testingServerId.value = s.id;
    testResult.value = null;

    axios.post(`/config/server/${s.id}/test`)
        .then(res => {
            testResult.value = res.data;
        })
        .catch(err => {
            testResult.value = {
                status: 'error',
                message: err.response?.data?.message || 'Koneksi gagal / timeout.',
            };
        })
        .finally(() => {
            testingServerId.value = null;
        });
};
</script>