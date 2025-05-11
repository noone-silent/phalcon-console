FROM composer:2 AS composer

FROM ghcr.io/phalcon/cphalcon:v5.9.3-php8.2

COPY --from=composer /usr/bin/composer /usr/bin/composer