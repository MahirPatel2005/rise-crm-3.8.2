#!/bin/bash
set -e

# If the /var/www/html/storage directory exists (where the persistent disk is mounted on Render)
if [ -d "/var/www/html/storage" ]; then
    echo "Persistent storage directory found at /var/www/html/storage."

    # Setup files directory
    if [ ! -d "/var/www/html/storage/files" ]; then
        echo "Initializing persistent files directory..."
        mkdir -p /var/www/html/storage/files
        if [ -d "/var/www/html/files" ]; then
            # Copy default files if any
            cp -a /var/www/html/files/. /var/www/html/storage/files/ || true
        fi
    fi

    # Setup writable directory
    if [ ! -d "/var/www/html/storage/writable" ]; then
        echo "Initializing persistent writable directory..."
        mkdir -p /var/www/html/storage/writable
        if [ -d "/var/www/html/writable" ]; then
            # Copy default files if any
            cp -a /var/www/html/writable/. /var/www/html/storage/writable/ || true
        fi
    fi

    # Remove the local folders (which will be replaced by symlinks)
    rm -rf /var/www/html/files
    rm -rf /var/www/html/writable

    # Symlink to persistent storage
    ln -s /var/www/html/storage/files /var/www/html/files
    ln -s /var/www/html/storage/writable /var/www/html/writable

    # Ensure permissions are correct on the persistent storage
    chown -R www-data:www-data /var/www/html/storage
    echo "Symlinks created successfully."
else
    echo "No persistent storage found at /var/www/html/storage. Using local directories."
fi

# Run the CMD passed to the docker container (usually apache2-foreground)
exec "$@"
