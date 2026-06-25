#!/bin/bash

echo "==============================================="
echo "   LadaPala-Bill Production Setup Script"
echo "==============================================="

echo "[1/6] Memperbarui hak akses direktori..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null

echo "[2/6] Menginstal dependensi PHP (Composer)..."
composer install --optimize-autoloader --no-dev

echo "[3/6] Menginstal dependensi Frontend (Node.js/NPM)..."
npm install

echo "[4/6] Membangun aset Frontend (Vite)..."
npm run build

echo "[5/6] Menjalankan Migrasi Database..."
php artisan migrate --force

echo "[6/6] Membersihkan dan membuat Cache konfigurasi..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "==============================================="
echo "   Instalasi Selesai! Sistem siap digunakan."
echo "==============================================="
