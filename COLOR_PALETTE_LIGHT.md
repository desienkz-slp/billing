# 🎨 Light Mode Color Palette

Dokumen ini adalah referensi resmi untuk pedoman pewarnaan antarmuka (UI) dalam **Mode Terang (Light Mode)**. Palet warna ini diadaptasi dari gaya desain *dashboard* modern dan profesional.

## 📌 Base Palette

| Elemen | Warna | Hex Code | Keterangan |
| :--- | :--- | :--- | :--- |
| **Background** | Putih | `#FFFFFF` | Warna kanvas utama aplikasi (Header, Sidebar, Body). |
| **Surface/Card** | Abu terang | `#F8FAFC` | Warna latar kotak konten, widget, dan tabel agar menonjol. |
| **Border** | Abu terang | `#E2E8F0` | Garis batas pemisah antar elemen, card, dan header. |

## 🅰️ Typography (Teks)

| Elemen | Warna | Hex Code | Keterangan |
| :--- | :--- | :--- | :--- |
| **Text Primary** | Hitam kebiruan | `#0F172A` | Teks utama, judul, angka dominan, tulisan menu aktif. |
| **Text Secondary** | Abu-abu | `#64748B` | Teks keterangan, sub-judul, deskripsi tambahan, ikon pasif. |

## 🔵 Aksen & Interaksi (Primary Colors)

| Elemen | Warna | Hex Code | Keterangan |
| :--- | :--- | :--- | :--- |
| **Primary** | Biru | `#2563EB` | Warna utama aplikasi: tombol aksi, link, indikator menu aktif. |
| **Primary Hover** | Biru gelap | `#1D4ED8` | Warna tombol utama saat disorot kursor (*hover*). |

## 🚦 Semantic Colors (Indikator Status)

| Elemen | Warna | Hex Code | Keterangan |
| :--- | :--- | :--- | :--- |
| **Success** | Hijau | `#22C55E` | Indikator berhasil, selesai, lunas, atau status "Online". |
| **Warning** | Oranye | `#F59E0B` | Peringatan, status *pending*, atau butuh perhatian. |
| **Danger** | Merah | `#EF4444` | Pesan error, aksi hapus/batal, koneksi terputus. |

---

> **Cara Penggunaan di Vue/CSS:**
> 1. Jangan gunakan langsung kode *hex* ini pada *class* komponen Vue.
> 2. Gunakan *CSS Variables* yang sudah saya deklarasikan di `dashboard.css`, contoh: `text-[var(--text-primary)]`, `bg-[var(--bg-card)]`, atau `border-[var(--border)]`.
> 3. Pendekatan ini memastikan komponen akan secara otomatis beradaptasi bila pengguna mengganti tema ke *Dark Mode*.
