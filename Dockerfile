# Image untuk deploy ke Render (PHP 8.3 + Apache + PostgreSQL)
FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev unzip git \
    && docker-php-ext-install pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Render memberi port lewat variabel PORT (default 10000)
ENV PORT=10000
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && chmod +x docker/start.sh \
    && chown -R www-data:www-data storage bootstrap/cache

CMD ["docker/start.sh"]
