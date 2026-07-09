#!/bin/sh
set -e

# Sin argumentos: arranca el servidor embebido de PHP.
# Con argumentos (ej. "php artisan schedule:work"): los ejecuta en su lugar,
# para poder usar esta misma imagen como servicio de scheduler/worker en Railway.
if [ "$#" -eq 0 ]; then
    PORT="${PORT:-8080}"

    php artisan storage:link || true
    php artisan migrate --force

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    exec php artisan serve --host=0.0.0.0 --port="${PORT}"
else
    exec "$@"
fi
