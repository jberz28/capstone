# Use official PHP + Apache image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install required PHP extensions for Laravel (MySQL + PostgreSQL)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Enable Apache mod_rewrite (required for Laravel routing)
RUN a2enmod rewrite

# Change Apache DocumentRoot to Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess to work inside Laravel's public folder
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Fix permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
