#!/bin/sh
set -e

# Ensure Laravel storage directories exist (volumes may mount empty directories)
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Sanity check: the app cannot serve traffic without public/index.php. If it is
# missing the build context dropped public/ or a volume is shadowing it — surface
# a clear message instead of a cryptic symlink() error further down.
if [ ! -f /var/www/html/public/index.php ]; then
    echo "ERROR: /var/www/html/public/index.php is missing." >&2
    echo "       The 'public' directory was not shipped in the image or is shadowed by a volume mount." >&2
    echo "       Check the build context and any mounts targeting /var/www/html or /var/www/html/public." >&2
fi

# Create public/storage symlink if it does not exist.
# Ensure the link's parent directory exists first: if a volume/bind mount or a
# broken build context leaves /var/www/html/public absent, symlink() fails with
# "No such file or directory" and (under set -e) kills the container.
mkdir -p /var/www/html/public
if [ ! -L /var/www/html/public/storage ]; then
    # --force recreates a stale/broken link (e.g. one baked into the build context).
    php artisan storage:link --force || echo "WARNING: storage:link failed; continuing without public/storage symlink"
fi

# Detect whether this container is running the web server
IS_WEB=false
for arg in "$@"; do
    case "$arg" in
        *supervisord*) IS_WEB=true ;;
    esac
done

if [ "$IS_WEB" = "true" ]; then
    # Create PHP-FPM log directory
    mkdir -p /var/log/php-fpm

    # Cache Laravel configuration, routes, and views in production
    if [ "$APP_ENV" = "production" ]; then
        php artisan optimize
    fi

    # Run migrations if enabled (useful for Dokploy deploys)
    if [ "$RUN_MIGRATIONS" = "true" ]; then
        echo "Running database migrations..."
        php artisan migrate --force
    fi
fi

exec "$@"
