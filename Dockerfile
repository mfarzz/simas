FROM php:8.1-apache

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

# Copy source code
COPY ./ /var/www/html

# Permission (Laravel butuh write access)
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

USER www-data
