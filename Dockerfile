# ============================
# PHP 8.2 + FrankenPHP + Octane
# ============================
FROM dunglas/frankenphp:1.7.0-php8.2-alpine

# ----------------------------
# System dependencies + PHP extensions
# ----------------------------
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev \
    mariadb-client \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
        xml

# ----------------------------
# Composer
# ----------------------------
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ----------------------------
# Redis PHP extension
# ----------------------------
RUN apk add --no-cache --virtual .build-deps \
        autoconf \
        g++ \
        make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

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

# Install TCPDF Arabic fonts
RUN composer run add-tcpdf-fonts

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

USER root

RUN chmod +x /usr/local/bin/entrypoint

USER www-data

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
