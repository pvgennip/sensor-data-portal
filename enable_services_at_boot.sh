#!/bin/sh 

sudo systemctl enable influxdb
sudo systemctl enable mosquitto
sudo systemctl enable mqttwarn
sudo systemctl enable spulserver 

exit 0