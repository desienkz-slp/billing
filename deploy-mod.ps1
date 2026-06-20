$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "Deploying modified files..."

# Ensure directory for GeniAcsService
ssh $SERVER "mkdir -p $REMOTE_PATH/app/Services"

# PHP Files
scp "app\Models\Customer.php" "${SERVER}:${REMOTE_PATH}/app/Models/"
scp "app\Http\Controllers\Web\PaymentApiController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "app\Http\Controllers\Web\CutiController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "app\Http\Controllers\Web\IsolirController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "app\Http\Controllers\Web\ConfigController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "app\Http\Controllers\DashboardController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/"
scp "app\Services\PaymentService.php" "${SERVER}:${REMOTE_PATH}/app/Services/"
scp "app\Services\GeniAcsService.php" "${SERVER}:${REMOTE_PATH}/app/Services/"
scp "app\Services\MikroTik\MikroTikService.php" "${SERVER}:${REMOTE_PATH}/app/Services/MikroTik/"
scp "routes\web.php" "${SERVER}:${REMOTE_PATH}/routes/"

# Migrations
scp "database\migrations\2026_06_19_180200_add_bhp_admin_to_customers_table.php" "${SERVER}:${REMOTE_PATH}/database/migrations/"

# Vue Files
ssh $SERVER "mkdir -p $REMOTE_PATH/resources/js/Pages/Customers/Components"
scp "resources\js\Pages\Dashboard.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/"
scp "resources\js\Pages\Settings\Profil.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Settings/"
scp "resources\js\Pages\Customers\Components\PaymentModal.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Customers/Components/"
scp "resources\js\Pages\Customers\Components\WaModal.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Customers/Components/"
scp "resources\js\Pages\Customers\Components\CustomerForm.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Customers/Components/"

# Public Build (zip and extract is faster, but scp -r works)
# We will use scp -r
scp -r "public\build" "${SERVER}:${REMOTE_PATH}/public/"

Write-Host "Running migrations..."
ssh $SERVER "cd $REMOTE_PATH && php artisan migrate --force"

Write-Host "Deploy complete!"
