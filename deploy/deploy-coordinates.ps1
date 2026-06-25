# Deploy Koordinat Pelanggan ke Server Dev
# Jalankan: .\deploy\deploy-coordinates.ps1

$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Deploy Koordinat Pelanggan" -ForegroundColor Cyan
Write-Host "  Lokasi: Jatitengah, Selopuro, Blitar" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Step 1: Pastikan direktori ada
Write-Host "`n[1/4] Membuat direktori di server..." -ForegroundColor Yellow
ssh $SERVER "mkdir -p $REMOTE_PATH/app/Console/Commands && echo DIR_OK"

# Step 2: Upload Artisan Command
Write-Host "`n[2/4] Upload GenerateCustomerCoordinates.php..." -ForegroundColor Yellow
scp "app\Console\Commands\GenerateCustomerCoordinates.php" "${SERVER}:${REMOTE_PATH}/app/Console/Commands/"
Write-Host "  Done." -ForegroundColor Green

# Step 3: Jalankan command
Write-Host "`n[3/4] Menjalankan php artisan customers:generate-coordinates..." -ForegroundColor Yellow
ssh $SERVER "cd $REMOTE_PATH && php artisan customers:generate-coordinates"

# Step 4: Verifikasi
Write-Host "`n[4/4] Verifikasi koordinat..." -ForegroundColor Yellow
ssh $SERVER "cd $REMOTE_PATH && php artisan tinker --execute=""echo App\\Models\\CustomerCoordinate::count() . ' koordinat tersedia di database' . PHP_EOL;"""

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  DEPLOY SELESAI!" -ForegroundColor Green
Write-Host "  Cek Map: http://172.18.20.136/map" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
