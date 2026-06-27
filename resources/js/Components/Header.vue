<template>
    <header class="header">
        <div class="header-left">
            <div class="header-brand">
                <img v-if="$page.props.companyProfile?.company_logo" :src="$page.props.companyProfile.company_logo" alt="Logo" class="h-9 w-9 object-cover rounded-md" />
                <div v-else class="flex items-center justify-center">
                    <img src="/logo-netora.png?v=3" alt="Logo" class="h-9 w-9 object-contain drop-shadow-sm" />
                </div>
                <div>
                    <div class="brand-text">NETORA MANAGE SYSTEM</div>
                    <div class="brand-version max-w-[200px] truncate" :title="$page.props.companyProfile?.company_nama">{{ $page.props.companyProfile?.company_nama ?? 'Nama Perusahaan' }}</div>
                </div>
            </div>
            <!-- Mobile Menu Toggle -->
            <button class="md:hidden p-2 text-slate-500 hover:text-slate-700" @click="$emit('toggle-sidebar')">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="header-right flex items-center gap-2 md:gap-3">
            <!-- User Name -->
            <div class="hidden md:block text-[14px] font-semibold text-[var(--text-primary)] mr-2">
                {{ user?.name }}
            </div>

            <!-- Pilih Modul -->
            <a href="/app-gateway" class="btn-header-icon" title="Pilih Modul">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            </a>

            <!-- Theme Toggle -->
            <button class="btn-header-icon" @click="toggleTheme" title="Toggle dark mode">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
            
            <!-- Logout -->
            <Link :href="route('logout')" method="post" as="button" class="btn-header-icon danger" title="Logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </Link>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    }
});

const page = usePage();
const emit = defineEmits(['toggle-sidebar', 'toggle-theme']);
const user = computed(() => page.props.auth?.user);
const role = computed(() => page.props.auth?.role);
const userInitial = computed(() => user.value?.name ? user.value.name.substring(0, 1).toUpperCase() : 'U');

function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    if (next === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
    localStorage.setItem('theme', next);
    emit('toggle-theme', next);
}
</script>
