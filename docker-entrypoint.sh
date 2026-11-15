#!/bin/sh
set -e

echo "=========================================================="
echo " Starting Cakrawala Digital Library (Production Deployment)"
echo "=========================================================="

echo "--> 1. Clearing config & route cache..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "--> 2. Discovering Laravel packages..."
php artisan package:discover --ansi || true

echo "--> 3. Ensuring storage symlink exists..."
php artisan storage:link || true

echo "--> Database migrations and seeding are not run automatically."
echo "--> Apply schema changes only through an approved release step."

PORT_LISTEN="${PORT:-8000}"
echo "--> Starting web server on port ${PORT_LISTEN}..."

if frankenphp --version > /dev/null 2>&1; then
    echo "--> Running with FrankenPHP server..."
    exec frankenphp php-server -l :${PORT_LISTEN} -r public/
else
    echo "--> [Notice] FrankenPHP binary restricted, falling back to Laravel native server..."
    exec php -d upload_max_filesize=100M -d post_max_size=120M -d memory_limit=256M artisan serve --host=0.0.0.0 --port=${PORT_LISTEN}
fi
