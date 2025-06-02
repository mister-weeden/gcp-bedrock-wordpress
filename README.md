# Harper Corporation Docker Infrastructure

This repository contains the Docker configuration for Harper Corporation's main applications. We use Docker Compose to manage three primary services, each running in its own container and serving different business functions.

## Overview

Our infrastructure consists of three main applications:

1. **Bedrock WordPress** - Corporate Website
   - **Service Name**: bedrock
   - **Purpose**: Main corporate website and content management
   - **URL**: [www.harper-corp.com](https://www.harper-corp.com)
   - **Tech Stack**: WordPress with Bedrock (modern WordPress stack with improved security and development workflow)

2. **Odoo ERM** - Enterprise Resource Management
   - **Service Name**: odoo
   - **Purpose**: Enterprise Resource Management system for business operations
   - **URL**: [apps.harper-corp.com](https://apps.harper-corp.com)
   - **Tech Stack**: Odoo ERP/CRM platform

3. **ComfyUI** - AI Media Generation
   - **Service Name**: comfyui
   - **Purpose**: Video and image generation using AI
   - **URL**: [ai.harper-corp.com](https://ai.harper-corp.com)
   - **Tech Stack**: ComfyUI (AI image and video generation framework)

## Docker Compose Configuration

The `docker-compose.yml` file in this repository orchestrates these services, managing their dependencies, networking, and persistent storage. Each service is configured with appropriate environment variables, volume mounts, and network settings to ensure proper isolation and communication.

## Getting Started

### Prerequisites

- Docker and Docker Compose installed on your system
- Access to Harper Corporation's private Docker registry (if applicable)

### Starting the Services

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down
```

## Service-Specific Information

### Bedrock WordPress (www.harper-corp.com)

The corporate website runs on a modern WordPress stack called Bedrock, which provides improved security, dependency management, and development workflows.

### Odoo ERM (apps.harper-corp.com)

Our ERM system handles business operations including inventory management, CRM, accounting, and more.

### ComfyUI (ai.harper-corp.com)

This AI-powered service generates images and videos for marketing, product visualization, and other creative needs.

## Maintenance

Regular backups of all service data are essential. Each service has volume mounts configured in the docker-compose.yml file to persist data.

## Network Configuration

All three services are exposed through a reverse proxy that handles SSL termination and routes traffic based on the domain name.

---

For more detailed information about each service, please refer to their respective documentation folders within this repository.
