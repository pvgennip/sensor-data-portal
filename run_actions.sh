#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

# docker
cd $base_dir/laradock
docker-compose stop
docker-compose up -d --build apache2 php-fpm mysql phpmyadmin influx workspace

# laravel
cd $base_dir/portal
composer install && sudo chmod -R 777 storage && sudo chmod -R 777 bootstrap/cache && php artisan migrate

# bower
cd $base_dir/portal
cd public/app && bower install --allow-root