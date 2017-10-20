#!/bin/bash

HOST="$XDEBUG_HOST"

# else if check if is Docker for Mac
if [ -z "$HOST" ]; then
    HOST=`getent hosts docker.for.mac.localhost | awk '{ print $2 }'`
fi

# else get host ip
if [ -z "$HOST" ]; then
    HOST=`/sbin/ip route|awk '/default/ { print $3 }'`
fi

sed -i "s/xdebug\.remote_host\=.*/xdebug\.remote_host\=$HOST/g" /etc/php7/conf.d/symfony.ini
