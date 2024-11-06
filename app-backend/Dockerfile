# Use the official PHP image with Apache
FROM php:8.2-apache

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# Set a default ServerName to suppress the warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the working directory
WORKDIR /var/www/html

# Copy the Composer executable from Composer’s official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy the application code
COPY . .

# Set the DocumentRoot to Laravel's 'public' directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Ensure Apache has the necessary permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
