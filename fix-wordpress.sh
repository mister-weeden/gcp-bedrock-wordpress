#!/bin/bash

# Update the .env file with proper database host and generate salts
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/# DB_HOST/DB_HOST/g' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/localhost/bedrock-db/g' .env"

# Generate WordPress salts
SALTS=$(curl -s https://api.wordpress.org/secret-key/1.1/salt/)
SALTS=$(echo "$SALTS" | sed "s/define('//g" | sed "s/', '/ = '/g" | sed "s/');/'/g" | sed "s/\\\\/\\\\\\\\/g")

# Replace the salts in the .env file
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/AUTH_KEY=.*/AUTH_KEY=\"$(echo "$SALTS" | grep AUTH_KEY | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/SECURE_AUTH_KEY=.*/SECURE_AUTH_KEY=\"$(echo "$SALTS" | grep SECURE_AUTH_KEY | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/LOGGED_IN_KEY=.*/LOGGED_IN_KEY=\"$(echo "$SALTS" | grep LOGGED_IN_KEY | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/NONCE_KEY=.*/NONCE_KEY=\"$(echo "$SALTS" | grep NONCE_KEY | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/AUTH_SALT=.*/AUTH_SALT=\"$(echo "$SALTS" | grep AUTH_SALT | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/SECURE_AUTH_SALT=.*/SECURE_AUTH_SALT=\"$(echo "$SALTS" | grep SECURE_AUTH_SALT | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/LOGGED_IN_SALT=.*/LOGGED_IN_SALT=\"$(echo "$SALTS" | grep LOGGED_IN_SALT | cut -d \= -f2- | sed 's/^ //')\"/' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/NONCE_SALT=.*/NONCE_SALT=\"$(echo "$SALTS" | grep NONCE_SALT | cut -d \= -f2- | sed 's/^ //')\"/' .env"

# Create the app directory structure
docker-compose exec bedrock bash -c "mkdir -p /srv/bedrock/web/app/uploads"
docker-compose exec bedrock bash -c "mkdir -p /srv/bedrock/web/app/plugins"
docker-compose exec bedrock bash -c "mkdir -p /srv/bedrock/web/app/themes"
docker-compose exec bedrock bash -c "mkdir -p /srv/bedrock/web/app/mu-plugins"

# Set proper permissions
docker-compose exec bedrock bash -c "chown -R www-data:www-data /srv/bedrock"
docker-compose exec bedrock bash -c "find /srv/bedrock -type d -exec chmod 755 {} \;"
docker-compose exec bedrock bash -c "find /srv/bedrock -type f -exec chmod 644 {} \;"

# Restart the container
docker-compose restart bedrock
