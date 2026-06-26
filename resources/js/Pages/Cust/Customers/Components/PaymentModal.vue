<template>
    <div class="p-6">
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pembayaran Tagihan
        </h3>

        <!-- Form Pembayaran -->
        <div v-if="!paymentSuccess">
            <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400">Pelanggan</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ customer?.name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400">Paket</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ customer?.package?.name || '-' }}</span>
                    </div>
                </div>
            </div>

            <div v-if="loadingMonths" class="py-8 flex justify-center">
                <svg class="animate-spin h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <form v-else @submit.prevent="submitPayment" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bulan Tagihan (Belum Lunas & 6 Bulan Kedepan)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-x-2 gap-y-4 max-h-48 overflow-y-auto p-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/30 mt-2">
                        <label v-for="(info, ym) in paidMonths" :key="ym" class="relative flex items-center p-3 rounded-lg cursor-pointer border hover:border-emerald-500 transition-colors" :class="[info.lunas ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700' : (form.bulan_bayar.includes(ym) ? 'bg-emerald-50 border-emerald-500 dark:bg-emerald-900/30 dark:border-emerald-500' : 'bg-white border-slate-300 dark:bg-slate-800 dark:border-slate-600')]">
                            <input type="checkbox" :value="ym" :checked="form.bulan_bayar.includes(ym)" :disabled="info.lunas" class="sr-only" @change="handleMonthChange(ym, $event.target.checked)" />
                            
                            <span v-if="ym === currentYm && !info.lunas" class="absolute -top-2.5 -right-1 bg-blue-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow z-10">Bulan Ini</span>
                            <span v-else-if="ym < currentYm && !info.lunas" class="absolute -top-2.5 -right-1 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow z-10">Menunggak</span>
                            
                            <div class="flex-1 text-center">
                                <span class="block text-sm font-medium" :class="info.lunas ? 'text-slate-400' : (form.bulan_bayar.includes(ym) ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300')">{{ formatMonth(ym) }}</span>
                                <span v-if="info.lunas" class="block text-[10px] text-slate-400 font-semibold mt-1">LUNAS</span>
                                <span v-else class="block text-[10px] text-rose-500 font-semibold mt-1">{{ formatRupiah(info.remaining || 0) }}</span>
                            </div>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-rose-500" v-if="errors.bulan_bayar">{{ errors.bulan_bayar }}</p>
                </div>

                <!-- Info Rincian Tagihan -->
                <div class="p-4 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Total Harga (Base x {{ form.bulan_bayar.length }} bln):</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ formatRupiah(baseTagihan) }}</span>
                        </div>
                        <div class="flex justify-between text-sm" v-if="form.diskon > 0">
                            <span class="text-slate-600 dark:text-slate-400">Diskon (Total):</span>
                            <span class="font-medium text-red-600">- {{ formatRupiah(form.diskon) }}</span>
                        </div>
                        <div class="flex justify-between text-sm" v-if="customerData.pakai_bhp">
                            <span class="text-slate-600 dark:text-slate-400">BHP USO ({{ customerData.bhp_uso_rate }}%):</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ formatRupiah(bhpTagihan) }}</span>
                        </div>
                        <div class="flex justify-between text-sm" v-if="customerData.pakai_ppn">
                            <span class="text-slate-600 dark:text-slate-400">PPN ({{ customerData.ppn_rate }}%):</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ formatRupiah(ppnTagihan) }}</span>
                        </div>
                        <div class="flex justify-between text-sm" v-if="customerData.pakai_admin">
                            <span class="text-slate-600 dark:text-slate-400">Biaya Admin ({{ customerData.admin_fee }}%):</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ formatRupiah(adminTagihan) }}</span>
                        </div>
                        
                        <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Total Tagihan:</span>
                            <span class="font-bold text-lg text-blue-600 dark:text-blue-400">{{ formatRupiah(totalTagihan) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Bayar</label>
                        <input type="date" v-model="form.tgl_bayar" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500 dark:text-white" required />
                        <p class="mt-1 text-xs text-rose-500" v-if="errors.tgl_bayar">{{ errors.tgl_bayar }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran</label>
                        <select v-model="form.metode" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500 dark:text-white" required>
                            <option value="cash">Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                        <p class="mt-1 text-xs text-rose-500" v-if="errors.metode">{{ errors.metode }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Potongan Extra / Diskon Transaksi (Rp)</label>
                    <input type="number" v-model="form.diskon" min="0" @input="calculateTotal" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500 dark:text-white" />
                </div>

                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Dibayar</span>
                        <input type="number" v-model="form.paid_amount" class="w-1/2 text-right rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500 dark:text-white" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                    <textarea v-model="form.keterangan" rows="2" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-emerald-500 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting || form.bulan_bayar.length === 0" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center transition-colors disabled:opacity-50">
                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Proses Bayar
                    </button>
                </div>
            </form>
        </div>

        <!-- Layar Sukses & Cetak -->
        <div v-else class="py-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 dark:bg-emerald-900/50 mb-6">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Pembayaran Berhasil!</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">
                Pembayaran pelanggan {{ customer?.name }} sebesar {{ formatRupiah(form.paid_amount) }} telah tercatat.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button @click="printThermal" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Struk (58mm)
                </button>
                <button @click="printA5Invoice" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Invoice A5
                </button>
                <button @click="sendInvoiceWA" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Kirim ke WA
                </button>
            </div>
            <div class="mt-8">
                <button @click="$emit('success')" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 font-medium">
                    Tutup & Kembali
                </button>
            </div>
        </div>

        <!-- Komponen Invoice 58mm (Hidden, only for printing) -->
        <div id="print-thermal" class="hidden">
            <div style="width: 58mm; max-width: 100%; font-family: monospace; font-size: 12px; margin: 0 auto; padding: 5px; box-sizing: border-box; overflow-wrap: break-word; color: #000; background: #fff;">
                <div style="text-align: center;">
                    <img v-if="$page.props.companyProfile?.company_logo" :src="$page.props.companyProfile.company_logo" alt="Logo" style="display: block; max-width: 50px; margin: 0 auto 5px auto; object-fit: contain;">
                    <h3 style="margin: 0; font-size: 14px; font-weight: bold;">{{ $page.props.companyProfile?.company_nama ?? 'NETORA' }}</h3>
                    <p style="margin: 0 0 10px 0; font-size: 10px;">{{ $page.props.companyProfile?.company_alamat ?? '-' }}<br>{{ $page.props.companyProfile?.company_telepon ?? '-' }}</p>
                </div>
                <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                
                <div style="text-align: left; margin: 10px 0; font-size: 11px;">
                    <div style="display: flex; justify-content: space-between;"><span>No:</span> <span>{{ 'INV-' + form.tgl_bayar.replace(/-/g, '') + '-' + customer?.id }}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Tgl:</span> <span>{{ form.tgl_bayar }}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Ksr:</span> <span>Admin</span></div>
                </div>
                <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                
                <div style="text-align: left; margin: 10px 0; font-size: 11px;">
                    <div><span>Nama  :</span> <strong>{{ customer?.name }}</strong></div>
                    <div><span>Alamat:</span> <span style="font-size: 10px;">{{ customer?.address || '-' }}</span></div>
                    <div><span>Telp  :</span> <span>{{ customer?.phone || '-' }}</span></div>
                    <div style="margin-top: 5px;"><span>Prd:</span> <span>{{ form.bulan_bayar.map(b => formatMonth(b)).join(', ') }}</span></div>
                    <div><span>Paket :</span> <span>{{ customer?.package?.name || '-' }}</span></div>
                </div>
                <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                
                <div style="text-align: left; margin: 10px 0; font-size: 11px;">
                    <div style="font-weight: bold; margin-bottom: 2px;">Detail Biaya:</div>
                    <div style="display: flex; justify-content: space-between;"><span>Langganan</span> <span>{{ formatRupiah(baseTagihan) }}</span></div>
                    <div v-if="form.diskon > 0" style="display: flex; justify-content: space-between;"><span>Diskon</span> <span>-{{ formatRupiah(form.diskon) }}</span></div>
                    <div v-if="customerData.pakai_bhp" style="display: flex; justify-content: space-between;"><span>BHP USO ({{ customerData.bhp_uso_rate }}%)</span> <span>{{ formatRupiah(bhpTagihan) }}</span></div>
                    <div v-if="customerData.pakai_ppn" style="display: flex; justify-content: space-between;"><span>PPN ({{ customerData.ppn_rate }}%)</span> <span>{{ formatRupiah(ppnTagihan) }}</span></div>
                    <div v-if="customerData.pakai_admin" style="display: flex; justify-content: space-between;"><span>Biaya Tambahan (Admin)</span> <span>{{ formatRupiah(adminTagihan) }}</span></div>
                </div>
                <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                
                <div style="display: flex; justify-content: space-between; margin: 10px 0; font-weight: bold; font-size: 14px;">
                    <span>TOTAL</span>
                    <span>{{ formatRupiah(form.paid_amount) }}</span>
                </div>
                <div style="border-top: 1px dashed #000; margin: 5px 0;"></div>
                
                <div style="text-align: center;">
                    <p style="margin-top: 10px; font-size: 10px;">Terima kasih atas pembayaran Anda.<br>Simpan struk ini sbg bukti sah.</p>
                </div>
            </div>
        </div>

        <!-- Komponen Invoice A5 (Hidden, only for printing) -->
        <div id="print-invoice" class="hidden">
            <div class="invoice-container p-8 max-w-[210mm] bg-white mx-auto border" style="font-family: sans-serif; color: #000;">
                <div class="flex items-center gap-4 border-b-2 border-black pb-4 mb-4">
                    <img v-if="$page.props.companyProfile?.company_logo" :src="$page.props.companyProfile.company_logo" class="h-16 w-16 object-cover" />
                    <div class="flex-1 text-center">
                        <h1 class="text-2xl font-bold uppercase tracking-wider">BUKTI PEMBAYARAN</h1>
                        <p class="text-sm font-bold">{{ $page.props.companyProfile?.company_nama ?? 'NETORA' }}</p>
                        <p class="text-xs text-gray-600">{{ $page.props.companyProfile?.company_alamat ?? '' }}</p>
                    </div>
                    <div class="w-16"></div> <!-- Balancer for centering -->
                </div>
                
                <div class="flex justify-between text-sm mb-6">
                    <div>
                        <p class="text-gray-500 text-xs uppercase mb-1">Ditagihkan Kepada:</p>
                        <p class="font-bold text-base">{{ customer?.name }}</p>
                        <p>{{ customer?.phone || '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-xs uppercase mb-1">Info Pembayaran:</p>
                        <p><strong>Tanggal:</strong> {{ form.tgl_bayar }}</p>
                        <p><strong>Metode:</strong> <span class="capitalize">{{ form.metode }}</span></p>
                    </div>
                </div>

                <table class="w-full text-sm mb-6 border-collapse">
                    <thead>
                        <tr class="border-y-2 border-black">
                            <th class="py-2 text-left">Deskripsi</th>
                            <th class="py-2 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-3">
                                <p class="font-bold">Pembayaran Tagihan Internet</p>
                                <p class="text-xs text-gray-500">Bulan: {{ form.bulan_bayar.map(b => formatMonth(b)).join(', ') }}</p>
                                <p class="text-xs text-gray-500">Paket: {{ customer?.package?.name }}</p>
                            </td>
                            <td class="py-3 text-right align-top">{{ formatRupiah(baseTagihan) }}</td>
                        </tr>
                        <tr v-if="form.diskon > 0" class="border-b border-gray-200 text-red-600">
                            <td class="py-2 text-right">Diskon</td>
                            <td class="py-2 text-right">-{{ formatRupiah(form.diskon) }}</td>
                        </tr>
                        <tr v-if="customerData.pakai_bhp" class="border-b border-gray-200">
                            <td class="py-2 text-right">BHP USO ({{ customerData.bhp_uso_rate }}%)</td>
                            <td class="py-2 text-right">{{ formatRupiah(bhpTagihan) }}</td>
                        </tr>
                        <tr v-if="customerData.pakai_ppn" class="border-b border-gray-200">
                            <td class="py-2 text-right">PPN ({{ customerData.ppn_rate }}%)</td>
                            <td class="py-2 text-right">{{ formatRupiah(ppnTagihan) }}</td>
                        </tr>
                        <tr v-if="customerData.pakai_admin" class="border-b border-gray-200">
                            <td class="py-2 text-right">Biaya Admin ({{ customerData.admin_fee }}%)</td>
                            <td class="py-2 text-right">{{ formatRupiah(adminTagihan) }}</td>
                        </tr>
                        <tr class="border-b-2 border-black font-bold">
                            <td class="py-3 text-right uppercase">Total Dibayar</td>
                            <td class="py-3 text-right text-lg">{{ formatRupiah(form.paid_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="text-center mt-12 text-sm text-gray-500">
                    <p>Simpan tanda terima ini sebagai bukti pembayaran yang sah.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    customer: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'success']);

const loadingMonths = ref(true);
const paidMonths = ref({});
const paymentSuccess = ref(false);
const isSubmitting = ref(false);
const errors = ref({});

const form = ref({
    customer_id: props.customer?.id,
    bulan_bayar: [],
    tgl_bayar: new Date().toISOString().substr(0, 10),
    metode: 'cash',
    diskon: 0,
    paid_amount: 0,
    keterangan: ''
});

const customerData = ref({
    pakai_ppn: false,
    pakai_bhp: false,
    pakai_admin: false,
    ppn_rate: 11,
    bhp_uso_rate: 1.25,
    admin_fee: 1.5
});

const currentYm = computed(() => new Date().toISOString().substr(0, 7));

onMounted(async () => {
    try {
        const res = await axios.get(`/api-web/payments/months/${props.customer.id}`);
        paidMonths.value = res.data.paid_months;
        if (res.data.customer) {
            customerData.value = { ...customerData.value, ...res.data.customer };
        }
        // Auto select unpaid months that are past or current
        for (const [ym, info] of Object.entries(paidMonths.value)) {
            if (!info.lunas && ym <= currentYm.value) {
                form.value.bulan_bayar.push(ym);
            }
        }
        calculateTotal();
    } catch (e) {
        console.error(e);
    } finally {
        loadingMonths.value = false;
    }
});

const handleMonthChange = (ym, checked) => {
    if (checked) {
        if (!form.value.bulan_bayar.includes(ym)) {
            form.value.bulan_bayar.push(ym);
        }
        // Auto select all previous unpaid months
        for (const [m, info] of Object.entries(paidMonths.value)) {
            if (m < ym && !info.lunas && !form.value.bulan_bayar.includes(m)) {
                form.value.bulan_bayar.push(m);
            }
        }
    } else {
        // Unselect this and all future months
        form.value.bulan_bayar = form.value.bulan_bayar.filter(m => m < ym);
    }
    
    // Trigger calculation
    calculateTotal();
};

const totalTagihan = ref(0);
const baseTagihan = ref(0);
const ppnTagihan = ref(0);
const bhpTagihan = ref(0);
const adminTagihan = ref(0);

const calculateTotal = () => {
    let total = 0;
    form.value.bulan_bayar.forEach(ym => {
        const info = paidMonths.value[ym];
        if (info) {
            total += info.remaining || 0;
        }
    });
    
    baseTagihan.value = total;
    let amountAfterDiscount = Math.max(0, total - (form.value.diskon || 0));
    
    // Calculate Taxes and Fees
    ppnTagihan.value = customerData.value.pakai_ppn ? Math.round(amountAfterDiscount * (customerData.value.ppn_rate / 100)) : 0;
    bhpTagihan.value = customerData.value.pakai_bhp ? Math.round(amountAfterDiscount * (customerData.value.bhp_uso_rate / 100)) : 0;
    adminTagihan.value = customerData.value.pakai_admin ? Math.round(amountAfterDiscount * (customerData.value.admin_fee / 100)) : 0;
    
    totalTagihan.value = amountAfterDiscount + ppnTagihan.value + bhpTagihan.value + adminTagihan.value;
    
    // Auto set paid amount
    form.value.paid_amount = totalTagihan.value;
};

const formatMonth = (ym) => {
    if (!ym) return '';
    const date = new Date(ym + '-01');
    return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(date);
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const submitPayment = async () => {
    isSubmitting.value = true;
    errors.value = {};
    
    try {
        await axios.post('/api-web/payments/single', form.value);
        paymentSuccess.value = true;
    } catch (e) {
        if (e.response?.data?.errors) {
            errors.value = e.response.data.errors;
        } else {
            alert(e.response?.data?.message || 'Terjadi kesalahan saat memproses pembayaran.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

const printThermal = () => {
    document.getElementById('print-style').innerHTML = `
        @media print {
            body * { visibility: hidden; }
            #print-thermal, #print-thermal * { visibility: visible; display: block !important; }
            #print-thermal { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
            @page { size: 58mm auto; margin: 0; }
        }
    `;
    const printContent = document.getElementById('print-thermal').innerHTML;
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
};

const printA5Invoice = () => {
    // Inject print styles and trigger print
    document.getElementById('print-style').innerHTML = `
        @media print {
            body * { visibility: hidden; }
            #print-invoice, #print-invoice * { visibility: visible; display: block !important; }
            #print-invoice { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
            @page { size: A5 landscape; margin: 0; }
        }
    `;
    const printContent = document.getElementById('print-invoice').innerHTML;
    const originalContent = document.body.innerHTML;

    // Temporary replace body for printing
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    
    // Reload to restore vue reactivity
    window.location.reload();
};

const sendInvoiceWA = () => {
    // For WA Web image sharing, it requires user to paste. 
    // We can just open WA Web with a text that tells them "Ini tagihan anda..."
    // Since we cannot automatically attach an image via wa.me URL without WhatsApp Business API.
    const text = `Terima kasih, pembayaran tagihan internet LadaPala atas nama *${props.customer?.name}* untuk periode *${form.value.bulan_bayar.map(b => formatMonth(b)).join(', ')}* sejumlah *${formatRupiah(form.value.paid_amount)}* telah kami terima.`;
    
    let phone = props.customer?.phone || '';
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }
    
    window.open(`https://web.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(text)}`, '_blank');
};
</script>

<style id="print-style">
/* Default print style is injected via JS before print */
</style>
