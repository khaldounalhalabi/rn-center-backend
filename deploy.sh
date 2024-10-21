#! /bin/bash
echo "Heading to project directory"

echo "Pulling ..."
git reset --hard
git clean -df
git pull origin

echo "Installing composer packages"
composer install

echo "running migrations"
php artisan migrate

echo "Cleaning cache"
php artisan optimize:clear

echo "Restarting jobs"
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start "laravel-worker:laravel-worker"
supervisorctl restart "laravel-worker:laravel-worker"
