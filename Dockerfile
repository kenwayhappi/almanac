FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    git \
    unzip \
    nodejs \
    npm

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip bcmath intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (better Docker layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files and install Node dependencies
COPY package.json vite.config.js ./

RUN npm install

# Copy the rest of the application code
COPY . .

# Re-run composer scripts (package:discover, etc.)
RUN composer dump-autoload --optimize --no-interaction

# Build frontend assets (Vite + Tailwind)
RUN npm run build

# Create storage link
RUN php artisan storage:link || true

# Copy server configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make entrypoint executable
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Permissions
RUN mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache/data storage/logs bootstrap/cache \
    && touch storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# PHP OPcache production settings
RUN echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.max_accelerated_files=10000' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

EXPOSE 10000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
