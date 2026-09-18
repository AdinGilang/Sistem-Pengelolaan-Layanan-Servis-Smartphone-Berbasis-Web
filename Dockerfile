# ---------- Tahap 1: Build aset frontend ----------
FROM node:22-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
# npm ci memakai package-lock.json apa adanya, jadi versi paket di server
# persis sama dengan yang diuji di mesin pengembang.
RUN npm ci

COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY app ./app

RUN npm run build


# ---------- Tahap 2: Dependensi PHP ----------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# --no-dev membuang seluruh paket pengembangan (Pest, Faker, Pail, Sail).
# Tanpa ini, perkakas debug ikut terpasang di server production.
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# ---------- Tahap 3: Runtime ----------
FROM php:8.3-apache

# CATATAN PERBAIKAN:
# Daftar paket pada versi sebelumnya kehilangan tanda "\" setelah libpng-dev,
# sehingga instruksi RUN terpotong di tengah dan baris "libonig-dev \"
# berikutnya dibaca Docker sebagai instruksi tersendiri. Akibatnya build image
# selalu gagal dengan galat "unknown instruction". Daftarnya kini utuh.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd exif \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi PHP untuk production: pesan galat tidak pernah ditampilkan ke
# pengunjung, hanya dicatat ke log kontainer.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf '%s\n' \
        'expose_php = Off' \
        'display_errors = Off' \
        'display_startup_errors = Off' \
        'log_errors = On' \
        'error_log = /dev/stderr' \
        'upload_max_filesize = 8M' \
        'post_max_size = 10M' \
        'memory_limit = 256M' \
        'session.cookie_httponly = 1' \
        'session.cookie_samesite = "Lax"' \
        'session.use_strict_mode = 1' \
        > "$PHP_INI_DIR/conf.d/99-app.ini"

# Sembunyikan versi Apache dan PHP dari header respons.
RUN a2enmod rewrite headers \
    && printf '%s\n' 'ServerTokens Prod' 'ServerSignature Off' 'TraceEnable Off' \
        > /etc/apache2/conf-available/security-hardening.conf \
    && a2enconf security-hardening

# Document root diarahkan ke public/ agar berkas seperti .env, storage, dan
# vendor tidak pernah bisa diakses langsung lewat URL.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=node_builder --chown=www-data:www-data /app/public/build ./public/build

# Berkas yang tidak boleh ikut ke image, seandainya lolos dari .dockerignore.
RUN rm -rf .env .env.backup .git tests database/database.sqlite

RUN php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cache konfigurasi, route, dan view dibangun saat kontainer mulai, bukan saat
# build, karena nilainya bergantung pada variabel lingkungan di server.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
