#!/bin/sh
set -e

# Создаём runtime от имени www-data
mkdir -p /var/www/html/runtime/logs
chown -R www-data:www-data /var/www/html/runtime
chmod -R 755 /var/www/html/runtime

if [ ! -d /var/www/html/vendor ]; then
    COMPOSER_FLAGS="--no-interaction --no-progress --optimize-autoloader --working-dir=/var/www/html"
    if [ "$YII_ENV" != "dev" ]; then
        COMPOSER_FLAGS="--no-dev $COMPOSER_FLAGS"
    fi

    echo "Installing Composer dependencies (YII_ENV=${YII_ENV:-prod})..."
    # Запускаем composer от имени хост-пользователя (UID/GID переданы через env),
    # чтобы созданный vendor/ на хосте не требовал sudo.
    su-exec "${APP_UID:-0}:${APP_GID:-0}" \
        composer install $COMPOSER_FLAGS
fi

echo "Running migrations..."
php /var/www/html/yii migrate --interactive=0

echo "Starting PHP-FPM..."
exec "$@"
