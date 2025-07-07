# Multi-stage build for Laravel + Vue.js application
FROM node:18-alpine AS frontend-builder

WORKDIR /app

# Copy package files
COPY package*.json ./
RUN npm ci --only=production

# Copy source code
COPY resources/ ./resources/
COPY vite.config.js ./
COPY public/ ./public/

# Build frontend assets
RUN npm run build

# PHP/Laravel stage
FROM php:8.2-fpm-alpine AS backend

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy PHP files
COPY . .
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Production stage
FROM nginx:alpine AS production

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copy application files
COPY --from=backend /var/www /var/www

# Copy PHP-FPM configuration
COPY --from=backend /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start services
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"] 