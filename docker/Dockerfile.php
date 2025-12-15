FROM php:8.3-fpm

COPY ./src /var/www/korka
RUN docker-php-ext-install pdo pdo_mysql mysqli
