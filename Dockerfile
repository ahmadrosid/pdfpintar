FROM node as builder
RUN npm install --global pnpm

WORKDIR /app
COPY . .
RUN pnpm install
RUN pnpm run build
RUN rm -rf node_modules

FROM serversideup/php:8.1-fpm-nginx

RUN apt-get update \
    && apt-get install -y --no-install-recommends php8.1-pgsql  \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --from=ocittwo/php-pdf:latest /app/libphp_pdf.so /usr/lib/php/20210902/libphp_pdf.so
RUN echo "extension=libphp_pdf.so" > /etc/php/8.1/cli/conf.d/php-pdf.ini
COPY  --from=builder --chown=$PUID:$PGID /app .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress --ansi

# artisan commands
RUN php ./artisan key:generate && \
    php ./artisan view:cache && \
    php ./artisan route:cache && \
    php ./artisan ziggy:generate && \
    php ./artisan storage:link

USER root:root
