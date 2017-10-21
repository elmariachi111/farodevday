FROM alpine:3.6

RUN apk add --update \
    php7-fpm \
    php7-calendar \
    php7-curl \
    php7-ctype \
    php7-dom \
    php7-iconv \
    php7-imagick \
    php7-json \
    php7-intl \
    php7-mcrypt \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-xdebug \
    php7-xml \
    php7-xmlwriter \
    php7-zlib \
    php7-phar \
    php7-tokenizer \
    make \
    curl \
    nano \
    bash \
    git

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN echo 'alias sf="php bin/console"' >> ~/.bashrc \
    && echo 'alias ll="ls -la"' >> ~/.bashrc \
    && echo 'alias ..="cd .."' >> ~/.bashrc

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

#ADD symfony.ini /etc/php7/php-fpm.d/
#ADD symfony.ini /etc/php7/conf.d/
#ADD symfony.pool.conf /etc/php7/php-fpm.d/

CMD ["php-fpm7", "-F"]

WORKDIR /var/www/application
EXPOSE 9000