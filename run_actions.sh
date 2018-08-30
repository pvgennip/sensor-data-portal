#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

backup.sh

# docker
#cd $base_dir/laradock
# docker-compose stop
# docker-compose up -d --remove-orphans --build apache2 php-fpm mysql phpmyadmin influx mosquitto spulserver mqttwarn workspace

# laravel set up and build
# cd $base_dir
# docker exec -it laradock_workspace_1 script /dev/null -c "if [ ! -f '.env' ]; then cp .env.example .env && php artisan key:generate; fi"
# docker exec -it laradock_workspace_1 script /dev/null -c "composer install && chmod -R 777 storage && chmod -R 777 bootstrap/cache && php artisan migrate && php artisan db:seed && php artisan storage:link"

# angular app (switch to laradock user with 'su laradock' to have access to node, npm, bower)
# cd $base_dir
# docker exec -it laradock_workspace_1 bash 
cd public/webapp
# mkdir vendor
bower install --allow-rootcd 

# set log rotate
sudo cp etc_logrotate.d_sensor-data-portal_backups /etc/logrotate.d/sensor-data-portal_backups
sudo crontab < crontab

./start_services.sh

exit 0

