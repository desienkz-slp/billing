<template>
    <AppLayout :title="pageTitle">
        <div class="h-full flex flex-col min-h-0 w-full p-2">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">{{ pageTitle }}</h1>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <form @submit.prevent="submitForm">
                    <div class="px-6 py-6 sm:p-8 space-y-6">
                        
                        <!-- Logo Perusahaan -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Logo Perusahaan</label>
                            <div class="flex items-center gap-6">
                                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center">
                                    <img v-if="logoPreview || props.configs?.company_logo" :src="logoPreview || props.configs?.company_logo" alt="Logo" class="h-full w-full object-contain">
                                    <svg v-else class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <input type="file" ref="logoInput" @change="handleLogoUpload" accept="image/jpeg, image/png, image/jpg, image/svg+xml" class="hidden">
                                    <button type="button" @click="$refs.logoInput.click()" class="inline-flex items-center rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        Pilih Gambar
                                    </button>
                                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">JPG, PNG, atau SVG. Maksimal 2MB.</p>
                                    <p v-if="form.errors.company_logo" class="mt-1 text-xs text-red-500">{{ form.errors.company_logo }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nama Perusahaan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Perusahaan / ISP</label>
                                <input v-model="form.company_nama" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Contoh: LadaPala Net">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ID Perusahaan</label>
                                <input v-model="form.company_id" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all font-mono uppercase" placeholder="Contoh: LADA">
                                <p v-if="form.errors.company_id" class="mt-1 text-xs text-red-500">{{ form.errors.company_id }}</p>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap</label>
                            <textarea v-model="form.company_alamat" rows="3" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Telepon -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Telepon Utama</label>
                                <input v-model="form.company_telepon" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="08xxx / 021xxx">
                            </div>

                            <!-- Nomor CS -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">WhatsApp / Nomor CS</label>
                                <input v-model="form.company_nomer_cs" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="08xxx (Untuk aduan pelanggan)">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email Perusahaan</label>
                                <input v-model="form.company_email" type="email" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="info@perusahaan.com">
                            </div>

                            <!-- Website -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Website</label>
                                <input v-model="form.company_website" type="text" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="www.perusahaan.com">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <!-- PPN Rate -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">PPN Rate (%)</label>
                                <input v-model="form.ppn_rate" type="number" step="0.01" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Misal: 11">
                            </div>

                            <!-- BHP USO Rate -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">BHP USO Rate (%)</label>
                                <input v-model="form.bhp_uso_rate" type="number" step="0.01" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Misal: 1.25">
                            </div>

                            <!-- Admin Fee -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Biaya Admin (%)</label>
                                <input v-model="form.admin_fee" type="number" step="0.01" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Misal: 1.5">
                            </div>
                        </div>

                    </div>

                    <!-- Footer / Submit -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end">
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 disabled:opacity-50 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    configs: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = computed(() => 'Profil Perusahaan');
const logoInput = ref(null);
const logoPreview = ref(null);

const form = useForm({
    company_id: props.configs?.company_id || '',
    company_nama: props.configs?.company_nama || '',
    company_alamat: props.configs?.company_alamat || '',
    company_telepon: props.configs?.company_telepon || '',
    company_nomer_cs: props.configs?.company_nomer_cs || '',
    company_email: props.configs?.company_email || '',
    company_website: props.configs?.company_website || '',
    company_logo: null,
    ppn_rate: props.configs?.ppn_rate || '11',
    bhp_uso_rate: props.configs?.bhp_uso_rate || '1.25',
    admin_fee: props.configs?.admin_fee || '2500',
});

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    form.company_logo = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        logoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submitForm = () => {
    // We use post to allow file uploads with method spoofing if needed, but since it's just a post route it's fine.
    form.post(route('config.profil.update'), {
        preserveScroll: true
    });
};
</script>