# Sensor data portal
Open source sensor data portal for MQTT sensors 

Dependencies:
* Docker Laravel back-end container (installs Apache2, PHP, MySQL, InfluxDB) https://github.com/pvgennip/laradock
* Angular front-end (JS)
 * Responsive Admin template (HTML, CSS) https://github.com/almasaeed2010/AdminLTE/
 * ChartJS (JS) https://github.com/chartjs/Chart.js

## Pre-install requirements: Git & Docker (Linux Debian 8 [Jessie])
Git
```
apt-get install git
```

Docker

https://docs.docker.com/engine/installation/linux/debian/ or https://gist.github.com/pvgennip/be5bb13b184069758bc14fbbe78599b5



## Clone this repo
```
git clone https://github.com/pvgennip/sensor-data-portal.git
```

## Run required actions to start
```
cd sensor-data-portal
./run-actions.sh
```

# Deploy updates
```
cd sensor-data-portal
./deploy.sh
```
