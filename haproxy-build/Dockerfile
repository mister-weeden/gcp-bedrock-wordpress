FROM haproxy:latest

# Copy HAProxy configuration
COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg

# Copy SSL certificates if available
COPY certs/ /certs/

# Copy error pages if available
COPY errors/ /etc/haproxy/errors/

# Expose ports
EXPOSE 80 443 8000 8080 8443

# Default command
CMD ["haproxy", "-f", "/usr/local/etc/haproxy/haproxy.cfg"]
