#!/bin/bash

# Script to fix ComfyUI startup by ensuring CPU mode is used
# Created on $(date)

# Create a temporary file with the modified script
docker exec comfyui bash -c 'cat /opt/ai-dock/bin/supervisor-comfyui.sh | sed "s/PLATFORM_ARGS=\"\"/PLATFORM_ARGS=\"--cpu\"/g" > /tmp/supervisor-comfyui.sh'

# Make it executable
docker exec comfyui bash -c 'chmod +x /tmp/supervisor-comfyui.sh'

# Replace the original script
docker exec comfyui bash -c 'cp /tmp/supervisor-comfyui.sh /opt/ai-dock/bin/supervisor-comfyui.sh'

# Remove any custom arguments that might conflict with CPU mode
docker exec comfyui bash -c 'echo "" > /etc/comfyui_args.conf'

# Restart the ComfyUI service
docker exec comfyui supervisorctl restart comfyui

echo "ComfyUI startup script has been modified to use CPU mode."
echo "Waiting for ComfyUI to restart..."
sleep 5

# Check if ComfyUI is running with the new settings
docker exec comfyui ps aux | grep "python main.py" | grep -v grep

echo ""
echo "Done! ComfyUI should now be running in CPU mode."
