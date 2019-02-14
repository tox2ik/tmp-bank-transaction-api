# How to use

## Steps to run the API

1. set up mysql somewhere (didn't have time to write docker-files)
2. set up env
3. install composer
4. create database
5. run the dev-server


## .env

    APP_ENV=dev
    DATABASE_SCHEMA=bank_transactions_dev
    DATABASE_HOST=127.0.0.1
    DATABASE_PASSWORD=root
    DATABASE_PORT=3306
    DATABASE_USER=root

## steps 4 and 5

	php bin/console doctrine:database:create
	php bin/console migrate
	php bin/console server:run
