#!/bin/sh

set -e

if [ ! -f "/opt/mqttwarn/mqttwarn.ini" ]; then cp /opt/mqttwarn/mqttwarn.ini.sample /opt/mqttwarn/mqttwarn.ini; fi

exec "$@"
