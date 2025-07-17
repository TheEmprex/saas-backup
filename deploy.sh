#!/bin/bash

# Laravel SaaS Production Deployment Script

echo "🚀 Starting deployment..."

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm ci --only=production

# Build assets
echo "🔨 Building assets..."
npm run build

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan down --refresh=15
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Queue restart
echo "🔄 Restarting queue..."
php artisan queue:restart

# Bring application back up
echo "✅ Bringing application up..."
php artisan up

echo "🎉 Deployment completed successfully!"
