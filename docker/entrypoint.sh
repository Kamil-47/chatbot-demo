#!/bin/sh
set -e

# Initialize app.db from seed.db on container start
if [ ! -f /var/www/html/database/app.db ]; then
    cp /opt/demo/seed.db /var/www/html/database/app.db
fi
chown www-data:www-data /var/www/html/database/app.db
chmod 664 /var/www/html/database/app.db

# Warm caches for production performance
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
