#!/bin/bash

# Deploy Database Fix to Production
# This script uploads migrations and seeds critical data on production

set -e  # Exit on any error

echo "🔧 Deploying Database Fix to Production"
echo "This will upload your latest migrations and seed critical data"

# Configuration
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"
PRODUCTION_PASSWORD="Maxou2000**"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    print_error "Not in Laravel project directory"
    exit 1
fi

print_status "Step 1: Uploading latest migration files to production..."

# Upload migration files
sshpass -p "$PRODUCTION_PASSWORD" scp -r -o StrictHostKeyChecking=no database/migrations/ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH/database/

print_status "Step 2: Uploading seeder files to production..."

# Upload seeder files
sshpass -p "$PRODUCTION_PASSWORD" scp -r -o StrictHostKeyChecking=no database/seeders/ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH/database/

print_status "Step 3: Running migrations and seeders on production..."

# Run migrations and seeders on production
sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    set -e
    cd /var/www/saas
    
    echo "🔄 Running migrations..."
    php artisan migrate --force
    
    echo "🌱 Seeding critical data..."
    php artisan db:seed --class=CriticalDataSeeder --force
    
    echo "🧹 Clearing application caches..."
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    php artisan view:clear
    php artisan view:cache
    
    echo "📊 Final migration status:"
    php artisan migrate:status | tail -10
    
    echo "✅ Database deployment completed successfully!"
EOSSH

print_status "✅ Production database deployment completed!"
print_status "Your production database now has all the critical data"

# Test the site
print_status "Testing production site..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/ || echo "000")
if [ "$HTTP_STATUS" -eq 200 ]; then
    print_status "✅ Site is responding correctly (HTTP $HTTP_STATUS)"
else
    print_warning "⚠️  Site returned HTTP $HTTP_STATUS - please check manually"
fi

print_status "🎯 Summary:"
echo "- Migration files uploaded to production"
echo "- Seeder files uploaded to production"
echo "- Migrations executed on production"
echo "- Critical data seeded (User Types, Subscription Plans, Admin User)"
echo "- Application caches cleared"
echo ""
echo "🔑 Admin Login Details:"
echo "Email: admin@onlyverified.io"
echo "Password: AdminMaxou2025!"
echo ""
echo "🌐 Production Site: https://onlyverified.io"
echo "👨‍💼 Admin Panel: https://onlyverified.io/filament-admin"
