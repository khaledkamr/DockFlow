#!/bin/bash

# Wait for any necessary services (like database) to be ready
# sleep 10

# Generate application key if not set
# php artisan key:generate --no-interaction --force

# Run Laravel optimization commands
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
exec "$@"
