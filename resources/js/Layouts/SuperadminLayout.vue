<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-[var(--bg-body)] transition-colors duration-200" :data-theme="theme">
            <!-- Header -->
            <Header :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar" />

            <!-- Sidebar -->
            <SuperadminSidebar :isOpen="isSidebarOpen" />

            <!-- Main Content Area -->
            <div class="main-content flex flex-col h-screen pt-16 transition-all duration-300">
                <main class="flex-1 overflow-y-auto m-2 flex flex-col relative">
                    <div class="flex-1 flex flex-col min-h-0">
                        <PageTransition>
                            <slot />
                        </PageTransition>
                    </div>
                </main>
            </div>
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
import { ref, onMounted } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import SuperadminSidebar from '../Components/SuperadminSidebar.vue';
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

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

onMounted(() => {
    theme.value = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme.value);
});
</script>
