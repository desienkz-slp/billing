#!/bin/bash

echo "==============================================="
echo "   LadaPala-Bill Automated Production Setup"
echo "==============================================="

# 1. Copy .env
if [ ! -f .env ]; then
    echo "[1] Membuat file .env otomatis..."
    cp .env.example .env
fi

# Set APP_URL
read -p "Masukkan URL/Domain aplikasi (contoh: http://192.168.1.10 atau https://billing.com): " app_url
sed -i "s|^APP_URL=.*|APP_URL=$app_url|" .env

echo ""
echo "[2] Pengaturan Database"
echo "Apakah Anda ingin membuat database secara (1) Otomatis [PostgreSQL] atau (2) Manual?"
read -p "Pilih [1/2]: " db_choice

if [ "$db_choice" == "1" ]; then
    # Otomatis PostgreSQL
    echo "Pilihan: Otomatis. Saya akan membuatkan database dan user PostgreSQL baru."
    
    db_name="ladapala_$(date +%s)"
    db_user="user_$(date +%s)"
    db_pass=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)
    
    echo "Membuat database $db_name..."
    # Mengeksekusi perintah psql sebagai user postgres
    sudo -u postgres psql -c "CREATE USER $db_user WITH PASSWORD '$db_pass';"
    sudo -u postgres psql -c "CREATE DATABASE $db_name OWNER $db_user;"
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $db_name TO $db_user;"
    
    # Update .env
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
    sed -i "s/^# DB_HOST=.*/DB_HOST=127.0.0.1/" .env
    sed -i "s/^# DB_PORT=.*/DB_PORT=5432/" .env
    sed -i "s/^# DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i "s/^# DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i "s/^# DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env
    
    sed -i "s/^DB_HOST=.*/DB_HOST=127.0.0.1/" .env
    sed -i "s/^DB_PORT=.*/DB_PORT=5432/" .env
    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env

else
    # Manual
    echo "Pilihan: Manual. Silakan masukkan kredensial database Anda."
    read -p "Masukkan Driver DB (pgsql/mysql/sqlite) [default: pgsql]: " db_conn
    db_conn=${db_conn:-pgsql}
    
    if [ "$db_conn" == "sqlite" ]; then
        sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
        touch database/database.sqlite
    else
        read -p "Masukkan Host DB [default: 127.0.0.1]: " db_host
        db_host=${db_host:-127.0.0.1}
        
        default_port=5432
        if [ "$db_conn" == "mysql" ]; then
            default_port=3306
        fi
        
        read -p "Masukkan Port DB [default: $default_port]: " db_port
        db_port=${db_port:-$default_port}
        
        read -p "Masukkan Nama Database: " db_name
        read -p "Masukkan Username Database: " db_user
        read -p "Masukkan Password Database: " db_pass

        # Hapus komentar jika ada
        sed -i "s/^# DB_HOST=.*/DB_HOST=$db_host/" .env
        sed -i "s/^# DB_PORT=.*/DB_PORT=$db_port/" .env
        sed -i "s/^# DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
        sed -i "s/^# DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
        sed -i "s/^# DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env

        # Update values
        sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=$db_conn/" .env
        sed -i "s/^DB_HOST=.*/DB_HOST=$db_host/" .env
        sed -i "s/^DB_PORT=.*/DB_PORT=$db_port/" .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env
    fi
fi

echo ""
echo "[3] Memperbarui hak akses direktori..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null

echo "[4] Menginstal dependensi PHP (Composer)..."
composer install --optimize-autoloader --no-dev

# Di versi production, front-end umumnya sudah di-build, tapi kalau mau memastikan:
# Cek apakah package.json ada
if [ -f package.json ]; then
    echo "[5] Menginstal dependensi Frontend (NPM)..."
    npm install
    npm run build
fi

echo "[6] Generate Application Key..."
php artisan key:generate --force

echo "[7] Menjalankan Migrasi Database..."
php artisan migrate --force

echo "[8] Membersihkan Cache..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "==============================================="
echo "   Instalasi Selesai! Sistem siap digunakan."
echo "   Silakan akses di: $app_url"
echo "==============================================="
