#FROM httpd:2.4-alpine
#FROM node:8.5.0-alpine
FROM mhart/alpine-node:8
RUN apk add --no-cache bash nano mysql-client

WORKDIR /app
COPY src/package*.json /app/
RUN npm install

CMD ["npm", "start"]

#RUN git clone https://github.com/magicmonty/bash-git-prompt.git .bash-git-prompt --depth=1
#&& sed -i  "s/#Include conf\/extra\/httpd-default\.conf/Include conf\/extra\/httpd-default\.conf/g" /usr/local/apache2/conf/httpd.conf \

#COPY ./www /usr/local/apache2/htdocs/
#COPY ./httpd-default.conf /usr/local/apache2/conf/extra/httpd-default.conf
#

#&& sed -i  "s/#Include conf\/extra\/httpd-default\.conf/Include conf\/extra\/httpd-default\.conf/g" /usr/local/apache2/conf/httpd.conf \
#COPY ./www /usr/local/apache2/htdocs/
#COPY ./httpd-default.conf /usr/local/apache2/conf/extra/httpd-default.conf
#