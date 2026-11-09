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

# Install system dependencies (git for composer, dos2unix for Windows CRLF line endings, libcap for setcap)
RUN apk add --no-cache dos2unix git libcap && \
    (setcap -r /usr/local/bin/frankenphp 2>/dev/null || true) && \
    (setcap -r $(which frankenphp) 2>/dev/null || true)

# Configure PHP upload limits (100MB PDF uploads) for FrankenPHP and CLI
RUN printf "upload_max_filesize = 100M\npost_max_size = 120M\nmemory_limit = 256M\nmax_execution_time = 300\nmax_input_time = 300\n" > "$PHP_INI_DIR/conf.d/99-uploads.ini" && \
    mkdir -p /etc/frankenphp && \
    cp "$PHP_INI_DIR/conf.d/99-uploads.ini" /etc/frankenphp/php.ini

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
