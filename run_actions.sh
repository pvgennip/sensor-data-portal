#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

./backup.sh

./stop_services.sh

cd portal
composer install && sudo chmod -R 777 storage && sudo chmod -R 777 bootstrap/cache && php artisan migrate && php artisan vendor:publish && php artisan storage:link
composer dumpautoload

cd portal/public/webapp
mkdir vendor
bower install --allow-root
cd $base_dir

# copy services
sudo cp /root/spul-mqtt-server/etc_systemd_system_spulserver /etc/systemd/system/spulserver.service
sudo cp /var/www/sensors-akvo-org/etc_systemd_system_mqttwarn /etc/systemd/system/mqttwarn.service

# set log rotate
sudo cp etc_logrotate.d_sensor-data-portal_backups /etc/logrotate.d/sensor-data-portal_backups
sudo cp etc_logrotate.d_sensor-data-portal_mqttwarn /etc/logrotate.d/sensor-data-portal_mqttwarn
sudo crontab < crontab

# enable services to start at boot
./enable_services_at_boot.sh

./start_services.sh

exit 0
