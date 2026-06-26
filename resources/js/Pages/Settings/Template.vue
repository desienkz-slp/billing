<template>
    <AppLayout :title="pageTitle">
        <div class="flex flex-col w-full p-2">
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
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 gap-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">Format Struk Thermal (58mm)</label>
                                <span class="text-[10px] sm:text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded leading-relaxed text-right sm:text-left break-words">
                                    Variabel: {logo_perusahaan}, {nama_perusahaan}, {alamat_perusahaan}, {kontak_perusahaan}, {no_invoice}, {tanggal_bayar}, {nama_kasir}, {nama_pelanggan}, {periode_tagihan}, {nama_paket}, {harga_paket}, {diskon}, {pajak}, {biaya_tambahan}, {total_bayar}, {alamat_pelanggan}, {telepon_pelanggan}
                                </span>
                            </div>
                            <textarea v-model="form.template_invoice_58mm" rows="6" class="block w-full font-mono text-sm rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6 transition-all" placeholder="Struk kasir sederhana untuk printer thermal mini..."></textarea>
                            <div class="mt-3 flex flex-wrap justify-end gap-2">
                                <button type="button" @click="previewHtml(form.template_invoice_58mm, '58mm')" class="text-xs text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white font-medium bg-slate-100 dark:bg-slate-700 px-4 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors shadow-sm">Preview</button>
                                <button type="button" @click="setThermalDefault" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-900/30 px-4 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800 transition-colors shadow-sm">Gunakan Template Default</button>
                                <button type="button" @click="submitForm" :disabled="form.processing" class="text-xs text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition-colors flex items-center gap-1">
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <!-- Invoice A5 -->
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 gap-2">
                                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">Format Invoice Standar (A5 / PDF)</label>
                                <span class="text-[10px] sm:text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded leading-relaxed text-right sm:text-left break-words">
                                    Variabel: {logo_perusahaan}, {nama_perusahaan}, {alamat_perusahaan}, {kontak_perusahaan}, {no_invoice}, {tanggal_bayar}, {nama_kasir}, {nama_pelanggan}, {periode_tagihan}, {nama_paket}, {harga_paket}, {diskon}, {pajak}, {biaya_tambahan}, {total_bayar}, {alamat_pelanggan}, {telepon_pelanggan}
                                </span>
                            </div>
                            <textarea v-model="form.template_invoice_a5" rows="8" class="block w-full font-mono text-sm rounded-xl border-0 py-2.5 px-3 text-slate-900 bg-slate-50 dark:text-white dark:bg-slate-900/50 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6 transition-all" placeholder="<html>...</html>"></textarea>
                            <div class="mt-3 flex flex-wrap justify-end gap-2">
                                <button type="button" @click="previewHtml(form.template_invoice_a5, 'A5')" class="text-xs text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white font-medium bg-slate-100 dark:bg-slate-700 px-4 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors shadow-sm">Preview</button>
                                <button type="button" @click="setA5Default" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-900/30 px-4 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800 transition-colors shadow-sm">Gunakan Template Default</button>
                                <button type="button" @click="submitForm" :disabled="form.processing" class="text-xs text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 font-semibold px-4 py-1.5 rounded-lg shadow-sm transition-colors flex items-center gap-1">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Modal -->
        <Modal :show="showPreviewModal" @close="showPreviewModal = false" :title="previewTypeTitle" maxWidth="5xl">
            <div class="bg-slate-200/50 p-4 sm:p-8 rounded overflow-x-auto overflow-y-auto text-center" style="max-height: 75vh;">
                <div v-html="previewContent" class="bg-white shadow-lg inline-block text-left" :style="previewTypeTitle.includes('58mm') ? 'width: 58mm; padding: 10px; margin: 0 auto;' : 'width: 210mm; min-height: 148mm; margin: 0 auto;'"></div>
            </div>
            <template #footer>
                <button type="button" class="w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showPreviewModal = false">
                    Tutup
                </button>
            </template>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    configs: {
        type: Object,
        default: () => ({})
    }
});

const pageTitle = computed(() => 'Manajemen Template');

const showPreviewModal = ref(false);
const previewContent = ref('');
const previewTypeTitle = ref('');

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

const getDummyHtml = (templateStr) => {
    const dummyLogo = props.configs?.company_logo 
        ? props.configs.company_logo 
        : "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Crect width='80' height='80' fill='%23e2e8f0'/%3E%3Ctext x='40' y='45' font-family='Arial' font-size='14' fill='%2364748b' text-anchor='middle'%3ELOGO%3C/text%3E%3C/svg%3E";
    
    return (templateStr || '')
        .replace(/{logo_perusahaan}/g, dummyLogo)
        .replace(/{nama_perusahaan}/g, props.configs?.company_nama || 'Nama Perusahaan')
        .replace(/{alamat_perusahaan}/g, props.configs?.company_alamat || 'Alamat Perusahaan')
        .replace(/{kontak_perusahaan}/g, props.configs?.company_telepon || 'Telepon Perusahaan')
        .replace(/{no_invoice}/g, 'INV-' + new Date().getFullYear() + '01-001')
        .replace(/{tanggal_bayar}/g, new Date().toLocaleString('id-ID'))
        .replace(/{nama_kasir}/g, 'Admin Pusat')
        .replace(/{nama_pelanggan}/g, 'Budi Santoso')
        .replace(/{periode_tagihan}/g, 'Bulan Ini')
        .replace(/{nama_paket}/g, 'Paket Internet')
        .replace(/{harga_paket}/g, '140.000')
        .replace(/{diskon}/g, '0')
        .replace(/{pajak}/g, '10.000')
        .replace(/{biaya_tambahan}/g, '0')
        .replace(/{total_bayar}/g, '150.000')
        .replace(/{alamat_pelanggan}/g, 'Alamat Pelanggan')
        .replace(/{telepon_pelanggan}/g, 'Telepon Pelanggan');
};

const previewHtml = (templateStr, type) => {
    previewTypeTitle.value = `Preview Invoice ${type}`;
    previewContent.value = getDummyHtml(templateStr);
    showPreviewModal.value = true;
};

const setThermalDefault = () => {
    form.template_invoice_58mm = `<div style="width: 58mm; max-width: 100%; font-family: monospace; font-size: 12px; margin: 0 auto; padding: 5px; box-sizing: border-box; overflow-wrap: break-word; color: #000; background: #fff;">
    <div style="text-align: center;">
        <img src="{logo_perusahaan}" alt="Logo" style="display: block; max-width: 50px; margin: 0 auto 5px auto; object-fit: contain;">
        <h3 style="margin: 0; font-size: 14px; font-weight: bold;">{nama_perusahaan}</h3>
        <p style="margin: 0 0 10px 0; font-size: 10px;">{alamat_perusahaan}<br>{kontak_perusahaan}</p>
    </div>
    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
    
    <div style="text-align: left; margin: 10px 0; font-size: 11px;">
        <div style="display: flex; justify-content: space-between;"><span>No:</span> <span>{no_invoice}</span></div>
        <div style="display: flex; justify-content: space-between;"><span>Tgl:</span> <span>{tanggal_bayar}</span></div>
        <div style="display: flex; justify-content: space-between;"><span>Ksr:</span> <span>{nama_kasir}</span></div>
    </div>
    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
    
    <div style="text-align: left; margin: 10px 0; font-size: 11px;">
        <div><span>Nama  :</span> <strong>{nama_pelanggan}</strong></div>
        <div><span>Alamat:</span> <span style="font-size: 10px;">{alamat_pelanggan}</span></div>
        <div><span>Telp  :</span> <span>{telepon_pelanggan}</span></div>
        <div style="margin-top: 5px;"><span>Prd:</span> <span>{periode_tagihan}</span></div>
        <div><span>Paket :</span> <span>{nama_paket}</span></div>
    </div>
    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
    
    <div style="text-align: left; margin: 10px 0; font-size: 11px;">
        <div style="font-weight: bold; margin-bottom: 2px;">Detail Biaya:</div>
        <div style="display: flex; justify-content: space-between;"><span>Langganan</span> <span>Rp {harga_paket}</span></div>
        <div style="display: flex; justify-content: space-between;"><span>Diskon</span> <span>Rp {diskon}</span></div>
        <div style="display: flex; justify-content: space-between;"><span>Pajak (PPN/BHP)</span> <span>Rp {pajak}</span></div>
        <div style="display: flex; justify-content: space-between;"><span>Biaya Tambahan</span> <span>Rp {biaya_tambahan}</span></div>
    </div>
    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
    
    <div style="display: flex; justify-content: space-between; margin: 10px 0; font-weight: bold; font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {total_bayar}</span>
    </div>
    <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
    
    <div style="text-align: center;">
        <p style="margin-top: 10px; font-size: 10px;">Terima kasih atas pembayaran Anda.<br>Simpan struk ini sbg bukti sah.</p>
    </div>
</div>`;
};

const setA5Default = () => {
    form.template_invoice_a5 = `<div style="width: 210mm; min-height: 148mm; font-family: Arial, sans-serif; padding: 20px; box-sizing: border-box; background: #fff;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center;">
            <img src="{logo_perusahaan}" alt="Logo" style="max-width: 80px; margin-right: 15px;">
            <div>
                <h1 style="margin: 0; font-size: 20px; color: #1e40af;">{nama_perusahaan}</h1>
                <p style="margin: 5px 0 0 0; font-size: 11px; color: #555;">{alamat_perusahaan}<br>{kontak_perusahaan}</p>
            </div>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; font-size: 24px; color: #333;">INVOICE</h2>
            <p style="margin: 5px 0 0 0; font-size: 12px;"><strong>No:</strong> {no_invoice}<br><strong>Tgl:</strong> {tanggal_bayar}</p>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
        <div style="width: 48%;">
            <h3 style="margin: 0 0 8px 0; font-size: 13px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Ditagihkan Kepada:</h3>
            <p style="margin: 0; font-size: 12px; line-height: 1.4;">
                <strong>{nama_pelanggan}</strong><br>
                {alamat_pelanggan}<br>
                Telp: {telepon_pelanggan}
            </p>
        </div>
        <div style="width: 48%;">
            <h3 style="margin: 0 0 8px 0; font-size: 13px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Info Tagihan:</h3>
            <p style="margin: 0; font-size: 12px; line-height: 1.4;">
                <strong>Periode:</strong> {periode_tagihan}<br>
                <strong>Paket:</strong> {nama_paket}<br>
                <strong>Status:</strong> <span style="color: green; font-weight: bold;">LUNAS</span>
            </p>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 12px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Deskripsi</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd; width: 35%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Tagihan Internet Paket {nama_paket} - {periode_tagihan}</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">Rp {total_bayar}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="padding: 10px; text-align: right; font-weight: bold; border: 1px solid #ddd;">TOTAL KESELURUHAN</td>
                <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 14px; border: 1px solid #ddd; color: #1e40af;">Rp {total_bayar}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div style="width: 40%; text-align: center;">
            <p style="margin-bottom: 50px; font-size: 12px;">Penerima / Kasir</p>
            <p style="margin: 0; border-top: 1px solid #333; padding-top: 5px; font-size: 12px;">{nama_kasir}</p>
        </div>
        <div style="width: 50%; font-size: 11px; color: #666; text-align: right;">
            <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.<br>Jika ada pertanyaan terkait invoice ini, silakan hubungi kami.</p>
        </div>
    </div>
</div>`;
};
</script>
