FROM php:apache

RUN apt-get update \
    && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd


RUN docker-php-ext-install mysqli