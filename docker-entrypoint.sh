#!/bin/sh
set -e

echo "=========================================================="
echo " Starting Cakrawala Digital Library (Koyeb Deployment)"
echo "=========================================================="

echo "--> 1. Clearing config & route cache..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "--> 2. Ensuring storage symlink exists..."
php artisan storage:link || true

echo "--> 3. Running database migrations on Neon.tech..."
php artisan migrate --force || true

echo "--> 4. Seeding default data if empty..."
php artisan db:seed --force || true

echo "--> 5. Starting FrankenPHP web server on port ${PORT:-8000}..."
exec frankenphp php-server -l 0.0.0.0:${PORT:-8000} -r public/
