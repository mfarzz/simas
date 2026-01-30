FROM php:8.4-apache

# Enable Apache rewrite (WAJIB untuk Laravel)
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev \
    libonig-dev libxml2-dev libpq-dev zip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (Laravel + PostgreSQL)
RUN docker-php-ext-install \
    pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set Apache DocumentRoot ke public Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-scripts --no-autoloader

# Copy source code
COPY ./ /var/www/html

# Generate Class map
RUN composer dump-autoload --optimize

# Permission (Laravel butuh write access)
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Laravel optimization - cache config, routes, and views
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
