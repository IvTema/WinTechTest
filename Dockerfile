FROM php:8.3.3-fpm

ARG PHPGROUP
ARG PHPUSER
ENV PHPGROUP=${PHPGROUP}
ENV PHPUSER=${PHPUSER}
RUN adduser -g ${PHPGROUP} -s /bin/sh -D ${PHPUSER}; exit 0

RUN mkdir -p /srv

WORKDIR /srv/wallet

USER ${PHPUSER}

RUN apt-get update -y && docker-php-ext-install pdo_mysql

RUN docker-php-ext-configure opcache --enable-opcache && docker-php-ext-install opcache

COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN apt install -y debian-keyring debian-archive-keyring apt-transport-https
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-stable.list
RUN apt update && apt install caddy

# # Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ls -la /srv/wallet

CMD php-fpm -D; caddy run --config /srv/wallet/Caddyfile
