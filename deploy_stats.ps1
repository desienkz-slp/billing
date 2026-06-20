$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

Write-Host "Deploying Statistics Module..."

ssh -o BatchMode=yes $SERVER "mkdir -p $REMOTE_PATH/resources/js/Pages/Reports/Components"

scp "routes\web.php" "${SERVER}:${REMOTE_PATH}/routes/"
scp "app\Http\Controllers\Web\StatisticController.php" "${SERVER}:${REMOTE_PATH}/app/Http/Controllers/Web/"
scp "resources\js\Components\Sidebar.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Components/"
scp "resources\js\Pages\Reports\Statistics.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Reports/"
scp "resources\js\Pages\Reports\Components\KpiCard.vue" "${SERVER}:${REMOTE_PATH}/resources/js/Pages/Reports/Components/"
scp "package.json" "${SERVER}:${REMOTE_PATH}/"
scp "package-lock.json" "${SERVER}:${REMOTE_PATH}/"

Write-Host "Deploying public build..."
scp -r "public\build" "${SERVER}:${REMOTE_PATH}/public/"

Write-Host "Running commands on server..."
ssh -o BatchMode=yes $SERVER "cd $REMOTE_PATH && npm install && php artisan route:clear && php artisan cache:clear && php artisan view:clear"

Write-Host "Deploy complete!"
