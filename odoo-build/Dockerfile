FROM odoo:latest

USER root

# Install additional dependencies if needed
RUN apt-get update && apt-get install -y \
    python3-pip \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy configuration
COPY odoo.conf /etc/odoo/odoo.conf

# Set permissions
RUN chown -R odoo:odoo /etc/odoo/odoo.conf

# Set working directory
WORKDIR /mnt/extra-addons

# Switch back to odoo user
USER odoo

EXPOSE 8069 8071
CMD ["odoo"]
