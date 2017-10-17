`docker-compose up` starts three machines:

* the official loggly Docker logger image that you can send logs towards (514/udp)
* a mysql instance
* the application that connects to mysql

to test logging to loggly yourself you must [create a loggly account](https://www.loggly.com/signup/) and get a [customer token](https://your-account.loggly.com/tokens). Then create an `.env` file by copying `.env.dist` and set the token there. It's picked up by the logger service automatically.

The application is bound to your local port 8080 and will log http requests to your loggly account. The source code is mounted to the application service's container so if you reflect any changes to the source code you have to restart the running application (`docker-compose restart application`).
