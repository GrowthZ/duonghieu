FROM php:8.2-apache


RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql mysqli


RUN a2enmod rewrite


COPY apache.conf /etc/apache2/sites-available/000-default.conf


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html

# Copy source code vào container (nếu bạn muốn build image với code)
# COPY . /var/www/html


RUN mkdir -p /var/www/html/updates \
    /var/www/html/storage/avatars \
    /var/www/html/storage/logos \
    /var/www/html/storage/logos/clients \
    /var/www/html/storage/logos/app \
    /var/www/html/storage/files \
    /var/www/html/storage/temp \
    /var/www/html/application/storage/app \
    /var/www/html/application/storage/app/public \
    /var/www/html/application/storage/cache \
    /var/www/html/application/storage/cache/data \
    /var/www/html/application/storage/debugbar \
    /var/www/html/application/storage/framework \
    /var/www/html/application/storage/framework/cache \
    /var/www/html/application/storage/framework/cache/data \
    /var/www/html/application/storage/framework/sessions \
    /var/www/html/application/storage/framework/testing \
    /var/www/html/application/storage/framework/views \
    /var/www/html/application/storage/logs \
    /var/www/html/application/bootstrap/cache \
    /var/www/html/application/storage/app/purifier \
    /var/www/html/application/storage/app/purifier/HTML


RUN chmod -R 777 /var/www/html/updates \
    /var/www/html/storage \
    /var/www/html/application/storage \
    /var/www/html/application/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/updates \
    /var/www/html/storage \
    /var/www/html/application/storage \
    /var/www/html/application/bootstrap/cache

# Mở port 80
EXPOSE 80