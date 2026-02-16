#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
while ! nc -z db 3306; do
  sleep 1
done
echo "MySQL is up!"

# Run migrations and seed data
# Note: In production, you might want to run this manually or check if migrations are needed.
# For local dev, we run it to ensure the DB is populated as requested.
php artisan migrate:fresh --seed --force

# Start the original command (php-fpm)
exec "$@"
