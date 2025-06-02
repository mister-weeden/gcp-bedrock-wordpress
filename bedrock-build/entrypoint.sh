#!/bin/bash
set -e

# Wait for database to be ready
if [ ! -z "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    ATTEMPTS=0
    MAX_ATTEMPTS=30
    until nc -z "$DB_HOST" 3306 || [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; do
        sleep 5
        ATTEMPTS=$((ATTEMPTS+1))
        echo "Waiting for database connection... $ATTEMPTS/$MAX_ATTEMPTS"
    done
    
    if [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; then
        echo "Error: Could not connect to database after $MAX_ATTEMPTS attempts"
        # Don't exit, just continue and retry
        echo "Waiting for database connection..."
        while ! nc -z "$DB_HOST" 3306; do
            sleep 5
            echo "Still waiting for database connection..."
        done
    fi
    
    echo "Database is up and running!"
fi

# Configure Memcached connection if available
if [ ! -z "$MEMCACHED_HOST" ]; then
    echo "Configuring Memcached connection..."
    echo "extension=memcached.so" > /usr/local/etc/php/conf.d/memcached.ini
    echo "session.save_handler = memcached" >> /usr/local/etc/php/conf.d/memcached.ini
    echo "session.save_path = \"$MEMCACHED_HOST:11211\"" >> /usr/local/etc/php/conf.d/memcached.ini
fi

# Set proper permissions
chown -R www-data:www-data /srv/bedrock
find /srv/bedrock -type d -exec chmod 755 {} \;
find /srv/bedrock -type f -exec chmod 644 {} \;

# Execute CMD
exec "$@"
