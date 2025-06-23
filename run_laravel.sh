#!/bin/bash

# YAYE DIA BTP Laravel Application Startup Script
echo "🏗️  Starting YAYE DIA BTP Real Estate Management System..."

# Clear Laravel caches
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Build frontend assets
echo "Building frontend assets..."
npm run build

# Start Laravel server
echo "Starting Laravel server on port 3000..."
echo "🌐 Access the application at: http://localhost:3000"
echo ""
echo "Demo accounts:"
echo "👤 Admin: admin@yayedia.com / admin123"
echo "👤 Manager: manager@yayedia.com / manager123" 
echo "👤 Commercial: commercial@yayedia.com / commercial123"
echo ""

php artisan serve --host=0.0.0.0 --port=3000