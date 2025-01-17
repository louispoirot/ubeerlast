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

# Copy project files
COPY . /app/

# Create necessary directories
RUN mkdir -p /app/var /app/public \
    && mkdir -p /var/log/nginx \
    && mkdir -p /var/cache/nginx

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/public \
    && chmod -R 777 /app/var \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/cache/nginx

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set environment variables
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose port
EXPOSE 80

# Start Nginx & PHP-FPM
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
