FROM alpine:3.5
RUN apk add --no-cache bash nano git wget make php7 php7-json php7-openssl php7-phar php7-iconv php7-mbstring php7-xml \
    && ln -sf /usr/bin/php7 /usr/bin/php     
RUN wget "https://getcomposer.org/installer" -O composer-setup.php \
    && php7 composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

RUN addgroup -S elmariachi && adduser -S -g elmariachi elmariachi 

COPY .bash.conf/ /home/elmariachi/.bash.conf/
COPY .bashrc /home/elmariachi/
ADD symfony /home/elmariachi/symfony

#COPY doesnt obey the USER directive.
RUN chown -R elmariachi:elmariachi /home/elmariachi 

USER elmariachi:elmariachi
WORKDIR /home/elmariachi
RUN git config --global user.email "elmariachi@bucho.mx" \
    && git config --global user.name "ElMariachi" \
    && git clone https://github.com/magicmonty/bash-git-prompt.git .bash.conf/bash-git-prompt --depth=1 \
    && wget "https://raw.githubusercontent.com/git/git/master/contrib/completion/git-completion.bash" -O .bash.conf/git-completion.bash \
	&& git clone https://github.com/iArren/composer-bash-completion.git .bash.conf/composer-bash-completion \
    && cd symfony && composer install



