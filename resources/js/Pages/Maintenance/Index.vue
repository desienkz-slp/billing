<template>
    <Head :title="pageTitle" />
    <div class="relative min-h-screen w-full overflow-hidden bg-slate-900 flex items-center justify-center">
        <!-- Full Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/images/maintenance-bitmap.png" alt="Maintenance Background" class="w-full h-full object-cover image-rendering-pixelated opacity-80" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        </div>

        <!-- Content Overlay -->
        <div class="relative z-10 p-8 text-center w-full max-w-3xl mt-64 sm:mt-80">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-sm sm:text-base font-bold tracking-widest mb-6 uppercase shadow-[0_0_15px_rgba(245,158,11,0.5)] backdrop-blur-md">
                <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Under Construction
            </div>
            
            <h1 class="text-5xl sm:text-7xl font-black text-white tracking-tight drop-shadow-[0_0_20px_rgba(99,102,241,0.8)] mb-4">
                {{ moduleName }} Modul
            </h1>
            
            <p class="text-slate-200 text-xl sm:text-2xl font-medium max-w-2xl mx-auto drop-shadow-lg mb-10 leading-relaxed">
                Sistem sedang dalam tahap pengembangan dan konfigurasi perangkat keras.
            </p>
            
            <div>
                <Link :href="route('app-gateway')" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-indigo-600/80 hover:bg-indigo-500 text-white font-bold text-lg shadow-[0_0_30px_rgba(79,70,229,0.5)] hover:shadow-[0_0_40px_rgba(79,70,229,0.8)] transition-all active:scale-95 backdrop-blur-md border border-indigo-400/30 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Gateway
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    module: {
        type: String,
        default: 'Sistem'
    }
});

const moduleName = computed(() => props.module);
const pageTitle = computed(() => `${props.module} Maintenance`);
</script>

<style scoped>
.image-rendering-pixelated {
    image-rendering: pixelated;
    image-rendering: crisp-edges;
}
</style>
