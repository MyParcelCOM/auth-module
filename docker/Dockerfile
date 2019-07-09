FROM composer:1.8

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS\
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps \
    && composer global require hirak/prestissimo \
    && rm -rf /tmp/*
