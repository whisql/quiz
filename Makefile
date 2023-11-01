# Export environmental variables from file
include .env
export

# aliases
php := docker-compose exec php
fix-vendor-permissions := $(php) chmod -fR 777 /app/vendor/


#############################################
#				Docker-compose				#
#############################################

## Build docker containers
build:
	docker compose build --parallel

## Start docker containers
up:
	docker compose up -d --remove-orphans

## Stop docker containers
stop:
	docker compose stop

## Down docker containers
down:
	docker compose down

## Restart docker containers
restart: stop up


#############################################
#				Project						#
#############################################

## Open bash console in PHP container
bash:
	$(php) bash

init:
	docker compose up -d --remove-orphans
	$(php) composer install
	$(MAKE) fix-vendor-permissions
	$(symfony) doctrine:migrations:migrate --no-interaction
	$(symfony) doctrine:fixtures:load --no-interaction
	# Add some extra steps here: clearing cache, creating databases, executing migrations, loading fixtures, etc.

## Run composer
composer:
	$(php) composer $(filter-out $@,$(MAKECMDGOALS))
	$(fix-vendor-permissions)

## Fix vendor dir permissions
fix-vendor-permissions:
	$(php) chmod -fR 777 /app/vendor/

## Start quiz
start-quiz:
	$(symfony) quiz:start

#############################################
#				Symfony						#
#############################################
symfony := $(php) bin/console