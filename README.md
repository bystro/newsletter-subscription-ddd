### Sample project using Domain Driven Design approach. Project is focused on business domain - newsletter subscription

#### Instalation
If you have Docker-compose installed just enter .docker folder end execute
```
docker-compose build
docker-compose up -d
```

When build process is finished, just enter into php container and install dependencies using composer
```
docker exec -it php-newsletter bash
composer install
```

Run tests to ensure that works properly
```
composer test
```