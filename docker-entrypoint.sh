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

echo "--> 4. Running database migrations on Neon.tech..."
php artisan migrate --force || true

echo "--> 5. Seeding default data if empty..."
php artisan db:seed --force || true

PORT_LISTEN="${PORT:-8000}"
echo "--> 6. Starting FrankenPHP web server on port ${PORT_LISTEN}..."
exec frankenphp php-server -l :${PORT_LISTEN} -r public/
