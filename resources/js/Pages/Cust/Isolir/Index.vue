<template>
    <AppLayout title="Manajemen Isolir">
        <div class="p-2 w-full max-w-full w-full mx-auto flex flex-col h-full">
            <!-- Header Section -->
            <div class="mb-4 flex flex-col gap-4 px-4 sm:px-0">
                <!-- Top Row: Title & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-red-600 dark:text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Manajemen Isolir</h1>
                    </div>
                    
                    <div class="flex items-center gap-3 mt-4 sm:mt-0 w-full sm:w-auto">
                        <div v-if="activeTab === 'isolated'" class="flex gap-2">
                            <button v-if="selectedIsolated.length > 0" @click="confirmBatchRelease" class="px-4 py-2 bg-emerald-100 dark:bg-emerald-900/50 hover:bg-emerald-200 dark:hover:bg-emerald-800/70 text-emerald-700 dark:text-emerald-400 text-sm font-medium rounded-xl transition-all flex items-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                Unisolir ({{ selectedIsolated.length }})
                            </button>
                        </div>
                        <div v-if="activeTab === 'candidates'" class="flex gap-2">
                            <button v-if="selectedCandidates.length > 0" @click="confirmBatchIsolate" class="px-4 py-2 bg-red-100 dark:bg-red-900/50 hover:bg-red-200 dark:hover:bg-red-800/70 text-red-700 dark:text-red-400 text-sm font-medium rounded-xl transition-all flex items-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Isolir ({{ selectedCandidates.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs & Filters -->
                <div class="flex flex-col sm:flex-row gap-3 items-center justify-between mt-2">
                    <!-- Tabs -->
                    <div class="flex bg-slate-100 dark:bg-slate-800/50 p-1 rounded-xl border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
                        <button 
                            @click="switchTab('isolated')"
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="activeTab === 'isolated' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                        >
                            Terisolir <span class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'isolated' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400'">{{ stats.total_isolated }}</span>
                        </button>
                        <button 
                            @click="switchTab('candidates')"
                            class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="activeTab === 'candidates' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                        >
                            Layak Isolir <span class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'candidates' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400'">{{ stats.total_active }}</span>
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full sm:w-auto overflow-x-auto whitespace-nowrap scrollbar-hide">
                        <select v-model="formFilters.per_page" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                            <option value="15">15</option>
                            <option value="50">50</option>
                            <option value="all">Semua</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <select v-model="formFilters.area_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                            <option value="">Semua Area</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                        </select>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                        <div class="relative min-w-[120px] w-full">
                            <input 
                                v-model="formFilters.search" 
                                type="text" 
                                placeholder="Cari pelanggan..." 
                                class="w-full bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 py-1 pl-8 pr-3"
                            />
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0 relative">
                    
                    <!-- TAB 1: TERISOLIR -->
                    <table v-if="activeTab === 'isolated'" class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-2 py-3 w-10 text-center whitespace-nowrap">
                                    <input type="checkbox" :checked="isAllIsolatedSelected" @change="toggleAllIsolated" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">NAMA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">WA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">SEJAK ISOLIR</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">PAKET</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AREA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="customer in isolated.data" :key="customer.id" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-2 py-3 text-center whitespace-nowrap">
                                    <input type="checkbox" :value="customer.id" v-model="selectedIsolated" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </td>
                                <td class="px-3 py-3 font-medium text-xs sm:text-sm text-slate-900 dark:text-white whitespace-nowrap text-center">
                                    {{ customer.name }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ customer.phone || '-' }}</td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">
                                        {{ customer.isolated_since ? new Date(customer.isolated_since).toLocaleDateString('id-ID') : '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.package?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.area?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap flex items-center justify-center">
                                    <button @click="confirmRelease(customer)" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400 dark:hover:bg-emerald-800/70 rounded-md transition-colors text-xs font-medium flex items-center" title="Unisolir">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                        Unisolir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="isolated.data.length === 0">
                                <td colspan="7" class="px-4 py-16 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Tidak ada pelanggan yang terisolir.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- TAB 2: LAYAK ISOLIR (CANDIDATES) -->
                    <table v-if="activeTab === 'candidates'" class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-2 py-3 w-10 text-center whitespace-nowrap">
                                    <input type="checkbox" :checked="isAllCandidatesSelected" @change="toggleAllCandidates" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">NAMA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">WA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">TGL TAGIH</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">PAKET</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AREA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="customer in candidates.data" :key="customer.id" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-2 py-3 text-center whitespace-nowrap">
                                    <input type="checkbox" :value="customer.id" v-model="selectedCandidates" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-900 dark:border-slate-600" />
                                </td>
                                <td class="px-3 py-3 font-medium text-xs sm:text-sm text-slate-900 dark:text-white whitespace-nowrap text-center">
                                    {{ customer.name }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ customer.phone || '-' }}</td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ customer.billing_date || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.package?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.area?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap flex items-center justify-center">
                                    <button @click="confirmIsolate(customer)" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/50 dark:text-red-400 dark:hover:bg-red-800/70 rounded-md transition-colors text-xs font-medium flex items-center" title="Isolir">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Isolir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="candidates.data.length === 0">
                                <td colspan="7" class="px-4 py-16 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Semua pelanggan berstatus aman. Tidak ada yang layak diisolir.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (Isolated) -->
                <div v-if="activeTab === 'isolated' && isolated.links && isolated.links.length > 3" class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-medium">{{ isolated.from || 0 }}</span> sampai <span class="font-medium">{{ isolated.to || 0 }}</span> dari <span class="font-medium">{{ isolated.total }}</span> data
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center">
                            <template v-for="(link, p) in isolated.links" :key="p">
                                <component :is="link.url ? Link : 'span'" :href="link.url || '#'" class="px-3 py-1.5 rounded-lg text-sm transition-colors" :class="{'bg-blue-600 text-white font-medium': link.active, 'text-slate-500 hover:bg-slate-200': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url}" v-html="link.label"></component>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Pagination (Candidates) -->
                <div v-if="activeTab === 'candidates' && candidates.links && candidates.links.length > 3" class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-medium">{{ candidates.from || 0 }}</span> sampai <span class="font-medium">{{ candidates.to || 0 }}</span> dari <span class="font-medium">{{ candidates.total }}</span> data
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center">
                            <template v-for="(link, p) in candidates.links" :key="p">
                                <component :is="link.url ? Link : 'span'" :href="link.url || '#'" class="px-3 py-1.5 rounded-lg text-sm transition-colors" :class="{'bg-blue-600 text-white font-medium': link.active, 'text-slate-500 hover:bg-slate-200': !link.active && link.url, 'text-slate-300 cursor-not-allowed': !link.url}" v-html="link.label"></component>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Confirm Unisolir Modal -->
        <Modal :show="isReleaseModalOpen" @close="closeReleaseModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mb-4">Konfirmasi Unisolir</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin melepas status isolir <span v-if="actionTarget">pelanggan <strong>{{ actionTarget.name }}</strong></span><span v-else><strong>{{ selectedIsolated.length }} pelanggan</strong> yang dipilih</span> dan memulihkan koneksi internet mereka?
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeReleaseModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Batal</button>
                    <button @click="executeRelease" :disabled="isProcessing" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Ya, Unisolir
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Confirm Isolir Modal -->
        <Modal :show="isIsolateModalOpen" @close="closeIsolateModal" maxWidth="md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4">Konfirmasi Isolir</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
                    Apakah Anda yakin ingin mengisolir <span v-if="actionTarget">pelanggan <strong>{{ actionTarget.name }}</strong></span><span v-else><strong>{{ selectedCandidates.length }} pelanggan</strong> yang dipilih</span>? Koneksi internet mereka akan diblokir atau dialihkan ke profil isolir.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeIsolateModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Batal</button>
                    <button @click="executeIsolate" :disabled="isProcessing" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Ya, Isolir
                    </button>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    isolated: Object,
    candidates: Object,
    stats: Object,
    areas: Array,
    filters: Object,
    tab: String,
});

const activeTab = ref(props.tab || 'isolated');

const formFilters = ref({
    search: props.filters?.search || '',
    area_id: props.filters?.area_id || '',
    per_page: props.filters?.per_page || '15',
    tab: activeTab.value,
});

const switchTab = (tabName) => {
    activeTab.value = tabName;
    formFilters.value.tab = tabName;
    // reset selections when switching tabs
    selectedIsolated.value = [];
    selectedCandidates.value = [];
};

let searchTimeout;
watch(formFilters, (newValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('cust.isolir.index'), newValues, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

// Checkbox logic Isolated
const selectedIsolated = ref([]);
const isAllIsolatedSelected = computed(() => {
    return props.isolated.data && props.isolated.data.length > 0 && selectedIsolated.value.length === props.isolated.data.length;
});
const toggleAllIsolated = (e) => {
    if (e.target.checked) selectedIsolated.value = props.isolated.data.map(c => c.id);
    else selectedIsolated.value = [];
};

// Checkbox logic Candidates
const selectedCandidates = ref([]);
const isAllCandidatesSelected = computed(() => {
    return props.candidates.data && props.candidates.data.length > 0 && selectedCandidates.value.length === props.candidates.data.length;
});
const toggleAllCandidates = (e) => {
    if (e.target.checked) selectedCandidates.value = props.candidates.data.map(c => c.id);
    else selectedCandidates.value = [];
};

// Modals
const actionTarget = ref(null);
const isProcessing = ref(false);

const isReleaseModalOpen = ref(false);
const confirmRelease = (customer) => { actionTarget.value = customer; isReleaseModalOpen.value = true; };
const confirmBatchRelease = () => { actionTarget.value = null; isReleaseModalOpen.value = true; };
const closeReleaseModal = () => { isReleaseModalOpen.value = false; setTimeout(() => { actionTarget.value = null; isProcessing.value = false; }, 200); };

const executeRelease = () => {
    isProcessing.value = true;
    if (actionTarget.value) {
        router.post(route('cust.isolir.release', actionTarget.value.id), {}, {
            onSuccess: () => { closeReleaseModal(); selectedIsolated.value = selectedIsolated.value.filter(id => id !== actionTarget.value.id); },
            onError: () => isProcessing.value = false
        });
    } else {
        router.post(route('cust.isolir.batch-release'), { ids: selectedIsolated.value }, {
            onSuccess: () => { closeReleaseModal(); selectedIsolated.value = []; },
            onError: () => isProcessing.value = false
        });
    }
};

const isIsolateModalOpen = ref(false);
const confirmIsolate = (customer) => { actionTarget.value = customer; isIsolateModalOpen.value = true; };
const confirmBatchIsolate = () => { actionTarget.value = null; isIsolateModalOpen.value = true; };
const closeIsolateModal = () => { isIsolateModalOpen.value = false; setTimeout(() => { actionTarget.value = null; isProcessing.value = false; }, 200); };

const executeIsolate = () => {
    isProcessing.value = true;
    if (actionTarget.value) {
        router.post(route('cust.isolir.isolate', actionTarget.value.id), {}, {
            onSuccess: () => { closeIsolateModal(); selectedCandidates.value = selectedCandidates.value.filter(id => id !== actionTarget.value.id); },
            onError: () => isProcessing.value = false
        });
    } else {
        router.post(route('cust.isolir.batch-isolate'), { ids: selectedCandidates.value }, {
            onSuccess: () => { closeIsolateModal(); selectedCandidates.value = []; },
            onError: () => isProcessing.value = false
        });
    }
};
</script>
