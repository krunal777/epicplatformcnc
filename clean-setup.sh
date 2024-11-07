#!/bin/sh

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

php artisan migrate
php artisan futureecom:service_resources:install --silent
php artisan futureecom:dynamic_resources:install --silent
php artisan futureecom:install:configuration --all
php artisan futureecom:create:presets
php artisan futureecom:store:abilities


blue='\033[0;34m'
reset='\e[0m'
echo "${blue}Building listener config file${reset}" 
sh build-listener-config.sh > /etc/supervisor/conf.d/futureecom-listeners-supervisord.conf
echo "${blue}Updating supervisor config${reset}" 
supervisorctl update
