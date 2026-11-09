#!/bin/sh
set -e

echo "=========================================================="
echo " Starting Cakrawala Digital Library (Production Deployment)"
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

PORT_LISTEN="${PORT:-8000}"
echo "--> 5. Starting FrankenPHP web server on port ${PORT_LISTEN}..."
exec frankenphp php-server -l :${PORT_LISTEN} -r public/
