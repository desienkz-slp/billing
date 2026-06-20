<template>
    <AppLayout :title="pageTitle">
        <div class="p-2 sm:p-2 bg-slate-50/50 dark:bg-transparent print:bg-white min-h-[calc(100vh-200px)]">
            <!-- Header & Filter -->
            <div class="mb-4 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-slate-200 dark:border-slate-700 shadow-sm rounded-2xl overflow-hidden p-3 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        Statistik Bisnis
                    </h2>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Periode</span>
                    <input type="month" v-model="selectedBulan" class="rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow p-2 text-sm" />
                    
                    <button @click="loadData" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap group">
                        <svg class="w-4 h-4 mr-1 group-hover:animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Tampilkan
                    </button>

                    <button @click="toggleHideMonth" :class="isHiddenMonth ? 'from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600' : 'from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600'" class="px-4 py-2 bg-gradient-to-r text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                        <svg v-if="isHiddenMonth" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg v-else class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        {{ isHiddenMonth ? 'Tampilkan' : 'Sembunyikan' }}
                    </button>
                    
                    <div class="relative group">
                        <button class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            Tersembunyi
                            <span v-if="hiddenMonths.length > 0" class="ml-1.5 bg-white text-orange-600 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ hiddenMonths.length }}</span>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                            <div class="px-3 py-2 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400">Bulan Tersembunyi</div>
                            <div class="max-h-40 overflow-y-auto p-1">
                                <div v-if="hiddenMonths.length === 0" class="p-2 text-xs text-slate-400 text-center">Tidak ada</div>
                                <div v-else v-for="hm in hiddenMonths" :key="hm" class="flex justify-between items-center p-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-lg">
                                    <span>{{ formatMonthLabel(hm) }}</span>
                                    <button @click="unhideMonth(hm)" class="text-blue-500 hover:text-blue-600" title="Tampilkan"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap gap-2 mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="activeTab === tab.id ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400 border-b-2 border-blue-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b-2 border-transparent'" class="px-4 py-2 rounded-t-xl text-sm font-semibold transition-all flex items-center gap-2">
                    <svg v-if="tab.id === 'payment'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <svg v-if="tab.id === 'growth'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    <svg v-if="tab.id === 'churn'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" /></svg>
                    <svg v-if="tab.id === 'management'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    <span class="hidden sm:inline">{{ tab.label }}</span>
                    <span class="sm:hidden">{{ tab.short }}</span>
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            </div>

            <!-- Panel: Pembayaran -->
            <div v-show="!isLoading && activeTab === 'payment'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <KpiCard title="Hari Ini" :value="formatRpShort(paymentData?.pay_today)" :sub="currentDateText" icon="calendar-day" color="emerald" />
                    <KpiCard title="Bulan Ini" :value="formatRpShort(paymentData?.pay_month)" :sub="paymentGrowthText" :subColor="paymentData?.growth >= 0 ? 'emerald' : 'rose'" icon="calendar-month" color="blue" />
                    <KpiCard title="Pendapatan Tahunan" :value="formatRpShort(paymentData?.pay_yearly)" :sub="`Tahun ${selectedTahun}`" icon="calendar-range" color="purple" />
                    <KpiCard title="Belum Bayar" :value="formatNum(paymentData?.unpaid_count)" :sub="`${formatRpShort(paymentData?.unpaid_amount)} tunggakan`" icon="exclamation-triangle" color="amber" />
                    <KpiCard title="Jatuh Tempo" :value="formatNum(paymentData?.overdue_count)" sub="Invoice expired" icon="clock-history" color="rose" />
                    <KpiCard title="Success Rate" :value="formatPct(paymentData?.success_rate)" :sub="`${paymentData?.paid_cust || 0}/${paymentData?.active_cust || 0} pelanggan`" icon="check-circle" color="cyan" />
                    <KpiCard title="ARPU" :value="formatRpShort(paymentData?.arpu)" sub="Per pelanggan aktif" icon="user-check" color="pink" />
                    <KpiCard title="Transaksi" :value="formatNum(paymentData?.txn_month)" sub="Bulan ini" icon="receipt" color="teal" />
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <div class="lg:col-span-1 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>Metode Pembayaran</h3>
                        <div class="h-48 relative"><canvas ref="canvasPayMethods"></canvas></div>
                    </div>
                    <div class="lg:col-span-2 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4 flex flex-col">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2"><svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Insight Pembayaran</h3>
                        <ul class="space-y-2 mt-2 flex-1 overflow-y-auto text-sm">
                            <li v-for="(ins, i) in paymentInsights" :key="i" class="flex gap-2 text-slate-600 dark:text-slate-300">
                                <span class="text-blue-500 mt-0.5">•</span> <span>{{ ins }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>Pendapatan Harian (30 Hari)</h3>
                    <div class="h-60 relative"><canvas ref="canvasPayDaily"></canvas></div>
                </div>

                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>Pendapatan Bulanan (12 Bulan)</h3>
                    <div class="h-60 relative"><canvas ref="canvasPayMonthly"></canvas></div>
                </div>
            </div>

            <!-- Panel: Pertumbuhan -->
            <div v-show="!isLoading && activeTab === 'growth'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <KpiCard title="Pelanggan Aktif" :value="formatNum(growthData?.active)" sub="Total enabled" icon="users" color="blue" />
                    <KpiCard title="Pelanggan Baru" :value="formatNum(growthData?.new)" :sub="`Bulan lalu: ${formatNum(growthData?.new_last || 0)}`" icon="user-plus" color="emerald" />
                    <KpiCard title="Growth Rate" :value="`${growthData?.growth_rate >= 0 ? '+' : ''}${formatPct(growthData?.growth_rate)}`" sub="Bulan ini" :valueColor="growthData?.growth_rate >= 0 ? 'text-emerald-500' : 'text-rose-500'" icon="trending-up" color="purple" />
                    <KpiCard title="PPPoE Aktif" :value="formatNum(growthData?.pppoe)" sub="Terhubung" icon="globe" color="cyan" />
                    <KpiCard title="Cuti" :value="formatNum(growthData?.cuti)" sub="Pelanggan cuti" icon="pause" color="amber" />
                    <KpiCard title="Isolir" :value="formatNum(growthData?.suspend)" sub="Saat ini" icon="slash" color="rose" />
                </div>
                
                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>Pertumbuhan Pelanggan (12 Bulan)</h3>
                    <div class="h-60 relative"><canvas ref="canvasCustGrowth"></canvas></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>Pelanggan Per Wilayah</h3>
                        <div class="h-[300px] relative"><canvas ref="canvasCustArea"></canvas></div>
                    </div>
                    <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>Total Pelanggan Kumulatif</h3>
                        <div class="h-[300px] relative"><canvas ref="canvasCustCumulative"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Panel: Churn -->
            <div v-show="!isLoading && activeTab === 'churn'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <KpiCard title="Churn Rate" :value="formatPct(churnData?.churn_rate)" sub="Bulan ini" :valueColor="churnData?.churn_rate > 5 ? 'text-rose-500' : (churnData?.churn_rate > 2 ? 'text-amber-500' : 'text-emerald-500')" icon="percent" color="rose" />
                    <KpiCard title="Suspend >30 Hari" :value="formatNum(churnData?.suspend_long)" sub="Risiko churn tinggi" icon="hourglass" color="pink" />
                    <KpiCard title="Recovery" :value="formatNum(churnData?.recovery)" sub="Kembali aktif" icon="refresh" color="emerald" />
                    <KpiCard title="Retention" :value="formatPct(churnData?.retention)" sub="Tingkat retensi" :valueColor="churnData?.retention >= 95 ? 'text-emerald-500' : (churnData?.retention >= 90 ? 'text-amber-500' : 'text-rose-500')" icon="shield" color="teal" />
                    <KpiCard title="Avg Umur Pelanggan" :value="`${churnData?.avg_life} bln`" sub="Bulan berlangganan" icon="calendar-check" color="blue" />
                    <KpiCard title="Total Isolir" :value="formatNum(churnData?.isolir)" sub="Saat ini" icon="slash" color="cyan" />
                    <KpiCard title="Total Cuti" :value="formatNum(churnData?.cuti)" sub="Saat ini" icon="pause" color="purple" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <div class="lg:col-span-2 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>Churn Trend Bulanan</h3>
                        <div class="h-60 relative"><canvas ref="canvasChurnTrend"></canvas></div>
                    </div>
                    <div class="lg:col-span-1 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4 flex flex-col">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2"><svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Insight Churn</h3>
                        <ul class="space-y-2 mt-2 flex-1 overflow-y-auto text-sm">
                            <li v-for="(ins, i) in churnInsights" :key="i" class="flex gap-2 text-slate-600 dark:text-slate-300">
                                <span class="text-rose-500 mt-0.5">•</span> <span>{{ ins }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Panel: Manajemen -->
            <div v-show="!isLoading && activeTab === 'management'" class="space-y-4">
                
                <!-- Scorecards -->
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-2">
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">MRR (Potensi)</span>
                        <span class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-white mt-1">{{ formatRpShort(mgmtData?.mrr) }}</span>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Pendapatan Kotor</span>
                        <span class="text-sm sm:text-base font-extrabold text-blue-500 mt-1">{{ formatRpShort(mgmtData?.kotor) }}</span>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Pengeluaran</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-500 mt-1">{{ formatRpShort(mgmtData?.pengeluaran) }}</span>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Net Revenue</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-500 mt-1">{{ formatRpShort(mgmtData?.net_revenue) }}</span>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Setoran Deviden</span>
                        <span class="text-sm sm:text-base font-extrabold text-fuchsia-500 mt-1">{{ formatRpShort(mgmtData?.deviden) }}</span>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Kas Ditahan</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-500 mt-1">{{ formatRpShort(mgmtData?.kas_ditahan) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                    <KpiCard title="Collection Eff." :value="formatPct(mgmtData?.collection_eff)" sub="Terkumpul vs MRR" :valueColor="mgmtData?.collection_eff >= 90 ? 'text-emerald-500' : (mgmtData?.collection_eff >= 70 ? 'text-amber-500' : 'text-rose-500')" icon="target" color="emerald" />
                    <KpiCard title="Avg Days to Pay" :value="`${mgmtData?.avg_days_to_pay || 0} hari`" sub="Rata-rata hari bayar" icon="clock" color="blue" />
                    <KpiCard title="Outstanding" :value="formatRpShort(mgmtData?.total_outstanding)" sub="Total piutang" icon="exclamation" color="amber" />
                    <KpiCard title="Customer LTV" :value="formatRpShort(mgmtData?.ltv)" :sub="`${formatRpShort(mgmtData?.arpu)} × 12 bln`" icon="heart-pulse" color="purple" />
                </div>

                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>Trend Pendapatan vs Pengeluaran</h3>
                    <div class="h-60 relative"><canvas ref="canvasRevExpense"></canvas></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>Aging Analysis Piutang</h3>
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
                                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50 dark:bg-slate-900/50 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3 whitespace-nowrap">Umur Piutang</th>
                                            <th class="px-4 py-3 whitespace-nowrap text-center">Pelanggan</th>
                                            <th class="px-4 py-3 whitespace-nowrap text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, idx) in agingData" :key="idx" class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                            <td class="px-4 py-3 font-medium" :class="item.label.includes('> 90') ? 'text-rose-500' : (item.label.includes('61') || item.label.includes('31') ? 'text-amber-500' : 'text-emerald-500')">{{ item.label }}</td>
                                            <td class="px-4 py-3 text-center">{{ formatNum(item.count) }} plg</td>
                                            <td class="px-4 py-3 text-right font-semibold" :class="item.label.includes('> 90') ? 'text-rose-500' : (item.label.includes('61') || item.label.includes('31') ? 'text-amber-500' : 'text-emerald-500')">{{ formatRpShort(item.amount) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-sm rounded-2xl p-4 flex flex-col">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2"><svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Insight Manajemen & Aksi</h3>
                        <div class="space-y-4 flex-1 overflow-y-auto mt-2 text-sm pr-2">
                            <div>
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 border-b border-slate-100 dark:border-slate-700 pb-1 mb-2">Insight</h4>
                                <ul class="space-y-1.5">
                                    <li v-for="(ins, i) in mgmtInsights" :key="'ins'+i" class="flex gap-2 text-slate-600 dark:text-slate-300"><span class="text-blue-500 mt-0.5">•</span> <span>{{ ins }}</span></li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 border-b border-slate-100 dark:border-slate-700 pb-1 mb-2">Rekomendasi Aksi</h4>
                                <ul class="space-y-1.5">
                                    <li v-for="(act, i) in actionInsights" :key="'act'+i" class="flex gap-2 text-slate-600 dark:text-slate-300"><span class="text-amber-500 mt-0.5"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg></span> <span>{{ act }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import KpiCard from './Components/KpiCard.vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    bulan: String,
});

const pageTitle = computed(() => 'Statistik Bisnis');
const selectedBulan = ref(props.bulan || new Date().toISOString().slice(0, 7));
const selectedTahun = computed(() => selectedBulan.value.split('-')[0]);

const tabs = [
    { id: 'payment', label: 'Pembayaran', short: 'Bayar' },
    { id: 'growth', label: 'Pertumbuhan', short: 'Growth' },
    { id: 'churn', label: 'Churn Analysis', short: 'Churn' },
    { id: 'management', label: 'Manajemen', short: 'Mgmt' },
];

const activeTab = ref('payment');
const isLoading = ref(false);
const isHiddenMonth = ref(false);
const hiddenMonths = ref([]);

// Data refs
const paymentData = ref(null);
const growthData = ref(null);
const churnData = ref(null);
const mgmtData = ref(null);
const agingData = ref([]);

// Insights
const paymentInsights = ref([]);
const churnInsights = ref([]);
const mgmtInsights = ref([]);
const actionInsights = ref([]);

// Canvas refs
const canvasPayMethods = ref(null);
const canvasPayDaily = ref(null);
const canvasPayMonthly = ref(null);
const canvasCustGrowth = ref(null);
const canvasCustArea = ref(null);
const canvasCustCumulative = ref(null);
const canvasChurnTrend = ref(null);
const canvasRevExpense = ref(null);

// Chart instances
let charts = {};

const formatRpShort = (n) => {
    n = Number(n || 0);
    if (n >= 1e9) return 'Rp ' + (n / 1e9).toFixed(1) + 'M';
    if (n >= 1e6) return 'Rp ' + (n / 1e6).toFixed(1) + 'jt';
    if (n >= 1e3) return 'Rp ' + (n / 1e3).toFixed(0) + 'rb';
    return 'Rp ' + n.toLocaleString('id-ID');
};
const formatNum = (n) => Number(n || 0).toLocaleString('id-ID');
const formatPct = (n) => Number(n || 0).toFixed(1) + '%';
const formatMonthLabel = (ym) => {
    const m = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    const p = (ym || '').split('-');
    return m[parseInt(p[1] || 1) - 1] + ' ' + (p[0] || '');
};
const shortMonth = (ym) => {
    const m = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    const p = (ym || '').split('-');
    return m[parseInt(p[1] || 1) - 1];
};

const currentDateText = computed(() => {
    return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
});

const paymentGrowthText = computed(() => {
    if (!paymentData.value) return '';
    const g = paymentData.value.growth;
    return `${g >= 0 ? '+' : '-'}${formatPct(Math.abs(g))} vs bulan lalu`;
});

const destroyChart = (key) => {
    if (charts[key]) {
        charts[key].destroy();
        delete charts[key];
    }
};

const fetchApi = async (action) => {
    try {
        const res = await axios.get('/reports/statistics/api', { params: { action, bulan: selectedBulan.value } });
        return res.data.data;
    } catch (e) {
        console.error(e);
        return null;
    }
};

const checkHiddenStatus = () => {
    isHiddenMonth.value = hiddenMonths.value.includes(selectedBulan.value);
};

const loadHiddenMonths = async () => {
    hiddenMonths.value = await fetchApi('list_hidden_months') || [];
    checkHiddenStatus();
};

const toggleHideMonth = async () => {
    if (!confirm(isHiddenMonth.value ? `Tampilkan kembali bulan ${formatMonthLabel(selectedBulan.value)}?` : `Sembunyikan bulan ${formatMonthLabel(selectedBulan.value)}?\nData akan ditampilkan sebagai 0 di laporan tren.`)) return;
    try {
        await axios.post('/reports/statistics/toggle-hide', { periode: selectedBulan.value });
        await loadHiddenMonths();
        loadData();
    } catch (e) {
        alert('Gagal toggle hide');
    }
};

const unhideMonth = async (periode) => {
    if (!confirm(`Tampilkan kembali bulan ${formatMonthLabel(periode)}?`)) return;
    try {
        await axios.post('/reports/statistics/toggle-hide', { periode });
        await loadHiddenMonths();
        loadData();
    } catch (e) {
        alert('Gagal toggle hide');
    }
};

const loadPaymentTab = async () => {
    const summary = await fetchApi('payment_summary');
    if (summary) paymentData.value = summary;

    // Generate insights
    if (summary) {
        let ins = [];
        const g = summary.growth;
        if (g > 0) ins.push(`Pendapatan naik ${formatPct(g)} dibanding bulan lalu.`);
        else if (g < 0) ins.push(`Pendapatan turun ${formatPct(Math.abs(g))} — perlu evaluasi.`);
        if (summary.success_rate >= 90) ins.push(`Collection rate sangat baik (${formatPct(summary.success_rate)}).`);
        else if (summary.success_rate >= 70) ins.push(`Collection rate cukup (${formatPct(summary.success_rate)}), masih bisa ditingkatkan.`);
        else ins.push(`Collection rate rendah (${formatPct(summary.success_rate)}) — perlu follow-up intensif.`);
        if (summary.unpaid_count > 0) ins.push(`${formatNum(summary.unpaid_count)} pelanggan memiliki total tunggakan ${formatRpShort(summary.unpaid_amount)}.`);
        if (summary.overdue_count > 0) ins.push(`${summary.overdue_count} invoice sudah melewati jatuh tempo.`);
        ins.push(`ARPU saat ini ${formatRpShort(summary.arpu)} per pelanggan aktif.`);
        paymentInsights.value = ins;
    }

    await nextTick();
    
    // Charts
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', sans-serif";

    const methods = await fetchApi('payment_methods');
    if (methods && canvasPayMethods.value) {
        destroyChart('payMethods');
        charts.payMethods = new Chart(canvasPayMethods.value, {
            type: 'pie',
            data: {
                labels: methods.map(r => r.metode),
                datasets: [{
                    data: methods.map(r => r.total),
                    backgroundColor: ['#818cf8','#34d399','#fbbf24','#f472b6','#22d3ee','#fb923c','#a78bfa'],
                    borderWidth: 0, hoverOffset: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    }

    const daily = await fetchApi('payment_daily');
    if (daily && canvasPayDaily.value) {
        destroyChart('payDaily');
        charts.payDaily = new Chart(canvasPayDaily.value, {
            type: 'line',
            data: {
                labels: daily.map(r => { const p = r.tgl.split('-'); return `${parseInt(p[2])}/${parseInt(p[1])}` }),
                datasets: [{
                    label: 'Pendapatan',
                    data: daily.map(r => r.total),
                    borderColor: '#818cf8', backgroundColor: 'rgba(129,140,248,.12)',
                    fill: true, tension: 0.4, borderWidth: 3, pointRadius: 3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => formatRpShort(v) } } } }
        });
    }

    const monthly = await fetchApi('payment_monthly');
    if (monthly && canvasPayMonthly.value) {
        destroyChart('payMonthly');
        charts.payMonthly = new Chart(canvasPayMonthly.value, {
            type: 'bar',
            data: {
                labels: monthly.map(r => shortMonth(r.bln)),
                datasets: [{
                    label: 'Pendapatan',
                    data: monthly.map(r => r.total),
                    backgroundColor: 'rgba(129,140,248,.7)', borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => formatRpShort(v) } } } }
        });
    }
};

const loadGrowthTab = async () => {
    const summary = await fetchApi('customer_summary');
    if (summary) growthData.value = summary;

    await nextTick();

    const growth = await fetchApi('customer_growth');
    if (growth && canvasCustGrowth.value) {
        destroyChart('custGrowth');
        charts.custGrowth = new Chart(canvasCustGrowth.value, {
            type: 'line',
            data: {
                labels: growth.map(r => shortMonth(r.bulan)),
                datasets: [
                    { label: 'Penambahan', data: growth.map(r => r.new), borderColor: '#34d399', backgroundColor: 'rgba(52,211,153,.1)', fill: true, tension: 0.3 },
                    { label: 'Penurunan', data: growth.map(r => r.churn), borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.1)', fill: true, tension: 0.3 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        destroyChart('custCumulative');
        charts.custCumulative = new Chart(canvasCustCumulative.value, {
            type: 'line',
            data: {
                labels: growth.map(r => shortMonth(r.bulan)),
                datasets: [{ label: 'Total', data: growth.map(r => r.active), borderColor: '#818cf8', tension: 0.4, borderWidth: 3 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    const area = await fetchApi('customer_by_area');
    if (area && canvasCustArea.value) {
        destroyChart('custArea');
        charts.custArea = new Chart(canvasCustArea.value, {
            type: 'bar',
            data: {
                labels: area.map(r => r.area_name),
                datasets: [{ label: 'Pelanggan', data: area.map(r => r.c), backgroundColor: ['#818cf8','#34d399','#fbbf24','#f472b6','#22d3ee'], borderRadius: 6 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
};

const loadChurnTab = async () => {
    const summary = await fetchApi('churn_summary');
    if (summary) {
        churnData.value = summary;
        let ins = [];
        if (summary.churn_rate <= 2) ins.push(`Churn rate sangat rendah (${formatPct(summary.churn_rate)}) — bisnis sehat.`);
        else if (summary.churn_rate <= 5) ins.push(`Churn rate moderat (${formatPct(summary.churn_rate)}) — monitor terus.`);
        else ins.push(`Churn rate tinggi (${formatPct(summary.churn_rate)}) — PERLU PERHATIAN SEGERA.`);
        if (summary.suspend_long > 0) ins.push(`${formatNum(summary.suspend_long)} pelanggan suspend >30 hari — risiko churn tinggi.`);
        ins.push(`Retention rate mencapai ${formatPct(summary.retention)}.`);
        churnInsights.value = ins;
    }

    await nextTick();

    const trend = await fetchApi('churn_trend');
    if (trend && canvasChurnTrend.value) {
        destroyChart('churnTrend');
        charts.churnTrend = new Chart(canvasChurnTrend.value, {
            type: 'line',
            data: {
                labels: trend.map(r => shortMonth(r.bulan)),
                datasets: [{ label: 'Churn Rate (%)', data: trend.map(r => r.rate), borderColor: '#f87171', tension: 0.4 }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
};

const loadManagementTab = async () => {
    const summary = await fetchApi('management_summary');
    if (summary) {
        mgmtData.value = summary;
        
        let ins = [];
        if (summary.collection_eff >= 95) ins.push(`Collection efficiency sangat baik (${formatPct(summary.collection_eff)}).`);
        else if (summary.collection_eff >= 80) ins.push(`Collection efficiency baik (${formatPct(summary.collection_eff)}), target >90%.`);
        else ins.push(`Collection efficiency rendah (${formatPct(summary.collection_eff)}) — perbaiki penagihan.`);
        if (summary.avg_days_to_pay <= 10) ins.push(`Rata-rata pembayaran cepat (${summary.avg_days_to_pay} hari).`);
        else ins.push(`Rata-rata pembayaran ${summary.avg_days_to_pay} hari.`);
        mgmtInsights.value = ins;

        let acts = [];
        if (summary.collection_eff < 80) acts.push('Tingkatkan follow-up tagihan otomatis.');
        if (summary.total_outstanding > summary.mrr * 0.5) acts.push('Lakukan penagihan intensif untuk piutang menumpuk.');
        if (acts.length === 0) acts.push('Kondisi keuangan sehat — pertahankan performa.');
        actionInsights.value = acts;
    }

    const aging = await fetchApi('aging_analysis');
    if (aging) agingData.value = aging;

    await nextTick();

    const trend = await fetchApi('revenue_expense_trend');
    if (trend && canvasRevExpense.value) {
        destroyChart('revExpense');
        charts.revExpense = new Chart(canvasRevExpense.value, {
            type: 'bar',
            data: {
                labels: trend.map(r => shortMonth(r.bulan)),
                datasets: [
                    { type: 'line', label: 'Net', data: trend.map(r => r.net), borderColor: '#14b8a6', tension: 0.4 },
                    { label: 'Revenue', data: trend.map(r => r.revenue), backgroundColor: 'rgba(52,211,153,.8)', borderRadius: 4 },
                    { label: 'Expense', data: trend.map(r => r.expense), backgroundColor: 'rgba(244,63,94,.8)', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { ticks: { callback: v => formatRpShort(v) } } } }
        });
    }
};

const loadData = async () => {
    isLoading.value = true;
    checkHiddenStatus();
    if (activeTab.value === 'payment') await loadPaymentTab();
    else if (activeTab.value === 'growth') await loadGrowthTab();
    else if (activeTab.value === 'churn') await loadChurnTab();
    else if (activeTab.value === 'management') await loadManagementTab();
    isLoading.value = false;
};

watch(activeTab, loadData);

onMounted(() => {
    loadHiddenMonths();
    loadData();
});
</script>