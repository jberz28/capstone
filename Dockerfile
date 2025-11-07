# Use official PHP + Apache image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install PHP extensions and utilities
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    zip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy the full project (including artisan) BEFORE running composer
COPY . .

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader \
    && rm composer-setup.php

# Update Apache DocumentRoot to Laravel's public folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
