#!/bin/sh
set -e

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Start Laravel with PHP's built-in server on 0.0.0.0:10000
exec php -S 0.0.0.0:10000 -t public server.php