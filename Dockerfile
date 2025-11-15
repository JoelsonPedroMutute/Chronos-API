# Multi-stage Dockerfile para Laravel 12 (PHP 8.2)

# Stage 1: instalar dependências Composer
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-plugins --ignore-platform-reqs

# Stage 2: runtime com Apache + PHP 8.2
FROM php:8.3-apache

# Instalar pacotes do sistema e extensões PHP necessárias
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_pgsql pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Definir document root para a pasta public do Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar código do app e vendor do stage vendor
COPY . .
COPY --from=vendor /app/vendor ./vendor

# Garantir diretórios e permissões para cache e storage
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

# O Apache no foreground é o padrão da imagem base
CMD ["apache2-foreground"]