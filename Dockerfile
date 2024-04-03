FROM php:8.2-fpm-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/project

RUN apk update && apk add \
    vim \
    git \
    libzip-dev \
    icu-dev \
    zip \
    make \
    curl-dev \
    gmp-dev \
    libpq \
    mysql-dev

COPY docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d

RUN docker-php-ext-configure mysqli

RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    bcmath \
    intl \
    curl \
    gmp \
    opcache

RUN adduser -s /bin/ash -u 1000 -D supinfo-j1 supinfo-j1

RUN touch /var/log/php-fpm.error.log
RUN touch /var/log/php-fpm.access.log

RUN chown -R supinfo-j1:supinfo-j1 /var/log/php-fpm.error.log /var/log/php-fpm.access.log

USER supinfo-j1

CMD ["php-fpm"]
