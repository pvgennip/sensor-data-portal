#!/bin/sh

set -e

if [ ! -f "/data/.env" ]; then cp /data/.env.example /data/.env; fi

exec "$@"
