#!/bin/bash

# Wait for any necessary services (like database) to be ready
# sleep 10

# Generate application key if not set
# php artisan key:generate --no-interaction --force

if [ -n "$PORT" ]; then
  sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
fi

# Run Laravel optimization commands
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
exec "$@"
