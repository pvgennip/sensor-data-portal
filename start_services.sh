#!/bin/sh 

sudo systemctl start influxdb
sudo systemctl start mosquitto
sudo systemctl start spulserver 
cd /root/mqttwarn && nohup python mqttwarn.py && cd /root &

exit 0