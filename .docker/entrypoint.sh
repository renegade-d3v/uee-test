#!/bin/sh
set -e

# Install composer dependencies if vendor is missing
if [ ! -f /var/www/vendor/autoload.php ]; then
    composer setup
fi

# Create SQLite file if it doesn't exist
if [ ! -f /var/www/database/database.sqlite ]; then
    touch /var/www/database/database.sqlite
fi

php artisan migrate

exec docker-php-entrypoint php-fpm
