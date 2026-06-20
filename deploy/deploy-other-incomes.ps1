# Deploy Other Incomes ke Server Dev 172.18.20.136

$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Deploy Other Incomes ke Server Dev" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Upload files
Write-Host "`n[1/6] Uploading Routes (web.php)..." -ForegroundColor Yellow
scp "routes\web.php" "${SERVER}:${REMOTE_PATH}/routes/"

Write-Host "`n[2/6] Uploading Controllers..." -ForegroundColor Yellow
scp "app\Http\Controllers\Web\ReportController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "app\Http\Controllers\Web\OtherIncomeController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"

Write-Host "`n[3/6] Uploading Models..." -ForegroundColor Yellow
scp "app\Models\OtherIncome.php" "${SERVER}:${REMOTE_PATH}/app/Models/"

Write-Host "`n[4/6] Uploading Migrations..." -ForegroundColor Yellow
scp "database\migrations\2026_06_20_143733_create_other_incomes_table.php" "${SERVER}:${REMOTE_PATH}/database/migrations/"

Write-Host "`n[5/6] Uploading build frontend (public/build)..." -ForegroundColor Yellow
ssh $SERVER "mkdir -p ${REMOTE_PATH}/public/build"
scp -r "public\build\*" "${SERVER}:${REMOTE_PATH}/public/build/"

# Run commands
Write-Host "`n[6/6] Menjalankan artisan commands..." -ForegroundColor Yellow
ssh $SERVER "cd $REMOTE_PATH && php artisan migrate --force && php artisan optimize:clear"

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  DEPLOY SELESAI!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
