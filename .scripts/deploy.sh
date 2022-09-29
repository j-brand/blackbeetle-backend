#!/bin/bash
set -e

echo "Deployment master branch started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
(/opt/plesk/php/8.1/bin/php artisan down) || true

# Pull the latest version of the app
git pull origin master

# Install composer dependencies
/opt/plesk/php/8.1/bin/php ~/composer.phar install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
/opt/plesk/php/8.1/bin/php artisan clear-compiled

# Recreate cache
/opt/plesk/php/8.1/bin/php artisan optimize

# Run database migrations
/opt/plesk/php/8.1/bin/php artisan migrate --force

# Exit maintenance mode
/opt/plesk/php/8.1/bin/php artisan up

echo "Deployment master branch finished!"