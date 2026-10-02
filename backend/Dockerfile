FROM php:8.2-cli-bookworm

# Binaire Composer seul (sans son propre interpréteur PHP) : évite que composer
# install vérifie les contraintes de composer.json contre une version de PHP
# différente de celle de l'image finale.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        libpq-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        zip \
        pdo_pgsql \
        mbstring \
        bcmath \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Les formulaires Filament (upload de vidéos "reel") attendent jusqu'à ~37 Mo —
# bien au-delà des valeurs par défaut de PHP sans php.ini (upload_max_filesize=2M).
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

# Couche mise en cache séparément : les dépendances ne sont réinstallées que si
# composer.json/composer.lock changent, pas à chaque modification du code.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN composer require league/flysystem-aws-s3-v3:^3.0 --update-no-dev --no-scripts --no-interaction --no-progress
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

ENV APP_ENV=production

# Render fournit le port d'écoute via la variable $PORT (par défaut 10000 en local docker run).
EXPOSE 10000

# Migrations, lien de stockage et compte admin sont rejoués à chaque démarrage de
# conteneur (identique à l'ancien Procfile Railway) : le filesystem du conteneur
# est réinitialisé à chaque déploiement, donc storage:link doit être refait, et
# migrate --force est idempotent (Laravel ne rejoue pas les migrations déjà appliquées).
CMD ["sh", "-c", "mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache && php artisan migrate --force && php artisan storage:link && php artisan admin:ensure && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
