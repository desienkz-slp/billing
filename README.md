# LadaPala-Bill (Production Version)

Repositori ini berisi *Source Code* aplikasi LadaPala-Bill yang sudah dikompilasi (UI Vue.js) dan diobfuskasi secara penuh (Backend PHP) untuk keperluan rilis ke server produksi *(Production)*.

Kode dalam repositori ini **TIDAK UNTUK DIKEMBANGKAN LOKAL (DEVELOPMENT)**, melainkan murni siap ditarik dan dijalankan di server klien.

## Prasyarat Server
Pastikan server Anda (Ubuntu/Debian) telah terpasang:
- PHP >= 8.2 (dengan ekstensi: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml)
- Composer
- Node.js & NPM
- Nginx / Apache
- PostgreSQL (atau MySQL/MariaDB sesuai konfigurasi asli Anda)

---

## 🚀 Panduan Deployment Cepat

### 1. Clone Repositori
Masuk ke direktori web server (misal: `/var/www/`) lalu clone repositori ini:
```bash
cd /var/www/
git clone https://github.com/desienkz-slp/billing.git nama-folder-app
cd nama-folder-app
```

### 2. Konfigurasi Lingkungan (.env)
Aplikasi membutuhkan file konfigurasi lingkungan:
```bash
# Copy file contoh konfigurasi
cp .env.example .env

# Edit konfigurasi (Masukkan kredensial Database, App URL, dsb)
nano .env
```
*(Jangan lupa mengisi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan benar).*

### 3. Eksekusi Script Instalasi
Jalankan skrip instalasi terpadu yang sudah disediakan. Skrip ini akan secara otomatis mengatur hak akses, menginstal dependensi (Composer & NPM), merakit *frontend*, menjalankan migrasi database, dan mengoptimalkan *cache*.

```bash
# Pastikan script dapat dieksekusi
chmod +x install.sh

# Jalankan instalasi
./install.sh
```

*(Catatan: Jika ada *error permission* pada langkah ini, Anda bisa menjalankannya menggunakan `sudo ./install.sh`)*

### 4. Konfigurasi Web Server (Nginx)
Arahkan Document Root Nginx ke dalam folder `public/`.
Contoh rute: `/var/www/nama-folder-app/public`

Berikan juga hak akses kepada user web server (`www-data`):
```bash
sudo chown -R www-data:www-data /var/www/nama-folder-app
sudo chmod -R 775 /var/www/nama-folder-app/storage
sudo chmod -R 775 /var/www/nama-folder-app/bootstrap/cache
```

### 5. Konfigurasi Background Jobs
Agar notifikasi, isolir otomatis, dan antrean aplikasi berjalan dengan baik, pastikan Anda memasang cronjob dan supervisor:

**A. Cron Job (Task Scheduler):**
Jalankan `crontab -e` dan tambahkan baris berikut:
```cron
* * * * * cd /var/www/nama-folder-app && php artisan schedule:run >> /dev/null 2>&1
```

**B. Supervisor (Queue Worker):**
Buat konfigurasi supervisor (misal di `/etc/supervisor/conf.d/ladapala-worker.conf`):
```ini
[program:ladapala-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/nama-folder-app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/nama-folder-app/storage/logs/worker.log
stopwaitsecs=3600
```
Lalu aktifkan dengan:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ladapala-worker:*
```

---
**Sistem LadaPala-Bill kini sudah siap beroperasi di Production! 🎉**
