FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources resources
COPY public public
COPY vite.config.js .
RUN npm run build

FROM php:8.4-cli-alpine

WORKDIR /app

# Laravel runtime deps (SQLite + common extensions)
RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev sqlite sqlite-dev unzip git \
  && docker-php-ext-install pdo pdo_sqlite intl mbstring zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy the minimum Laravel skeleton needed for Composer scripts (artisan/package discovery)
COPY composer.json composer.lock ./
COPY artisan artisan
COPY bootstrap bootstrap
COPY config config
COPY routes routes
COPY app app
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader

COPY . .
COPY --from=assets /app/public/build /app/public/build

EXPOSE 80

# Ensure the SQLite database exists, clear stale local caches, run migrations, and run the HTTP server.
CMD ["sh", "-lc", "rm -f bootstrap/cache/*.php && mkdir -p \"$(dirname \"$DB_DATABASE\")\" && touch \"$DB_DATABASE\" && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80"]
