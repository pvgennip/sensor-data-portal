#!/bin/sh

sudo mkdir /root/backups

# Tell mysql container to make backup 
mysqldump -u akvo -p'o3qxQfejCKWYBd4y' sensors-akvo-org | gzip > /root/backups/sensors-akvo-org.sql.gz

# Tell Influx container to make backup 
sudo mkdir /tmp/influxdb/backups
influxd backup -database sensordata /tmp/influxdb/backups
tar -zcvf /root/backups/influx_sensordata.tar.gz /tmp/influxdb/backups
sudo rm /tmp/influxdb/backups

exit 0