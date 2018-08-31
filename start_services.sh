#!/bin/sh 

sudo systemctl restart influxdb
sudo systemctl restart mosquitto
sudo systemctl restart mqttwarn
sudo systemctl restart spulserver 

exit 0