#!/bin/bash

docker-compose exec php bin/console doctrine:schema:drop --force --full-database
docker-compose exec php bin/console doctrine:migrations:migrate
docker-compose exec php bin/console doctrine:fixtures:load