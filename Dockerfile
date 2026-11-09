FROM dunglas/frankenphp:php8.3-alpine

# Install PHP extensions required by Laravel & PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    intl \
    zip \
    opcache \
    pcntl

# Install dos2unix to prevent Windows CRLF issues
RUN apk add --no-cache dos2unix

WORKDIR /app

# Copy application files
COPY . /app

# Ensure entrypoint script has execution permissions and LF line endings
RUN dos2unix /app/docker-entrypoint.sh && chmod +x /app/docker-entrypoint.sh

# Install composer production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set up writable permissions for Laravel storage and cache
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
