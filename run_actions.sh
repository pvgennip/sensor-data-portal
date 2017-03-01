#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

# docker
cd $base_dir/laradock
docker-compose stop
docker-compose up -d --build apache2 php-fpm mysql phpmyadmin influx workspace

# laravel set up and build
cd $base_dir
docker exec -it laradock_workspace_1 script /dev/null -c "if [ ! -f '.env' ]; then cp .env.example .env && php artisan key:generate; fi"
docker exec -it laradock_workspace_1 script /dev/null -c "composer install && chmod -R 777 storage && chmod -R 777 bootstrap/cache && php artisan migrate"

# angular app 
cd $base_dir/portal
cd public/app && bower install --allow-root