.DEFAULT_GOAL := test

init: docker-down-clear app-clear docker-pull docker-build docker-up app-init
app-init: composer-install assets-install wait-db app-migrations app-fixtures app-ready
test: app-test
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

wait-db:
	docker-compose run --rm php-cli wait-for-it db:5432 -t 30

app-test:
	docker-compose run --rm php-cli php bin/phpunit

composer-install:
	docker-compose run --rm php-cli composer install

assets-install:
	docker-compose run --rm node-cli yarn install
	docker-compose run --rm node-cli npm rebuild node-sass

app-ready:
	docker run --rm -v ${PWD}/app:/app --workdir=/app alpine touch .ready

app-clear:
	docker run --rm -v ${PWD}/app:/app --workdir=/app alpine rm -f .ready

app-migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

app-fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction
