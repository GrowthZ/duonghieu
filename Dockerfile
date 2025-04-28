# Dùng PHP 8.2 kèm Apache
FROM php:8.2-apache

# Cài mysqli, gd extension cho PHP
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Bật module rewrite cho Apache
RUN a2enmod rewrite

# Copy file config Apache mới
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Làm việc tại thư mục project
WORKDIR /var/www/html

# Mở port 80
EXPOSE 80
