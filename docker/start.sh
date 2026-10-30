#!/bin/sh
# Dijalankan setiap kali server Render menyala
set -e

php artisan migrate --force      # buat/ubah tabel di database Neon
php artisan db:seed --force      # isi data spreadsheet (hanya kalau masih kosong)

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
