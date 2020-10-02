ARG PHP_VERSION=7.2
FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y git libzip-dev zip
RUN docker-php-ext-install zip

# Install pickle to help manage extensions
RUN curl --location https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar -o /usr/local/sbin/pickle
RUN chmod +x /usr/local/sbin/pickle

RUN pickle install uopz && docker-php-ext-enable uopz
RUN echo "uopz.exit=1" >> /usr/local/etc/php/conf.d/docker-php-ext-uopz.ini

ARG COVERAGE
RUN if [ "$COVERAGE" = "pcov" ]; then pickle install pcov && docker-php-ext-enable pcov; fi

# Install composer to manage PHP dependencies
RUN curl https://getcomposer.org/download/1.10.13/composer.phar -o /usr/local/sbin/composer
RUN chmod +x /usr/local/sbin/composer
RUN composer self-update

WORKDIR /app
