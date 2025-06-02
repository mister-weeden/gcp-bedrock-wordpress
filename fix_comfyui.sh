#!/bin/bash

# Script to fix ComfyUI startup by modifying the supervisor script
# Created on $(date)

# Create a temporary file with the modified script
docker exec comfyui bash -c 'cat /opt/ai-dock/bin/supervisor-comfyui.sh | sed "s/PLATFORM_ARGS=\"--cpu\"/PLATFORM_ARGS=\"\"/g" > /tmp/supervisor-comfyui.sh'

# Make it executable
docker exec comfyui bash -c 'chmod +x /tmp/supervisor-comfyui.sh'

# Replace the original script
docker exec comfyui bash -c 'cp /tmp/supervisor-comfyui.sh /opt/ai-dock/bin/supervisor-comfyui.sh'

# Create a custom arguments file with highvram setting
docker exec comfyui bash -c 'echo "--highvram" > /etc/comfyui_args.conf'

# Restart the ComfyUI service
docker exec comfyui supervisorctl restart comfyui

echo "ComfyUI startup script has been modified to enable high VRAM mode."
echo "Waiting for ComfyUI to restart..."
sleep 5

# Check if ComfyUI is running with the new settings
docker exec comfyui ps aux | grep "python main.py" | grep -v grep

echo ""
echo "Done! ComfyUI should now be running with high VRAM mode enabled."
