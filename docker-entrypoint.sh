#!/bin/bash

cd /var/www || exit

php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

# Ensure correct permissions for storage and cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

#chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
#find /var/www/storage /var/www/bootstrap/cache -type d -exec chmod 775 {} \;
#find /var/www/storage /var/www/bootstrap/cache -type f -exec chmod 664 {} \;

# Start cron in the background
service cron start

# Start php-fpm as the main process
exec php-fpm
