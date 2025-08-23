#!/bin/bash

# Remote Database Synchronization Script
# This script connects to production server and runs pending migrations

set -e  # Exit on any error

echo "🌐 Remote Database Synchronization Script"
echo "This will connect to production server and sync database migrations"

# Configuration from deployment script
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

print_status "Connecting to production server to check migration status..."

# Check current migration status on production
print_status "Current migration status on production:"
sshpass -p 'Maxou2000**' ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
    cd $PRODUCTION_PATH
    php artisan migrate:status
EOF

print_warning "The above shows the current migration status on production server."
echo "Any 'Pending' migrations will be run."
echo ""
read -p "Do you want to proceed with running pending migrations on production? (y/n): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Running pending migrations on production server..."
    
    # Run migrations on production server
    sshpass -p 'Maxou2000**' ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
        set -e
        cd $PRODUCTION_PATH
        
        echo "Running migrations..."
        php artisan migrate --force
        
        echo "Clearing caches..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        echo "Final migration status:"
        php artisan migrate:status
        
        echo "✅ Database synchronization completed on production!"
EOF
    
    print_status "✅ Remote database synchronization completed!"
    
else
    print_warning "Migration sync cancelled by user"
fi

print_status "Remote database sync process completed"
