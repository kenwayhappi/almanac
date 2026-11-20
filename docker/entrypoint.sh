#!/bin/sh
set -e

# Render uses PORT env variable (default 10000)
PORT="${PORT:-10000}"
sed -i "s/listen [0-9]*;/listen ${PORT};/g" /etc/nginx/nginx.conf

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

touch /var/www/html/storage/logs/laravel.log

# Clear and cache configs for production
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run migrations automatically
php artisan migrate --force 2>/dev/null || true

# Set permissions AFTER all artisan commands so www-data owns everything
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Almanach Laravel ready on port ${PORT}"

exec "$@"
