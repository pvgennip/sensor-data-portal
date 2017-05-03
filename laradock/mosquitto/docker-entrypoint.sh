#!/bin/sh

set -e

if [ ! -f "/mqtt/config/conf.d/mqtt_passwords" ]; then cp /mqtt/config/conf.d/mqtt_passwords.example /mqtt/config/conf.d/mqtt_passwords; fi

exec "$@"
