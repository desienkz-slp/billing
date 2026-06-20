<template>
    <aside class="sidebar" :class="{ 'open': isOpen }" id="sidebar">

        <nav class="sidebar-nav">
            
            <!-- Normal Main Sidebar -->
            <template v-if="!route().current('config.*')">
                <div class="nav-section">
                    <div class="nav-section-title">Utama</div>
                    <Link :href="route('cust.dashboard')" class="nav-item" :class="{ 'active': route().current('cust.dashboard') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        <span class="nav-text">Dashboard</span></Link>

                    <Link v-if="isAdmin || role?.can_view_all_customers" :href="route('cust.customers.index')" class="nav-item" :class="{ 'active': route().current('cust.customers.*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span class="nav-text">Pelanggan</span></Link>

                    <Link v-if="isAdmin || role?.can_manage_cuti" :href="route('cust.cuti.index')" class="nav-item" :class="{ 'active': route().current('cust.cuti.*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        <span class="nav-text">Pelanggan Cuti</span></Link>

                    <Link v-if="isAdmin || role?.can_manage_isolir" :href="route('cust.isolir.index')" class="nav-item" :class="{ 'active': route().current('cust.isolir.*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        <span class="nav-text">Isolir</span></Link>
                </div>

                <div class="nav-section" v-if="isAdmin || role?.can_view_reports || role?.can_view_finance">
                    <div class="nav-section-title">Laporan</div>
                    
                    <Link v-if="isAdmin || role?.can_view_reports" :href="route('reports.income.index')" class="nav-item" :class="{ 'active': route().current('reports.income.*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span class="nav-text">Income</span></Link>

                    <Link v-if="isAdmin || role?.can_view_reports" :href="route('reports.other-incomes.index')" class="nav-item" :class="{ 'active': route().current('reports.other-incomes.*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2-.9 2-2s-.9-2-2-2H8"/><circle cx="15" cy="5" r="3"/></svg>
                        <span class="nav-text">Other-IN</span></Link>

                    <template v-if="isAdmin || role?.can_view_finance">
                        <Link :href="route('reports.expenses.index')" class="nav-item" :class="{ 'active': route().current('reports.expenses.*') }">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span class="nav-text">Expenses</span></Link>
                        <Link :href="route('reports.tax')" class="nav-item" :class="{ 'active': route().current('reports.tax') }">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M14 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg>
                            <span class="nav-text">Tax</span></Link>
                        <Link :href="route('reports.total')" class="nav-item" :class="{ 'active': route().current('reports.total') }">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                            <span class="nav-text">Recap</span></Link>
                        <Link :href="route('reports.cashflow')" class="nav-item" :class="{ 'active': route().current('reports.cashflow') }">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                            <span class="nav-text">Cashflow</span></Link>
                        <Link :href="route('reports.statistics')" class="nav-item" :class="{ 'active': route().current('reports.statistics*') }">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                            <span class="nav-text">Statistics</span></Link>
                    </template>
                </div>

                <div class="nav-section" v-if="isAdmin || role?.can_view_finance || role?.can_manage_deposits">
                    <div class="nav-section-title">Laporan Sales</div>
                    
                    <Link v-if="isAdmin || role?.can_view_finance" :href="route('sales.fee')" class="nav-item" :class="{ 'active': route().current('sales.fee*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v12M15 9.5a3 3 0 0 0-6 0c0 2 6 3 6 6a3 3 0 0 1-6 0"/></svg>
                        <span class="nav-text">Fee</span></Link>
                    <Link v-if="isAdmin || role?.can_manage_deposits" :href="route('sales.setoran')" class="nav-item" :class="{ 'active': route().current('sales.setoran') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg>
                        <span class="nav-text">Setoran</span></Link>
                    <Link v-if="isAdmin || role?.can_view_finance" :href="route('sales.saldo')" class="nav-item" :class="{ 'active': route().current('sales.saldo') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        <span class="nav-text">Saldo</span></Link>
                    <Link v-if="isAdmin || role?.can_view_finance" :href="route('sales.mitra')" class="nav-item" :class="{ 'active': route().current('sales.mitra') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span class="nav-text">Mitra</span></Link>
                </div>

                <div class="nav-section" v-if="isAdmin || role?.can_manage_packages || role?.can_manage_areas">
                    <div class="nav-section-title">Master</div>
                    
                    <Link v-if="isAdmin || role?.can_manage_packages" :href="route('settings.packages')" class="nav-item" :class="{ 'active': route().current('settings.packages*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg>
                        <span class="nav-text">Master Paket</span></Link>
                    <Link v-if="isAdmin || role?.can_manage_areas" :href="route('settings.areas')" class="nav-item" :class="{ 'active': route().current('settings.areas*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="nav-text">Master Area</span></Link>
                </div>
            </template>

            <!-- CONFIG Section (Only shows if route is config.*) -->
            <template v-else>
                <div class="nav-section">
                    <div class="nav-section-title">Config</div>
                    
                    <Link :href="route('config.profil')" class="nav-item" :class="{ 'active': route().current('config.profil*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span class="nav-text">Profil Perusahaan</span></Link>

                    <Link :href="route('config.template')" class="nav-item" :class="{ 'active': route().current('config.template*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <span class="nav-text">Template</span></Link>

                    <Link :href="route('config.router')" class="nav-item" :class="{ 'active': route().current('config.router*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="18" x2="6.01" y2="18"/><line x1="10" y1="18" x2="10.01" y2="18"/><line x1="12" y1="14" x2="12" y2="10"/><path d="M8 8a4 4 0 0 1 8 0"/><path d="M5 5a8 8 0 0 1 14 0"/></svg>
                        <span class="nav-text">Router</span></Link>

                    <Link :href="route('config.server')" class="nav-item" :class="{ 'active': route().current('config.server*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                        <span class="nav-text">Genieacs</span></Link>

                    <Link v-if="isAdmin || role?.can_manage_radius" :href="route('config.radius-server')" class="nav-item" :class="{ 'active': route().current('config.radius-server*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                        <span class="nav-text">Radius Local</span></Link>

                    <Link :href="route('config.db-pusat')" class="nav-item" :class="{ 'active': route().current('config.db-pusat*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12v7c0 1.66 4 3 9 3s9-1.34 9-3v-7"/></svg>
                        <span class="nav-text">Radius Pusat</span></Link>

                    <Link :href="route('config.wa-gateway')" class="nav-item" :class="{ 'active': route().current('config.wa-gateway*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span class="nav-text">WA Gateway</span></Link>

                    <Link href="#" class="nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                        <span class="nav-text">API Sheets</span></Link>

                    <Link href="#" class="nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="nav-text">API Map</span></Link>

                    <Link href="#" class="nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span class="nav-text">API Client</span></Link>

                    <Link :href="route('config.users')" class="nav-item" :class="{ 'active': route().current('config.users*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span class="nav-text">User</span></Link>
                    
                    <Link :href="route('config.roles')" class="nav-item" :class="{ 'active': route().current('config.roles*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        <span class="nav-text">Role</span></Link>

                    <Link :href="route('config.log-sistem')" class="nav-item" :class="{ 'active': route().current('config.log-sistem*') }">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <span class="nav-text">Log</span></Link>
                </div>
            </template>
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
