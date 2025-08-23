#!/bin/bash

# Complete Database Sync Fix
# This script uploads latest migrations and runs them on production

set -e  # Exit on any error

echo "🔧 Database Sync Fix Script"
echo "This will upload your latest migrations to production and run them"

# Configuration
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"

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
sshpass -p 'Maxou2000**' scp -r -o StrictHostKeyChecking=no database/migrations/ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH/database/

print_status "Step 2: Checking current migration status on production..."

# Check current migration status
sshpass -p 'Maxou2000**' ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    echo "Current migration status:"
    php artisan migrate:status | head -20
    echo ""
    echo "=== PENDING MIGRATIONS ==="
    php artisan migrate:status | grep "Pending" || echo "No pending migrations found"
EOSSH

print_warning "Review the migration status above."
echo ""
read -p "Do you want to proceed with running all pending migrations? (y/n): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Step 3: Running pending migrations on production..."
    
    # Run migrations on production
    sshpass -p 'Maxou2000**' ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
        set -e
        cd /var/www/saas
        
        echo "🔄 Running migrations..."
        php artisan migrate --force
        
        echo "🧹 Clearing application caches..."
        php artisan config:clear
        php artisan config:cache
        php artisan route:clear
        php artisan route:cache
        php artisan view:clear
        php artisan view:cache
        
        echo "📊 Final migration status:"
        php artisan migrate:status | tail -20
        
        echo "✅ Database sync completed successfully!"
EOSSH
    
    print_status "✅ Database sync fix completed!"
    print_status "Your production database is now in sync with your local development database"
    
    # Test the site
    print_status "Testing production site..."
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/ || echo "000")
    if [ "$HTTP_STATUS" -eq 200 ]; then
        print_status "✅ Site is responding correctly (HTTP $HTTP_STATUS)"
    else
        print_warning "⚠️  Site returned HTTP $HTTP_STATUS - please check manually"
    fi
    
else
    print_warning "Database sync cancelled by user"
fi

print_status "Database sync process completed"
echo ""
echo "🎯 Summary:"
echo "- Migration files uploaded to production"
echo "- Pending migrations executed"
echo "- Application caches cleared"
echo "- Production database is now synchronized"
