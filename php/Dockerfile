FROM php:7.4-apache

RUN apt-get update

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

#RUN docker-php-ext-install mbstring
#RUN docker-php-ext-install gd
#RUN docker-php-ext-install zip

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable mod_rewrite in Apache
RUN a2enmod rewrite
