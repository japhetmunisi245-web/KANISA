# ──────────────────────────────────────────────
# Stage 1: Build Vite frontend assets
# ──────────────────────────────────────────────
FROM node:22.12-bookworm AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . ./
RUN npm run build

# ──────────────────────────────────────────────
# Stage 2: PHP base with required extensions
# ──────────────────────────────────────────────
FROM php:8.3-cli AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        curl \
        gd \
        mbstring \
        opcache \
        pdo_pgsql \
        pcntl \
        zip \
    && rm -rf /var/lib/apt/lists/*

# OPcache tuning for production
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# ──────────────────────────────────────────────
# Stage 3: Install Composer dependencies
# ──────────────────────────────────────────────
FROM php-base AS vendor
WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

# ──────────────────────────────────────────────
# Stage 4: Final production image
# ──────────────────────────────────────────────
FROM php-base
WORKDIR /var/www/html

# Copy application source
COPY . ./

# Copy built vendor and frontend assets from previous stages
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Prepare Laravel storage and cache directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && php artisan package:discover --ansi \
    && php artisan storage:link --force \
    && chown -R www-data:www-data storage bootstrap/cache public/storage \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Run migrations then start the server
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]

