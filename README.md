# Sensor data portal
Open source sensor data portal for MQTT sensors 

![Akvo - Sensor data portal](Akvo%20-%20Sensor%20data%20portal.png)

Dependencies:
* Docker Laravel back-end container (installs Apache2, PHP, MySQL, InfluxDB) https://github.com/pvgennip/laradock
* Angular front-end (JS)
 * Responsive Admin template (HTML, CSS) https://github.com/almasaeed2010/AdminLTE/
 * ChartJS (JS) https://github.com/chartjs/Chart.js

# Installation
## Pre-install requirements: Git & Docker/LAMP (Linux Debian 8 [Jessie])
Git
```
apt-get install git
```

### Docker Engine + Compose 

https://docs.docker.com/engine/installation/linux/debian/ or https://gist.github.com/pvgennip/be5bb13b184069758bc14fbbe78599b5


OR


### LAMP Stack + Required software

#### LAMP:
https://gist.github.com/pvgennip/ab147414848f036a68d5dae6277987a5


Apache config:

SSL cert
```
sudo nano /etc/ssl/private/sensors-akvo-org.key
sudo nano /etc/ssl/certs/sensors-akvo-org.crt
```

VHost
```
sudo nano /etc/apache2/sites-available/sensors-akvo-org.conf

<VirtualHost sensors.akvo.org:80>
    ServerAdmin akvo@iconize.nl
    ServerName "sensors.akvo.org"

    Redirect / https://sensors.akvo.org

</VirtualHost>

<IfModule mod_ssl.c>
    <VirtualHost sensors.akvo.org:443>
        DocumentRoot /var/www/sensors-akvo-org/portal/public
        ServerName "sensors.akvo.org"

        <Directory /var/www/sensors-akvo-org/portal/public/>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Order allow,deny
            Allow from all
         </Directory>

        SSLEngine on
        SSLCertificateFile    /etc/ssl/certs/sensors-akvo-org.crt
        SSLCertificateKeyFile /etc/ssl/private/sensors-akvo-org.key

        <FilesMatch "\.(cgi|shtml|phtml|php)$">
                        SSLOptions +StdEnvVars
        </FilesMatch>
        <Directory /usr/lib/cgi-bin>
                        SSLOptions +StdEnvVars
        </Directory>

        BrowserMatch "MSIE [2-6]" \
                        nokeepalive ssl-unclean-shutdown \
                        downgrade-1.0 force-response-1.0
        # MSIE 7 and newer should be able to use keepalive
        BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown

    </VirtualHost>
</IfModule>
```

Mysql database:
```
mysql -u root -p 

CREATE DATABASE `sensors-akvo-org`;
CREATE USER 'akvo' IDENTIFIED BY 'o3qxQfejCKWYBd4y';
GRANT USAGE ON sensors-akvo-org TO 'akvo'@localhost IDENTIFIED BY 'o3qxQfejCKWYBd4y';

#### Required software:

* Influx
* Mosquitto: https://mosquitto.org/man/mosquitto-8.html
* MQTTWarn: https://github.com/jpmens/mqttwarn
* SPULserver: https://github.com/pvgennip/spul-mqtt-server

Add source for InfluxDB
```
curl -sL https://repos.influxdata.com/influxdb.key | sudo apt-key add -
source /etc/os-release
test $VERSION_ID = "7" && echo "deb https://repos.influxdata.com/debian wheezy stable" | tee /etc/apt/sources.list.d/influxdb.list
test $VERSION_ID = "8" && echo "deb https://repos.influxdata.com/debian jessie stable" | tee /etc/apt/sources.list.d/influxdb.list
```


```
sudo apt-get update
sudo apt-get install influxdb mosquitto python-pip
pip install paho-mqtt
cd ~
git clone https://github.com/jpmens/mqttwarn.git
```

Set configs:

Influx
```
curl -i -XPOST localhost:8086/query --user root:root --data-urlencode "q=CREATE USER admin WITH PASSWORD 'password' WITH ALL PRIVILEGES"
curl -i -XPOST localhost:8086/query --user admin:password --data-urlencode "q=CREATE DATABASE sensordata"
```
Restore data (old way as in https://docs.influxdata.com/influxdb/v1.6/administration/backup_and_restore/#restore)
```
influxd restore -database sensordata -metadir /var/lib/influxdb/meta -datadir /var/lib/influxdb/data /root/sensor-data-portal/backups/sensordata/
sudo chown -R influxdb:influxdb /var/lib/influxdb
```

Mosquitto
```
cd /etc/mosquitto/conf.d
mosquitto_passwd -c mqtt_passwords itay
sodaq
```

MQTT Warn:
```
cd ~/mqttwarn
sudo cp mqttwarn.ini.sample mqttwarn.ini
sudo nano mqttwarn.ini
```

Edit file according to ~/sensor-data-portal/laradock/mqttwarn/mqttwarn.ini.sample (NB: replace 'mosquitto' by 'localhost')
```
sudo cp ~/sensor-data-portal/laradock/mqttwarn/services/test.py services
sudo cp ~/sensor-data-portal/laradock/mqttwarn/services/influxdb_akvo.py services
```


Spul server
```
sudo nano ~/spul-mqtt-server/.env
AUTH_TOKEN=
MQTT_USER=itay
MQTT_PASS=sodaq
MQTT_HOST=localhost
MQTT_TOPIC=ITAY/HAP
DEBUG=false
LOG_NAME=spulserver
LOG_FILE=/tmp/output.log
SPUL_TS_PORT=9007
SPUL_PORT=9008
SOCKET_TIMEOUT=30000
BIG_ENDIAN=true
LITTLE_ENDIAN_PAYLOAD=false
HEADER_SIZE=12
MAX_FRAME_SIZE=512
```

Influx database


### Composer (for Laravel installation)
See https://getcomposer.org/download/

Use compuer globally
```mv composer.phar /usr/local/bin/composer```


### Start software:
```
./start_services.sh
```


## Clone this repo
```
cd ~/
git clone https://github.com/pvgennip/sensor-data-portal.git
cd /var/www
ln -s /root/sensor-data-portal/portal sensors-akvo-org
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
