#!/bin/bash

# Bước 1: Tạo Dockerfile
echo "Tạo Dockerfile..."
cat <<EOL > Dockerfile
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
EOL

# Bước 2: Tạo docker-compose.yml
echo "Tạo docker-compose.yml..."
cat <<EOL > docker-compose.yml
services:
  web:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    restart: unless-stopped

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: growcrm
      MYSQL_USER: growcrm_user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped

volumes:
  db_data:
EOL

# Bước 3: Tạo apache.conf
echo "Tạo apache.conf..."
cat <<EOL > apache.conf
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL

# Bước 4: Chạy docker-compose up
echo "Đang chạy docker-compose..."
docker-compose up -d --build

# Bước 5: Sửa quyền cho thư mục storage
echo "Sửa quyền cho thư mục storage..."
docker-compose exec web bash -c "chown -R www-data:www-data /var/www/html"
docker-compose exec web bash -c "chmod -R 775 /var/www/html/storage"

# Bước 6: Khởi động lại Docker Compose
docker-compose restart

echo "Setup hoàn tất! Bạn có thể truy cập GrowCRM tại http://localhost:8080"
echo "Đã hoàn tất việc tạo file cấu hình và khởi động Docker Compose."