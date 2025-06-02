#!/bin/bash

# Update the .env file with proper database host
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/# DB_HOST/DB_HOST/g' .env"
docker-compose exec bedrock bash -c "cd /srv/bedrock && sed -i 's/localhost/bedrock-db/g' .env"

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
