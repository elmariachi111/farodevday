docker build -t faro/rsyslog .
docker run -itd -p 8080:80 -v /Users/stadolf/work/faro/rsyslog/logs:/logs faro/bash
docker exec -it --user elmariachi ff60 bash
