<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  router: { type: Object, required: true },
  enrichedSecrets: { type: Array, default: () => [] },
  activeDetails: { type: Array, default: () => [] },
  profiles: { type: Array, default: () => [] },
  resources: { type: [Object, Array], default: () => ({}) },
  identity: { type: [Object, String], default: () => ({}) }
});

const activeTab = ref('active');
const searchQuery = ref('');

// Formatting Bytes (for Memory KPI)
const formatBytes = (bytes) => {
  if (!bytes || bytes === 0 || bytes === '0') return '0 B';
  const parsedBytes = parseInt(bytes, 10);
  if (isNaN(parsedBytes)) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(parsedBytes) / Math.log(k));
  return parseFloat((parsedBytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Check if a secret is in active connections
const isActive = (secretName) => {
  return props.activeDetails.some(active => active.name === secretName);
};

// Back Action
const goBack = () => {
  window.history.back();
};

const filteredActive = computed(() => {
    if (!searchQuery.value) return props.activeDetails;
    const lowerQ = searchQuery.value.toLowerCase();
    return props.activeDetails.filter(s => s.name.toLowerCase().includes(lowerQ));
});

const filteredSecrets = computed(() => {
    if (!searchQuery.value) return props.enrichedSecrets;
    const lowerQ = searchQuery.value.toLowerCase();
    return props.enrichedSecrets.filter(s => s.name.toLowerCase().includes(lowerQ));
});
</script>

<template>
  <AppLayout :title="router.name || 'Router Details'" :hideSidebar="true">
    <div class="min-h-full font-sans text-[#e5eef8] pb-12" style="background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.16), transparent 28%), radial-gradient(circle at top right, rgba(52, 211, 153, 0.14), transparent 24%), linear-gradient(180deg, #08111f 0%, #0b1728 55%, #08111f 100%);">
      <div class="max-w-[1360px] mx-auto px-4 py-4 h-full flex flex-col">

        <!-- Topbar / Back Link -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <button @click="goBack" class="text-[#38bdf8] hover:text-[#7dd3fc] text-[15px] font-medium flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </button>
                <div class="px-2.5 py-1 rounded-full bg-[rgba(56,189,248,0.1)] border border-[rgba(56,189,248,0.24)] text-[#b7e8ff] text-[12.5px] tracking-wide flex items-center">
                    <span>{{ router.name }}</span>
                    <span class="w-1.5 h-1.5 ml-2 rounded-full bg-[#34d399] animate-pulse shadow-[0_0_6px_rgba(52,211,153,0.5)]"></span>
                </div>
            </div>
            <button class="px-3 py-1.5 rounded-xl border border-[rgba(52,211,153,0.28)] bg-gradient-to-b from-[rgba(52,211,153,0.22)] to-[rgba(52,211,153,0.12)] text-[#d9fff0] text-[13px] font-semibold hover:brightness-110 transition-all flex items-center shadow-lg">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Sync
            </button>
        </div>

        <!-- 4 KPI Cards (System Resources) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
          <!-- Identity -->
          <div class="px-4 py-3 rounded-xl border border-[rgba(148,163,184,0.16)] bg-[rgba(10,22,39,0.82)] backdrop-blur-md shadow-lg flex items-center gap-3">
              <div class="w-7 h-7 rounded-lg bg-[rgba(56,189,248,0.12)] text-[#a9e4ff] flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
              </div>
              <div class="flex-1">
                  <div class="text-[14px] font-bold text-white leading-tight truncate">{{ typeof identity === 'string' ? identity : (identity?.name || 'Unknown') }}</div>
                  <div class="text-[11px] text-[#c3d3e4]">Identity</div>
              </div>
          </div>
          <!-- CPU -->
          <div class="px-4 py-3 rounded-xl border border-[rgba(148,163,184,0.16)] bg-[rgba(10,22,39,0.82)] backdrop-blur-md shadow-lg flex items-center gap-3">
              <div class="w-7 h-7 rounded-lg bg-[rgba(56,189,248,0.12)] text-[#a9e4ff] flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
              </div>
              <div class="flex-1">
                  <div class="text-[14.5px] font-bold text-white leading-tight">{{ resources?.['cpu-load'] || 0 }}%</div>
                  <div class="text-[11px] text-[#c3d3e4]">CPU Load</div>
              </div>
          </div>
          <!-- Memory -->
          <div class="px-4 py-3 rounded-xl border border-[rgba(148,163,184,0.16)] bg-[rgba(10,22,39,0.82)] backdrop-blur-md shadow-lg flex items-center gap-3">
              <div class="w-7 h-7 rounded-lg bg-[rgba(56,189,248,0.12)] text-[#a9e4ff] flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
              </div>
              <div class="flex-1">
                  <div class="text-[14px] font-bold text-white leading-tight">{{ formatBytes(resources?.['free-memory']) }}</div>
                  <div class="text-[11px] text-[#c3d3e4]">of {{ formatBytes(resources?.['total-memory']) }} total</div>
              </div>
          </div>
          <!-- Uptime -->
          <div class="px-4 py-3 rounded-xl border border-[rgba(148,163,184,0.16)] bg-[rgba(10,22,39,0.82)] backdrop-blur-md shadow-lg flex items-center gap-3">
              <div class="w-7 h-7 rounded-lg bg-[rgba(56,189,248,0.12)] text-[#a9e4ff] flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <div class="flex-1">
                  <div class="text-[14px] font-bold text-white leading-tight truncate">{{ resources?.uptime || '00:00:00' }}</div>
                  <div class="text-[11px] text-[#c3d3e4]">Uptime</div>
              </div>
          </div>
        </div>

        <!-- NOC Table Title & Toolbar -->
        <div class="flex justify-between items-end mb-2 mt-4 flex-wrap gap-2">
            <h2 class="text-[15px] font-bold text-white tracking-wide">User PPPoE</h2>
            <div class="flex items-center gap-2">
                <input type="text" v-model="searchQuery" placeholder="Cari user, profile..." 
                       class="bg-[rgba(255,255,255,0.04)] border border-[rgba(255,255,255,0.08)] text-white rounded-lg px-2.5 py-1 text-[13px] min-w-[160px] focus:outline-none focus:border-[rgba(56,189,248,0.45)] placeholder-[#90a4ba]" />
                <div class="flex gap-1 p-0.5 bg-[rgba(255,255,255,0.06)] rounded-lg">
                    <button @click="activeTab = 'active'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', activeTab === 'active' ? 'bg-[rgba(56,189,248,0.18)] text-[#b7e8ff]' : 'text-[#8ca1b8] hover:text-white']">Online</button>
                    <button @click="activeTab = 'secrets'" :class="['px-3 py-1 rounded-md text-[12px] font-semibold transition-colors', activeTab === 'secrets' ? 'bg-[rgba(56,189,248,0.18)] text-[#b7e8ff]' : 'text-[#8ca1b8] hover:text-white']">All Secrets</button>
                </div>
            </div>
        </div>

        <!-- Data Tables Area -->
        <div class="rounded-xl border border-[rgba(148,163,184,0.16)] bg-[rgba(10,22,39,0.82)] backdrop-blur-md shadow-lg overflow-x-auto flex-1 min-h-[400px]">

          <!-- Tab 1: Active Connections -->
          <table v-if="activeTab === 'active'" class="w-full text-left border-collapse whitespace-nowrap text-[13.5px] text-[#e5eef8]">
            <thead class="sticky top-0 z-10 text-[11.5px] font-semibold uppercase tracking-wider text-[#9eb3c9] bg-[rgba(10,22,39,0.95)] backdrop-blur-sm shadow-[0_1px_0_0_rgba(255,255,255,0.06)]">
              <tr>
                <th class="px-4 py-2.5">User</th>
                <th class="px-4 py-2.5">Service</th>
                <th class="px-4 py-2.5">Caller ID (MAC)</th>
                <th class="px-4 py-2.5">Address</th>
                <th class="px-4 py-2.5">Uptime</th>
                <th class="px-4 py-2.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.06)]">
              <tr v-for="active in filteredActive" :key="active['.id'] || active.name" class="hover:bg-[rgba(255,255,255,0.03)] transition-colors duration-150">
                <td class="px-4 py-3 font-bold text-white">
                  <span class="inline-block w-2 h-2 rounded-full mr-2 bg-[#34d399] shadow-[0_0_6px_rgba(52,211,153,0.5)]"></span>
                  {{ active.name }}
                </td>
                <td class="px-4 py-3 text-[12.5px] text-[#c3d3e4]">{{ active.service || 'pppoe' }}</td>
                <td class="px-4 py-3 font-mono text-[12.5px] text-[#8ca1b8]">{{ active['caller-id'] || '-' }}</td>
                <td class="px-4 py-3 font-mono text-[12.5px] text-[#38bdf8]">{{ active.address || '-' }}</td>
                <td class="px-4 py-3 text-[12.5px] text-[#c3d3e4]">{{ active.uptime }}</td>
                <td class="px-4 py-3 text-right">
                  <button class="px-2.5 py-1 rounded-md border border-[rgba(239,68,68,0.3)] bg-[rgba(239,68,68,0.15)] text-[#fecaca] text-[11px] font-semibold hover:brightness-110 transition-all">
                    Kick
                  </button>
                </td>
              </tr>
              <tr v-if="!filteredActive.length">
                <td colspan="6" class="px-4 py-12 text-center text-[#8ca1b8] italic">Tidak ada koneksi aktif yang cocok.</td>
              </tr>
            </tbody>
          </table>

          <!-- Tab 2: PPPoE Secrets -->
          <table v-if="activeTab === 'secrets'" class="w-full text-left border-collapse whitespace-nowrap text-[13.5px] text-[#e5eef8]">
            <thead class="sticky top-0 z-10 text-[11.5px] font-semibold uppercase tracking-wider text-[#9eb3c9] bg-[rgba(10,22,39,0.95)] backdrop-blur-sm shadow-[0_1px_0_0_rgba(255,255,255,0.06)]">
              <tr>
                <th class="px-4 py-2.5">User</th>
                <th class="px-4 py-2.5">Profile</th>
                <th class="px-4 py-2.5">Local / Remote</th>
                <th class="px-4 py-2.5">Status</th>
                <th class="px-4 py-2.5">Last Logged Out</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(255,255,255,0.06)]">
              <tr v-for="secret in filteredSecrets" :key="secret['.id'] || secret.name" class="hover:bg-[rgba(255,255,255,0.03)] transition-colors duration-150">
                <td class="px-4 py-3 font-bold text-white">
                  <span v-if="isActive(secret.name)" class="inline-block w-2 h-2 rounded-full mr-2 bg-[#34d399] shadow-[0_0_6px_rgba(52,211,153,0.5)]"></span>
                  <span v-else class="inline-block w-2 h-2 rounded-full mr-2 bg-[#64748b]"></span>
                  {{ secret.name }}
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.03)] text-[11.5px] font-semibold text-[#c3d3e4]">{{ secret.profile }}</span>
                </td>
                <td class="px-4 py-3 font-mono text-[12px] text-[#8ca1b8]">
                    {{ secret['local-address'] || 'Default' }} <span class="text-[#64748b] mx-1">/</span> <span class="text-[#38bdf8]">{{ secret['remote-address'] || 'Default' }}</span>
                </td>
                <td class="px-4 py-3">
                  <span v-if="secret.disabled === 'true' || secret.disabled === true"
                        class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-[rgba(148,163,184,0.12)] text-[#cbd5e1] border border-transparent">
                    Disabled
                  </span>
                  <span v-else-if="isActive(secret.name)"
                        class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-full border border-[rgba(52,211,153,0.18)] bg-[rgba(52,211,153,0.12)] text-[#b9f3dd]">
                    Online
                  </span>
                  <span v-else
                        class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-[rgba(148,163,184,0.12)] text-[#cbd5e1]">
                    Offline
                  </span>
                </td>
                <td class="px-4 py-3 text-[12px] text-[#8ca1b8]">{{ secret['last-logged-out'] || 'Never' }}</td>
              </tr>
              <tr v-if="!filteredSecrets.length">
                <td colspan="5" class="px-4 py-12 text-center text-[#8ca1b8] italic">Tidak ada secrets PPPoE yang cocok.</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Hidden default scrollbars for cleaner NOC look */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: rgba(10, 22, 39, 0.5); 
}
::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15); 
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25); 
}
</style>