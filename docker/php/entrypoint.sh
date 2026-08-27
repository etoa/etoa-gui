#!/bin/sh
# Does what provision.sh did on every boot of the vagrant box, minus the parts the
# image already covers. Idempotent: safe to run on every "docker compose up".
set -e

if [ "$1" = "php-fpm" ]; then
    echo "[etoa] waiting for the database"
    until mysqladmin ping -h database -uetoa -petoa --silent 2>/dev/null; do
        sleep 2
    done

    if [ ! -f vendor/autoload_runtime.php ]; then
        echo "[etoa] installing composer dependencies"
        COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --no-progress
    fi

    # webpack writes the real files, this only keeps the app bootable before the
    # first asset build has finished
    mkdir -p public/build
    [ -f public/build/manifest.json ] || echo '{}' > public/build/manifest.json
    [ -f public/build/entrypoints.json ] || echo '{"entrypoints":{}}' > public/build/entrypoints.json

    # the pid file of the event handler and the symfony cache live here
    mkdir -p var/cache var/log tmp
    chmod -R 777 var tmp

    echo "[etoa] applying database migrations"
    php bin/console database:migrate --no-interaction
    APP_ENV=test php bin/console database:migrate --no-interaction || \
        echo "[etoa] test database migration failed, continuing"

    # Three config rows the environment has to provide:
    #  - loginurl empty, as in provision.sh
    #  - daemon_exe, because eventhandler/target is shadowed by the bind mount
    #  - daemon_pidfile in var/, which is a volume rather than the slow bind mount
    mysql -h database -uetoa -petoa etoa <<'SQL' || echo "[etoa] could not update config rows"
INSERT INTO config (config_name, config_value, config_param1, config_param2)
    VALUES ('loginurl', '', '', '')
    ON DUPLICATE KEY UPDATE config_value = '';
INSERT INTO config (config_name, config_value, config_param1, config_param2)
    VALUES ('daemon_exe', '/usr/local/bin/etoad', '', '')
    ON DUPLICATE KEY UPDATE config_value = '/usr/local/bin/etoad';
INSERT INTO config (config_name, config_value, config_param1, config_param2)
    VALUES ('daemon_pidfile', 'var/eventhandler.pid', '', '')
    ON DUPLICATE KEY UPDATE config_value = 'var/eventhandler.pid';
SQL

    echo "[etoa] ready"
fi

exec "$@"
