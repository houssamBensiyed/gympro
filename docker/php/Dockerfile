# ============================================
# GYM MANAGEMENT PLATFORM
# PHP Dockerfile
# ============================================
# Base Image: PHP 8.2 with FPM
# Extensions: mysqli, pdo_mysql, gd, etc.
# ============================================

FROM php:8.2-fpm-alpine

# ============================================
# METADATA
# ============================================
LABEL maintainer="Gym Management Platform"
LABEL version="1.0.0"
LABEL description="PHP-FPM container for Gym Management Platform"

# ============================================
# ENVIRONMENT VARIABLES
# ============================================
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=development

# ============================================
# INSTALL SYSTEM DEPENDENCIES
# ============================================
RUN apk update && apk add --no-cache \
    # Build dependencies
    autoconf \
    gcc \
    g++ \
    make \
    # Required libraries
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zlib-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    # Utilities
    curl \
    git \
    zip \
    unzip \
    bash \
    # Fonts (for PDF generation)
    ttf-freefont \
    fontconfig

# ============================================
# INSTALL PHP EXTENSIONS
# ============================================

# Configure GD extension
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# Install required extensions
RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    gd \
    zip \
    mbstring \
    xml \
    intl \
    opcache \
    bcmath

# ============================================
# INSTALL ADDITIONAL TOOLS
# ============================================

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ============================================
# CONFIGURE PHP-FPM
# ============================================

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# ============================================
# SET WORKING DIRECTORY
# ============================================
WORKDIR /var/www/html

# ============================================
# CREATE APPLICATION USER
# ============================================
RUN addgroup -g 1000 gymapp && \
    adduser -u 1000 -G gymapp -s /bin/sh -D gymapp

# Set proper permissions
RUN chown -R gymapp:gymapp /var/www/html

# ============================================
# EXPOSE PORT
# ============================================
EXPOSE 9000

# ============================================
# DEFAULT COMMAND
# ============================================
CMD ["php-fpm"]