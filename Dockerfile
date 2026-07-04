# syntax=docker/dockerfile:1.7

# Stage 1: build frontend assets
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build


# Stage 2: PHP runtime
FROM php:8.2-fpm-alpine AS app

# Install runtime packages: nginx, supervisor, sqlite CLI, BusyBox cron, plus tini for signal handling
RUN apk add --no-cache \
        nginx \
        supervisor \
        sqlite \
        dcron \
        tini \
        bash

# PHP extensions via mlocati installer (handles all build deps automatically)
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite bcmath opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer deps first (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# App source
COPY . .

# Built assets from stage 1
COPY --from=assets /app/public/build ./public/build

# Finalize composer
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Generate seed.db during build with DEMO_MODE=true.
# We stash it in /opt/demo (outside /database) so runtime state resets don't overwrite it.
RUN cp .env.example .env \
    && php artisan key:generate --force \
    && sed -i 's|^DEMO_MODE=.*|DEMO_MODE=true|' .env \
    && sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|' .env \
    && echo "DB_DATABASE=/var/www/html/database/build.sqlite" >> .env \
    && touch database/build.sqlite \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && mkdir -p /opt/demo \
    && cp database/build.sqlite /opt/demo/seed.db \
    && rm database/build.sqlite .env

# Docker-managed config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/crontab /etc/crontabs/root
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh \
    && chmod 0600 /etc/crontabs/root

# Ownership: nginx and PHP-FPM run as user 'nobody' in Alpine images by default;
# we standardize on www-data (present in php:*-fpm-alpine)
RUN chown -R www-data:www-data storage bootstrap/cache database /opt/demo \
    && chmod -R 775 storage bootstrap/cache database

EXPOSE 80

ENTRYPOINT ["/sbin/tini", "--", "/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
