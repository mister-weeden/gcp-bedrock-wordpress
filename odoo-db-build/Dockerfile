FROM postgres:13

# Environment variables for database setup
ENV POSTGRES_USER=odoo
ENV POSTGRES_PASSWORD=odoo_password
ENV POSTGRES_DB=postgres

# Copy initialization scripts if needed
# COPY ./init-scripts/ /docker-entrypoint-initdb.d/

EXPOSE 5432

# Default command
CMD ["postgres"]
