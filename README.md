# Harper Corp Container Images

![Build and Push Container Images](https://github.com/mister-weeden/harper-corp-containers/actions/workflows/build-and-push.yml/badge.svg)

This repository contains Docker container images used for various services.

## Available Images

- **harper-corp/haproxy**: HAProxy load balancer
- **harper-corp/bedrock**: WordPress Bedrock installation
- **harper-corp/odoo**: Odoo ERP system
- **harper-corp/odoo-db**: PostgreSQL database for Odoo
- **harper-corp/comfyui**: ComfyUI application

## Building Images

To build all images, run the build script:

```bash
./build-images.sh
```

## Docker Compose

The `docker-compose.yml` file in this repository defines all services and their relationships.

To start all services:

```bash
docker-compose up -d
```

## GitHub Workflow

This repository includes a GitHub workflow that automatically builds and pushes images to GitHub Container Registry (ghcr.io) when changes are pushed to the main branch.

## Image Directories

- `haproxy-build/`: HAProxy image build files
- `bedrock-build/`: WordPress Bedrock image build files
- `odoo-build/`: Odoo image build files
- `odoo-db-build/`: PostgreSQL for Odoo image build files
- `comfyui-build/`: ComfyUI image build files