# ============================
# Stage 1: Build Vite assets
# ============================
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ============================
# Stage 2: PHP runtime
# ============================
FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    default-mysql-client

RUN docker-php-ext-install pdo pdo_mysql mbstring zip bcmath

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app source
COPY . .

# Copy the compiled Vite assets from the node-builder stage
COPY --from=node-builder /app/public/build ./public/build

RUN composer install --optimize-autoloader

EXPOSE 10000

CMD ["sh", "-c", "chmod -R 775 storage bootstrap/cache && php artisan config:clear && php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && php artisan serve --host=0.0.0.0 --port=10000"]