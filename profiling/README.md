This sets up a simple nginx/php-fpm server to demonstrate xdebug debugging and profiling. /application contains a Symfony4-beta / Flex application with some well known libraries (e.g. twig, stopwatches, phpunit, the web profiler).
 
Application logic
----------------- 
 The demo "application" consists of logic that computes the "out of office minutes" given a parseable "opening time" string and a daterange. E.g.: our office is open from "mon-wed 8-19 and thu-fri 10-18", when a colleague is on vacation from "2017-10-01" until "2017-10-10", how many minutes has he not spent in office considering when the office is open.   

setup (xdebug not enabled)
--------------------------

```
docker-compose build
docker-compose up
``` 
http://localhost:8080 should then show some random number. 

Enable Debugging 
----------------

To enable debugging and/or profiling, edit the `php-fpm/synfomy.ini` on your local machine followed by an `docker-compose restart php`. Follow the instructions in that file. Note that the container needs to contact the **host** machine so its IP has to be known (the solution for this is shown in symfony.ini)

We're using a default nginx container here that we access using a locally forwarded port on our developer machine so our "server name" that we have to configure in our ide (prefereably PHPStorm) debug settings has to match the nginx servername. It's set to "_" which basically means "no name". I've set that value as environment variable in the php container using docker compose so debugging the console app should run right away. 

Setup your IDE/PHP Storm to listen to remote xdebug connections, mind the port that you're listening to (I changed it to 11078 to avoid conflicts on 9000). As soon as you visit localhost:8080 again your IDE will setup a new "server" configuration for you (named "_") and ask you for path mappings. Map your local /application folder to /var/www/application on the container and the rest should work fine.

Execute / Debug the cli command
-------------------------------

open a bash on your container:
`docker-compose exec php bash`

Once connected execute:
`bin/console faro:timerange "mo-fr 12-18" "2017-10-01" "2017-10-03"`

Execute the same thing using the controller:
`http://localhost:8080/timediff?opening_times=mo-fr%2012-18&from=2017-10-01&to=2017-10-02`

Activate the XDebug Profiler
----------------------------

edit `php-fpm/symfony.ini` to activate the profiler. You can disable the debugger for the time being (but leave xdebug.so activated!). Restart the container `docker-compose restart php` after doing so. Profiles (.cachegrind files) will go to the logs/symfony folder. To activate the profiler add `XDEBUG_PROFILE=1` as parameter to any URL e.g. `localhost:8080?XDEBUG_PROFILE=1`. To enable the debugger on demand when using the command line you can `php -d xdebug.profiler_enable=On bin/console your:command`

note that profiling a symfony application running in dev mode can add quite some load on xdebug so it's better `to switch to prod mode and profile with that one (use the /application/.env file in Symfony 4). Before adding the profiler

Run tests
---------
in the php container / application root execute: `vendor/bin/phpunit`

Launch on Heroku and enable Blackfire.io
----------------------------------------

- Create a new application on heroku, install heroku cli
- Create a new git repo in `application` (`git init`), add your heroku app's remote to it (`heroku git:remote --app=yourappname`)
- commit inside that repo and push to your application (`git add . && git commit -a`)
- add environment variables as shown in .env.dist as heroku config vars (`heroku config:push` adds all .env entries at once). The vars must exist BEFORE you deploy the app! 
- push to heroku master (`git push heroku master`)
- add the heroku blackfire.io plugin (`heroku addons:create blackfire:test`)
- redeploy (`git commit --allow-empty -m "redeploy with Blackfire enabled" && git push heroku master`) 
- add the blackfire chrome extension and start live profiling.

