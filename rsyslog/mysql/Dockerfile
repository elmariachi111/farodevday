FROM mysql:5.6
RUN apt-get update && apt-get -y install rsyslog
ADD rsyslog.conf /etc/
USER mysql:mysql
CMD mysqld --user=mysql