FROM php:8.3.13-cli-alpine AS base
WORKDIR /var/www/
RUN chown -R www-data:www-data .
RUN apk update && apk add --no-cache php83-dev $PHPIZE_DEPS

####################################################################################

FROM composer:latest AS composerinstaller
WORKDIR /composer
COPY ./composer.json /composer
RUN composer install

####################################################################################

FROM base AS unittest
COPY ./docker/php-fpm/phpunit.ini $PHP_INI_DIR/conf.d/custom.php.ini
COPY ./src ./src
COPY ./tests ./tests
COPY ./phpunit.xml ./phpunit.xml
COPY --from=composerinstaller /composer/vendor ./vendor
