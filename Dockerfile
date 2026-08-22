FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD sh -c "echo '--- DB DEBUG ---'; echo DB_CONNECTION=$DB_CONNECTION; echo DB_HOST=$DB_HOST; echo DB_PORT=$DB_PORT; echo DB_DATABASE=$DB_DATABASE; echo DB_USERNAME=$DB_USERNAME; echo '--- END DEBUG ---'; php artisan config:clear; timeout 20 php artisan migrate --force || echo 'MIGRATE FAILED OR TIMED OUT'; php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
