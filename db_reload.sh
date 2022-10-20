#!/bin/bash

php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load