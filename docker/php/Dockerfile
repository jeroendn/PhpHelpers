FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    bash \
    mc \
    git \
    tzdata

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
