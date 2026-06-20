#!/bin/bash
set -e

# Create PostgreSQL user and database
sudo -u postgres psql -c "CREATE USER ladapala WITH PASSWORD 'secret';" 2>/dev/null || echo "User already exists"
sudo -u postgres psql -c "CREATE DATABASE billing_db OWNER ladapala;" 2>/dev/null || echo "Database already exists"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE billing_db TO ladapala;"
sudo -u postgres psql -d billing_db -c "GRANT ALL ON SCHEMA public TO ladapala;"
echo "DB_READY"

# Update .env for native install
cd /var/www/ladapala-bill
sed -i 's/DB_HOST=postgres/DB_HOST=127.0.0.1/' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/REDIS_HOST=redis/REDIS_HOST=127.0.0.1/' .env
sed -i 's/REDIS_HOST=.*/REDIS_HOST=127.0.0.1/' .env
sed -i 's/CACHE_STORE=redis/CACHE_STORE=redis/' .env
sed -i 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=redis/' .env
sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=redis/' .env
echo "ENV_UPDATED"

# Set permissions
chmod -R 775 storage bootstrap/cache
echo "PERMS_SET"

# Run migrations
php artisan migrate --force --no-interaction 2>&1
echo "MIGRATE_DONE"

# Run seeders
php artisan db:seed --force --no-interaction 2>&1
echo "SEED_DONE"

# Start dev server on 0.0.0.0:80
echo "ALL_COMPLETE - Starting server on port 80..."
nohup php artisan serve --host=0.0.0.0 --port=8080 > /tmp/laravel.log 2>&1 &
echo "SERVER_PID=$!"
echo "Access: http://172.18.20.136:8080"
