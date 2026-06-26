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

NEEDS_UPDATE=0

# Cek versi PHP (minimal 8.2)
if check_command php; then
    php_version=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null)
    if [ "$(printf '%s\n' "8.2" "$php_version" | sort -V | head -n1)" != "8.2" ]; then
        echo "⚠️  PHP versi lawas terdeteksi ($php_version). Membutuhkan >= 8.2"
        NEEDS_UPDATE=1
    fi
else
    NEEDS_UPDATE=1
fi

# Cek versi Node (minimal 18)
if check_command node; then
    node_version=$(node -v 2>/dev/null | sed 's/v//' | cut -d. -f1)
    if [ "$node_version" -lt 18 ]; then
        echo "⚠️  Node.js versi lawas terdeteksi (v$node_version). Membutuhkan >= 18"
        NEEDS_UPDATE=1
    fi
else
    NEEDS_UPDATE=1
fi

check_command composer || NEEDS_UPDATE=1
check_command npm || NEEDS_UPDATE=1
check_command psql || NEEDS_UPDATE=1
check_command nginx || NEEDS_UPDATE=1
check_command supervisorctl || NEEDS_UPDATE=1

if [ $NEEDS_UPDATE -eq 1 ]; then
    echo "🚨 PERINGATAN: Server Anda tidak memiliki dependensi atau versinya terlalu lawas!"
    echo "Sistem membutuhkan minimal: PHP 8.2+, Node.js 18+, PostgreSQL, Nginx, dan Supervisor."
    echo "Skipping dependency installation for one-command install."
fi

# 1. Copy .env
if [ ! -f .env ]; then
    echo "[1] Membuat file .env otomatis..."
    cp .env.example .env
fi

# Set APP_URL
app_url="http://172.18.20.139"
app_url_escaped=$(echo "$app_url" | sed 's/\//\\\//g')
sed -i "s/^APP_URL=.*/APP_URL=$app_url_escaped/" .env

echo ""
echo "[2] Pengaturan Database"
echo "Pilih mode konfigurasi database:"
echo "1) Otomatis  (Membuat DB & User PostgreSQL lokal dengan nama acak)"
echo "2) Manual    (Membuat DB & User PostgreSQL lokal dengan nama sesuai input Anda)"
echo "3) Eksternal (Menggunakan Database yang sudah ada di server lain/lokal)"
db_choice="2"

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
    db_name="billing_db"
    db_user="ladapala"
    db_pass="secret"

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


echo "[4] Menginstal dependensi PHP (Composer)..."
composer install --optimize-autoloader --no-dev

# Hapus npm karena ini versi production yang sudah dibuild
# (Frontend sudah terkompilasi di public/build/)

echo "[5] Generate Application Key..."
php artisan key:generate --force

echo "[6] Menjalankan Migrasi Database..."
php artisan migrate --force

echo "[7] Membuat Akun Superadmin Default..."
php artisan make:sysadmin

echo "[8] Membersihkan Cache..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "[9] Konfigurasi Nginx Web Server..."
if [ -d "/etc/nginx/sites-available" ]; then
    domain_stripped=$(echo "$app_url" | sed -e 's|^[^/]*//||' -e 's|/.*$||')
    app_dir=$(pwd)
    
    cat <<EOF > /etc/nginx/sites-available/billing
server {
    listen 80;
    server_name $domain_stripped;
    root $app_dir/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    ln -sf /etc/nginx/sites-available/billing /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl restart nginx
    echo "Virtual Host Nginx berhasil dikonfigurasi untuk $domain_stripped di $app_dir/public"
fi

echo "[10] Memperbarui hak akses direktori..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data /var/www/billing 2>/dev/null

echo "==============================================="
echo "   Instalasi Selesai! Sistem siap digunakan."
echo "==============================================="
echo "URL Aplikasi : $app_url"
echo "Akun Default : sysadmin / sysadmin"
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
