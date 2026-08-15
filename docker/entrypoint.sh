#!/bin/sh

set -e

# --------------------------------------------------
# Worker / scheduler containers
# --------------------------------------------------
# The same image is used by app, queue and scheduler.
# If Docker asks us to execute "php ...", run it directly
# instead of performing web-container initialization.
# --------------------------------------------------

if [ "$1" = "php" ]; then
    exec "$@"
fi


# --------------------------------------------------
# Web container initialization
# --------------------------------------------------

echo "Initializing Laravel..."


# Remove stale Laravel bootstrap cache.
#
# This allows the application to boot even if packages
# were added/removed between deployments.
rm -f \
    bootstrap/cache/packages.php \
    bootstrap/cache/services.php \
    bootstrap/cache/config.php \
    bootstrap/cache/routes-v7.php


# --------------------------------------------------
# Storage symlink
# --------------------------------------------------

if [ ! -L public/storage ]; then
    php artisan storage:link
fi


# --------------------------------------------------
# Laravel package discovery
# --------------------------------------------------

php artisan package:discover --ansi


# --------------------------------------------------
# Production caches
# --------------------------------------------------

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running database migrations..."
php artisan migrate --force


# --------------------------------------------------
# Start FrankenPHP / Laravel Octane
# --------------------------------------------------

echo "Starting Laravel Octane with FrankenPHP..."

exec php artisan octane:frankenphp --port=80
