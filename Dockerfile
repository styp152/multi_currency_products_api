FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN cp .env.example .env \
    && mkdir -p database \
    && touch database/database.sqlite \
    && composer install --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan key:generate

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --seed --force && php artisan serve --host=0.0.0.0 --port=8000"]
