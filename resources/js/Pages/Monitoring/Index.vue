<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GlobalSecrets from './GlobalSecrets.vue';

const props = defineProps({
    routers: { type: Array, default: () => [] },
    summary: { type: Object, default: () => ({}) },
    pppoeUsers: { type: Array, default: () => [] },
});

const activeGlobalTab = ref('status');
const pageTitle = computed(() => 'Monitoring Router');

// UI State for User PPPoE Table
const searchQuery = ref('');
const activeTab = ref('All'); // 'All', 'Online', 'Offline'
const selectedRouter = ref(props.routers.length > 0 ? props.routers[0].id : 'all');

const filteredUsers = computed(() => {
    let users = props.pppoeUsers;
    
    // Filter by Router
    if (selectedRouter.value !== 'all') {
        users = users.filter(u => u.router_id === selectedRouter.value);
    }
    
    // Filter by Status Tab
    if (activeTab.value === 'Online') {
        users = users.filter(u => u.is_online);
    } else if (activeTab.value === 'Offline') {
        users = users.filter(u => !u.is_online);
    }
    
    // Filter by Search Query
    if (searchQuery.value) {
        const lowerQ = searchQuery.value.toLowerCase();
        users = users.filter(u => 
            u.name.toLowerCase().includes(lowerQ) || 
            (u.profile && u.profile.toLowerCase().includes(lowerQ))
        );
    }
    
    return users;
});

// Format relative time
const formatRelativeTime = (timestamp) => {
    if (!timestamp) return 'Belum pernah sync';
    const diff = Math.floor((Date.now() - new Date(timestamp).getTime()) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    return Math.floor(diff / 86400) + ' hari lalu';
};

const formatDate = (timestamp) => {
    if (!timestamp) return '-';
    return new Date(timestamp).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

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
    <AppLayout :title="pageTitle" :hideSidebar="true">
        <div class="min-h-full font-sans text-slate-800 dark:text-white">
            <div class="w-full px-4 py-4 flex flex-col">

                <!-- Status Live Header -->
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg p-3 mb-2">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-4">
                            <h2 class="text-[15px] font-bold text-slate-800 dark:text-white tracking-wide">Status Live</h2>
                            
                            <!-- Tab Buttons -->
                            <div class="flex items-center bg-slate-50 dark:bg-slate-900/50 rounded-lg p-0.5 border border-slate-300 dark:border-slate-600">
                                <button @click="activeGlobalTab = 'status'" :class="['px-4 py-1.5 rounded-md text-[12px] font-semibold transition-all', activeGlobalTab === 'status' ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700']">
                                    Status
                                </button>
                                <button @click="activeGlobalTab = 'secret'" :class="['px-4 py-1.5 rounded-md text-[12px] font-semibold transition-all', activeGlobalTab === 'secret' ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700']">
                                    Secret
                                </button>
                            </div>
                        </div>
                        <span class="text-[11.5px] text-slate-500 dark:text-slate-400">Update {{ new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}</span>
                    </div>

                    <!-- 4 Top Cards -->
                    <div v-if="activeGlobalTab === 'status'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
                        <div class="px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ summary.total_online || 0 }} user online</div>
                            <div class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-0.5">Sesi aktif</div>
                        </div>
                        <div class="px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ summary.healthy_routers || 0 }} router terhubung</div>
                            <div class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-0.5">Router live</div>
                        </div>
                        <div class="px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ summary.error_routers || 0 }} router bermasalah</div>
                            <div class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-0.5">{{ summary.error_routers ? 'Perlu dicek' : 'Normal' }}</div>
                        </div>
                        <div class="px-4 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div class="text-lg font-bold text-slate-800 dark:text-white leading-tight">Baru saja</div>
                            <div class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-0.5">{{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</div>
                        </div>
                    </div>

                    <!-- Summary Stats Grid (6 small stat cards) -->
                    <div v-if="activeGlobalTab === 'status'" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <!-- Router -->
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center text-[13px]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[14px] font-bold text-slate-800 dark:text-white">{{ summary.total_routers || 0 }}</div>
                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400">{{ summary.healthy_routers || 0 }} aktif</div>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 text-right">Router</div>
                        </div>
                        <!-- Secrets -->
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-[13px]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[14px] font-bold text-slate-800 dark:text-white">{{ summary.total_secrets || 0 }}</div>
                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400">{{ summary.total_secrets - summary.mapped_customers }} belum match</div>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 text-right">Secrets</div>
                        </div>
                        <!-- Profiles -->
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-[13px]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[14px] font-bold text-slate-800 dark:text-white">{{ summary.total_profiles || 0 }}</div>
                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400">profiles</div>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 text-right">Profiles</div>
                        </div>
                        <!-- Customers -->
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-[13px]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[14px] font-bold text-slate-800 dark:text-white">{{ summary.total_customers || 0 }}</div>
                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400">{{ summary.mapped_customers || 0 }} linked</div>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 text-right">Customers</div>
                        </div>
                        <!-- Synced -->
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center text-[13px]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-[14px] font-bold text-slate-800 dark:text-white">{{ summary.healthy_routers || 0 }}</div>
                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400">100%</div>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 text-right">Synced</div>
                        </div>
                    </div>
                </div>

                <!-- Sync Router Table -->
                <div v-if="activeGlobalTab === 'status'" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg overflow-hidden mb-4">
                    <div class="flex justify-between items-center px-3 py-2.5 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-[14px] font-bold text-slate-800 dark:text-white">Sync Router</h3>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Snapshot sync · live health</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap text-[13px] text-slate-800 dark:text-white">
                            <thead class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-3 py-2">ROUTER</th>
                                    <th class="px-3 py-2">STATUS</th>
                                    <th class="px-3 py-2">DATA SYNC</th>
                                    <th class="px-3 py-2">CUSTOMER</th>
                                    <th class="px-3 py-2">LIVE</th>
                                    <th class="px-3 py-2">LOKAL BACKUP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[rgba(255,255,255,0.06)]">
                                <tr v-for="router in routers" :key="router.id" 
                                    @click="router.visit(route('monitoring.router.detail', router.id))"
                                    class="hover:bg-slate-50 dark:bg-slate-900/50 transition-colors duration-150 cursor-pointer">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ router.name }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                            {{ router.ip_address }}:{{ router.port || 8728 }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="flex gap-1.5">
                                            <span class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full" :class="router.is_active ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-300' : 'bg-slate-100 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300'">
                                                <span class="w-1.5 h-1.5 mr-1 rounded-full" :class="router.is_active ? 'bg-sky-500 dark:bg-sky-400' : 'bg-slate-400 dark:bg-slate-500'"></span>
                                                {{ router.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            <span v-if="router.last_sync" class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300">Fresh</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-[13px] text-slate-800 dark:text-white">{{ router.pppoe_secrets_count }} secret</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ router.pppoe_profiles_count }} profile</div>
                                        <div v-if="router.disabled_secrets" class="text-[11px] text-amber-600 dark:text-amber-400">{{ router.disabled_secrets }} secret disabled</div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-[13px] text-slate-800 dark:text-white">{{ router.mapped_customers }} linked</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ router.customers_count }} customer</div>
                                        <div v-if="router.unmapped_secrets" class="text-[11px] text-red-600 dark:text-red-400">{{ router.unmapped_secrets }} belum match</div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full" :class="{
                                            'border border-emerald-200 dark:border-emerald-800/50 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400': router.live_status === 'ok',
                                            'border border-red-200 dark:border-red-800/50 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400': router.live_status === 'error',
                                            'bg-slate-100 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300': router.live_status === 'pending',
                                        }">
                                            <span class="w-1.5 h-1.5 mr-1 rounded-full" :class="{
                                                'bg-emerald-500 dark:bg-emerald-400 shadow-[0_0_6px_rgba(16,185,129,0.5)]': router.live_status === 'ok',
                                                'bg-red-500 dark:bg-red-400': router.live_status === 'error',
                                                'bg-slate-400 dark:bg-slate-500': router.live_status === 'pending',
                                            }"></span>
                                            <template v-if="router.live_status === 'ok'">Terhubung</template>
                                            <template v-else-if="router.live_status === 'error'">Error</template>
                                            <template v-else>Pending</template>
                                        </span>
                                        <div v-if="router.live_status === 'ok'" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ router.online_count }} online</div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-[13px] text-slate-800 dark:text-white">{{ formatRelativeTime(router.last_sync) }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ formatDate(router.last_sync) }}</div>
                                    </td>
                                </tr>
                                <tr v-if="!routers.length">
                                    <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400 italic">
                                        Tidak ada router aktif ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- User PPPoE Section (Full Table) -->
                <div v-if="activeGlobalTab === 'status'" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-md shadow-lg overflow-hidden flex-1 flex flex-col min-h-[400px]">
                    <div class="flex justify-between items-center px-3 py-2.5 border-b border-slate-200 dark:border-slate-700 flex-wrap gap-2">
                        <div class="flex items-center gap-3">
                            <h3 class="text-[14px] font-bold text-slate-800 dark:text-white">User PPPoE</h3>
                            <div class="flex items-center gap-2">
                                <input type="text" v-model="searchQuery" placeholder="Cari user, profile, router..." 
                                    class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-2.5 py-1 text-[13px] min-w-[180px] focus:outline-none focus:border-sky-500 placeholder-slate-400 dark:placeholder-slate-500" />
                                <select v-model="selectedRouter" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-2.5 py-1 text-[13px] focus:outline-none focus:border-sky-500">
                                    <option value="all" class="bg-white dark:bg-slate-800">Semua router</option>
                                    <option v-for="router in routers" :key="router.id" :value="router.id" class="bg-white dark:bg-slate-800">{{ router.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1 p-0.5 bg-slate-100 dark:bg-slate-800/50 rounded-lg">
                                <button @click="activeTab = 'All'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', activeTab === 'All' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">All</button>
                                <button @click="activeTab = 'Online'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', activeTab === 'Online' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">Online</button>
                                <button @click="activeTab = 'Offline'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', activeTab === 'Offline' ? 'bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']">Offline</button>
                            </div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ filteredUsers.length }} user</span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse whitespace-nowrap text-[13px] text-slate-800 dark:text-white">
                            <thead class="sticky top-0 z-10 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-3 py-2">USER</th>
                                    <th class="px-3 py-2">ROUTER</th>
                                    <th class="px-3 py-2">PROFILE</th>
                                    <th v-if="activeTab !== 'Offline'" class="px-3 py-2">IP</th>
                                    <th v-if="activeTab !== 'Offline'" class="px-3 py-2">INTERFACE</th>
                                    <th v-if="activeTab !== 'Offline'" class="px-3 py-2">UPTIME</th>
                                    <th v-if="activeTab === 'Offline'" class="px-3 py-2">LAST LOGOUT</th>
                                    <th class="px-3 py-2 text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[rgba(255,255,255,0.06)]">
                                <tr v-for="user in filteredUsers" :key="user.name + '-' + user.router_id" class="hover:bg-slate-50 dark:bg-slate-800/50 transition-colors duration-150">
                                    <td class="px-3 py-2.5 font-bold text-slate-800 dark:text-white">
                                        <span v-if="user.is_online" class="inline-block w-2 h-2 rounded-full mr-2 bg-emerald-500 dark:bg-emerald-400 shadow-[0_0_6px_rgba(16,185,129,0.5)]"></span>
                                        <span v-else class="inline-block w-2 h-2 rounded-full mr-2 bg-[#64748b]"></span>
                                        {{ user.name }}
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-800 dark:text-white font-bold text-[12.5px]">{{ user.router_name }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 text-[11px] font-semibold text-slate-700 dark:text-slate-300">{{ user.profile }}</span>
                                    </td>
                                    <td v-if="activeTab !== 'Offline'" class="px-3 py-2.5 font-mono text-[12px] text-sky-600 dark:text-sky-400">
                                        <a v-if="user.ip !== '-'" :href="`http://${user.ip}`" target="_blank" class="hover:text-slate-900 dark:hover:text-white hover:underline transition-all">{{ user.ip }}</a>
                                        <span v-else>{{ user.ip }}</span>
                                    </td>
                                    <td v-if="activeTab !== 'Offline'" class="px-3 py-2.5 font-mono text-[12px] text-slate-500 dark:text-slate-400">{{ user.caller_id }}</td>
                                    <td v-if="activeTab !== 'Offline'" class="px-3 py-2.5 text-[12px] text-slate-700 dark:text-slate-300">{{ user.uptime }}</td>
                                    <td v-if="activeTab === 'Offline'" class="px-3 py-2.5">
                                        <span v-if="!user.is_online" class="text-[11.5px] font-mono text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-2 py-0.5 rounded-md border border-rose-100 dark:border-rose-800/30">
                                            {{ formatOfflineDuration(user.last_logout) }}
                                        </span>
                                        <span v-else class="text-[12px] font-mono text-slate-400 dark:text-slate-500">-</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span v-if="user.disabled" class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-slate-100 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">Disabled</span>
                                        <span v-else-if="user.is_online" class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full border border-emerald-200 dark:border-emerald-800/50 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">Online</span>
                                        <span v-else class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-slate-100 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">Offline</span>
                                    </td>
                                </tr>
                                <tr v-if="!filteredUsers.length">
                                    <td :colspan="activeTab === 'Offline' ? 5 : 7" class="px-3 py-12 text-center text-slate-500 dark:text-slate-400 italic">Tidak ada user PPPoE yang cocok dengan filter.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Global Secrets View -->
                <GlobalSecrets v-else-if="activeGlobalTab === 'secret'" :routers="routers" />

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>