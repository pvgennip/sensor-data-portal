#!/bin/sh 
branch=${1:-master}  
base_dir=$(pwd)

./backup.sh

cd portal/public/webapp
mkdir vendor
bower install --allow-root
cd $base_dir

# set log rotate
sudo cp etc_logrotate.d_sensor-data-portal_backups /etc/logrotate.d/sensor-data-portal_backups
sudo crontab < crontab

./start_services.sh

exit 0
