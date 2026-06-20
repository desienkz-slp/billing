<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Pelanggan Baru</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Masukkan detail pelanggan dan konfigurasi koneksi jaringan (Radius/Router).</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
                <!-- Data Pelanggan Card -->
                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800 rounded-t-2xl">
                        <h3 class="text-lg leading-6 font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Umum
                        </h3>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" required class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" placeholder="Cth: Budi Santoso" />
                            <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <!-- No WA -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">No. WhatsApp</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" placeholder="+6281234567890" />
                        </div>

                        <!-- Tgl Tagihan & Isolir -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tgl Tagihan</label>
                                <select v-model="form.billing_date" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option v-for="n in 31" :key="n" :value="n">{{ n }}</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tgl Isolir</label>
                                <select v-model="form.tgl_isolir" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option v-for="n in 31" :key="n" :value="n">{{ n }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Max Tunggakan -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Max Telat (Bulan)</label>
                            <input v-model="form.max_tunggakan" type="number" min="0" max="12" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" />
                        </div>

                        <!-- Jenis Bayar & Tgl Daftar -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Jenis Pembayaran</label>
                                <select v-model="form.jenis_bayar" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="prabayar">Prabayar</option>
                                    <option value="pascabayar">Pascabayar</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tgl Daftar</label>
                                <input v-model="form.registration_date" type="date" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" />
                            </div>
                        </div>

                        <!-- Area & ODP -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Area <span class="text-red-500">*</span></label>
                                <select v-model="form.area_id" required class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="">Pilih Area</option>
                                    <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">ODP</label>
                                <select v-model="form.odp_id" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="">Pilih ODP (Opsional)</option>
                                    <option v-for="odp in odps" :key="odp.id" :value="odp.id">{{ odp.name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Paket & Kordinator -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Paket <span class="text-red-500">*</span></label>
                                <select v-model="form.package_id" required class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="">Pilih Paket</option>
                                    <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">{{ pkg.name }}</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kordinator / Sales <span class="text-red-500">*</span></label>
                                <select v-model="form.sales_id" required class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="">Pilih Kordinator</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Diskon & Tambahan Layanan -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Diskon (Rp)</label>
                                <input v-model="form.diskon" type="number" min="0" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tambahan Layanan</label>
                                <input v-model="form.tambahan_layanan" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" placeholder="Cth: IP Public" />
                            </div>
                        </div>

                        <!-- Deskripsi Layanan -->
                        <div class="col-span-1 md:col-span-2 space-y-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Layanan Tambahan</label>
                            <textarea v-model="form.deskripsi_layanan" rows="2" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" placeholder="Keterangan layanan tambahan..."></textarea>
                        </div>
                        
                        <!-- Koordinat -->
                        <div class="grid grid-cols-2 gap-4 col-span-1 md:col-span-2">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Latitude</label>
                                <input v-model="form.latitude" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" placeholder="-6.200000" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Longitude</label>
                                <input v-model="form.longitude" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white" placeholder="106.816666" />
                            </div>
                        </div>

                        <!-- Pengaturan Lanjutan -->
                        <div class="col-span-1 md:col-span-2 pt-4 mt-2 border-t border-slate-200 dark:border-slate-700">
                            <h4 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-4">Pengaturan Lanjutan</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                                <!-- Auto Isolir -->
                                <label class="flex items-center justify-between cursor-pointer p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <span class="text-sm font-medium text-slate-800 dark:text-slate-200">Auto Isolir</span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.auto_isolir" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                </label>

                                <!-- Pajak & Biaya Tambahan -->
                                <label class="flex items-center justify-between cursor-pointer p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-800 dark:text-slate-200">Gunakan PPN</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Termasuk BHP USO & Biaya Admin</span>
                                    </div>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.pakai_ppn" @change="form.pakai_bhp = form.pakai_ppn; form.pakai_admin = form.pakai_ppn" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                </label>

                                <!-- Auto WA Tagihan -->
                                <label class="flex items-center justify-between cursor-pointer p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <span class="text-sm font-medium text-slate-800 dark:text-slate-200">Auto WA Tagihan</span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.auto_wa_tagihan" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                </label>

                                <!-- Sync DB Pusat -->
                                <label class="flex items-center justify-between cursor-pointer p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <span class="text-sm font-medium text-slate-800 dark:text-slate-200">Sync DB Pusat</span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" v-model="form.sync_db_pusat" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-900 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jaringan / Koneksi Card -->
                <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-slate-800 dark:to-slate-800 rounded-t-2xl">
                        <h3 class="text-lg leading-6 font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Konfigurasi Jaringan & User
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Tipe Koneksi Toggle -->
                        <div class="flex items-center space-x-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="radio" v-model="form.connection_type" value="radius" class="w-5 h-5 text-purple-600 focus:ring-purple-500 border-slate-300" />
                                <span class="text-slate-700 dark:text-slate-300 font-medium group-hover:text-purple-600 transition-colors">Radius Server</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="radio" v-model="form.connection_type" value="router" class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-slate-300" />
                                <span class="text-slate-700 dark:text-slate-300 font-medium group-hover:text-blue-600 transition-colors">Router (MikroTik)</span>
                            </label>
                        </div>

                        <!-- Jika Router Terpilih, Pilih Router Dulu -->
                        <div v-if="form.connection_type === 'router'" class="bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-800">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Router MikroTik</label>
                            <select v-model="form.router_id" class="w-full max-w-md rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                <option value="">-- Pilih Router --</option>
                                <option v-for="router in routers" :key="router.id" :value="router.id">{{ router.name }} ({{ router.host }})</option>
                            </select>
                        </div>

                        <div v-if="form.connection_type === 'radius' || (form.connection_type === 'router' && form.router_id)">
                            <!-- Aksi User Toggle -->
                            <div class="flex bg-slate-100 dark:bg-slate-900 p-1 rounded-lg w-fit mb-6">
                                <button type="button" @click="form.user_action = 'select'" :class="form.user_action === 'select' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'" class="px-4 py-2 rounded-md text-sm font-medium transition-all">
                                    Pilih User Ada
                                </button>
                                <button type="button" @click="form.user_action = 'create'" :class="form.user_action === 'create' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'" class="px-4 py-2 rounded-md text-sm font-medium transition-all">
                                    Buat User Baru
                                </button>
                            </div>

                            <!-- Pilih User Existing -->
                            <div v-if="form.user_action === 'select'" class="space-y-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Cari User PPPoE / Radius</label>
                                <div class="relative w-full max-w-xl">
                                    <input 
                                        type="text" 
                                        v-model="searchUserQuery" 
                                        @focus="isUserDropdownOpen = true"
                                        @blur="handleBlur"
                                        placeholder="Ketik minimal 3 huruf..."
                                        class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white"
                                    />
                                    
                                    <div v-if="isUserDropdownOpen && searchUserQuery.length >= 3" class="absolute z-[70] w-full mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto">
                                        <ul v-if="searchedUsers.length > 0">
                                            <li 
                                                v-for="(u, i) in searchedUsers" 
                                                :key="i"
                                                @click="selectUser(u)"
                                                class="px-4 py-2 hover:bg-purple-50 dark:hover:bg-slate-700 cursor-pointer text-sm text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                            >
                                                {{ u.username }} 
                                                <span v-if="u.profile" class="text-xs px-2 py-0.5 ml-2 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                                    {{ u.profile }}
                                                </span>
                                                <span class="text-xs text-slate-500 ml-2">({{ u.source }})</span>
                                            </li>
                                        </ul>
                                        <div v-else class="px-4 py-3 text-sm text-slate-500 text-center">
                                            Tidak ada user yang cocok
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center gap-2" v-if="form.username">
                                        <p class="text-sm text-slate-500">Terpilih: <strong class="text-purple-600 dark:text-purple-400">{{ form.username }}</strong></p>
                                        <span v-if="form.connection_type === 'radius' && form.custom_group" class="text-xs px-2 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                            Profil: {{ form.custom_group }}
                                        </span>
                                        <span v-if="form.connection_type === 'router' && form.custom_profile" class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                            Profil: {{ form.custom_profile }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Buat User Baru -->
                            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username PPPoE</label>
                                    <input v-model="form.username" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white" />
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                                    <input v-model="form.password_pppoe" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white" />
                                </div>
                                
                                <!-- Group / Profil Selector -->
                                <div class="space-y-1" v-if="form.connection_type === 'radius'">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Radius Group</label>
                                    <select v-model="form.custom_group" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white">
                                        <option value="">Ikuti Paket</option>
                                        <option v-for="(g, i) in radiusGroups" :key="i" :value="g">{{ g }}</option>
                                    </select>
                                </div>
                                
                                <div class="space-y-1" v-if="form.connection_type === 'router'">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Router Profile</label>
                                    <select v-model="form.custom_profile" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white">
                                        <option value="">Ikuti Paket</option>
                                        <option v-for="(p, i) in routerProfiles" :key="i" :value="p.name">{{ p.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="$emit('close')" class="px-6 py-2.5 border border border-slate-300 bg-white dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="form.processing" class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 flex items-center">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Simpan Pelanggan
                    </button>
                </div>
            </form>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const emit = defineEmits(['close', 'success']);

const props = defineProps({
    areas: Array,
    packages: Array,
    routers: Array,
    servers: Array,
    odps: Array,
    users: Array,
    defaultSalesId: Number,
    customer: Object,
});

const form = useForm({
    auto_wa_tagihan: true,
    sync_db_pusat: true,
    name: props.customer?.name || '',
    phone: props.customer?.phone || '+62',
    billing_date: props.customer?.billing_date || 1,
    tgl_isolir: props.customer?.tgl_isolir || 1,
    jenis_bayar: props.customer?.jenis_bayar || 'prabayar',
    package_id: props.customer?.package_id || '',
    area_id: props.customer?.area_id || '',
    sales_id: props.customer?.sales_id || props.defaultSalesId || '',
    registration_date: props.customer?.registration_date || (props.customer?.created_at ? props.customer.created_at.substring(0, 10) : new Date().toISOString().substring(0, 10)),
    diskon: props.customer?.diskon || 0,
    tambahan_layanan: props.customer?.tambahan_layanan || '',
    deskripsi_layanan: props.customer?.deskripsi_layanan || '',
    odp_id: props.customer?.odp_id || '',
    latitude: props.customer?.latitude || '',
    longitude: props.customer?.longitude || '',
    status: props.customer?.status || 'active',
    max_tunggakan: props.customer?.max_tunggakan || 1,
    auto_isolir: props.customer ? props.customer.auto_isolir : true,
    pakai_ppn: props.customer ? props.customer.pakai_ppn : false,
    pakai_bhp: props.customer ? props.customer.pakai_bhp : false,
    pakai_admin: props.customer ? props.customer.pakai_admin : false,
    auto_wa_tagihan: props.customer ? props.customer.auto_wa_tagihan : true,
    sync_db_pusat: props.customer ? props.customer.sync_db_pusat : true,
    
    connection_type: props.customer ? (props.customer.router_id ? 'router' : 'radius') : 'radius',
    router_id: props.customer?.router_id || '',
    server_id: props.customer?.server_id || '',
    
    user_action: props.customer?.username ? 'select' : 'create',
    username: props.customer?.username || '',
    password_pppoe: '',
    custom_profile: '',
    custom_group: '',
    create_pppoe: false,
});

const pppoeUsersRaw = ref([]);
const routerProfiles = ref([]);
const radiusGroups = ref([]);

const searchUserQuery = ref('');
const isUserDropdownOpen = ref(false);

const handleBlur = () => {
    window.setTimeout(() => {
        isUserDropdownOpen.value = false;
    }, 200);
};

const searchUsers = async () => {
    try {
        const res = await axios.get(route('cust.customers.search-pppoe'));
        pppoeUsersRaw.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const getRouterProfiles = async (routerId) => {
    try {
        const res = await axios.get(route('cust.customers.router-profiles', routerId));
        routerProfiles.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const getRadiusGroups = async () => {
    try {
        const res = await axios.get(route('cust.customers.radius-groups'));
        radiusGroups.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    searchUsers();
    getRadiusGroups();
    
    // Set default radius server if exists
    if(props.servers && props.servers.length > 0) {
        form.server_id = props.servers.find(s => s.type === 'freeradius')?.id || '';
    }
});

watch(() => form.router_id, (newVal) => {
    if (newVal && form.connection_type === 'router') {
        getRouterProfiles(newVal);
    }
});

watch(() => form.connection_type, (newVal) => {
    if (newVal === 'radius') {
        form.router_id = '';
        if(props.servers && props.servers.length > 0) {
            form.server_id = props.servers.find(s => s.type === 'freeradius')?.id || '';
        }
    } else {
        form.server_id = '';
        form.custom_group = '';
    }
});

const filteredUsers = computed(() => {
    if (form.connection_type === 'radius') {
        return pppoeUsersRaw.value.filter(u => u.source_type === 'radius');
    } else {
        return pppoeUsersRaw.value.filter(u => u.source_type === 'router' && u.source_id === form.router_id);
    }
});

const searchedUsers = computed(() => {
    if (searchUserQuery.value.length < 3) return [];
    const query = searchUserQuery.value.toLowerCase();
    return filteredUsers.value.filter(u => u.username.toLowerCase().includes(query));
});

const selectUser = (user) => {
    form.username = user.username;
    form.password_pppoe = user.password;
    if (form.connection_type === 'radius') {
        form.custom_group = user.profile || '';
    } else {
        form.custom_profile = user.profile || '';
    }
    searchUserQuery.value = user.username;
    isUserDropdownOpen.value = false;
};

const submit = () => {
    if (form.user_action === 'create') {
        form.create_pppoe = true;
    } else {
        form.create_pppoe = false;
    }
    
    if (props.customer) {
        form.put(route('cust.customers.update', props.customer.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('success');
                emit('close');
            }
        });
    } else {
        form.post(route('cust.customers.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('success');
                emit('close');
            }
        });
    }
};
</script>
