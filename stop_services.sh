#!/bin/sh 

sudo systemctl stop spulserver 
sudo systemctl stop mqttwarn
sudo systemctl stop mosquitto
sudo systemctl stop influxdb

exit 0