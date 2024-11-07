FROM ghcr.io/devops-team-epicenter/php-app-web:prod-3.3.4 as intermediate

ARG COMPOSER_AUTH_ARG
ENV COMPOSER_AUTH $COMPOSER_AUTH_ARG
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY . /var/www/html
RUN composer install

FROM ghcr.io/devops-team-epicenter/php-app-web:prod-3.3.4

COPY --from=intermediate /var/www/html /var/www/html
COPY futureecom-jobs-supervisord.conf /etc/supervisor/conf.d/
RUN chown -R www-data /var/www/html/storage/framework /var/www/html/storage/app /var/www/html/storage/logs  /var/www/html/bootstrap/cache

