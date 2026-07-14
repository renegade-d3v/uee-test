#!/bin/sh
set -e

# Create SQLite file if it doesn't exist
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

php artisan migrate

exec docker-php-entrypoint php-fpm
