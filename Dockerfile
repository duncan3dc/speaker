ARG PHP_VERSION=7.3
FROM php:${PHP_VERSION}-cli

RUN pecl install uopz || pecl install uopz-6.1.2 && docker-php-ext-enable uopz
RUN echo "uopz.exit=1" >> /usr/local/etc/php/conf.d/docker-php-ext-uopz.ini

ARG COVERAGE
RUN if [ "$COVERAGE" = "pcov" ]; then pecl install pcov && docker-php-ext-enable pcov; fi

# Install composer to manage PHP dependencies
RUN apt-get update && apt-get install -y git zip
RUN curl https://getcomposer.org/download/2.2.6/composer.phar -o /usr/local/sbin/composer
RUN chmod +x /usr/local/sbin/composer
RUN echo "if [[ $PHP_VERSION == 7.* ]]; then composer self-update --1; else composer self-update; fi" > composer.sh
RUN bash composer.sh

WORKDIR /app
