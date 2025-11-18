FROM php:8.2-fpm-alpine
RUN apk add --no-cache \
  $PHPIZE_DEPS \
  libxml2-dev \
  oniguruma-dev \
  sqlite-dev \
  mysql-client \
  && docker-php-ext-install pdo_mysql opcache
WORKDIR /var/www/html
EXPOSE 9000
