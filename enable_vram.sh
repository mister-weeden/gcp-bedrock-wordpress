#!/bin/bash

# Script to modify ComfyUI startup to enable VRAM state
# Created on $(date)

# Create a custom arguments file in the container
# The --vram-state parameter is not valid, we need to use the correct parameter
docker exec comfyui bash -c 'echo "--highvram" > /etc/comfyui_args.conf'

# Restart the ComfyUI service to apply changes
docker exec comfyui supervisorctl restart comfyui

echo "VRAM state has been set to highvram for ComfyUI."
echo "Waiting for ComfyUI to restart..."
sleep 5

# Check if ComfyUI is running with the new settings
docker exec comfyui ps aux | grep "python main.py" | grep -v grep

echo ""
echo "Done! ComfyUI should now be running with high VRAM mode enabled."
