.PHONY: config

export DOCKER_BUILDKIT=0
export COMPOSE_DOCKER_CLI_BUILD=0

local-run-seed: env_local down up migrations-seed 
local-run: env_local down up migrations 

up:
	docker-compose up -d --build
	docker-compose exec app composer install	

down:
	docker-compose down

env_local:
	cp ./env.local ./wallet/.env

migrations:
	docker-compose exec app php artisan migrate

migrations-seed:
	docker-compose exec app php artisan migrate:refresh --seed

