FROM php:7.4-apache

RUN apt-get update && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip

RUN a2enmod rewrite
