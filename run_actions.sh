#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

./backup.sh

./stop_services.sh

cd portal/public/webapp
mkdir vendor
bower install --allow-root
cd $base_dir

sudo cp etc_systemd_system_mqttwarn /etc/systemd/system/mqttwarn.service

# set log rotate
sudo cp etc_logrotate.d_sensor-data-portal_backups /etc/logrotate.d/sensor-data-portal_backups
sudo crontab < crontab

# enable services to start at boot
./enable_services_at_boot.sh

./start_services.sh

exit 0
