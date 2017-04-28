FROM influxdb:1.2

EXPOSE 8083 8086

COPY create-database.sh /etc/influxdb/create-database.sh
RUN chmod +x /etc/influxdb/create-database.sh
COPY create-admin.sh /etc/influxdb/create-admin.sh
RUN chmod +x /etc/influxdb/create-admin.sh

RUN ["/etc/influxdb/create-database.sh", "sensordata"]
RUN ["/etc/influxdb/create-admin.sh", "admin"]