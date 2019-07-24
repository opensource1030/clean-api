# CLEAN Platform API

[![CircleCI](https://circleci.com/gh/WirelessAnalytics/clean-api.svg?style=svg&circle-token=c6b1c08cf02dda6a4e5c192397919064a63abdd3)](https://circleci.com/gh/WirelessAnalytics/clean-api) [![Coverage Status](https://coveralls.io/repos/github/WirelessAnalytics/clean-api/badge.svg?t=Gtw2zj)](https://coveralls.io/github/WirelessAnalytics/clean-api)

<img src="http://static.wirelessanalytics.com/shots/dumps_cdi_wa.jpg"  width="50" height="50">     **Wireless Analytics:** Development environment setup with Docker
### Clone repository

```
git clone git@github.com:WirelessAnalytics/clean-api.git
cd clean-api
cp .env.local .env
chmod 660 storage/oauth-p*
```

You can customize the **.env** file, but **Docker** will override some variables in order to work properly inside the container.
You **don't need to** define none of this variables:

- DB_HOST
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- DB_CONNECTION
- API_DOMAIN

---

### Docker installation

This only needs to be done once in order to setup the development environment.

```
./docker/ldocker install-docker
exec su -l $USER
./docker/dcomposer install --ignore-platform-reqs --no-scripts
./docker/ldocker start
./docker/ldocker install-memcached
./docker/dartisan migrate
./docker/dartisan db:seed
```

Some of the commands run sudo and will request you to enter your password.
These scripts are prepared to be run under **Ubuntu 18 LTS**, plase addapt the install-docker section of the ldocker script if you are using another OS.
In the install-memcached step you should accept the default answer in all questions.

---

### Development commands

From now on you can use the following commands:
- `./docker/dartisan` -> To run artisan inside the docker container, it accepts the same parameters as artisan
- `./docker/ldocker` -> To start or stop all docker containers
- `./docker/dcomposer` -> To run composer inside the docker container
- `./docker/dphpunit` -> To run phpunit inside the docker container
- `./docker/dmysql` -> To connect to MySQL from the command line
- `docker-compose down` -> to reset you docker environment, if you run this you'll have to run from the `./ldocker start` step in the section above.

---

## URLs and ports

- App: http://localhost:1080
- phpMyAdmin: http://localhost:1081 (root/password)
- MySQL: listens in localhost:13306
- MemCacheD: listens in localhost:1082
