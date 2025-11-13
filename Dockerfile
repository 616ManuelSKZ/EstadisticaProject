# ============================
# Stage 1: Build de assets
# ============================
FROM node:18-alpine AS assets

WORKDIR /app

COPY package*.json vite.config.js ./
RUN npm install

COPY . .
RUN npm run build


# ============================
# Stage 2: PHP + Composer + SQLite
# ============================
FROM php:8.2-fpm

# Instalación de dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev sqlite3 \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd pdo_sqlite

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Copiar los assets generados desde el stage de Node
COPY --from=assets /app/public/build ./public/build

# Instalar dependencias PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Crear archivo SQLite (si no existe)
RUN mkdir -p storage/database && touch storage/database/database.sqlite

# Permisos correctos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto (Render usará $PORT)
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT
