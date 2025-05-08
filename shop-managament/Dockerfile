# Base image PHP + Composer
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup: clear caches, run migrations + seed
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan optimize && \
    php artisan migrate --force && \
    php artisan db:seed --force

# File permission (production safe)
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose port 9000 (for php-fpm or 8080 if using artisan serve)
EXPOSE 8080

# Default command (php-fpm, or use artisan serve below)
CMD ["php-fpm"]
