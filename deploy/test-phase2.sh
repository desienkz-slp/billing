#!/bin/bash
set -e

cd /var/www/ladapala-bill

# Kill existing server
pkill -f 'artisan serve' 2>/dev/null || true
sleep 1

# Start server
nohup php artisan serve --host=0.0.0.0 --port=8080 > /tmp/laravel.log 2>&1 &
sleep 2

# Test login page
echo "LOGIN_PAGE: $(curl -s http://127.0.0.1:8080/login -o /dev/null -w '%{http_code}')"

# Test API login
echo "API_LOGIN:"
curl -s -X POST http://127.0.0.1:8080/api/v1/login \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"username":"admin","password":"admin123"}' 2>&1

echo ""
echo "---DONE---"
