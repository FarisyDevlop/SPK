# Dockerfile untuk Hugging Face Spaces (salin ke root Space)
# Kode diambil langsung dari GitHub, jadi Space cukup berisi file ini + README.md
FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev unzip git \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN git clone --depth 1 https://github.com/FarisyDevlop/SPK.git . \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && chown -R 1000:1000 /app

ENV APP_NAME="SPK SAW Vendor SIMRS" \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_LOCALE=id \
    DB_CONNECTION=pgsql \
    SESSION_DRIVER=database \
    SESSION_SAME_SITE=none \
    SESSION_SECURE_COOKIE=true \
    CACHE_STORE=database \
    LOG_CHANNEL=stderr \
    PHP_CLI_SERVER_WORKERS=4 \
    PORT=7860

USER 1000
EXPOSE 7860
CMD ["sh", "docker/start-hf.sh"]
