#!/bin/sh
set -e

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Start PHP-FPM
exec php-fpm
