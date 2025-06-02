#!/bin/bash

# Script to build Docker images for harper-corp containers

echo "Building harper-corp Docker images..."

# Generate random passwords
echo "Generating secure passwords..."
BEDROCK_ROOT_PASSWORD=$(openssl rand -base64 12)
BEDROCK_PASSWORD=$(openssl rand -base64 12)
DATABASE_PASSWORD=$(openssl rand -base64 12)
ODOO_ADMIN_PASSWORD=$(openssl rand -base64 12)
ODOO_PASSWORD=$(openssl rand -base64 12)

# Update env.local file with new passwords
cat > ./env.local << EOF
# HAProxy Configuration
HAPROXY_ADMIN_HOST_IP=192.168.1.10
HAPROXY_ADMIN_PORT=8000
HAPROXY_HOST_IP=192.168.1.145
HAPROXY_HTTP_PORT=8080
HAPROXY_HTTPS_PORT=8443

# Bedrock Configuration
BEDROCK_DB_HOST=bedrock-db
BEDROCK_MEMCACHED_HOST=bedrock-memcached
BEDROCK_ROOT_PASSWORD=$BEDROCK_ROOT_PASSWORD
BEDROCK_DATABASE=bedrock
BEDROCK_USER=bedrock
BEDROCK_PASSWORD=$BEDROCK_PASSWORD

# Bedrock WordPress Configuration
DB_NAME=bedrock
DB_USER=bedrock
DB_PASSWORD=$DATABASE_PASSWORD
WP_ENV=production
WP_HOME=http://www.harper-corp.com
WP_SITEURL=\${WP_HOME}/wp
AUTH_KEY=$(openssl rand -base64 48)
SECURE_AUTH_KEY=$(openssl rand -base64 48)
LOGGED_IN_KEY=$(openssl rand -base64 48)
NONCE_KEY=$(openssl rand -base64 48)
AUTH_SALT=$(openssl rand -base64 48)
SECURE_AUTH_SALT=$(openssl rand -base64 48)
LOGGED_IN_SALT=$(openssl rand -base64 48)
NONCE_SALT=$(openssl rand -base64 48)

# Odoo Configuration
ODOO_PORT=8069
ODOO_LONGPOLLING_PORT=8071
POSTGRES_PORT=5432

# Odoo Server Configuration
ODOO_ADMIN_PASSWORD=$ODOO_ADMIN_PASSWORD
ODOO_DB_HOST=odoo-db
ODOO_DB_PORT=5432
ODOO_DB_USER=odoo
ODOO_DB_PASSWORD=$ODOO_PASSWORD
ODOO_ADDONS_PATH=/mnt/extra-addons
ODOO_DATA_DIR=/var/lib/odoo

# Odoo SMTP Configuration
ODOO_SMTP_SERVER=mailhog
ODOO_SMTP_PORT=1025
ODOO_SMTP_SSL=False
ODOO_SMTP_USER=False
ODOO_SMTP_PASSWORD=False

# Mailhog Configuration
MAILHOG_SMTP_PORT=1025
MAILHOG_UI_PORT=8025

# ComfyUI Configuration
COMFYUI_HOST_IP=192.168.1.145
COMFYUI_PORT=18188
EOF

echo "Updated env.local with secure passwords"

# Copy files from source directories to build directories
echo "Copying files to build directories..."

# ComfyUI
echo "Preparing comfyui build..."
mkdir -p ./comfyui-build/supervisor
cp -r ./comfyui/* ./comfyui-build/ 2>/dev/null || true
cp -r ./supervisor/* ./comfyui-build/supervisor/ 2>/dev/null || true

# Bedrock
echo "Preparing bedrock build..."
cp -r ./bedrock/* ./bedrock-build/ 2>/dev/null || true

# Update Bedrock .env file if it exists
if [ -f "./bedrock-build/.env" ]; then
  sed -i "s/DB_PASSWORD=.*/DB_PASSWORD='$DATABASE_PASSWORD'/g" ./bedrock-build/.env
fi

# Create .env file from .env.example if it exists
if [ -f "./bedrock-build/.env.example" ] && [ ! -f "./bedrock-build/.env" ]; then
  cp ./bedrock-build/.env.example ./bedrock-build/.env
  sed -i "s/DB_PASSWORD=.*/DB_PASSWORD='$DATABASE_PASSWORD'/g" ./bedrock-build/.env
fi

# Odoo
echo "Preparing odoo build..."
mkdir -p ./odoo-build
cp -r ./odoo-config/* ./odoo-build/ 2>/dev/null || true

# Update odoo.conf with new passwords
if [ -f "./odoo-build/odoo.conf" ]; then
  sed -i "s/admin_passwd = .*/admin_passwd = $ODOO_ADMIN_PASSWORD/g" ./odoo-build/odoo.conf
  sed -i "s/db_password = .*/db_password = $ODOO_PASSWORD/g" ./odoo-build/odoo.conf
fi

# HAProxy
echo "Preparing haproxy build..."
cp -r ./haproxy/* ./haproxy-build/ 2>/dev/null || true

# Update docker-compose.yml with new passwords
sed -i "s/BEDROCK_ROOT_PASSWORD:-bedrock_root_password}/BEDROCK_ROOT_PASSWORD:-$BEDROCK_ROOT_PASSWORD}/g" docker-compose.yml
sed -i "s/BEDROCK_PASSWORD:-bedrock_password}/BEDROCK_PASSWORD:-$BEDROCK_PASSWORD}/g" docker-compose.yml

# Build comfyui image
echo "Building comfyui image..."
docker build -t harper-corp/comfyui:latest ./comfyui-build

# Build bedrock image
echo "Building bedrock image..."
docker build -t harper-corp/bedrock:latest ./bedrock-build

# Build odoo image
echo "Building odoo image..."
docker build -t harper-corp/odoo:latest ./odoo-build

# Build odoo-db image
echo "Building odoo-db image..."
docker build -t harper-corp/odoo-db:latest ./odoo-db-build

# Build haproxy image
echo "Building haproxy image..."
docker build -t harper-corp/haproxy:latest ./haproxy-build

echo "All images built successfully!"
echo "Run 'docker images | grep harper-corp' to see the built images."
echo "Secure passwords have been generated and stored in env.local"