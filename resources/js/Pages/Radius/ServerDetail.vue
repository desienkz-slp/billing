<template>
  <AppLayout :title="serverIdentity ? `${server.name} (${serverIdentity})` : (server.name || 'RADIUS Dashboard')" :hideSidebar="true">
    <div class="w-full p-2 flex flex-col">

      <!-- Main Glassmorphism Card -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl overflow-hidden flex flex-col flex-1 min-h-[500px]">
        <!-- Tabs & Toolbar Header -->
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
          <!-- Tabs -->
          <div class="flex flex-wrap gap-2">
            <button @click="activeTab = 'status'" :class="['px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center', activeTab === 'status' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border border-transparent']">
              Status
            </button>
            <button @click="activeTab = 'users'" :class="['px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center', activeTab === 'users' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border border-transparent']">
              Users
            </button>
            <button @click="activeTab = 'profiles'" :class="['px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center', activeTab === 'profiles' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border border-transparent']">
              Profiles
            </button>
          </div>
          
          <!-- Actions & Search -->
          <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
            <div class="relative w-full sm:w-auto min-w-[250px]">
              <input type="text" v-model="searchQuery" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" placeholder="Cari data..." />
              <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            
            <button @click="fetchData" class="px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium shadow-sm flex items-center" :class="{ 'opacity-70': isLoading }" :disabled="isLoading">
               <svg class="w-4 h-4 mr-1.5" :class="{ 'animate-spin': isLoading }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
               Refresh
            </button>
            
            <button v-if="activeTab === 'users'" @click="openCreateUserModal" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Tambah User
            </button>
            <button v-if="activeTab === 'profiles'" @click="openCreateProfileModal" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Tambah Profil
            </button>
          </div>
        </div>

        <!-- Data Tables Area -->
        <div class="overflow-x-auto overflow-y-auto max-h-[65vh] flex-1">

          <!-- TAB 1: STATUS (Active Sessions) -->
          <table v-if="activeTab === 'status'" class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
            <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
              <tr>
                <th class="px-5 py-3.5">Username</th>
                <th class="px-5 py-3.5">IP Address</th>
                <th class="px-5 py-3.5">MAC / Caller ID</th>
                <th class="px-5 py-3.5">Start Time</th>
                <th class="px-5 py-3.5">Uptime</th>
                <th class="px-5 py-3.5">Traffic (D/U)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading && !sessions.length" class="border-b border-slate-200 dark:border-slate-700">
                  <td colspan="6" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400">Memuat sesi aktif...</td>
              </tr>
              <tr v-else-if="!filteredSessions.length" class="border-b border-slate-200 dark:border-slate-700">
                <td colspan="6" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400 italic flex flex-col items-center">
                    <svg class="w-10 h-10 mb-3 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4M12 4v16" /></svg>
                    Tidak ada koneksi aktif saat ini.
                </td>
              </tr>
              <tr v-for="session in filteredSessions" :key="session.radacctid" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-5 py-3 font-semibold text-slate-800 dark:text-white flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2.5 bg-emerald-500"></span>
                  {{ session.username }}
                </td>
                <td class="px-5 py-3 font-mono text-blue-600 dark:text-blue-400">{{ session.framedipaddress || '-' }}</td>
                <td class="px-5 py-3 font-mono text-slate-500 dark:text-slate-400">{{ session.callingstationid || '-' }}</td>
                <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ formatDate(session.acctstarttime) }}</td>
                <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-200">{{ formatUptime(session.acctsessiontime) }}</td>
                <td class="px-5 py-3 font-mono text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400">{{ formatBytes(session.acctoutputoctets) }}</span> <span class="text-slate-400 mx-1">↓</span> 
                    <span class="text-amber-600 dark:text-amber-400">{{ formatBytes(session.acctinputoctets) }}</span> <span class="text-slate-400 ml-1">↑</span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- TAB 2: USERS -->
          <table v-if="activeTab === 'users'" class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
            <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
              <tr>
                <th class="px-5 py-3.5">Username</th>
                <th class="px-5 py-3.5">Password</th>
                <th class="px-5 py-3.5">Profil</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading && !users.length" class="border-b border-slate-200 dark:border-slate-700">
                  <td colspan="4" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400">Memuat data user...</td>
              </tr>
              <tr v-else-if="!filteredUsers.length" class="border-b border-slate-200 dark:border-slate-700">
                <td colspan="4" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400 italic">Tidak ada user ditemukan.</td>
              </tr>
              <tr v-for="user in filteredUsers" :key="user.username" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-5 py-3 font-semibold text-slate-800 dark:text-white">
                  <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 flex items-center justify-center font-bold text-sm uppercase">
                          {{ user.username.charAt(0) }}
                      </div>
                      <span class="font-mono text-sm">{{ user.username }}</span>
                  </div>
                </td>
                <td class="px-5 py-3 font-mono text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2 group cursor-pointer w-max" @click="togglePassword(user)">
                        <span class="tracking-wider">{{ user.showPassword ? user.password : '••••••••' }}</span>
                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="user.showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </td>
                <td class="px-5 py-3">
                  <span v-if="user.profile" class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-400">
                      {{ user.profile }}
                  </span>
                  <span v-else class="text-slate-500 dark:text-slate-400 italic text-xs">Tanpa Profil</span>
                </td>
                <td class="px-5 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                      <button @click="openEditUserModal(user)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg dark:text-blue-400 dark:hover:bg-blue-900/30 transition-colors" title="Edit">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                      </button>
                      <button @click="deleteUser(user)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/30 transition-colors" title="Hapus">
                          <svg class="w-4 h-4" fill="none" viewBox="0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- TAB 3: PROFILES -->
          <table v-if="activeTab === 'profiles'" class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
            <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
              <tr>
                <th class="px-5 py-3.5">Grup / Profil</th>
                <th class="px-5 py-3.5">Atribut</th>
                <th class="px-5 py-3.5">Operator</th>
                <th class="px-5 py-3.5">Value</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading && !profiles.length" class="border-b border-slate-200 dark:border-slate-700">
                  <td colspan="5" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400">Memuat data profil...</td>
              </tr>
              <tr v-else-if="!filteredProfiles.length" class="border-b border-slate-200 dark:border-slate-700">
                <td colspan="5" class="px-5 py-16 text-center text-slate-500 dark:text-slate-400 italic">Tidak ada profil ditemukan.</td>
              </tr>
              <tr v-for="profile in filteredProfiles" :key="profile.id" class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <td class="px-5 py-3 font-semibold text-slate-800 dark:text-white">
                  <div class="flex items-center gap-2">
                      <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                      {{ profile.groupname }}
                  </div>
                </td>
                <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-300">{{ profile.attribute }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-mono font-bold rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                        {{ profile.op }}
                    </span>
                </td>
                <td class="px-5 py-3 font-mono font-medium text-slate-800 dark:text-slate-200">{{ profile.value }}</td>
                <td class="px-5 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                      <button @click="openEditProfileModal(profile)" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg dark:text-purple-400 dark:hover:bg-purple-900/30 transition-colors" title="Edit">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                      </button>
                      <button @click="deleteProfile(profile)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/30 transition-colors" title="Hapus">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>
    </div>

    <!-- User Modal Form -->
    <Modal :show="showUserModal" @close="closeUserModal" :title="userForm.isEdit ? 'Edit User PPPoE' : 'Tambah User PPPoE'" maxWidth="md">
        <div class="grid grid-cols-1 gap-5">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username</label>
                <input v-model="userForm.username" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow font-mono" :disabled="userForm.isEdit" placeholder="contoh: user01" />
                <p v-if="userForm.isEdit" class="text-xs text-slate-500 mt-1">Username tidak dapat diubah setelah dibuat.</p>
                <p v-if="userForm.errors.username" class="text-xs text-red-500 mt-1">{{ userForm.errors.username }}</p>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                <input v-model="userForm.password" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow font-mono" />
                <p v-if="userForm.errors.password" class="text-xs text-red-500 mt-1">{{ userForm.errors.password }}</p>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Profil / Paket</label>
                <select v-model="userForm.profile" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow">
                    <option value="">-- Tanpa Profil --</option>
                    <option v-for="profileName in uniqueProfiles" :key="profileName" :value="profileName">{{ profileName }}</option>
                </select>
                <p v-if="userForm.errors.profile" class="text-xs text-red-500 mt-1">{{ userForm.errors.profile }}</p>
            </div>
        </div>
        <template #footer>
            <button @click="closeUserModal" class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium mr-2">Batal</button>
            <button @click="submitUserForm" :disabled="isSubmitting" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl text-sm font-medium transition-all disabled:opacity-50">Simpan User</button>
        </template>
    </Modal>

    <!-- Profile Modal Form -->
    <Modal :show="showProfileModal" @close="closeProfileModal" :title="profileForm.id ? 'Edit Atribut Profil' : 'Tambah Atribut Profil'" maxWidth="md">
        <div class="grid grid-cols-1 gap-5">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Profil (Group)</label>
                <input v-model="profileForm.groupname" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white transition-shadow" placeholder="Misal: 10M-Unlimited" />
                <p v-if="profileForm.errors.groupname" class="text-xs text-red-500 mt-1">{{ profileForm.errors.groupname }}</p>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Attribute</label>
                <select v-model="profileForm.attribute" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white transition-shadow font-mono">
                    <option value="Mikrotik-Rate-Limit">Mikrotik-Rate-Limit (Limit Kecepatan)</option>
                    <option value="Framed-Pool">Framed-Pool (IP Pool)</option>
                    <option value="Session-Timeout">Session-Timeout</option>
                    <option value="Idle-Timeout">Idle-Timeout</option>
                    <option value="Port-Limit">Port-Limit (Simultaneous Use)</option>
                    <option value="Mikrotik-Address-List">Mikrotik-Address-List (Isolir/Firewall)</option>
                    <option value="Acct-Interim-Interval">Acct-Interim-Interval (Interval Update)</option>
                    <option value="Framed-IP-Address">Framed-IP-Address (IP Statik)</option>
                    <option value="Mikrotik-Group">Mikrotik-Group (Bind ke Router Profile)</option>
                </select>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1 space-y-1">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Operator</label>
                    <select v-model="profileForm.op" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white transition-shadow font-mono">
                        <option value=":=">:=</option>
                        <option value="==">==</option>
                        <option value="+=">+=</option>
                        <option value="=">=</option>
                    </select>
                </div>
                <div class="col-span-2 space-y-1">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Value</label>
                    <input v-model="profileForm.value" type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-purple-500 dark:text-white transition-shadow font-mono" placeholder="Contoh: 10M/10M" />
                    <p v-if="profileForm.errors.value" class="text-xs text-red-500 mt-1">{{ profileForm.errors.value }}</p>
                </div>
            </div>
        </div>
        <template #footer>
            <button @click="closeProfileModal" class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium mr-2">Batal</button>
            <button @click="submitProfileForm" :disabled="isSubmitting" class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-xl text-sm font-medium transition-all disabled:opacity-50">Simpan Profil</button>
        </template>
    </Modal>

  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({
    server: { type: Object, required: true }
});

const { confirm } = useConfirm();

// State
const activeTab = ref('status');
const searchQuery = ref('');
const isLoading = ref(true);
const isSubmitting = ref(false);

const serverIdentity = ref('');

const sessions = ref([]);
const users = ref([]);
const profiles = ref([]);

// Modals
const showUserModal = ref(false);
const showProfileModal = ref(false);

const userForm = ref({ isEdit: false, username: '', password: '', profile: '', errors: {} });
const profileForm = ref({ id: null, groupname: '', attribute: 'Mikrotik-Rate-Limit', op: ':=', value: '', errors: {} });

// Formatters
const formatBytes = (bytes) => {
  if (!bytes || bytes === 0 || bytes === '0') return '0 B';
  const parsedBytes = parseInt(bytes, 10);
  if (isNaN(parsedBytes)) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(parsedBytes) / Math.log(k));
  return parseFloat((parsedBytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatUptime = (seconds) => {
    if (!seconds) return '0s';
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    if (h > 0) return `${h}h ${m}m`;
    if (m > 0) return `${m}m ${s}s`;
    return `${s}s`;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
};

// Computed Filters
const filteredSessions = computed(() => {
    if (!searchQuery.value) return sessions.value;
    const q = searchQuery.value.toLowerCase();
    return sessions.value.filter(s => s.username?.toLowerCase().includes(q) || s.framedipaddress?.toLowerCase().includes(q));
});

const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    const q = searchQuery.value.toLowerCase();
    return users.value.filter(u => u.username?.toLowerCase().includes(q) || u.profile?.toLowerCase().includes(q));
});

const filteredProfiles = computed(() => {
    if (!searchQuery.value) return profiles.value;
    const q = searchQuery.value.toLowerCase();
    return profiles.value.filter(p => p.groupname?.toLowerCase().includes(q) || p.attribute?.toLowerCase().includes(q));
});

const uniqueProfiles = computed(() => {
    const groups = new Set();
    profiles.value.forEach(p => groups.add(p.groupname));
    return Array.from(groups).sort();
});

// Actions
const fetchData = async () => {
    isLoading.value = true;
    try {
        const [sessRes, userRes, profRes, confRes] = await Promise.all([
            axios.get(route('radius.api.sessions.get', props.server.id)),
            axios.get(route('radius.api.users.get', props.server.id)),
            axios.get(route('radius.api.profiles.get', props.server.id)),
            axios.get(route('radius.api.config.get', props.server.id)).catch(() => ({ data: {} }))
        ]);
        
        sessions.value = sessRes.data || [];
        users.value = (userRes.data || []).map(u => ({ ...u, showPassword: false }));
        profiles.value = profRes.data || [];
        serverIdentity.value = confRes.data?.SERVER_IDENTITY || '';
    } catch (error) {
        alert('Gagal memuat data dari RADIUS Server: ' + (error.response?.data?.error || error.message));
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchData();
});

// Users logic
const togglePassword = (user) => { user.showPassword = !user.showPassword; };

const openCreateUserModal = () => {
    userForm.value = { isEdit: false, username: '', password: '', profile: '', errors: {} };
    showUserModal.value = true;
};

const openEditUserModal = (user) => {
    userForm.value = { isEdit: true, username: user.username, password: user.password, profile: user.profile || '', errors: {} };
    showUserModal.value = true;
};

const closeUserModal = () => { showUserModal.value = false; };

const submitUserForm = async () => {
    isSubmitting.value = true;
    userForm.value.errors = {};
    if (!userForm.value.username) userForm.value.errors.username = 'Username wajib diisi';
    if (!userForm.value.password) userForm.value.errors.password = 'Password wajib diisi';
    
    if (Object.keys(userForm.value.errors).length > 0) { isSubmitting.value = false; return; }

    try {
        await axios.post(route('radius.api.users.store', props.server.id), {
            username: userForm.value.username,
            password: userForm.value.password,
            profile: userForm.value.profile || null
        });
        closeUserModal();
        fetchData();
    } catch (error) {
        alert('Gagal menyimpan user: ' + (error.response?.data?.message || error.message));
    } finally {
        isSubmitting.value = false;
    }
};

const deleteUser = (user) => {
    confirm({
        title: 'Hapus User PPPoE?',
        message: `Hapus user ${user.username} secara permanen?`,
        type: 'danger',
        confirmText: 'Ya, Hapus'
    }).then(async (c) => {
        if (c) {
            try {
                await axios.post(route('radius.api.users.batch-destroy', props.server.id), { usernames: [user.username] });
                fetchData();
            } catch (error) {
                alert('Gagal menghapus user');
            }
        }
    });
};

// Profiles logic
const openCreateProfileModal = () => {
    profileForm.value = { id: null, groupname: '', attribute: 'Mikrotik-Rate-Limit', op: ':=', value: '', errors: {} };
    showProfileModal.value = true;
};

const openEditProfileModal = (profile) => {
    profileForm.value = { id: profile.id, groupname: profile.groupname, attribute: profile.attribute, op: profile.op, value: profile.value, errors: {} };
    showProfileModal.value = true;
};

const closeProfileModal = () => { showProfileModal.value = false; };

const submitProfileForm = async () => {
    isSubmitting.value = true;
    profileForm.value.errors = {};
    if (!profileForm.value.groupname) profileForm.value.errors.groupname = 'Nama grup wajib diisi';
    if (!profileForm.value.value) profileForm.value.errors.value = 'Value wajib diisi';
    
    if (Object.keys(profileForm.value.errors).length > 0) { isSubmitting.value = false; return; }

    try {
        if (profileForm.value.id) {
            await axios.put(route('radius.api.profiles.update', [props.server.id, profileForm.value.id]), profileForm.value);
        } else {
            await axios.post(route('radius.api.profiles.store', props.server.id), profileForm.value);
        }
        closeProfileModal();
        fetchData();
    } catch (error) {
        alert('Gagal menyimpan profil');
    } finally {
        isSubmitting.value = false;
    }
};

const deleteProfile = (profile) => {
    confirm({
        title: 'Hapus Atribut Profil?',
        message: `Hapus atribut ${profile.attribute} dari grup ${profile.groupname}?`,
        type: 'danger',
        confirmText: 'Ya, Hapus'
    }).then(async (c) => {
        if (c) {
            try {
                await axios.delete(route('radius.api.profiles.destroy', [props.server.id, profile.id]));
                fetchData();
            } catch (error) {
                alert('Gagal menghapus atribut profil');
            }
        }
    });
};
</script>

<style scoped>
/* Hidden default scrollbars for cleaner look */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: transparent; 
}
::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.3); 
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.5); 
}
.dark ::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15); 
}
.dark ::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25); 
}
</style>
