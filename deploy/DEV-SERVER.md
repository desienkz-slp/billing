# Dev Server Access Guide

## Server Info

| Item | Detail |
|------|--------|
| **Hostname** | `test-dev` |
| **IP** | `172.18.20.136` |
| **User** | `root` |
| **OS** | Ubuntu 22.04.5 LTS |
| **Auth** | SSH Key (passwordless) — key sudah terdaftar di `~/.ssh/authorized_keys` |
| **Password** | `628268` (backup, jika SSH key bermasalah) |

## Cara Akses SSH

```powershell
# Dari background process (tanpa password)
ssh -o StrictHostKeyChecking=no -o BatchMode=yes root@172.18.20.136 "<command>"

# Contoh cek status
ssh root@172.18.20.136 "hostname && uptime"
```

**Catatan**: SSH key passwordless sudah dikonfigurasi. Jika gagal, pastikan:
1. Windows SSH Agent aktif: `Start-Service ssh-agent` (perlu Admin)
2. Key ter-load: `ssh-add $env:USERPROFILE\.ssh\id_rsa`
3. Server `/root` ownership: `chown root:root /root`
4. Server `PubkeyAuthentication yes` di `/etc/ssh/sshd_config`

## Aplikasi Laravel (Billing)

| Item | Detail |
|------|--------|
| **Path** | `/var/www/ladapala-bill` |
| **PHP** | 8.2.31 |
| **Laravel** | 12.61.0 |
| **Web Server** | Nginx 1.18.0 |
| **Database** | PostgreSQL (host: 127.0.0.1, db: billing_db, user: ladapala, pass: secret) |
| **URL** | `http://172.18.20.136` (port 80 via Nginx) |

## Akun Login Aplikasi

| Username | Password | Level | Redirect |
|----------|----------|-------|----------|
| `sysadmin` | `sysadmin` | System Admin (cross-tenant) | `/superadmin/tenants` |
| `superadmin` | `admin123` | Superadmin (tenant-level) | `/app-gateway` |
| `admin` | `admin123` | Admin (tenant-level) | `/app-gateway` |

## Deploy dari Lokal ke Dev Server

### Deploy file tunggal via SCP
```powershell
# Format
scp "<local-path>" "root@172.18.20.136:/var/www/ladapala-bill/<remote-relative-path>"

# Contoh
scp "app\Console\Commands\MakeSysadmin.php" "root@172.18.20.136:/var/www/ladapala-bill/app/Console/Commands/"
```

### Deploy via script
```powershell
# Dari c:\xampp\htdocs\laravel-bill
powershell -ExecutionPolicy Bypass -File .\deploy\deploy-dev.ps1
```

### Artisan commands di server
```powershell
ssh root@172.18.20.136 "cd /var/www/ladapala-bill && php artisan <command>"
```

### Useful commands
```powershell
# Cek log Laravel
ssh root@172.18.20.136 "tail -50 /var/www/ladapala-bill/storage/logs/laravel.log"

# Clear cache
ssh root@172.18.20.136 "cd /var/www/ladapala-bill && php artisan cache:clear && php artisan config:clear && php artisan view:clear"

# Restart Nginx
ssh root@172.18.20.136 "systemctl restart nginx"

# Cek status services
ssh root@172.18.20.136 "systemctl status nginx php8.2-fpm"
```

## Struktur Direktori di Server

```
/var/www/ladapala-bill/
├── app/
│   ├── Console/Commands/    ← artisan commands (MakeSysadmin.php)
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Providers/
│   ├── Services/
│   └── Traits/
├── bootstrap/cache/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── deploy/
├── public/
├── resources/views/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
└── vendor/
```
