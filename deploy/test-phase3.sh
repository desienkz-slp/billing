#!/bin/bash
set -e
cd /var/www/ladapala-bill

pkill -f 'artisan serve' 2>/dev/null || true
sleep 1
php artisan route:clear 2>/dev/null; php artisan cache:clear 2>/dev/null
truncate -s 0 storage/logs/laravel.log
nohup php artisan serve --host=0.0.0.0 --port=8080 > /tmp/laravel.log 2>&1 &
sleep 2

# Login
TOKEN=$(curl -s -X POST http://127.0.0.1:8080/api/v1/login \
  -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"username":"superadmin","password":"admin123"}' | python3 -c "import sys,json; print(json.load(sys.stdin)['data']['token'])" 2>/dev/null)
echo "TOKEN: ${TOKEN:0:20}..."

H="Authorization: Bearer $TOKEN"

echo ""; echo "=== CUSTOMER STATS ==="
curl -s http://127.0.0.1:8080/api/v1/customers/stats -H "$H" -H 'Accept: application/json' 2>&1

echo ""; echo "=== CUSTOMERS LIST ==="
curl -s "http://127.0.0.1:8080/api/v1/customers?per_page=3" -H "$H" -H 'Accept: application/json' | python3 -c "
import sys,json
d=json.load(sys.stdin)
print(f'Total: {d.get(\"meta\",{}).get(\"total\",0)}')
for c in d.get('data',[])[:3]:
    print(f'  - {c[\"name\"]} | {c.get(\"package\",{}).get(\"name\",\"?\")} | {c[\"status\"]}')
" 2>/dev/null || echo "parse error"

echo ""; echo "=== CREATE CUSTOMER ==="
CUST=$(curl -s -X POST http://127.0.0.1:8080/api/v1/customers \
  -H "$H" -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"name":"Test API User","phone":"081234567890","address":"Jl. Test No. 1","package_id":1,"area_id":1}')
echo "$CUST" | python3 -c "import sys,json; d=json.load(sys.stdin); print(f'Created: {d[\"data\"][\"name\"]} (ID: {d[\"data\"][\"id\"]})')" 2>/dev/null
CUST_ID=$(echo "$CUST" | python3 -c "import sys,json; print(json.load(sys.stdin)['data']['id'])" 2>/dev/null)

echo ""; echo "=== CREATE PAYMENT ==="
curl -s -X POST http://127.0.0.1:8080/api/v1/payments \
  -H "$H" -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d "{\"customer_id\":$CUST_ID,\"periods\":[\"2026-06\"],\"payment_method\":\"cash\"}" | python3 -c "
import sys,json
d=json.load(sys.stdin)
print(d.get('message','?'))
" 2>/dev/null || echo "Payment response received"

echo ""; echo "=== PAYMENT SUMMARY ==="
curl -s "http://127.0.0.1:8080/api/v1/payments/summary?month=6&year=2026" \
  -H "$H" -H 'Accept: application/json' 2>&1

echo ""; echo "=== PACKAGES ==="
curl -s http://127.0.0.1:8080/api/v1/packages -H "$H" -H 'Accept: application/json' | python3 -c "
import sys,json; [print(f'  - {p[\"name\"]}: {p[\"price_formatted\"]}') for p in json.load(sys.stdin).get('data',[])]
" 2>/dev/null

echo ""; echo "=== ERROR LOG ==="
cat storage/logs/laravel.log | head -3 2>/dev/null || echo "No errors"

echo ""; echo "---ALL_TESTS_DONE---"
