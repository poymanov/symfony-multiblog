init: docker-down-clear docker-pull docker-build docker-up app-init
app-init: wait-db

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

test:
	docker-compose run --rm php-cli php bin/phpunit
