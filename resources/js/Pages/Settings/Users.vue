<template>
    <AppLayout title="Kelola User">
        <div class="h-full flex flex-col min-h-0 w-full p-2">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Kelola Pengguna</h1>
                </div>
                <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah User
                </button>
            </div>

            <!-- Users Grid / Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Pengguna</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Role / Posisi</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kontak</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-inner relative">
                                                {{ user.name.substring(0, 2).toUpperCase() }}
                                                <div v-if="user.is_default_sales" class="absolute -bottom-1 -right-1 bg-amber-400 text-white rounded-full p-0.5 shadow-sm border-2 border-white dark:border-slate-800" title="User Default untuk Pendaftaran Pelanggan Baru">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                                {{ user.name }}
                                                <span v-if="user.is_default_sales" class="inline-flex items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">Default</span>
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">@{{ user.username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1.5" :class="getRoleColor(user.role?.name)">
                                        {{ user.role?.name || 'No Role' }}
                                    </div>
                                    <div class="flex items-center text-xs text-slate-500 dark:text-slate-400">
                                        <span v-if="user.role?.fee_type === 'fix'" class="flex items-center gap-1" title="Fix Fee per Transaksi">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Rp {{ formatMoney(user.role.fee_fix) }}
                                        </span>
                                        <span v-else-if="user.role?.fee_type === 'persen'" class="flex items-center gap-1" title="Persentase Fee per Transaksi">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                                            {{ Number(user.role.fee_persen) }}%
                                        </span>
                                        <span v-else class="text-slate-400 italic">Tanpa Fee</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <div v-if="user.email" class="flex items-center gap-1.5 mb-1">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ user.email }}
                                    </div>
                                    <div v-if="user.phone" class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ user.phone }}
                                    </div>
                                    <span v-if="!user.email && !user.phone" class="italic text-slate-400">-</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    <button @click="toggleUserStatus(user)" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2" :class="user.is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'" role="switch" :aria-checked="user.is_active">
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="user.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(user)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">Edit</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        <p>Belum ada pengguna yang terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700 flex flex-col" @click.stop>
                        
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/50">
                                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">{{ editingUser ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h3>
                            </div>
                            <button @click="closeModal" type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-300 transition-colors focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 py-5">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                                    <input v-model="form.name" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Misal: Administrator">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
                                    <input v-model="form.username" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="username_login">
                                    <p v-if="form.errors.username" class="mt-1 text-sm text-red-500">{{ form.errors.username }}</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                                        <input v-model="form.email" type="email" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="opsional@email.com">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">No. WhatsApp</label>
                                        <input v-model="form.phone" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="08...">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Role / Hak Akses</label>
                                    <select v-model="form.role_id" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all">
                                        <option value="">-- Pilih Role --</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                    </select>
                                    <p v-if="form.errors.role_id" class="mt-1 text-sm text-red-500">{{ form.errors.role_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Password <span class="text-slate-400 font-normal">{{ editingUser ? '(Kosongkan jika tidak diubah)' : '' }}</span></label>
                                    <input v-model="form.password" type="password" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Minimal 6 karakter">
                                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                                </div>
                                <div class="pt-3">
                                    <label class="flex items-start gap-3 cursor-pointer p-3.5 rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/10 hover:bg-amber-100 dark:hover:bg-amber-900/20 transition-colors">
                                        <div class="flex h-6 items-center">
                                            <input type="checkbox" v-model="form.is_default_sales" class="h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-600 dark:border-amber-700 dark:bg-amber-900 dark:ring-offset-slate-900">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-amber-900 dark:text-amber-400">Jadikan Akun Default Sales</span>
                                            <span class="text-xs text-amber-700/70 dark:text-amber-500/70 mt-0.5">Akun ini akan otomatis terpilih (default) saat proses pendaftaran pelanggan baru.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 flex flex-row-reverse gap-3">
                            <button @click="submitForm" type="button" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 disabled:opacity-50 transition-all">
                                {{ editingUser ? 'Simpan Perubahan' : 'Tambahkan' }}
                            </button>
                            <button @click="closeModal" type="button" class="inline-flex justify-center rounded-xl bg-white dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    users: Array,
    roles: Array
});

const isModalOpen = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    username: '',
    email: '',
    phone: '',
    role_id: '',
    password: '',
    is_default_sales: false
});

const getRoleColor = (roleName) => {
    if (!roleName) return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
    const lower = roleName.toLowerCase();
    if (lower.includes('admin')) return 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300';
    if (lower.includes('teknisi')) return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
    if (lower.includes('sales')) return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
    return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300';
};

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form.is_default_sales = false;
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.reset();
    form.clearErrors();
    form.name = user.name;
    form.username = user.username;
    form.email = user.email || '';
    form.phone = user.phone || '';
    form.role_id = user.role_id;
    form.password = ''; // empty password on edit
    form.is_default_sales = !!user.is_default_sales;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('config.users.update', editingUser.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('config.users.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const toggleUserStatus = (user) => {
    router.post(route('config.users.toggle', user.id), {}, { preserveScroll: true });
};

const formatMoney = (amount) => {
    if (!amount) return '0';
    return Number(amount).toLocaleString('id-ID');
};
</script>