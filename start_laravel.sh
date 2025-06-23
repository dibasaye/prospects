#!/bin/bash

# Start Laravel development server
echo "Starting YAYE DIA BTP Laravel Application..."
echo "Building assets..."
npm run build

echo "Starting Laravel server on port 3000..."
php artisan serve --host=0.0.0.0 --port=3000