#!/bin/bash

# Script to build Docker images for harper-corp containers

echo "Building harper-corp Docker images..."

# Copy files from source directories to build directories
echo "Copying files to build directories..."

# ComfyUI
echo "Preparing comfyui build..."
cp -r ./comfyui/* ./comfyui-build/ 2>/dev/null || true
cp -r ./supervisor/* ./comfyui-build/supervisor/ 2>/dev/null || true

# Bedrock
echo "Preparing bedrock build..."
cp -r ./bedrock/* ./bedrock-build/ 2>/dev/null || true

# Odoo
echo "Preparing odoo build..."
cp -r ./odoo-config/* ./odoo-build/ 2>/dev/null || true

# HAProxy
echo "Preparing haproxy build..."
cp -r ./haproxy/* ./haproxy-build/ 2>/dev/null || true

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