<template>
    <AppLayout :title="pageTitle">
        <div class="p-2">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Manajemen Paket</h1>
                </div>
                <button @click="openModal()" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                    <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Paket
                </button>
            </div>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 sm:pl-6 uppercase tracking-wider">Nama Paket</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jml Pelanggan</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">MikroTik Bind</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">RADIUS Bind</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        <tr v-for="pkg in packages" :key="pkg.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                <div class="font-medium text-xs sm:text-sm text-slate-900 dark:text-white">{{ pkg.name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ pkg.speed }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-xs sm:text-sm">
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">Rp {{ formatNumber(pkg.price) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ pkg.customers_count }} Pelanggan
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-center text-xs sm:text-sm">
                                <span class="text-slate-600 dark:text-slate-400">{{ pkg.package_routers?.length || 0 }} Router</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-center text-xs sm:text-sm">
                                <span class="text-slate-600 dark:text-slate-400">{{ pkg.package_servers?.length || 0 }} Server</span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 sm:pr-6 text-right text-xs sm:text-sm font-medium space-x-3">
                                <button @click="openModal(pkg)" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    Edit
                                </button>
                                <button @click="confirmDelete(pkg)" class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!packages.length">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    <span class="text-sm">Belum ada data paket yang tersimpan.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Modal -->
        <Modal :show="isModalOpen" @close="closeModal" maxWidth="4xl" :noPadding="true">
            <div class="p-6">
                <div class="flex justify-between items-center mb-5 pb-4 border-b border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">
                        {{ editingPackage ? 'Edit Paket' : 'Tambah Paket Baru' }}
                    </h2>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- General Info -->
                    <div>
                        <h3 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">Informasi Umum</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Paket</label>
                                <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 text-slate-800 dark:text-slate-100" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Speed (Opsional)</label>
                                <input v-model="form.speed" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 text-slate-800 dark:text-slate-100" placeholder="Contoh: 10M/10M" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga (Rp)</label>
                                <input v-model="form.price" type="number" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 text-slate-800 dark:text-slate-100" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                                <input v-model="form.description" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 text-slate-800 dark:text-slate-100" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Mikrotik Bindings -->
                        <div>
                            <div class="flex justify-between items-center mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">
                                <h3 class="text-sm font-medium text-slate-800 dark:text-slate-200">Bind MikroTik</h3>
                                <button type="button" @click="addRouterBind" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded">
                                    + Tambah MikroTik
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(bind, index) in form.router_profiles" :key="'r'+index" class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 relative group">
                                    <button type="button" @click="removeRouterBind(index)" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                    <div class="space-y-3 pr-6">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Router</label>
                                            <select v-model="bind.router_id" @change="fetchRouterProfiles(bind.router_id)" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                <option disabled value="">Pilih Router</option>
                                                <option v-for="r in routers" :key="r.id" :value="r.id">{{ r.name }}</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Profile Active</label>
                                                <select v-model="bind.pppoe_profile" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-emerald-500 focus:ring-emerald-500" required>
                                                    <option disabled value="">Pilih Profile</option>
                                                    <option v-for="p in routerProfilesMap[bind.router_id] || []" :key="p.name" :value="p.name">{{ p.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Profile Isolir</label>
                                                <select v-model="bind.isolir_profile" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                                                    <option value="">Tidak Ada (Opsional)</option>
                                                    <option v-for="p in routerProfilesMap[bind.router_id] || []" :key="p.name" :value="p.name">{{ p.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!form.router_profiles.length" class="text-xs text-center text-slate-400 py-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 border-dashed">
                                    Belum ada MikroTik yang di-bind.
                                </div>
                            </div>
                        </div>

                        <!-- Radius Bindings -->
                        <div>
                            <div class="flex justify-between items-center mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">
                                <h3 class="text-sm font-medium text-slate-800 dark:text-slate-200">Bind RADIUS</h3>
                                <button type="button" @click="addServerBind" class="text-xs font-medium text-blue-600 hover:text-blue-700 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">
                                    + Tambah RADIUS
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(bind, index) in form.server_profiles" :key="'s'+index" class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 relative group">
                                    <button type="button" @click="removeServerBind(index)" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                    <div class="space-y-3 pr-6">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Server RADIUS</label>
                                            <select v-model="bind.server_id" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                                                <option disabled value="">Pilih Server</option>
                                                <option v-for="s in servers" :key="s.id" :value="s.id">{{ s.name }}</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Group Active</label>
                                                <select v-model="bind.radius_group" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                                                    <option disabled value="">Pilih Group</option>
                                                    <option v-for="g in db_pusat_profiles" :key="g" :value="g">{{ g }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Group Isolir</label>
                                                <select v-model="bind.radius_isolir_group" class="w-full text-sm rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-1.5 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Tidak Ada (Opsional)</option>
                                                    <option v-for="g in db_pusat_profiles" :key="g" :value="g">{{ g }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!form.server_profiles.length" class="text-xs text-center text-slate-400 py-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 border-dashed">
                                    Belum ada RADIUS yang di-bind.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium shadow-md transition-all disabled:opacity-50 flex items-center gap-2 whitespace-nowrap">
                            <span v-if="form.processing" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="deleteData !== null" @close="deleteData = null" max-width="md">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 text-red-600 dark:text-red-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">Hapus Paket</h3>
                        <p class="text-sm text-slate-500 mt-1">Yakin ingin menghapus paket ini? Tindakan ini tidak bisa dibatalkan.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button @click="deleteData = null" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
                    <button @click="executeDelete" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium shadow-sm transition-colors">Ya, Hapus</button>
                </div>
            </div>
        </Modal>
        
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    capabilities: Object,
    packages: Array,
    routers: Array,
    servers: Array,
    db_pusat_profiles: Array,
});

const pageTitle = computed(() => 'Manajemen Paket');

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
};

const isModalOpen = ref(false);
const editingPackage = ref(null);
const deleteData = ref(null);
const routerProfilesMap = ref({});

const fetchRouterProfiles = async (routerId) => {
    if (!routerId || routerProfilesMap.value[routerId]) return;
    try {
        const res = await axios.get(route('settings.packages.router-profiles', routerId));
        routerProfilesMap.value[routerId] = res.data;
    } catch (e) {
        console.error("Gagal mengambil profil router", e);
    }
};

const form = useForm({
    name: '',
    speed: '',
    price: '',
    description: '',
    router_profiles: [],
    server_profiles: []
});

const openModal = (pkg = null) => {
    if (pkg) {
        editingPackage.value = pkg;
        
        let rb = [];
        if (pkg.package_routers) {
            pkg.package_routers.forEach(pr => {
                rb.push({
                    router_id: pr.router_id,
                    pppoe_profile: pr.pppoe_profile || '',
                    isolir_profile: pr.isolir_profile || ''
                });
                fetchRouterProfiles(pr.router_id);
            });
        }

        let sb = [];
        if (pkg.package_servers) {
            pkg.package_servers.forEach(ps => {
                sb.push({
                    server_id: ps.server_id,
                    radius_group: ps.radius_group || '',
                    radius_isolir_group: ps.radius_isolir_group || ''
                });
            });
        }

        form.name = pkg.name;
        form.speed = pkg.speed || '';
        form.price = pkg.price;
        form.description = pkg.description || '';
        form.router_profiles = rb;
        form.server_profiles = sb;
    } else {
        editingPackage.value = null;
        form.reset();
        form.router_profiles = [];
        form.server_profiles = [];
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const addRouterBind = () => {
    form.router_profiles.push({ router_id: '', pppoe_profile: '', isolir_profile: '' });
};

const removeRouterBind = (index) => {
    form.router_profiles.splice(index, 1);
};

const addServerBind = () => {
    form.server_profiles.push({ server_id: '', radius_group: '', radius_isolir_group: '' });
};

const removeServerBind = (index) => {
    form.server_profiles.splice(index, 1);
};

const submit = () => {
    let backendRouterProfiles = {};
    form.router_profiles.forEach(b => {
        if (b.router_id) {
            backendRouterProfiles[b.router_id] = {
                enabled: true,
                pppoe_profile: b.pppoe_profile,
                isolir_profile: b.isolir_profile
            };
        }
    });

    let backendServerProfiles = {};
    form.server_profiles.forEach(b => {
        if (b.server_id) {
            backendServerProfiles[b.server_id] = {
                enabled: true,
                radius_group: b.radius_group,
                radius_isolir_group: b.radius_isolir_group
            };
        }
    });

    const submitForm = form.transform((data) => ({
        ...data,
        router_profiles: backendRouterProfiles,
        server_profiles: backendServerProfiles
    }));

    if (editingPackage.value) {
        submitForm.put(route('settings.packages.update', editingPackage.value.id), {
            onSuccess: () => closeModal()
        });
    } else {
        submitForm.post(route('settings.packages.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const confirmDelete = (pkg) => {
    if (pkg.customers_count > 0) {
        alert(`Paket ${pkg.name} tidak bisa dihapus karena digunakan oleh ${pkg.customers_count} pelanggan.`);
        return;
    }
    deleteData.value = pkg;
};

const executeDelete = () => {
    if (deleteData.value) {
        router.delete(route('settings.packages.destroy', deleteData.value.id), {
            onSuccess: () => {
                deleteData.value = null;
            }
        });
    }
};
</script>