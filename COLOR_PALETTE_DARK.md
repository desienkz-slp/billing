# 🌙 Dark Mode Color Palette (Rekomendasi)

Berikut adalah rancangan palet warna **Mode Gelap (Dark Mode)** yang dirancang khusus untuk menjadi pasangan seimbang bagi *Light Mode* sebelumnya. Warna-warna ini dipilih agar mata tetap nyaman (tidak terlalu kontras/menyilaukan), namun elemen interaktif tetap tajam dan menonjol.

## 📌 Base Palette

| Elemen | Warna | Hex Code | Keterangan (vs Light Mode) |
| :--- | :--- | :--- | :--- |
| **Background** | Hitam Kebiruan Sangat Gelap | `#020617` | Latar belakang kanvas yang sangat pekat, menggantikan `#FFFFFF`. |
| **Surface/Card** | Biru Dongker / Slate Gelap | `#1E293B` | Warna kotak konten yang sedikit lebih terang dari *background* untuk efek elevasi/dimensi. |
| **Border** | Abu-abu Gelap | `#334155` | Garis pemisah yang halus, menjaga batas kotak tanpa terlihat mencolok. |

## 🅰️ Typography (Teks)

| Elemen | Warna | Hex Code | Keterangan (vs Light Mode) |
| :--- | :--- | :--- | :--- |
| **Text Primary** | Putih Tulang / Terang | `#F1F5F9` | Teks judul utama. Kita tidak menggunakan `#FFFFFF` murni agar mata tidak sakit/silau saat membaca. |
| **Text Secondary** | Abu-abu Menengah | `#94A3B8` | Teks keterangan, lebih terang dari teks sekunder di mode *light* agar terbaca di latar gelap. |

## 🔵 Aksen & Interaksi (Primary Colors)

| Elemen | Warna | Hex Code | Keterangan (vs Light Mode) |
| :--- | :--- | :--- | :--- |
| **Primary** | Biru Terang (Vibrant) | `#3B82F6` | Warna biru tombol dibuat lebih menyala dari mode terang (`#2563EB`) agar "melompat" keluar di latar gelap. |
| **Primary Hover** | Biru Sedikit Lembut | `#60A5FA` | Warna biru yang lebih muda saat tombol disorot *mouse*. |

## 🚦 Semantic Colors (Indikator Status)

| Elemen | Warna | Hex Code | Keterangan |
| :--- | :--- | :--- | :--- |
| **Success** | Hijau Terang | `#4ADE80` | Hijau yang dioptimalkan untuk visibilitas ekstra di atas latar gelap. |
| **Warning** | Kuning / Oranye Terang | `#FBBF24` | Status *pending* yang sangat mudah dikenali. |
| **Danger** | Merah Cerah | `#F87171` | Indikator error yang tidak pedas di mata saat *dark mode*. |

---

> **Mengapa Warna Aksen (Primary) Berbeda?**
> Pada latar belakang putih, biru pekat (`#2563EB`) terlihat luar biasa. Namun, jika warna yang sama ditempel di atas hitam (`#020617`), ia akan terlihat "tenggelam" dan redup. Oleh karena itu, kita menaikkan *brightness* birunya menjadi `#3B82F6` khusus saat masuk Mode Gelap agar tetap memancarkan kesan yang kuat!
