FROM php:7.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

RUN apt-get update \
    && rm -rf /var/lib/apt/lists/* \

RUN a2enmod rewrite
