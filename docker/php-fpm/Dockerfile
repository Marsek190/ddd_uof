FROM php:8.1-fpm as php_81_base

RUN apt-get update; \
    apt-get install -y --no-install-recommends locales unzip;

RUN printf 'en_US.UTF-8 UTF-8\n' > /etc/locale.gen; \
    printf 'ru_RU.UTF-8 UTF-8\n' >> /etc/locale.gen; \
    printf ' ru_RU.CP1251 CP1251\n' >> /etc/locale.gen; \
    locale-gen;

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl soap gd xdebug && \
      echo "[xdebug]" > /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.mode=develop,debug,profile" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.output_dir=/tmp/xdebug_data" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      php -m;

RUN rm -rf /var/cache/apk/* && docker-php-source delete

ARG GID
ARG UID
RUN groupadd --gid ${GID} www && useradd --uid ${UID} --gid www --shell /bin/sh --create-home www

USER root

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

USER www-data:www-data

WORKDIR /var/www/

COPY www.conf /usr/local/etc/php-fpm.d
COPY php.ini /usr/local/etc/php/conf.d
