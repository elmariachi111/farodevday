FROM alpine:3.6

ARG xdebug=0
ARG profiling=0

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

RUN if [ "$xdebug" -eq "1" ]; then \
        apk add --update php7-xdebug \
        && echo "zend_extension=xdebug.so" >> /etc/php7/conf.d/xdebug.ini \
    ;fi

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN echo 'alias sf="php bin/console"' >> ~/.bashrc \
    && echo 'alias ll="ls -la"' >> ~/.bashrc \
    && echo 'alias ..="cd .."' >> ~/.bashrc

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

ADD symfony.ini /etc/php7/php-fpm.d/
ADD symfony.ini /etc/php7/conf.d/
ADD symfony.pool.conf /etc/php7/php-fpm.d/
ADD setupxdebug.sh /root/

RUN if [ "$xdebug" -eq "1" ]; then \
        bash /root/setupxdebug.sh \
    ;fi

RUN if [ "$profiling" -eq "1" ]; then \
        echo "xdebug.profiler_enable=1" >> /etc/php7/conf.d/symfony.ini \
        && echo "xdebug.profiler_enable_trigger=1" >> /etc/php7/conf.d/symfony.ini \
        && echo "xdebug.profiler_output_dir=/var/www/application/var/logs" >> /etc/php7/conf.d/symfony.ini \
    ;fi

CMD ["php-fpm7", "-F"]

WORKDIR /var/www/application
EXPOSE 9000