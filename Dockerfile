# ============================
# PHP 8.2 + FrankenPHP + Octane
# ============================
FROM dunglas/frankenphp:1.7.0-php8.2-alpine

# ----------------------------
# System tools (not PHP extensions)
# ----------------------------
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    curl \
    mariadb-client \
    su-exec

# ----------------------------
# Composer
# ----------------------------
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ----------------------------
# PHP extensions via the bundled installer
# ----------------------------
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache \
    xml \
    redis

# ----------------------------
# PHP configuration
# ----------------------------
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# ----------------------------
# Application
# ----------------------------
WORKDIR /var/www

# Copy Composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Copy application
COPY . .

# ----------------------------
# Laravel runtime directories
# ----------------------------
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www

# ----------------------------
# Entrypoint
# ----------------------------
COPY --chown=www-data:www-data \
    docker/entrypoint.sh \
    /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint
# no USER line here — container starts as root so the entrypoint
# can chown the mounted volumes, then drops privileges to www-data itself

# ----------------------------
# FrankenPHP
# ----------------------------
EXPOSE 80
HEALTHCHECK \
    --interval=30s \
    --timeout=5s \
    --start-period=30s \
    --retries=3 \
    CMD curl -fsS http://localhost:80/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["php", "artisan", "octane:frankenphp", "--port=80"]
