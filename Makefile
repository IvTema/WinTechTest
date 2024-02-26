.PHONY: config

export DOCKER_BUILDKIT=0
export COMPOSE_DOCKER_CLI_BUILD=0

local-run-seed: env_local down up migrations-seed
local-run: env_local down up migrations

# pre_setup:
# 	sudo service apache2 stop
# 	sudo chmod o+w ./storage/ -R
# 	sudo chown www-data:www-data -R ./storage

up:
	docker-compose up -d --build
	docker-compose exec app composer install	

down:
	docker-compose down

env_local:
	cp ./env.local ./.env
	cp ./env.local ./wallet/.env

migrations:
	docker-compose exec app php artisan migrate

migrations-seed:
	docker-compose exec app php artisan migrate:refresh --seed
	docker-compose exec app php artisan db:seed

