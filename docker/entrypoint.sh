#!/bin/sh
set -e

# Prepare sandbox directory for per-session SQLite copies
mkdir -p /var/www/html/database/sandbox
chown www-data:www-data /var/www/html/database/sandbox
chmod 775 /var/www/html/database/sandbox

# Warm caches for production performance
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
