ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-cli

RUN apt update && apt install -y curl zip
RUN curl -L https://github.com/krakjoe/uopz/archive/14c8fc2d6eff14ec9acd926b9cab85d6961c64ac.zip -o /tmp/uopz.zip
RUN unzip /tmp/uopz.zip -d /tmp
RUN cd /tmp/uopz-14c8fc2d6eff14ec9acd926b9cab85d6961c64ac && phpize && ./configure --enable-uopz && make && make install
RUN docker-php-ext-enable uopz
RUN echo "uopz.exit=1" >> /usr/local/etc/php/conf.d/docker-php-ext-uopz.ini

ARG COVERAGE
RUN if [ "$COVERAGE" = "pcov" ]; then pecl install pcov && docker-php-ext-enable pcov; fi

RUN apt update && apt install -y git zip
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN git config --global --add safe.directory /app
