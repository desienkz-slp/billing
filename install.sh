#!/bin/bash

echo "==============================================="
echo "   LadaPala-Bill Automated Production Setup"
echo "==============================================="

# Cek Root/Sudo privileges untuk instalasi otomatis
if [ "$EUID" -ne 0 ]; then
  echo "Peringatan: Anda tidak menjalankan skrip ini sebagai Root."
  echo "Untuk instalasi dependensi secara otomatis, pastikan Anda menggunakan 'sudo ./install.sh'"
  echo "==============================================="
fi

# Fungsi cek perintah (dependency)
check_command() {
    if ! command -v "$1" &> /dev/null; then
        return 1
    else
        return 0
    fi
}

# Cek dependensi utama
MISSING_DEPS=0
check_command php || MISSING_DEPS=1
check_command composer || MISSING_DEPS=1
check_command npm || MISSING_DEPS=1
check_command psql || MISSING_DEPS=1

if [ $MISSING_DEPS -eq 1 ]; then
    echo "🚨 PERINGATAN: Server Anda masih kosong! (PHP, Composer, NPM, atau PostgreSQL belum terpasang)."
    read -p "Apakah Anda ingin saya MENGINSTAL OTOMATIS semua dependensi tersebut? (hanya untuk Ubuntu/Debian) [y/N]: " install_deps
    
    if [[ "$install_deps" == "y" || "$install_deps" == "Y" ]]; then
        echo "Memulai instalasi dependensi server. Mohon tunggu..."
        apt-get update
        apt-get install -y php php-cli php-fpm php-pgsql php-mysql php-mbstring php-xml php-bcmath php-curl php-zip unzip curl
        apt-get install -y postgresql postgresql-contrib
        apt-get install -y npm
        
        # Install Composer jika tidak ada di repositori APT standar
        if ! command -v composer &> /dev/null; then
            curl -sS https://getcomposer.org/installer | php
            mv composer.phar /usr/local/bin/composer
        fi
        
        # Pastikan service postgresql berjalan
        systemctl enable postgresql
        systemctl start postgresql
        
        echo "✅ Instalasi dependensi server selesai!"
    else
        echo "❌ Proses dibatalkan. Skrip tidak dapat dilanjutkan tanpa dependensi yang lengkap."
        exit 1
    fi
fi

# 1. Copy .env
if [ ! -f .env ]; then
    echo "[1] Membuat file .env otomatis..."
    cp .env.example .env
fi

# Set APP_URL
read -p "Masukkan URL/Domain aplikasi (contoh: http://192.168.1.10 atau https://billing.com): " app_url
app_url_escaped=$(echo "$app_url" | sed 's/\//\\\//g')
sed -i "s/^APP_URL=.*/APP_URL=$app_url_escaped/" .env

echo ""
echo "[2] Pengaturan Database"
echo "Pilih mode konfigurasi database:"
echo "1) Otomatis  (Membuat DB & User PostgreSQL lokal dengan nama acak)"
echo "2) Manual    (Membuat DB & User PostgreSQL lokal dengan nama sesuai input Anda)"
echo "3) Eksternal (Menggunakan Database yang sudah ada di server lain/lokal)"
read -p "Pilih [1/2/3]: " db_choice

if [ "$db_choice" == "1" ]; then
    # Otomatis PostgreSQL
    echo "Pilihan: Otomatis. Saya akan membuatkan database dan user PostgreSQL baru."
    
    db_name="ladapala_$(date +%s)"
    db_user="user_$(date +%s)"
    db_pass=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)
    
    echo "Membuat database $db_name..."
    sudo -u postgres psql -c "CREATE USER $db_user WITH PASSWORD '$db_pass';"
    sudo -u postgres psql -c "CREATE DATABASE $db_name OWNER $db_user;"
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $db_name TO $db_user;"
    
    db_conn="pgsql"
    db_host="127.0.0.1"
    db_port="5432"

elif [ "$db_choice" == "2" ]; then
    # Manual (Bikin DB lokal, tapi namanya input sendiri)
    echo "Pilihan: Manual. Anda akan membuat database PostgreSQL lokal."
    read -p "Masukkan Nama Database baru yang ingin dibuat: " db_name
    read -p "Masukkan Username baru yang ingin dibuat: " db_user
    read -p "Masukkan Password untuk user tersebut: " db_pass

    echo "Membuat database $db_name..."
    sudo -u postgres psql -c "CREATE USER $db_user WITH PASSWORD '$db_pass';"
    sudo -u postgres psql -c "CREATE DATABASE $db_name OWNER $db_user;"
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $db_name TO $db_user;"

    db_conn="pgsql"
    db_host="127.0.0.1"
    db_port="5432"

else
    # Eksternal (Konek ke DB yang sudah eksis)
    echo "Pilihan: Eksternal. Anda akan mengkoneksikan aplikasi ke database yang sudah ada."
    read -p "Masukkan Driver DB (pgsql/mysql/sqlite) [default: pgsql]: " db_conn
    db_conn=${db_conn:-pgsql}
    
    if [ "$db_conn" != "sqlite" ]; then
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
    fi
fi

# Update .env Database config
if [ "$db_conn" == "sqlite" ]; then
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
    touch database/database.sqlite
else
    # Update values in .env
    sed -i "s/^#\?\s*DB_CONNECTION=.*/DB_CONNECTION=$db_conn/" .env
    sed -i "s/^#\?\s*DB_HOST=.*/DB_HOST=$db_host/" .env
    sed -i "s/^#\?\s*DB_PORT=.*/DB_PORT=$db_port/" .env
    sed -i "s/^#\?\s*DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i "s/^#\?\s*DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i "s/^#\?\s*DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env
fi

echo ""
echo "[3] Memperbarui hak akses direktori..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null

echo "[4] Menginstal dependensi PHP (Composer)..."
composer install --optimize-autoloader --no-dev

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
echo "==============================================="
echo "URL Aplikasi : $app_url"
echo "-----------------------------------------------"
echo "KREDENSIAL DATABASE ANDA:"
echo "DB Driver    : $db_conn"
echo "DB Host      : $db_host:$db_port"
echo "DB Name      : $db_name"
echo "DB User      : $db_user"
echo "DB Password  : $db_pass"
echo "-----------------------------------------------"
echo "PERHATIAN: Harap simpan data di atas dengan aman!"
echo "==============================================="
