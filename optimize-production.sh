#!/bin/bash

echo "🚀 Optimizing for production..."

# Enable OPcache
echo "⚡ Enabling OPcache..."
sudo phpenmod opcache

# Configure Redis for sessions and cache
echo "🔄 Configuring Redis..."
php artisan cache:clear
php artisan config:cache

# Optimize Composer autoloader
echo "📦 Optimizing Composer..."
composer install --no-dev --optimize-autoloader

# Build and optimize assets
echo "🎨 Building assets..."
npm run build

# Cache everything
echo "💾 Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set up queue workers
echo "👷 Setting up queue workers..."
sudo cp /path/to/your/supervisor/config /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all

# Configure log rotation
echo "📝 Setting up log rotation..."
sudo cp /path/to/your/logrotate/config /etc/logrotate.d/

echo "✅ Production optimization complete!"
