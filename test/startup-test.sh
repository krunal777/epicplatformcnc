#!/bin/sh
composer require futureecom/futureecom:"dev-${BRANCH} as 1.56"
apt update && apt install -y expect
php artisan migrate
expect ./test/migrate.exp
expect ./test/setup.exp
php artisan futureecom:demo
php artisan futureecom:install:configuration --all
/usr/bin/supervisord

