#!/bin/sh
# Dijalankan setiap kali Space Hugging Face menyala
set -e

php artisan migrate --force      # buat/ubah tabel di database Neon
php artisan db:seed --force      # isi data spreadsheet (hanya kalau masih kosong)

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-7860}"
