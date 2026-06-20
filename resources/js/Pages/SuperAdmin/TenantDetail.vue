<template>
    <SuperadminLayout :title="pageTitle">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-full h-full flex flex-col min-h-0 w-full mx-auto">
            
            <!-- Page Header -->
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div>
                    <div class="flex items-center space-x-3">
                        <Link :href="route('superadmin.tenants')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center">
                            {{ tenant.name }}
                            <span v-if="tenant.is_active" class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Aktif</span>
                            <span v-else class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                        </h1>
                    </div>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 ml-9">
                        {{ tenant.domain || (tenant.slug + '.ladapala.com') }}
                    </p>
                </div>
            </div>

            <!-- Stats Overview -->
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
                <div class="px-4 py-5 bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden sm:p-6">
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">Total Pengguna (Staff)</dt>
                    <dd class="mt-1 text-3xl font-semibold text-slate-900 dark:text-white">{{ stats?.total_users || 0 }}</dd>
                </div>
                <div class="px-4 py-5 bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden sm:p-6">
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">Pengguna Aktif</dt>
                    <dd class="mt-1 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ stats?.active_users || 0 }}</dd>
                </div>
                <div class="px-4 py-5 bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden sm:p-6">
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">Total Pelanggan</dt>
                    <dd class="mt-1 text-3xl font-semibold text-slate-900 dark:text-white">{{ stats?.total_customers || 0 }}</dd>
                </div>
                <div class="px-4 py-5 bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden sm:p-6">
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 truncate">Role Tersedia</dt>
                    <dd class="mt-1 text-3xl font-semibold text-slate-900 dark:text-white">{{ stats?.total_roles || 0 }}</dd>
                </div>
            </dl>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (Identity & Status) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Identity Form -->
                    <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                            <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Identitas & Profil Tenant
                            </h3>
                            <button class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium">Edit Profil</button>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="updateTenant">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Perusahaan / ISP</label>
                                        <input type="text" :value="tenant.company_profile?.name || tenant.name" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Slug URL (Subdomain)</label>
                                        <input type="text" :value="tenant.slug" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50 dark:bg-slate-600" disabled>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat Perusahaan</label>
                                        <textarea rows="2" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ tenant.company_profile?.address || '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Telepon</label>
                                        <input type="text" :value="tenant.company_profile?.phone || ''" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Custom Domain</label>
                                        <input type="text" :value="tenant.domain || ''" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="app.perusahaan.com">
                                    </div>
                                </div>
                                <div class="mt-5 flex justify-end">
                                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-slate-800 dark:bg-slate-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-slate-900 dark:hover:bg-slate-500 focus:outline-none">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Administrator Accounts -->
                    <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Pengguna (Staff) Tenant
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama & Username</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                    <tr v-for="user in users" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ user.name }}</div>
                                            <div class="text-sm text-slate-500">{{ user.username }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            {{ user.role?.name || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="user.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Aktif</span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                                        </td>
                                    </tr>
                                    <tr v-if="!users || users.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-slate-500">Belum ada pengguna di tenant ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Subscription) -->
                <div class="space-y-6">
                    
                    <!-- Subscription Form -->
                    <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Paket Langganan SaaS
                            </h3>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="updateSubscription">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status Langganan</label>
                                        <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                            <option value="active" selected>Aktif</option>
                                            <option value="suspend">Ditangguhkan (Suspend)</option>
                                            <option value="trial">Masa Percobaan (Trial)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Paket</label>
                                        <select class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                            <option selected>Pro (Up to 500 User)</option>
                                            <option>Starter (Up to 100 User)</option>
                                            <option>Enterprise (Unlimited)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Limit Pelanggan</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pr-12 focus:border-amber-500 focus:ring-amber-500 sm:text-sm" value="500">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-slate-500 sm:text-sm">User</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Jatuh Tempo</label>
                                        <input type="date" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-5 border-t border-slate-200 dark:border-slate-700 pt-5">
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                        Update Subscription
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Subscription History (Read Only) -->
                    <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Riwayat Perpanjangan</h3>
                        </div>
                        <ul class="divide-y divide-slate-200 dark:divide-slate-700 max-h-60 overflow-y-auto">
                            <li v-for="sub in subscriptions" :key="sub.id" class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ sub.plan_name || 'Paket Kustom' }}</div>
                                    <div class="text-xs text-slate-500">{{ new Date(sub.created_at).toLocaleDateString() }}</div>
                                </div>
                                <div class="mt-2 flex justify-between">
                                    <div class="sm:flex">
                                        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400">
                                            <span class="mr-1">Berakhir:</span> {{ new Date(sub.expires_at).toLocaleDateString() }}
                                        </div>
                                    </div>
                                    <div class="flex items-center text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        Rp {{ new Intl.NumberFormat('id-ID').format(sub.price || 0) }}
                                    </div>
                                </div>
                            </li>
                            <li v-if="!subscriptions || subscriptions.length === 0" class="px-6 py-4 text-center text-sm text-slate-500">
                                Belum ada riwayat perpanjangan.
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </SuperadminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import SuperadminLayout from '@/Layouts/SuperadminLayout.vue';

const props = defineProps({
    tenant: Object,
    users: Array,
    roles: Array,
    subscriptions: Array,
    stats: Object
});

const pageTitle = computed(() => {
    return 'Detail Tenant - ' + (props.tenant?.name || '');
});

const updateTenant = () => {
    alert('Fungsi update profil tenant akan diimplementasikan!');
};

const updateSubscription = () => {
    alert('Fungsi update subscription SaaS akan diimplementasikan!');
};
</script>
