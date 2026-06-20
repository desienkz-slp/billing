# Deploy UI Fixes ke Server Dev 172.18.20.136

$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Deploy UI Fixes & Income Created By   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# 1. SCP Controllers
Write-Host "1. Uploading Controllers..." -ForegroundColor Yellow
scp .\app\Http\Controllers\Web\IncomeController.php "$SERVER`:$REMOTE_PATH/app/Http/Controllers/Web/IncomeController.php"

# 2. SCP JS Components and Pages (optional but good for tracking, though build covers it)
Write-Host "2. Uploading JS Files (source)..." -ForegroundColor Yellow
scp .\resources\js\Pages\Reports\Income.vue "$SERVER`:$REMOTE_PATH/resources/js/Pages/Reports/Income.vue"
scp .\resources\js\Pages\Cust\Customers\Components\CustomerForm.vue "$SERVER`:$REMOTE_PATH/resources/js/Pages/Cust/Customers/Components/CustomerForm.vue"
scp .\resources\js\Components\Modal.vue "$SERVER`:$REMOTE_PATH/resources/js/Components/Modal.vue"
scp .\resources\css\app.css "$SERVER`:$REMOTE_PATH/resources/css/app.css"

# 3. SCP Build Directory
Write-Host "3. Uploading Frontend Build..." -ForegroundColor Yellow
ssh $SERVER "rm -rf $REMOTE_PATH/public/build/*"
scp -r .\public\build\* "$SERVER`:$REMOTE_PATH/public/build/"

# 4. Permissions & Cache Clear
Write-Host "4. Resetting Permissions and Cache..." -ForegroundColor Yellow
ssh $SERVER "cd $REMOTE_PATH && chown -R www-data:www-data public/build storage bootstrap/cache && chmod -R 775 public/build storage bootstrap/cache"

Write-Host "========================================" -ForegroundColor Green
Write-Host "         Deploy Selesai!                " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
