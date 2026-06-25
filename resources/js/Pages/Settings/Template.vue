<template>
    <AppLayout :title="pageTitle">
        <div class="h-full flex flex-col min-h-0 w-full p-2">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                <div class="flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">{{ pageTitle }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="submitForm" :disabled="form.processing" class="inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:from-blue-700 hover:to-purple-700 disabled:opacity-50 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Template WA Section -->
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Template WhatsApp</h3>
                    </div>
                    
                    <div class="p-6 sm:p-8 space-y-8">
                        <!-- WA Tagihan -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">WA Notifikasi Tagihan</label>
                                <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Variabel: {nama}, {tagihan}, {periode}, {jatuh_tempo}</span>
                            </div>
                            <textarea v-model="form.template_wa_tagihan" rows="4" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Yth. {nama}, tagihan bulan {periode} sebesar {tagihan} telah terbit..."></textarea>
                        </div>

                        <!-- WA Lunas -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">WA Notifikasi Lunas</label>
                                <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Variabel: {nama}, {nominal}, {tanggal}, {metode}</span>
                            </div>
                            <textarea v-model="form.template_wa_lunas" rows="4" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Terima kasih {nama}, pembayaran sebesar {nominal} pada {tanggal} telah kami terima."></textarea>
                        </div>

                        <!-- WA Informasi -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">WA Notifikasi Informasi / Blast</label>
                                <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Variabel: {nama}, {pesan}</span>
                            </div>
                            <textarea v-model="form.template_wa_info" rows="4" class="block w-full rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Halo {nama}, {pesan}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Template Invoice Section -->
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Template Cetak Invoice</h3>
                    </div>

                    <div class="p-6 sm:p-8 space-y-8">
                        <!-- Invoice 58mm -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">Format Struk Thermal (58mm)</label>
                                <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Mendukung format HTML dasar</span>
                            </div>
                            <textarea v-model="form.template_invoice_58mm" rows="6" class="block w-full font-mono text-sm rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6 transition-all" placeholder="Struk kasir sederhana untuk printer thermal mini..."></textarea>
                        </div>

                        <!-- Invoice A5 -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">Format Invoice Standar (A5 / PDF)</label>
                                <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Mendukung format HTML lengkap</span>
                            </div>
                            <textarea v-model="form.template_invoice_a5" rows="8" class="block w-full font-mono text-sm rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6 transition-all" placeholder="<html>...</html>"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    configs: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = computed(() => 'Manajemen Template');

const form = useForm({
    template_wa_tagihan: props.configs?.template_wa_tagihan || '',
    template_wa_lunas: props.configs?.template_wa_lunas || '',
    template_wa_info: props.configs?.template_wa_info || '',
    template_invoice_58mm: props.configs?.template_invoice_58mm || '',
    template_invoice_a5: props.configs?.template_invoice_a5 || ''
});

const submitForm = () => {
    form.post(route('config.template.update'), {
        preserveScroll: true
    });
};
</script>
