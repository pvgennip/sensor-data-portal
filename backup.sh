#!/bin/sh

cd /root/sensor-data-portal

mkdir backups

mkdir /var/lib/docker/volumes/laradock_mysql/_data/backups
# Tell mysql container to make backup (Without -it for calling from cron!!)
docker exec laradock_mysql_1 bash -c "mysqldump -u homestead -p'secret' homestead | gzip > /var/lib/mysql/backups/homestead.sql.gz"
mv /var/lib/docker/volumes/laradock_mysql/_data/backups/*.gz backups


mkdir /var/lib/docker/volumes/laradock_influx/_data/backups
# Tell Influx container to make backup (Without -it for calling from cron!!)
docker exec laradock_influx_1 bash -c "influxd backup -database sensordata /var/lib/influxdb/backups"
tar -zcvf /var/lib/docker/volumes/laradock_influx/_data/influx_sensordata.tar.gz /var/lib/docker/volumes/laradock_influx/_data/backups
mv /var/lib/docker/volumes/laradock_influx/_data/*.gz backups
rm /var/lib/docker/volumes/laradock_influx/_data/backups/*

exit 0