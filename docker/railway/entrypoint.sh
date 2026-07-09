#!/bin/sh
set -e

# Sin argumentos: arranca el servicio web (Apache).
# Con argumentos (ej. "php artisan schedule:work"): los ejecuta en su lugar,
# para poder usar esta misma imagen como servicio de scheduler/worker en Railway.
if [ "$#" -eq 0 ]; then
    PORT="${PORT:-80}"

    sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

    php artisan storage:link || true
    php artisan migrate --force

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    exec apache2-foreground
else
    exec "$@"
fi
