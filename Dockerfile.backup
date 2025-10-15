FROM php:8.3-fpm

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libxml2-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip gd pdo_mysql pdo_pgsql pgsql bcmath opcache


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-dev

# Copy the rest of the app
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel will run on port 8080 in Railway
EXPOSE 8080

# Run Laravel's built-in server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
