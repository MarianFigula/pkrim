FROM php:8.0-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/

# Copy project files into the container
COPY . /var/www/html/

# Set permissions for public/arts directory
RUN mkdir -p /var/www/html/public/arts && \
    chmod -R 777 /var/www/html/public/arts && \
    chown -R www-data:www-data /var/www/html/public/arts
    
# Install dependencies with Composer
RUN composer install

EXPOSE 80