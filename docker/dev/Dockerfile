FROM php:8.1-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    nginx \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev \
    zip ruby mariadb-client imagemagick libmagickwand-dev libmagickcore-dev libgeoip-dev \
    default-libmysqlclient-dev libmcrypt-dev libc-client-dev libkrb5-dev libzip-dev libmemcached-dev \
    libmemcached11 curl wget git libssl-dev libxslt-dev librabbitmq-dev libssh-dev libxml2-dev \
    libcurl4-openssl-dev pkg-config libtidy-dev supervisor

RUN docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip opcache sockets gd tidy intl xsl bcmath \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip

RUN pecl install imagick ds apcu memcached amqp xdebug \
    && docker-php-ext-enable imagick ds apcu memcached amqp sodium xdebug

RUN mkdir -p /var/log/supervisor

RUN curl -sL https://deb.nodesource.com/setup_current.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn \
    && yarn install

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    && chmod +x /usr/local/bin/symfony

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./docker/dev/containers/php/php.ini  /usr/local/etc/php/conf.d/php-custom.ini
COPY ./docker/dev/containers/supervisor/supervisord.conf /etc/supervisord.conf
COPY ./docker/dev/containers/nginx/nginx-site.conf /etc/nginx/sites-enabled/default
COPY ./docker/dev/containers/bin/entrypoint.sh /usr/local/bin

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
   && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
   && rm composer-setup.php

ENV APP_ROOT=/var/www/html
ENV APP_ENV=dev
ENV PROJECT_NAME=web_bounces

COPY --chown=www-data . $APP_ROOT
WORKDIR $APP_ROOT

RUN composer install
#RUN yarn install
#RUN yarn encore dev

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]