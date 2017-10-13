FROM mhart/alpine-node:8
RUN apk add --no-cache bash nano mysql-client

WORKDIR /app
COPY src/package*.json /app/
RUN npm install

CMD ["npm", "start"]
