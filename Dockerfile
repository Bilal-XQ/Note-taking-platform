# syntax=docker/dockerfile:1

# --- Stage 1: Base PHP image with required extensions ---
FROM php:8.2-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
        bash \
        libpng libpng-dev \
        libjpeg-turbo libjpeg-turbo-dev \
        freetype freetype-dev \
        icu-dev \
        zlib-dev \
        libzip-dev \
        oniguruma-dev \
        curl \
        mariadb-client \
        shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd intl zip mbstring

# Install composer
COPY --link --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# --- Stage 2: Final image ---
FROM php:8.2-fpm-alpine AS final

# Create non-root user and group
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

# Copy PHP extensions and composer from base
COPY --from=base /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=base /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code (excluding .git, .env, IDE, etc. via .dockerignore)
COPY --link . .

# Set permissions for the application
RUN chown -R appuser:appgroup /var/www/html

# Expose port 9000 for php-fpm
EXPOSE 9000

# Switch to non-root user
USER appuser

# Default command (php-fpm)
CMD ["php-fpm"]
