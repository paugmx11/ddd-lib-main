FROM php:8.4-cli-alpine

WORKDIR /app

# PHP extensions required by the project (Doctrine + SQLite)
RUN apk add --no-cache sqlite-dev unzip git \
  && docker-php-ext-install pdo pdo_sqlite

# Install Composer (kept small; no dev tooling needed at runtime)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader

COPY . .

EXPOSE 8000

# Use PHP built-in server as in README.md
CMD ["php", "-S", "0.0.0.0:8000", "index.php"]
