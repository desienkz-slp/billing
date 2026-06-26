<template>
    <AppLayout :title="pageTitle" :hideSidebar="true">
        <!-- Main Content -->
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-10 relative z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                            RADIUS Management
                        </h1>
                        <p class="mt-2 text-sm text-slate-400 font-medium">
                            Kelola profil dan akun pelanggan secara terpusat pada server RADIUS.
                        </p>
                    </div>
                    
                    <div>
                        <Link :href="route('config.radius-server')" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center whitespace-nowrap hover:scale-105 active:scale-95 duration-300">
                            <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Konfigurasi Server
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Servers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div v-if="servers.length === 0" class="col-span-full py-20 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4 shadow-[0_0_20px_rgba(0,0,0,0.5)]">
                        <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-300">Belum ada Server RADIUS</h3>
                    <p class="text-slate-500 mt-2">Tambahkan server RADIUS (Upluk Upluk API) pada menu Config terlebih dahulu.</p>
                </div>

                <!-- Server Card -->
                <div v-for="server in servers" :key="server.id" 
                     class="group relative bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 hover:border-indigo-500/50 shadow-xl rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1">
                    
                        <!-- Card Header -->
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-50/50 to-blue-50/50 dark:from-slate-800 dark:to-slate-800">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/50 flex items-center justify-center shadow-inner">
                                        <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                                            <line x1="6" y1="18" x2="6.01" y2="18"/>
                                            <line x1="10" y1="18" x2="10.01" y2="18"/>
                                            <line x1="12" y1="14" x2="12" y2="10"/>
                                            <path d="M8 8a4 4 0 0 1 8 0"/>
                                            <path d="M5 5a8 8 0 0 1 14 0"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-800"></div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">{{ server.name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1 text-xs font-mono text-slate-500 dark:text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        {{ server.api_endpoint }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                        <!-- Action Buttons -->
                        <div class="mt-auto">
                            <Link :href="route('radius.server', server.id)" class="w-full flex items-center justify-center gap-2 py-3 border border-indigo-200 dark:border-indigo-500/30 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all duration-300 text-sm font-semibold shadow-sm group/btn">
                                <svg class="w-4 h-4 transition-transform group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Buka NOC Dashboard
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    servers: {
        type: Array,
        default: () => []
    }
});

const pageTitle = computed(() => 'RADIUS Management');
</script>