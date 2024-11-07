#!/bin/sh

events=$(php artisan streamer:list --compact | grep -v '^Event' | grep -v 'example.streamer.event')

for event in $events
do
  echo "[program:${event}]
process_name=%(program_name)s
command=php /var/www/html/artisan streamer:listen ${event} %(ENV_STREAMER_OPTIONS)s
autostart=true
autorestart=true
environment=HOME="/var/www/html",USER="www-data"
stdout_logfile=/dev/fd/1
stdout_logfile_maxbytes=0
redirect_stderr=true

"
done
