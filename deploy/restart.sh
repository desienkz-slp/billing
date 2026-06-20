#!/bin/bash
cd /var/www/ladapala-bill
pkill -f 'artisan serve' 2>/dev/null || true
sleep 1
php artisan route:clear 2>&1
php artisan view:clear 2>&1
php artisan cache:clear 2>&1
truncate -s 0 storage/logs/laravel.log
nohup php artisan serve --host=0.0.0.0 --port=8080 > /tmp/laravel.log 2>&1 &
sleep 2

echo "=== Login Page ==="
curl -s -o /dev/null -w "HTTP %{http_code}" http://127.0.0.1:8080/login
echo ""

echo "=== Error Log ==="
cat storage/logs/laravel.log | head -5 2>/dev/null || echo "No errors"
echo ""
echo "---DONE---"
