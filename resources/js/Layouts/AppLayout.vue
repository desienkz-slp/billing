<template>
    <div class="flex h-screen bg-[var(--bg-body)] transition-colors duration-200" :data-theme="theme">
        <Sidebar :isOpen="isSidebarOpen" />
        
        <div class="flex-1 flex flex-col main-content min-w-0 transition-all duration-300">
            <div v-if="page.props.flash?.license_warning" class="license-banner" :class="{ 'expired': page.props.flash?.license_expired }">
                ⚠️ {{ page.props.flash.license_warning }}
            </div>

            <Header :title="title" @toggle-sidebar="isSidebarOpen = !isSidebarOpen" @toggle-theme="theme = $event" />

            <main class="flex-1 overflow-y-auto m-2 flex flex-col relative">
                <transition name="fade">
                    <div v-if="showSuccess && page.props.flash?.success" class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 shrink-0 flex justify-between items-center shadow-sm">
                        <span>{{ page.props.flash.success }}</span>
                        <button @click="showSuccess = false" class="text-emerald-700/70 hover:text-emerald-900 dark:text-emerald-400/70 dark:hover:text-emerald-300 transition-colors ml-4 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </transition>
                <transition name="fade">
                    <div v-if="showError && page.props.flash?.error" class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 shrink-0 flex justify-between items-center shadow-sm">
                        <span>{{ page.props.flash.error }}</span>
                        <button @click="showError = false" class="text-red-700/70 hover:text-red-900 dark:text-red-400/70 dark:hover:text-red-300 transition-colors ml-4 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </transition>
                
                <div v-if="globalError" class="m-4 p-6 bg-red-100 text-red-800 rounded-xl border border-red-300 shrink-0">
                    <h2 class="text-xl font-bold mb-2">Rendering Error Captured!</h2>
                    <pre class="whitespace-pre-wrap text-sm">{{ globalError }}</pre>
                </div>
                <div v-else class="flex-1 flex flex-col min-h-0">
                    <slot />
                </div>
            </main>

        </div>

        <!-- Footer -->
        <footer class="fixed bottom-0 left-0 w-full py-2 px-4 text-center text-xs text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700/50 bg-[var(--bg-body)] z-[60]">
            &copy; 2026 upluk-upluk_dev version 2.0
        </footer>

        <!-- Mobile overlay -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-black/50 z-[55] md:hidden"></div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onErrorCaptured } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';
import Header from '../Components/Header.vue';

defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    }
});

const page = usePage();
const isSidebarOpen = ref(false);
const theme = ref('light');

const showSuccess = ref(false);
const showError = ref(false);
let successTimeout = null;
let errorTimeout = null;

watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success) {
        showSuccess.value = true;
        clearTimeout(successTimeout);
        successTimeout = setTimeout(() => showSuccess.value = false, 5000);
    }
    if (newFlash?.error) {
        showError.value = true;
        clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => showError.value = false, 5000);
    }
}, { deep: true, immediate: true });

const globalError = ref(null);
onErrorCaptured((err, instance, info) => {
    globalError.value = `${err.toString()} \n Info: ${info}`;
    console.error('AppLayout Captured Error:', err, info);
    return false; // prevent propagation
});

onMounted(() => {
    theme.value = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme.value);
    if (theme.value === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
</script>
