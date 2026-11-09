FROM dunglas/frankenphp:1-php8.4-alpine

# Set environment variables for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer

# Install Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP extensions required by Laravel & PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    intl \
    zip \
    opcache \
    pcntl \
    bcmath \
    mbstring

# Install system dependencies (git for composer, dos2unix for Windows CRLF line endings)
RUN apk add --no-cache dos2unix git

WORKDIR /app

# Copy application files
COPY . /app

# Ensure entrypoint script has execution permissions and LF line endings
RUN dos2unix /app/docker-entrypoint.sh && chmod +x /app/docker-entrypoint.sh

# Install composer production dependencies without running artisan scripts during build
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Set up writable permissions for Laravel storage and cache
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

EXPOSE 8000 10000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
