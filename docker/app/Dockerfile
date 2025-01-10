FROM php:apache

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/app/php.ini /usr/local/etc/php/php.ini-production
COPY docker/app/php.ini /usr/local/etc/php/php.ini-development
