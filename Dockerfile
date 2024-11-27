# Dockerfile

# Use the official PHP 8.2 image with extensions and Composer installed
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Add the cron job
#RUN echo "0 0 * * * www-data php /var/www/artisan fetch:articles >> /var/log/cron.log 2>&1" > /etc/cron.d/fetch_articles
RUN echo "* * * * * www-data php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/schedule

# Set permissions and register cron job
RUN chmod 0644 /etc/cron.d/schedule \
    && crontab /etc/cron.d/schedule

# Create the log file and give it permissions
RUN touch /var/log/cron.log && chmod 0666 /var/log/cron.log

# Ensure the required Laravel directories have correct permissions
RUN mkdir -p /var/www/storage/logs /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && find /var/www/storage /var/www/bootstrap/cache -type d -exec chmod 775 {} \; \
    && find /var/www/storage /var/www/bootstrap/cache -type f -exec chmod 664 {} \; \

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Copy the entrypoint script
COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the entrypoint script to start both services
ENTRYPOINT ["docker-entrypoint.sh"]

