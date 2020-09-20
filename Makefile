.PHONY: setup
setup:
	docker-compose pull
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php bin/console doctrine:schema:create --no-interaction
	docker-compose exec php bin/console cache:clear

.PHONY: start
start:
	docker-compose up -d

.PHONY: stop
stop:
	docker-compose stop

.PHONY: clean
clean:
	docker-compose down

.PHONY: test
test:
	docker-compose exec php bin/console doctrine:fixtures:load --env=test --no-interaction
	docker-compose exec php vendor/bin/simple-phpunit