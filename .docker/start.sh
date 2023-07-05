#!/usr/bin/env bash

set -e

env=${APP_ENV:-production}

role=${CONTAINER_ROLE:-app}

echo "Current environment is $env"

if [ "$env" != "local" ]; then
    echo "Removing XDebug"
    rm -rf /usr/local/etc/php/conf.d/{docker-php-ext-xdebug.ini,xdebug.ini}
    echo "Migrate database"
    ( cd /var/www/html && php artisan migrate --seed --force)
    echo "Caching configs"
    ( cd /var/www/html && php artisan config:cache && php artisan route:cache && php artisan view:cache)
fi

if [ "$role" = "app" ]; then
    exec apache2-foreground
    exit 0
    elif [ "$role" = "queue" ]; then
    echo "Running Laravel Horizon"
    php /var/www/html/artisan horizon
    elif [ "$role" = "scheduler" ]; then
    while [ true ]
    do
        php /var/www/html/artisan schedule:run --verbose --no-interaction & sleep 60
    done
else
    echo "Cant find container role $role"
    exit 1
fi
