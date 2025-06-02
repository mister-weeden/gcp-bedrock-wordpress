FROM python:3.10-slim

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    wget \
    curl \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create workspace directory
RUN mkdir -p /workspace

# Set working directory
WORKDIR /workspace

# Clone ComfyUI repository
RUN git clone https://github.com/comfyanonymous/ComfyUI.git /workspace/ComfyUI

# Install Python dependencies
RUN pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cpu
RUN cd /workspace/ComfyUI && pip install -r requirements.txt

# Copy supervisor configurations
COPY supervisor/*.conf /etc/supervisor/conf.d/

# Create entrypoint script
RUN echo '#!/bin/bash\n\
cd /workspace/ComfyUI\n\
exec supervisord -c /etc/supervisor/supervisord.conf\n' > /usr/local/bin/init.sh \
    && chmod +x /usr/local/bin/init.sh

# Expose port
EXPOSE 18188

# Set entrypoint
ENTRYPOINT ["init.sh"]
