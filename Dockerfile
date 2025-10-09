# ------------------------------------------------------------
# Stage 1: Build PHP dependencies with Composer
# ------------------------------------------------------------
FROM php:8.3-fpm AS builder

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev libxml2-dev zlib1g-dev g++ locales curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip gd pdo_mysql bcmath opcache \
    && docker-php-ext-enable intl zip gd opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy only composer files first to leverage Docker caching
COPY composer.json composer.lock ./

# Install dependencies (no dev for production)
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copy the rest of the application code
COPY . .

# ------------------------------------------------------------
# Stage 2: Production image with Nginx + PHP-FPM
# ------------------------------------------------------------
FROM php:8.3-fpm

# Install system tools + Nginx + Supervisor
RUN apt-get update && apt-get install -y \
    nginx supervisor libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip gd pdo_mysql bcmath opcache \
    && docker-php-ext-enable intl zip gd opcache

# Copy application from builder stage
WORKDIR /var/www/html
COPY --from=builder /var/www/html /var/www/html

# Copy nginx and supervisor configs
COPY .docker/nginx.conf /etc/nginx/sites-available/default
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Start Supervisor (runs both PHP-FPM + Nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
