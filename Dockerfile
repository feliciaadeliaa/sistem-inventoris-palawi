FROM php:8.4-fpm AS base

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        gosu \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

FROM base AS vendor

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

FROM node:24-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY vite.config.js ./
COPY resources ./resources

COPY --from=vendor \
    /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
    ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN npm run build

FROM base AS app

COPY --from=vendor /var/www/html/vendor ./vendor

COPY . .

COPY --from=assets /app/public/build ./public/build

COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8001

ENTRYPOINT ["entrypoint"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001", "--no-reload"]
