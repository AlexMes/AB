FROM php:7.4-apache-buster

ENV PATH="./vendor/bin:$PATH" \
    OPCACHE_VALIDATE_TIMESTAMPS=0 \
    OPCACHE_MAX_ACCELERETED_FILES=20000 \
    OPCACHE_MEMORY_CONSUMPTION=256

RUN apt update && apt install -y libpq-dev
RUN  docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pcntl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug opcache \
    && a2enmod rewrite

COPY . /var/www/html
COPY .docker/config/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/config/php/conf.d/*.ini /usr/local/etc/php/conf.d/
COPY .docker/start.sh /usr/local/bin/start

RUN chown -R www-data:www-data /var/www/html \
    && chmod +x /usr/local/bin/start

CMD ["/usr/local/bin/start"]
