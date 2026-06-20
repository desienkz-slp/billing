<template>
    <aside class="sidebar" :class="{ 'open': isOpen }" id="sidebar">

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Menu Utama</div>
                
                <Link :href="route('superadmin.tenants')" class="nav-item" :class="{ 'active': route().current('superadmin.tenants*') }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span class="nav-text">Kelola Tenant</span>
                </Link>

                <Link :href="route('superadmin.packages')" class="nav-item" :class="{ 'active': route().current('superadmin.packages*') }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    <span class="nav-text">Master Paket SaaS</span>
                </Link>
            </div>
        </nav>

    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => page.props.auth.role);
const isAdmin = computed(() => page.props.auth.isAdmin);
const userInitial = computed(() => user.value?.name ? user.value.name.substring(0, 1).toUpperCase() : 'U');
</script>
