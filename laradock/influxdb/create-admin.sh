#!/bin/bash

# Check cli input
if [ -z ${1+x} ]
then 
	PASS="admin"
else
	PASS="$1"
fi

echo "Creating Influx admin user..."

curl -i -XPOST https:/localhost:8086/query --user root:root --data-urlencode "q=CREATE USER admin WITH PASSWORD '$DBNAME' WITH ALL PRIVILEGES"
