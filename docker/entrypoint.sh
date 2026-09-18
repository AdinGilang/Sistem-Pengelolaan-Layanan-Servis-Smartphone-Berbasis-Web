#!/bin/sh
set -e

# Titik masuk kontainer.
#
# Cache konfigurasi, route, dan view dibangun di sini, bukan saat image dibuat,
# karena isinya bergantung pada variabel lingkungan yang baru tersedia ketika
# kontainer berjalan di server.

if [ -z "${APP_KEY}" ]; then
    echo "APP_KEY belum diisi. Jalankan 'php artisan key:generate --show' lalu simpan hasilnya sebagai variabel lingkungan APP_KEY." >&2
    exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrasi dijalankan hanya bila diminta secara eksplisit, supaya perubahan
# skema tidak pernah terjadi tanpa sepengetahuan operator.
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    php artisan migrate --force
fi

# Tautan storage publik dibuat bila belum ada.
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

exec "$@"
