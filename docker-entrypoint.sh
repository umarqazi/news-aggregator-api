#!/bin/bash

cd /var/www/html || exit

php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear

# Ensure correct permissions for storage and cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start cron in the background
service cron start

# Start php-fpm as the main process
exec php-fpm
