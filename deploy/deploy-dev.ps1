# Deploy ke Server Dev 172.18.20.136
# Jalankan: .\deploy\deploy-dev.ps1

$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Deploy ke Server Dev" -ForegroundColor Cyan
Write-Host "  (masukkan password setiap kali diminta)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Step 1: Buat direktori yang belum ada
Write-Host "`n[1/4] Membuat direktori di server..." -ForegroundColor Yellow
ssh $SERVER "mkdir -p $REMOTE_PATH/app/Console/Commands && echo DIR_OK"

# Step 2: Upload MakeSysadmin.php
Write-Host "`n[2/4] Upload MakeSysadmin.php..." -ForegroundColor Yellow
scp "app\Console\Commands\MakeSysadmin.php" "${SERVER}:${REMOTE_PATH}/app/Console/Commands/"
Write-Host "  Done." -ForegroundColor Green

# Step 3: Upload DatabaseSeeder.php
Write-Host "`n[3/4] Upload DatabaseSeeder.php..." -ForegroundColor Yellow
scp "database\seeders\DatabaseSeeder.php" "${SERVER}:${REMOTE_PATH}/database/seeders/"
Write-Host "  Done." -ForegroundColor Green

# Step 4: Jalankan artisan command
Write-Host "`n[4/4] Menjalankan php artisan make:sysadmin..." -ForegroundColor Yellow
ssh $SERVER "cd $REMOTE_PATH && php artisan make:sysadmin"

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  DEPLOY SELESAI!" -ForegroundColor Green
Write-Host "  Login: http://172.18.20.136" -ForegroundColor Green
Write-Host "  Username: sysadmin" -ForegroundColor Green
Write-Host "  Password: sysadmin" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
