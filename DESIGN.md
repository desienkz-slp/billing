---
version: 1.1
name: LadaPala-Bill Tailwind Design System
description: Sistem desain LadaPala-Bill yang menggabungkan struktur hierarki Meta dengan estetika premium modern (Glassmorphism, Gradasi, dan Dark Mode) menggunakan utility classes Tailwind CSS.
---

## 1. Overview & Filosofi

Desain aplikasi **LadaPala-Bill** berevolusi dari sekadar struktur datar menjadi antarmuka yang sangat premium, hidup, dan dinamis. Sistem ini menggunakan **Tailwind CSS** sebagai pondasi utama, dipadukan dengan teknik desain web modern:
- **Glassmorphism**: Penggunaan latar belakang semi-transparan (`bg-white/70`, `backdrop-blur-xl`) untuk menciptakan efek kedalaman ruang (depth).
- **Gradients**: Penggunaan gradasi warna halus pada tombol aksi utama dan header kartu untuk menonjolkan elemen penting tanpa terlihat kaku.
- **Dark Mode First**: Semua komponen wajib memiliki dukungan mode gelap (`dark:bg-slate-800`, `dark:border-slate-700`) yang mulus.
- **Micro-animations**: Transisi halus (`transition-all`, `hover:-translate-y-0.5`) saat *hover* maupun fokus.

---

## 2. Tailwind Core Tokens

### Typography (Sistem Teks)
- **Heading 1/Title**: `text-3xl font-bold text-slate-800 dark:text-white tracking-tight`
- **Heading 2/Section**: `text-2xl font-bold text-slate-800 dark:text-white`
- **Heading 3/Card Title**: `text-lg leading-6 font-semibold text-slate-800 dark:text-white`
- **Body Text**: `text-sm text-slate-700 dark:text-slate-300`
- **Muted/Caption**: `text-xs text-slate-500 dark:text-slate-400`

### Colors & Gradients
- **Primary Gradient (Aksi Utama)**: `bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white`
- **Secondary Gradient (Aksi Alternatif)**: `bg-gradient-to-r from-purple-500 to-pink-500`
- **Success Tone**: `text-emerald-600 bg-emerald-100 dark:bg-emerald-900/50 dark:text-emerald-400`
- **Danger/Error Tone**: `text-red-600 bg-red-100 dark:bg-red-900/50 dark:text-red-400`
- **Warning Tone**: `text-amber-600 bg-amber-100 dark:bg-amber-900/50 dark:text-amber-400`

### Borders & Radius
- **Cards/Modals**: `rounded-2xl`
- **Buttons/Inputs**: `rounded-xl` atau `rounded-lg`
- **Badges/Pills**: `rounded-full`

---

## 3. Tailwind Components Library

Berikut adalah referensi kelas Tailwind persis yang harus di-*copy-paste* untuk menjaga konsistensi desain di seluruh halaman:

### A. Buttons (Tombol)

**1. Primary Button (Gradient)**
Digunakan untuk aksi utama seperti "Simpan", "Kirim", atau "Tambah".
```html
<button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-md transition-all flex items-center whitespace-nowrap">
    <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">...</svg>
    Label Aksi
    Text Here
</button>
```

**Cards & Containers**:
- Use `bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden` for main containers (tables, forms).

**2. Secondary Button (Outline)**
Digunakan untuk "Batal", "Kembali", atau aksi opsional.
```html
<button class="px-6 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
    Batal
</button>
```

### B. Cards & Containers

**1. Glassmorphism Card (Standar Panel)**
Digunakan untuk membungkus form, tabel, atau informasi di dalam halaman.
```html
<div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl overflow-hidden">
    <!-- Card Header with subtle gradient -->
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50/50 to-purple-50/50 dark:from-slate-800 dark:to-slate-800">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Judul Kartu</h3>
    </div>
    <!-- Card Body -->
    <div class="p-6">
        Konten...
    </div>
</div>
```

**2. Compact KPI Card (Statistik)**
Digunakan untuk menampilkan angka ringkasan/statistik di bagian atas halaman (Dashboard, Tax, Expenses).
Desain ini menekankan layout horizontal yang ringkas (compact), teks di kiri, ikon berlatar warna di kanan, serta ornamen lingkaran lengkung di pojok kanan atas.
```html
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden group shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50">
    <!-- Ornamen pojok kanan atas (sesuaikan warna dengan tema KPI: blue, emerald, rose, dll) -->
    <div class="absolute right-0 top-0 w-20 h-20 bg-blue-500/10 dark:bg-blue-500/20 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
    <div class="flex justify-between items-start relative z-10">
        <div>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Judul KPI</p>
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Nilai KPI</h3>
        </div>
        <!-- Ikon aksen (sesuaikan warna dengan tema) -->
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">...</svg>
        </div>
    </div>
</div>
```


### C. Forms & Inputs

Setiap input WAJIB memiliki `border`, `bg-white`, dan warna border `slate-300` agar kontras dengan latar belakang.

**1. Input Text/Number & Select Dropdown**
```html
<div class="space-y-1">
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Label Input</label>
    <input type="text" class="w-full rounded-lg border border-slate-300 bg-white dark:border-slate-600 dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" placeholder="Placeholder..." />
</div>
```

**2. Search Input (dengan Icon)**
```html
<div class="relative w-full">
    <input type="text" class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-shadow" placeholder="Cari data..." />
    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
</div>
```

### D. Data Tables (Tabel Responsif)

Standar tabel menggunakan garis pemisah tipis (`border-b`), *hover effect* baris, dan sticky header. Gunakan `text-center` untuk teks yang pendek atau angka, dan beri ruang tabel di dalam kontainer ber-*border* yang memiliki `overflow-hidden`.
```html
<div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto overflow-y-auto max-h-[65vh]">
        <table class="w-full text-xs sm:text-sm text-left text-slate-500 dark:text-slate-400 relative">
            <thead class="sticky top-0 z-10 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-[#1E293B] dark:text-slate-400 shadow-[0_1px_0_0_#e2e8f0] dark:shadow-[0_1px_0_0_#334155]">
                <tr>
                    <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Kolom 1</th>
                    <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Kolom 2</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                    <td class="px-4 py-3 text-center whitespace-nowrap">Isi Data 1</td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">Isi Data 2</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### E. Badges (Status)

**1. Active / Success**
```html
<span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400">
    Aktif
</span>
```

**2. Inactive / Error**
```html
<span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">
    Nonaktif
</span>
```

### F. Footer Standar (Fixed Full-Width)

Untuk memastikan konsistensi dan estetika di seluruh halaman (*modul, page all*), seluruh *layout* utama (`AppLayout.vue`, `SuperadminLayout.vue`, dll.) menggunakan *footer* yang posisinya **menembus ke bawah sidebar** (melintang penuh dari kiri ke kanan).

**Implementasi:**
1. Footer diletakkan **di luar** *container* utama konten dan sidebar.
2. Menggunakan atribut `fixed bottom-0 left-0 w-full z-[60]` agar tidak tertutup elemen lain.

```html
<footer class="fixed bottom-0 left-0 w-full py-2 px-4 text-center text-xs text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700/50 bg-[var(--bg-body)] z-[60]">
    &copy; 2026 upluk-upluk_dev version 2.0
</footer>
```
*Catatan:* Sidebar dan `main-content` harus memiliki `padding-bottom: 36px;` pada CSS (`dashboard.css`) agar konten yang di-*scroll* tidak tersembunyi di balik footer.

### G. Layout Dinamis Resolusi (Flexbox Content)

Untuk halaman yang memiliki tabel data (seperti Pelanggan), halaman dirancang dinamis menyesuaikan sisa tinggi layar (resolusi layar apa pun).
1. *Container* utama menggunakan `flex flex-col h-full`.
2. *Container* tabel menggunakan `flex-1 flex flex-col min-h-0` dan penampung tabelnya `flex-1 min-h-0`.
3. Tabel akan otomatis meregang menyentuh batas footer dan bagian isi data akan ber-*scroll* secara internal tanpa membuat *header* halaman maupun *footer* ikut terscroll.

---

## 4. Aturan Penting Implementasi (Do's & Don'ts)
1. **PENGGUNAAN GLASSMORPHISM**: Gunakan efek `backdrop-blur-xl` hanya pada wadah elemen terbesar (seperti Card utama atau Modal), jangan tumpuk efek blur di dalam blur karena akan memberatkan *render* browser.
2. **KONTRAST INPUT**: Selalu pastikan form input memiliki `border`, `bg-white`, dan warna border yang tidak terlalu transparan (`border-slate-300`) agar jelas terlihat batasnya oleh pengguna (Accessibility).
3. **PENGGUNAAN GRADASI**: Jangan terlalu banyak gradasi di satu halaman. Cukup untuk elemen fokus (Tombol Primary, Header Card tertentu, atau Icon Avatar).
4. **RESPONSIVITAS TABEL**: Kolom tabel harus membungkus (wrap) ke baris baru saat di layar kecil, oleh karena itu selalu gunakan `align-top` pada `<td>`.
5. **LAYOUT DINAMIS**: Jangan lagi menggunakan `height` statis seperti `vh` atau `px` pada tabel tabel, selalu gunakan prinsip flexbox (`flex-1 min-h-0`) seperti pada poin G.
