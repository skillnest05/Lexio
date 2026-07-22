#!/bin/bash
# docker-entrypoint.sh
# Railway sets $PORT dynamically. Apache must listen on it.

PORT=${PORT:-80}

# Update Apache to listen on the Railway-assigned PORT
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-enabled/000-default.conf

exec "$@"
