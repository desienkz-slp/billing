<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    routers: { type: Array, required: true }
});

const selectedRouterId = ref('');

onMounted(() => {
    if (props.routers && props.routers.length > 0) {
        const smallest = [...props.routers].sort((a, b) => a.id - b.id)[0];
        selectedRouterId.value = smallest.id;
        fetchSecrets();
    }
});

const secrets = ref([]);
const profiles = ref([]);
const isLoading = ref(false);
const fetchError = ref('');
const deletingUser = ref('');

// Forms
const formAdd = ref({ username: '', password: '', profile: '' });
const isAdding = ref(false);
const addMessage = ref('');

const formUpdate = ref({ profile: '' });
const isUpdating = ref(false);
const updateMessage = ref('');

// Table Selection
const selectedUsers = ref([]);
const selectAll = ref(false);
const searchQuery = ref('');
const statusFilter = ref('all'); // 'all', 'online', 'offline'

// Load secrets when router changes
watch(selectedRouterId, async (newVal) => {
    if (!newVal) {
        secrets.value = [];
        profiles.value = [];
        return;
    }
    fetchSecrets();
});

const fetchSecrets = async () => {
    if (!selectedRouterId.value) return;
    isLoading.value = true;
    fetchError.value = '';
    selectedUsers.value = [];
    selectAll.value = false;
    
    try {
        const response = await axios.get(`/monitoring/router/${selectedRouterId.value}/secrets-json`);
        if (response.data.status === 'success') {
            secrets.value = response.data.secrets;
            profiles.value = response.data.profiles;
        } else {
            fetchError.value = response.data.message || 'Gagal memuat data secrets.';
        }
    } catch (error) {
        fetchError.value = 'Terjadi kesalahan jaringan: ' + (error.message || error);
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

const handleAddSecret = async () => {
    if (!formAdd.value.username || !formAdd.value.password || !formAdd.value.profile) {
        addMessage.value = 'Semua field wajib diisi.';
        return;
    }
    isAdding.value = true;
    addMessage.value = '';
    try {
        const response = await axios.post(`/monitoring/router/${selectedRouterId.value}/secrets/add`, formAdd.value);
        if (response.data.status === 'success') {
            addMessage.value = 'Success: ' + response.data.message;
            formAdd.value.username = '';
            formAdd.value.password = '';
            fetchSecrets(); // reload
        } else {
            addMessage.value = 'Error: ' + response.data.message;
        }
    } catch (error) {
        addMessage.value = 'Terjadi kesalahan sistem.';
    } finally {
        isAdding.value = false;
    }
};

const handleUpdateProfile = async () => {
    if (selectedUsers.value.length === 0) {
        updateMessage.value = 'Pilih minimal satu user.';
        return;
    }
    if (!formUpdate.value.profile) {
        updateMessage.value = 'Pilih profile baru.';
        return;
    }
    isUpdating.value = true;
    updateMessage.value = '';
    try {
        const response = await axios.post(`/monitoring/router/${selectedRouterId.value}/secrets/update-profile`, {
            users: selectedUsers.value,
            profile: formUpdate.value.profile
        });
        if (response.data.status === 'success') {
            updateMessage.value = 'Success: ' + response.data.message;
            selectedUsers.value = [];
            selectAll.value = false;
            fetchSecrets(); // reload
        } else {
            updateMessage.value = 'Error: ' + response.data.message;
        }
    } catch (error) {
        updateMessage.value = 'Terjadi kesalahan sistem.';
    } finally {
        isUpdating.value = false;
    }
};

import { useConfirm } from '../../Composables/useConfirm';

const { confirm } = useConfirm();

const handleDeleteSecret = async (username) => {
    const isConfirmed = await confirm({
        title: 'Hapus User PPPoE',
        message: `Yakin ingin menghapus user PPPoE "${username}" secara permanen dari router ini?`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        confirmColor: 'rose'
    });
    
    if (!isConfirmed) return;
    
    deletingUser.value = username;
    try {
        const response = await axios.post(`/monitoring/router/${selectedRouterId.value}/secrets/delete`, { username });
        if (response.data.status === 'success') {
            fetchSecrets(); // reload
        } else {
            alert('Gagal menghapus: ' + response.data.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan saat menghapus user.');
    } finally {
        deletingUser.value = '';
    }
};

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedUsers.value = filteredSecrets.value.map(s => s.name);
    } else {
        selectedUsers.value = [];
    }
};

const filteredSecrets = computed(() => {
    let result = secrets.value;
    if (statusFilter.value === 'online') result = result.filter(s => s.is_online);
    if (statusFilter.value === 'offline') result = result.filter(s => !s.is_online);
    
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(s => s.name.toLowerCase().includes(q) || (s.profile && s.profile.toLowerCase().includes(q)));
    }
    return result;
});

const formatOfflineDuration = (dateString) => {
    if (!dateString || dateString === '-') return '-';
    const lastLogout = new Date(dateString);
    if (isNaN(lastLogout.getTime())) return dateString; // fallback
    
    const now = new Date();
    const diffMs = now - lastLogout;
    if (diffMs < 0) return dateString; // time drift fallback
    
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffDays > 0) {
        const remainingHours = diffHours % 24;
        return `${diffDays} hari ${remainingHours > 0 ? remainingHours + ' jam' : ''}`.trim();
    } else if (diffHours > 0) {
        const remainingMins = diffMins % 60;
        return `${diffHours} jam ${remainingMins > 0 ? remainingMins + ' mnt' : ''}`.trim();
    } else {
        return `${diffMins} mnt`;
    }
};
</script>

<template>
    <div class="flex flex-col gap-4">
        <!-- Top Control Bar -->
        <div class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg flex flex-wrap items-center gap-4">
            <div class="flex flex-col flex-1 min-w-[200px]">
                <label class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wide font-semibold mb-1">Pilih Router</label>
                <select v-model="selectedRouterId" class="bg-slate-100 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-[13px] rounded-lg px-3 py-2 focus:outline-none focus:border-sky-500">
                    <option value="">-- Silakan Pilih Router --</option>
                    <option v-for="router in routers" :key="router.id" :value="router.id">
                        {{ router.name }} {{ !router.is_active ? '(Nonaktif)' : '' }}
                    </option>
                </select>
            </div>
            <div v-if="isLoading" class="text-[13px] text-sky-600 dark:text-sky-400 font-semibold flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-sky-600 dark:text-sky-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memuat data rahasia...
            </div>
            <div v-if="fetchError" class="text-[13px] text-red-600 dark:text-red-400 font-semibold">
                {{ fetchError }}
            </div>
        </div>

        <template v-if="selectedRouterId && !isLoading">
            <!-- Management Forms Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Add Secret -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg">
                    <h3 class="text-[14px] font-bold text-slate-800 dark:text-white mb-3">TAMBAH USER</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div class="flex flex-col">
                            <label class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Username</label>
                            <input v-model="formAdd.username" type="text" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-3 py-1.5 text-[13px]" placeholder="Username">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Password</label>
                            <input v-model="formAdd.password" type="text" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-3 py-1.5 text-[13px]" placeholder="Password">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Profile</label>
                            <select v-model="formAdd.profile" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-3 py-1.5 text-[13px]">
                                <option value="">Pilih...</option>
                                <option v-for="p in profiles" :key="p.name" :value="p.name">{{ p.name }}</option>
                            </select>
                        </div>
                    </div>
                    <button @click="handleAddSecret" :disabled="isAdding" class="w-full py-2 rounded-lg border border-[rgba(52,211,153,0.28)] bg-gradient-to-b from-[rgba(52,211,153,0.22)] to-[rgba(52,211,153,0.12)] text-[#d9fff0] text-[13px] font-bold hover:brightness-110 transition-all disabled:opacity-50">
                        <i class="bi bi-plus-lg mr-1"></i> TAMBAH
                    </button>
                    <div v-if="addMessage" class="mt-2 text-[12px]" :class="addMessage.includes('Error') ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'">{{ addMessage }}</div>
                </div>

                <!-- Update Profile -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-[14px] font-bold text-slate-800 dark:text-white">UPDATE PROFILE</h3>
                        <span class="text-[12px] text-slate-500 dark:text-slate-400">{{ selectedUsers.length }} user dipilih</span>
                    </div>
                    <div class="flex flex-col mb-3">
                        <label class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Pilih profile baru</label>
                        <select v-model="formUpdate.profile" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-3 py-1.5 text-[13px]">
                            <option value="">Pilih profile baru...</option>
                            <option v-for="p in profiles" :key="p.name" :value="p.name">{{ p.name }}</option>
                        </select>
                    </div>
                    <button @click="handleUpdateProfile" :disabled="isUpdating" class="w-full py-2 rounded-lg border border-[rgba(56,189,248,0.28)] bg-gradient-to-b from-[rgba(56,189,248,0.22)] to-[rgba(56,189,248,0.12)] text-sky-600 dark:text-sky-300 text-[13px] font-bold hover:brightness-110 transition-all disabled:opacity-50">
                        <i class="bi bi-arrow-repeat mr-1"></i> UPDATE PROFIL
                    </button>
                    <div v-if="updateMessage" class="mt-2 text-[12px]" :class="updateMessage.includes('Error') ? 'text-red-600 dark:text-red-400' : 'text-sky-600 dark:text-sky-300'">{{ updateMessage }}</div>
                </div>
            </div>

            <!-- Secrets Data Table -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg overflow-hidden flex-1 flex flex-col min-h-[400px]">
                <div class="flex justify-between items-center px-3 py-2.5 border-b border-slate-200 dark:border-slate-700 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <input type="text" v-model="searchQuery" placeholder="Cari user / profile..." class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-2.5 py-1 text-[13px] min-w-[200px] focus:outline-none focus:border-sky-500">
                        <div class="flex gap-1 p-0.5 bg-slate-100 dark:bg-slate-800/50 rounded-lg">
                            <button @click="statusFilter = 'all'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', statusFilter === 'all' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">All</button>
                            <button @click="statusFilter = 'online'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', statusFilter === 'online' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">Online</button>
                            <button @click="statusFilter = 'offline'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', statusFilter === 'offline' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">Offline</button>
                        </div>
                    </div>
                    <div class="text-[12px] text-slate-500 dark:text-slate-400">{{ filteredSecrets.length }} user ditampilkan</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap text-[13px] text-slate-800 dark:text-white">
                        <thead class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 bg-white/90 dark:bg-slate-900/90">
                            <tr>
                                <th class="px-3 py-2.5 w-[40px] text-center">
                                    <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" class="accent-[#38bdf8] w-3.5 h-3.5 cursor-pointer rounded border-[rgba(255,255,255,0.2)]">
                                </th>
                                <th class="px-3 py-2.5">USERNAME</th>
                                <th class="px-3 py-2.5">PROFILE</th>
                                <th v-if="statusFilter !== 'offline'" class="px-3 py-2.5">UPTIME</th>
                                <th v-if="statusFilter === 'offline'" class="px-3 py-2.5">LAST LOGOUT</th>
                                <th class="px-3 py-2.5 text-right">STATUS</th>
                                <th class="px-3 py-2.5 w-[60px] text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgba(255,255,255,0.06)]">
                            <tr v-for="secret in filteredSecrets" :key="secret.name" class="hover:bg-slate-50 dark:bg-slate-800/50 transition-colors duration-150">
                                <td class="px-3 py-2.5 text-center">
                                    <input type="checkbox" v-model="selectedUsers" :value="secret.name" class="accent-[#38bdf8] w-3.5 h-3.5 cursor-pointer rounded border-[rgba(255,255,255,0.2)]">
                                </td>
                                <td class="px-3 py-2.5 font-bold text-slate-800 dark:text-white">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full mr-1.5" :class="secret.is_online ? 'bg-emerald-500 dark:bg-emerald-400 shadow-[0_0_6px_rgba(16,185,129,0.5)]' : 'bg-[#64748b]'"></span>
                                    {{ secret.name }}
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[11.5px] font-bold">{{ secret.profile }}</span>
                                </td>
                                <td v-if="statusFilter !== 'offline'" class="px-3 py-2.5 text-slate-500 dark:text-slate-400 font-mono text-[12px]">{{ secret.uptime || '-' }}</td>
                                <td v-if="statusFilter === 'offline'" class="px-3 py-2.5">
                                    <span v-if="!secret.is_online" class="text-[11.5px] font-mono text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-2 py-0.5 rounded-md border border-rose-100 dark:border-rose-800/30">
                                        {{ formatOfflineDuration(secret.last_logout) }}
                                    </span>
                                    <span v-else class="text-[12px] font-mono text-slate-400 dark:text-slate-500">-</span>
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <span class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full border" :class="secret.is_online ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800/50 border-[rgba(148,163,184,0.18)] text-slate-600 dark:text-slate-300'">
                                        {{ secret.is_online ? 'Online' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <button @click="handleDeleteSecret(secret.name)" :disabled="deletingUser === secret.name" class="p-1.5 rounded-md text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors disabled:opacity-50" title="Hapus User">
                                        <svg v-if="deletingUser === secret.name" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!filteredSecrets.length">
                                <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400 italic">
                                    Tidak ada data secret.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</template>
