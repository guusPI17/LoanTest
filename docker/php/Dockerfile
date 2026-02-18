FROM php:8.2-fpm-alpine

RUN apk add --no-cache postgresql-dev git unzip su-exec \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY app/ .

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
