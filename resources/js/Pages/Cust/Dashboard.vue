<template>
    <AppLayout :title="pageTitle">
        <div class="px-2 py-1 w-full max-w-full flex flex-col w-full mx-auto">
            <!-- Header -->
            <div class="mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0 mt-1">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Dashboard</h1>
                    </div>
                    
                    <input 
                        type="month" 
                        v-model="selectedMonth" 
                        class="rounded-xl border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow px-4 py-2" 
                    />
                </div>
                
                <!-- Wajib Setor Highlight -->
                <div class="bg-emerald-600 dark:bg-emerald-700 rounded-lg border border-emerald-700 dark:border-emerald-600 px-3 py-1.5 shadow-sm relative overflow-hidden group text-white">
                    <div class="absolute right-0 top-0 w-16 h-16 bg-white/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex items-center gap-2 relative z-10">
                        <div class="w-8 h-8 rounded-md bg-white/20 flex items-center justify-center text-white shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black tracking-tight leading-none">Rp {{ formatCurrency(stats.wajib_setor || 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                <!-- KPI Cards List -->
                <button @click="filterKpi('')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === '' ? 'border-slate-400 dark:border-slate-500 ring-1 ring-slate-400 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-slate-500/10 dark:bg-slate-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Pelanggan</p>
                            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ stats.total_customers }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                </button>

                <button @click="filterKpi('baru')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === 'baru' ? 'border-blue-500 dark:border-blue-500 ring-1 ring-blue-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-blue-500/10 dark:bg-blue-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Baru (Bulan Ini)</p>
                            <h3 class="text-2xl font-extrabold text-blue-500">{{ stats.baru }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                    </div>
                </button>

                <button @click="filterKpi('lunas')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === 'lunas' ? 'border-emerald-500 dark:border-emerald-500 ring-1 ring-emerald-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Lunas</p>
                            <h3 class="text-2xl font-extrabold text-emerald-500">{{ stats.lunas }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </button>

                <!-- Row 2 -->
                <button @click="filterKpi('jatuh_tempo')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === 'jatuh_tempo' ? 'border-rose-500 dark:border-rose-500 ring-1 ring-rose-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-rose-500/10 dark:bg-rose-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                            <h3 class="text-2xl font-extrabold text-rose-500">{{ stats.jatuh_tempo }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-500 dark:text-rose-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </button>

                <button @click="filterKpi('telat')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === 'telat' ? 'border-purple-500 dark:border-purple-500 ring-1 ring-purple-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-purple-500/10 dark:bg-purple-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Telat</p>
                            <h3 class="text-2xl font-extrabold text-purple-500">{{ stats.telat }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-500 dark:text-purple-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>
                </button>

                <button @click="filterKpi('deadline')" class="bg-white dark:bg-slate-800 rounded-xl border p-5 relative overflow-hidden group text-left cursor-pointer transition-all" :class="activeKpi === 'deadline' ? 'border-amber-500 dark:border-amber-500 ring-1 ring-amber-500 shadow-md' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-sm'">
                    <div class="absolute right-0 top-0 w-20 h-20 bg-amber-500/10 dark:bg-amber-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Deadline</p>
                            <h3 class="text-2xl font-extrabold text-amber-500">{{ stats.deadline }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Toolbar (Search & Filter) -->
            <div class="mb-4">
                <div class="flex items-center flex-wrap bg-white dark:bg-slate-800 rounded-xl border border-slate-300 dark:border-slate-700 p-1 shadow-sm w-full gap-y-1 relative z-20">
                    <!-- Per Page -->
                    <select v-model="formFilters.per_page" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">Semua</option>
                    </select>
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                    <!-- Area -->
                    <select v-model="formFilters.area_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                        <option value="">Semua Area</option>
                        <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                    </select>
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1 hidden md:block"></div>
                    <!-- Koordinator -->
                    <select v-model="formFilters.sales_id" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max hidden md:block">
                        <option value="">Semua Mitra</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                    <!-- Status -->
                    <select v-model="formFilters.status" class="bg-transparent border-none text-sm text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-3 pr-7 min-w-max">
                        <option value="">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="isolated">Isolated</option>
                    </select>
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 shrink-0 mx-1"></div>
                    <!-- Billing Calendar Range Picker -->
                    <div class="px-2 border-r border-slate-200 dark:border-slate-700">
                        <BillingCalendarFilter 
                            :modelValue="{ start: formFilters.billing_start, end: formFilters.billing_end }"
                            @update:modelValue="val => { formFilters.billing_start = val.start; formFilters.billing_end = val.end }"
                            :projections="billing_projections"
                        />
                    </div>
                    <!-- Search -->
                    <div class="relative min-w-[120px] flex-1">
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

            <!-- Table -->
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0">
                    <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                        <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                            <tr>
                                <th scope="col" class="px-2 py-3 w-10 text-center whitespace-nowrap">#</th>
                                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">NAMA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">WA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">DAFTAR</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">PAKET</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">AREA</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">ODP</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">TAGIH</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">ISOLIR</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">JENIS</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">STATUS</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">SALES</th>
                                <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(customer, index) in customers.data" :key="customer.id" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-2 py-3 text-center">{{ (customers.current_page - 1) * customers.per_page + index + 1 }}</td>
                                <td class="px-3 py-3 font-medium text-xs sm:text-sm text-slate-900 dark:text-white whitespace-nowrap flex items-center gap-2">
                                    {{ customer.name }}
                                    <span v-if="isNewThisMonth(customer.registration_date)" class="bg-blue-100 text-blue-800 text-[10px] font-semibold px-2 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Baru</span>
                                </td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap">{{ customer.phone || '-' }}</td>
                                <td class="px-4 py-3 text-center text-xs sm:text-sm whitespace-nowrap hidden xl:table-cell">{{ formatDate(customer.registration_date) }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.package?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">{{ customer.area?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">{{ customer.odp?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">{{ customer.billing_date }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">{{ customer.tgl_isolir || '-' }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">
                                    <span class="bg-slate-100 text-slate-600 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 capitalize">{{ customer.jenis_bayar || 'Pra' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap hidden xl:table-cell">
                                    <span :class="getStatusClass(customer.payment_status)" class="text-xs font-medium px-2.5 py-1 rounded-full border whitespace-nowrap">
                                        {{ customer.payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ customer.sales?.name || '-' }}</td>
                                <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                                    <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_process_payment" @click="openPaymentModal(customer)" class="text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-full px-3 py-1 text-xs font-medium transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Bayar
                                    </button>
                                    <button v-if="can('billing.whatsapp.send')" @click="openWaModal(customer)" class="text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-full px-3 py-1 text-xs font-medium transition-colors flex items-center gap-1">
                                        WA
                                    </button>
                                    <template v-if="customer.is_isolated">
                                        <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_manage_isolir" @click="confirmUnisolir(customer)" class="text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-full px-3 py-1 text-xs font-medium transition-colors flex items-center gap-1">
                                            Unisolir
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_manage_isolir" @click="confirmIsolir(customer)" class="text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-full px-3 py-1 text-xs font-medium transition-colors flex items-center gap-1">
                                            Isolir
                                        </button>
                                    </template>
                                    <button v-if="$page.props.auth.isAdmin || $page.props.auth.role?.can_cuti" @click="confirmCuti(customer)" class="text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-full px-3 py-1 text-xs font-medium transition-colors flex items-center gap-1">
                                        Cuti
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="customers.data.length === 0">
                                <td colspan="12" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                    Tidak ada data pelanggan ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-center">
                    <nav class="flex gap-1" aria-label="Pagination">
                        <template v-for="(link, idx) in customers.links" :key="idx">
                            <button 
                                v-if="link.url"
                                @click="goToPage(link.url)"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 text-sm rounded-full transition-colors',
                                    link.active 
                                        ? 'bg-blue-600 text-white font-medium' 
                                        : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                                ]"
                            ></button>
                            <span 
                                v-else 
                                v-html="link.label" 
                                class="px-3 py-1 text-sm text-slate-400 dark:text-slate-600 cursor-not-allowed"
                            ></span>
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <Modal :show="isPaymentModalOpen" @close="closePaymentModal" maxWidth="2xl">
            <PaymentModal v-if="selectedCustomer" :customer="selectedCustomer" @close="closePaymentModal" @success="handlePaymentSuccess" />
        </Modal>

        <Modal :show="isWaModalOpen" @close="closeWaModal" maxWidth="lg">
            <WaModal v-if="selectedCustomer" :customer="selectedCustomer" @close="closeWaModal" />
        </Modal>

        <!-- General Confirm Modal -->
        <Modal :show="isConfirmModalOpen" @close="closeConfirmModal" maxWidth="sm">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">{{ confirmTitle }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">{{ confirmMessage }}</p>
                <div class="flex justify-end gap-3">
                    <button @click="closeConfirmModal" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">Batal</button>
                    <button @click="executeConfirm" :disabled="isConfirming" :class="confirmBtnClass" class="px-6 py-2 text-white rounded-xl text-sm font-medium transition-colors disabled:opacity-50">
                        {{ confirmBtnText }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import PaymentModal from '@/Pages/Cust/Customers/Components/PaymentModal.vue';
import WaModal from '@/Pages/Cust/Customers/Components/WaModal.vue';
import BillingCalendarFilter from '@/Components/BillingCalendarFilter.vue';

const props = defineProps({
    capabilities: Array,
    stats: Object,
    customers: Object,
    areas: Array,
    packages: Array,
    users: Array,
    filters: Object,
    billing_projections: Object,
    month: String,
});

const can = (capability) => props.capabilities.includes(capability);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
};

const pageTitle = computed(() => 'Dashboard');
const activeKpi = ref(new URLSearchParams(window.location.search).get('kpi_filter') || '');

const formFilters = ref({
    search: props.filters?.search || '',
    area_id: props.filters?.area_id || '',
    sales_id: props.filters?.sales_id || '',
    per_page: props.filters?.per_page || '10',
    status: props.filters?.status || '',
    billing_start: props.filters?.billing_start || '',
    billing_end: props.filters?.billing_end || '',
});

const getCurrentMonth = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
};

const selectedMonth = ref(props.month || getCurrentMonth());

let searchTimeout;
watch(formFilters, (newValues) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('cust.dashboard'), { ...newValues, kpi_filter: activeKpi.value, month: selectedMonth.value }, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

watch(selectedMonth, (newVal) => {
    router.get(
        route('cust.dashboard'),
        { ...formFilters.value, kpi_filter: activeKpi.value, month: newVal },
        { preserveState: true, preserveScroll: true }
    );
});

const filterKpi = (kpi) => {
    activeKpi.value = activeKpi.value === kpi ? '' : kpi;
    router.get(
        route('cust.dashboard'),
        { ...formFilters.value, kpi_filter: activeKpi.value, month: selectedMonth.value },
        { preserveState: true, preserveScroll: true }
    );
};

const goToPage = (url) => {
    router.get(url, {
        ...formFilters.value,
        kpi_filter: activeKpi.value,
        month: selectedMonth.value,
    }, { preserveState: true });
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const isNewThisMonth = (dateStr) => {
    if (!dateStr) return false;
    const date = new Date(dateStr);
    const now = new Date();
    return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
};

const getStatusClass = (status) => {
    switch (status) {
        case 'Lunas':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
        case 'Jatuh Tempo':
            return 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800';
        case 'Deadline':
            return 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800';
        case 'Telat':
            return 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800';
        default:
            return 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
    }
};

// Modal states
const isPaymentModalOpen = ref(false);
const isWaModalOpen = ref(false);
const selectedCustomer = ref(null);

const openPaymentModal = (customer) => {
    selectedCustomer.value = customer;
    isPaymentModalOpen.value = true;
};
const closePaymentModal = () => {
    isPaymentModalOpen.value = false;
    setTimeout(() => selectedCustomer.value = null, 300);
};
const handlePaymentSuccess = () => {
    closePaymentModal();
    router.reload({ only: ['customers', 'stats'] });
};

const openWaModal = (customer) => {
    selectedCustomer.value = customer;
    isWaModalOpen.value = true;
};
const closeWaModal = () => {
    isWaModalOpen.value = false;
    setTimeout(() => selectedCustomer.value = null, 300);
};

// Confirm Modal logic
const isConfirmModalOpen = ref(false);
const confirmTitle = ref('');
const confirmMessage = ref('');
const confirmBtnText = ref('');
const confirmBtnClass = ref('');
const isConfirming = ref(false);
let confirmAction = null;

const closeConfirmModal = () => {
    isConfirmModalOpen.value = false;
    confirmAction = null;
};

const confirmIsolir = (customer) => {
    confirmTitle.value = 'Konfirmasi Isolir';
    confirmMessage.value = `Apakah Anda yakin ingin mengisolir pelanggan ${customer.name}? Profil rahasia PPPoE di MikroTik akan diubah ke profil isolir dan perangkat akan di-reboot via ACS.`;
    confirmBtnText.value = 'Isolir';
    confirmBtnClass.value = 'bg-amber-600 hover:bg-amber-700';
    confirmAction = () => router.post(route('cust.isolir.isolate', customer.id), {}, {
        onSuccess: () => closeConfirmModal(),
        onFinish: () => isConfirming.value = false
    });
    isConfirmModalOpen.value = true;
};

const confirmUnisolir = (customer) => {
    confirmTitle.value = 'Konfirmasi Unisolir';
    confirmMessage.value = `Apakah Anda yakin ingin melepaskan isolir pelanggan ${customer.name}? Profil rahasia PPPoE di MikroTik akan dikembalikan ke profil normal.`;
    confirmBtnText.value = 'Unisolir';
    confirmBtnClass.value = 'bg-emerald-600 hover:bg-emerald-700';
    confirmAction = () => router.post(route('cust.isolir.release', customer.id), {}, {
        onSuccess: () => closeConfirmModal(),
        onFinish: () => isConfirming.value = false
    });
    isConfirmModalOpen.value = true;
};

const confirmCuti = (customer) => {
    confirmTitle.value = 'Konfirmasi Cuti';
    confirmMessage.value = `Apakah Anda yakin ingin menonaktifkan pelanggan ${customer.name} (Cuti)? Secret PPPoE pada router akan Dihapus secara permanen.`;
    confirmBtnText.value = 'Nonaktifkan (Cuti)';
    confirmBtnClass.value = 'bg-rose-600 hover:bg-rose-700';
    confirmAction = () => router.post(route('cust.cuti.store', customer.id), {}, {
        onSuccess: () => closeConfirmModal(),
        onFinish: () => isConfirming.value = false
    });
    isConfirmModalOpen.value = true;
};

const executeConfirm = () => {
    if (confirmAction) {
        isConfirming.value = true;
        confirmAction();
    }
};
</script>
