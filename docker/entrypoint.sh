#!/bin/sh
set -e

# Substitute PORT into nginx configuration if set by Render
PORT="${PORT:-8080}"
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/nginx.conf

# Ensure storage directories exist and have permissions
mkdir -p /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear cached configs to ensure runtime environment variables are read
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

exec "$@"
