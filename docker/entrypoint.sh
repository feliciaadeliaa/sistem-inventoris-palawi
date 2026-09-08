#!/bin/sh
set -e

cd /var/www/html

as_app() {
    gosu www-data "$@"
}

mkdir -p \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chown -R www-data:www-data storage bootstrap/cache

wait_for_database() {
    attempt=1

    until as_app php -r '
        try {
            new PDO(
                sprintf(
                    "mysql:host=%s;port=%s;dbname=%s",
                    getenv("DB_HOST"),
                    getenv("DB_PORT") ?: "3306",
                    getenv("DB_DATABASE")
                ),
                getenv("DB_USERNAME"),
                getenv("DB_PASSWORD")
            );
        } catch (Throwable $e) {
            exit(1);
        }
    ' 2>/dev/null; do
        if [ "$attempt" -ge 30 ]; then
            echo "entrypoint: ${DB_USERNAME}@${DB_HOST}:${DB_PORT} (${DB_DATABASE}) unreachable after 60s, giving up" >&2
            exit 1
        fi

        echo "entrypoint: waiting for the database (${attempt}/30)"
        attempt=$((attempt + 1))
        sleep 2
    done
}

as_app php artisan config:clear
as_app php artisan event:clear
as_app php artisan route:clear
as_app php artisan view:clear

wait_for_database

php artisan storage:link --force
as_app php artisan config:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    as_app php artisan migrate --force
fi

as_app php artisan event:cache
as_app php artisan route:cache
as_app php artisan view:cache

exec gosu www-data "$@"
