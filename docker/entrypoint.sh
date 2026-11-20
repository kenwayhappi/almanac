#!/bin/sh
set -e

# Render uses PORT env variable (default 10000)
PORT="${PORT:-10000}"
sed -i "s/listen [0-9]*;/listen ${PORT};/g" /etc/nginx/nginx.conf

# Ensure storage directories exist and have permissions
mkdir -p /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear cached configs to ensure runtime environment variables are read
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Cache config and routes for production performance
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run migrations automatically (safe with --force)
php artisan migrate --force 2>/dev/null || true

echo "==> Almanach Laravel ready on port ${PORT}"

exec "$@"
