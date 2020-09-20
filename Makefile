.PHONY: setup
setup:
	docker-compose pull
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php sh -c '\
		set -e \
		mkdir -p config/jwt \
		jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')} \
		echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 \
		echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout \
		setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt \
		setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt \
	'
	docker-compose exec php bin/console doctrine:database:create --no-interaction
	docker-compose exec php bin/console doctrine:schema:create --no-interaction
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
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