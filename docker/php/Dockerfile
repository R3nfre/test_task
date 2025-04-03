FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

COPY ./app /var/www/html/app
COPY ./migrations /var/www/html/migrations

WORKDIR /var/www/html/app
RUN composer install --no-interaction --no-plugins --no-scripts

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/migrations

EXPOSE 9000

WORKDIR /var/www/html