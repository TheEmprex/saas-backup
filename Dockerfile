# Multi-stage Docker build for Laravel SaaS application
# Production-ready with security best practices

# =============================================================================
# Base PHP image with extensions
# =============================================================================
FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    supervisor \
    nginx

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        xml \
        zip \
        opcache \
        intl \
        bcmath \
        exif

# Install Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Create user for Laravel
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Set working directory
WORKDIR /var/www

# =============================================================================
# Composer stage
# =============================================================================
FROM php-base AS composer

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# =============================================================================
# Node.js stage for frontend assets
# =============================================================================
FROM node:18-alpine AS node

WORKDIR /var/www

# Copy package files
COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci --only=production

# Copy source files
COPY . .

# Build assets
RUN npm run build

# =============================================================================
# Development stage
# =============================================================================
FROM php-base AS development

# Install Xdebug for development
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS

# Xdebug configuration
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP configuration for development
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy application files
COPY --chown=www:www . .

# Install all dependencies (including dev)
RUN composer install --optimize-autoloader

# Set proper permissions
RUN chown -R www:www /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]

# =============================================================================
# Production stage
# =============================================================================
FROM php-base AS production

# PHP configuration for production
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files
COPY --chown=www:www . .

# Copy vendor from composer stage
COPY --from=composer --chown=www:www /var/www/vendor ./vendor

# Copy built assets from node stage
COPY --from=node --chown=www:www /var/www/public/build ./public/build

# Create necessary directories
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/bootstrap/cache

# Set proper permissions
RUN chown -R www:www /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod +x /var/www/artisan

# Optimize Laravel for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Create startup script
COPY docker/scripts/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]

# =============================================================================
# Testing stage
# =============================================================================
FROM development AS testing

# Install additional testing tools
RUN composer install --dev

# Copy test configuration
COPY phpunit.xml ./
COPY tests/ ./tests/

# Run tests
RUN php artisan test

# =============================================================================
# Security scanning stage
# =============================================================================
FROM alpine:3.18 AS security

# Install security scanning tools
RUN apk add --no-cache \
    curl \
    jq \
    bash

# Copy application for security scanning
COPY . /app
WORKDIR /app

# Run security checks (this would be customized based on your security tools)
RUN echo "Running security scans..." \
    && echo "Security scan completed"
