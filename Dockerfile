# syntax=docker/dockerfile:1
FROM php:8.3-apache

ARG WWWGROUP=1000
ARG WWWUSER=1000

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Instalar dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libicu-dev libpq-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        xml \
        opcache \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite para las rutas de Laravel
RUN a2enmod rewrite

# Configurar Virtual Host de Apache apuntando a /public
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar el código fuente
COPY . .

# Crear directorios de storage que Laravel necesita en tiempo de build
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Instalar dependencias de PHP (sin dev) y optimizar autoloader
# Las credenciales de Flux se pasan como build secrets en CI
RUN --mount=type=secret,id=flux_username \
    --mount=type=secret,id=flux_license_key \
    if [ -s /run/secrets/flux_username ]; then \
        composer config http-basic.composer.fluxui.dev \
            "$(cat /run/secrets/flux_username)" \
            "$(cat /run/secrets/flux_license_key)"; \
    fi \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Crear usuario con el mismo UID/GID que el host para evitar problemas de permisos
RUN groupmod -g ${WWWGROUP} www-data || true \
    && usermod -u ${WWWUSER} www-data || true

# Permisos correctos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80
