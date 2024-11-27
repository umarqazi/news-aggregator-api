#!/bin/bash

cd /var/www

php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

# Start cron in the background
service cron start

# Start php-fpm as the main process
exec php-fpm
