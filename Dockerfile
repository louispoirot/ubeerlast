FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p /var/log/nginx /var/cache/nginx /app/var /app/var/cache /app/var/log \
    && chown -R www-data:www-data /app \
    && chmod -R 777 /app/var \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/cache/nginx

# Set environment variables
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PORT=80

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose port (Railway uses PORT env variable)
EXPOSE ${PORT}

# Start Nginx & PHP-FPM
CMD sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/nginx.conf && php-fpm -D && nginx -g 'daemon off;'
