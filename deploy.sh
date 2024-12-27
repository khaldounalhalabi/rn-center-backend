#! /bin/bash
echo "Deploying ((((PRODUCTION)))) branch"

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
sudo supervisorctl start "POM_BACKEND_QUEUE_WORKER_PRODUCTION:*"
sudo supervisorctl restart "POM_BACKEND_QUEUE_WORKER_PRODUCTION:*"
