docker build -t faro/bash .
docker run -itd -p 8080:80 -v /Users/stadolf/work/faro/bash/logs:/logs faro/bash
docker exec -it --user elmariachi ff60 bash
