<template>
    <SuperadminLayout :title="pageTitle">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-full flex flex-col w-full mx-auto">
            
            <!-- Page Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Kelola Tenant SaaS</h1>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button v-if="!showForm" @click="showForm = true" type="button" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Tenant Baru
                    </button>
                    <button v-else @click="showForm = false" type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white dark:bg-slate-800 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:w-auto transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Daftar
                    </button>
                </div>
            </div>

            <!-- List View -->
            <div v-if="!showForm" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div v-if="!tenants || tenants.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Belum ada tenant terdaftar</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Mulai dengan menambahkan tenant baru ke sistem SaaS Anda.</p>
                    <div class="mt-6">
                        <button @click="showForm = true" type="button" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all inline-flex items-center whitespace-nowrap">
                            <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Tenant Pertama
                        </button>
                    </div>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Tenant</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Domain/URL</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengguna</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pelanggan</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            <tr v-for="tenant in tenants" :key="tenant.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                                            {{ tenant.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-xs sm:text-sm font-medium text-slate-900 dark:text-white">{{ tenant.name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ tenant.company_profile?.name || tenant.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="text-xs sm:text-sm text-slate-900 dark:text-white">{{ tenant.domain || (tenant.slug + '.ladapala.com') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    {{ tenant.users_count || 0 }} User
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    {{ tenant.customers_count || 0 }} Pelanggan
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <span v-if="tenant.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        Aktif
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Nonaktif
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium">
                                    <Link :href="route('superadmin.tenants.detail', tenant.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Detail</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form View -->
            <div v-else class="space-y-6">
                <form @submit.prevent="submitForm">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        <!-- Section 1: Data Identitas Tenant -->
                        <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    1. Data Identitas Tenant
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Informasi dasar perusahaan ISP klien.</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Tenant (Singkat)</label>
                                    <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="LadaPala">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Perusahaan / ISP</label>
                                    <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="PT LadaPala Network">
                                    <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-500">{{ form.errors.company_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">NPWP / NIB</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="01.234.567.8-910.000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat Lengkap</label>
                                    <textarea v-model="form.company_address" rows="3" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jl. Contoh No. 123..."></textarea>
                                    <p v-if="form.errors.company_address" class="mt-1 text-sm text-red-500">{{ form.errors.company_address }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Provinsi</label>
                                        <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option>Pilih Provinsi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kota/Kabupaten</label>
                                        <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option>Pilih Kota</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kode Pos</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="12345">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Telepon</label>
                                    <input v-model="form.company_phone" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="021-xxxxxxx">
                                    <p v-if="form.errors.company_phone" class="mt-1 text-sm text-red-500">{{ form.errors.company_phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email Perusahaan</label>
                                    <input type="email" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="info@perusahaan.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Domain Khusus (Opsional)</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-600 px-3 text-sm text-slate-500 dark:text-slate-300">https://</span>
                                        <input v-model="form.domain" type="text" class="block w-full flex-1 rounded-none rounded-r-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="tenant.ladapala.com">
                                    </div>
                                    <p v-if="form.errors.domain" class="mt-1 text-sm text-red-500">{{ form.errors.domain }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Data Owner / Administrator -->
                        <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    2. Data Administrator
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Akun utama untuk login ke dashboard tenant.</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap Administrator</label>
                                    <input v-model="form.admin_name" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="John Doe">
                                    <p v-if="form.errors.admin_name" class="mt-1 text-sm text-red-500">{{ form.errors.admin_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username Login</label>
                                    <input v-model="form.admin_username" type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="admin_isp">
                                    <p v-if="form.errors.admin_username" class="mt-1 text-sm text-red-500">{{ form.errors.admin_username }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor WhatsApp</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="0812xxxxxx">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                                    <input v-model="form.admin_password" type="password" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="••••••••">
                                    <p v-if="form.errors.admin_password" class="mt-1 text-sm text-red-500">{{ form.errors.admin_password }}</p>
                                    <p v-else class="mt-1 text-xs text-slate-500">Minimal 6 karakter.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role Sistem</label>
                                    <select disabled class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-600 dark:text-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm cursor-not-allowed">
                                        <option>Owner / Superadmin Tenant</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status Akun</label>
                                    <div class="flex items-center">
                                        <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-emerald-500 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" role="switch" aria-checked="true">
                                            <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-5"></span>
                                        </button>
                                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Paket Langganan SaaS -->
                        <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    3. Paket Langganan SaaS
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengaturan limitasi dan tagihan tenant.</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Paket</label>
                                    <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                        <option>Starter (Up to 100 User)</option>
                                        <option>Pro (Up to 500 User)</option>
                                        <option>Enterprise (Unlimited)</option>
                                        <option>Custom</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Jumlah Pelanggan Maksimal</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pr-12 focus:border-amber-500 focus:ring-amber-500 sm:text-sm" placeholder="100">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-slate-500 sm:text-sm">User</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Harga Bulanan</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="text" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm" placeholder="150.000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Aktif</label>
                                    <input type="date" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Jatuh Tempo</label>
                                    <input type="date" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status Subscription</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sub_status" value="active" class="h-4 w-4 border-slate-300 text-amber-600 focus:ring-amber-500 dark:border-slate-600 dark:bg-slate-700" checked>
                                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sub_status" value="suspend" class="h-4 w-4 border-slate-300 text-amber-600 focus:ring-amber-500 dark:border-slate-600 dark:bg-slate-700">
                                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Suspend</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sub_status" value="trial" class="h-4 w-4 border-slate-300 text-amber-600 focus:ring-amber-500 dark:border-slate-600 dark:bg-slate-700">
                                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Trial</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex justify-end space-x-3 bg-white dark:bg-slate-800 p-4 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm">
                        <button type="button" @click="showForm = false" class="rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 py-2 px-4 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-gradient-to-r from-blue-600 to-purple-600 py-2 px-6 text-sm font-medium text-white shadow-md hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            Simpan Tenant
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </SuperadminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, useForm } from '@inertiajs/vue3';
import SuperadminLayout from '@/Layouts/SuperadminLayout.vue';

const props = defineProps({
    tenants: {
        type: Array,
        default: () => []
    }
});

const pageTitle = computed(() => {
    return 'Kelola Tenant SaaS - LadaPala-Bill';
});

// Access shared company data
const company = computed(() => usePage().props.company || {});

// State for toggling form
const showForm = ref(false);

const form = useForm({
    name: '',
    domain: '',
    company_name: company.value.name || '',
    company_address: company.value.address || '',
    company_phone: company.value.phone || '',
    admin_name: '',
    admin_username: '',
    admin_password: '',
});

const submitForm = () => {
    form.post(route('superadmin.tenants.store'), {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        }
    });
};
</script>