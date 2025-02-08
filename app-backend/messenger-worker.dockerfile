FROM php:8.3.2-fpm-alpine
#RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#    && docker-php-ext-install pgsql pdo_pgsql
RUN apk add libpq-dev
RUN docker-php-ext-install pgsql pdo_pgsql