<template>
    <div class="p-6">
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Kirim WA Tagihan
        </h3>

        <div v-if="loading" class="py-8 flex justify-center">
            <svg class="animate-spin h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <div v-else class="space-y-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Preview Pesan WA</label>
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800 text-sm whitespace-pre-wrap text-slate-700 dark:text-slate-300">
                    {{ messageText }}
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="button" @click="$emit('close')" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                    Batal
                </button>
                <button @click="openWaWeb" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors">
                    Kirim via Web WA
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    customer: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

const loading = ref(true);
const messageText = ref('');
const phoneNumber = ref('');

onMounted(async () => {
    try {
        const res = await axios.get(`/api-web/payments/wa-template/${props.customer.id}`);
        messageText.value = res.data.message;
        phoneNumber.value = res.data.phone;
    } catch (e) {
        console.error(e);
        messageText.value = 'Gagal memuat template WA. Pastikan pelanggan ini adalah milik Anda.';
    } finally {
        loading.value = false;
    }
});

const openWaWeb = () => {
    let phone = phoneNumber.value || '';
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }
    
    window.open(`https://web.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(messageText.value)}`, '_blank');
    emit('close');
};
</script>
