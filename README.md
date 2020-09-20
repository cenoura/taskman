# TaskMan

A simple task manager created using Symfony.

This project applies concepts of TDD, DDD, CQRS and Event Driven Architecture.

To run this project, you must have `Docker` and `Docker-Compose` installed.

Before running the project, please run the following commands:
```
make setup

docker-compose exec php sh -c '
    set -e
    mkdir -p config/jwt
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```

After the project has been setup, and the containers have started, you can:
- create a new user using the command:
```
docker-compose exec php bin/console app:register-user
```
- get a JWT token to use on API calls:
```
curl --location --request POST 'http://localhost/api/auth-token' \
--form 'username=YOUR_USERNAME' \
--form 'password=YOUR_PASSWORD'
```
- create new tasks:
```
curl --location --request POST 'http://localhost/api/tasks' \
--header 'Authorization: Bearer YOUR_JWT_TOKEN' \
--form 'title=Task #1' \
--form 'execution_date=2020-09-20'
```
- list tasks:
```
# if you don't set the `execution_date` param, the default value is the current date

curl --location --request GET 'http://localhost/api/tasks?execution_date=2020-09-20' \
--header 'Authorization: Bearer YOUR_JWT_TOKEN'
```

### Tests
To run unit and functional tests:
```
make test
```