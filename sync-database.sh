#!/bin/bash

# Database Synchronization Script
# This script safely runs missing migrations on production

set -e  # Exit on any error

echo "🔄 Database Synchronization Script"
echo "This will sync your local migrations to production database"

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

# Check if production environment file exists
if [ ! -f ".env.production" ]; then
    print_error ".env.production file not found"
    exit 1
fi

# Create a backup of current .env
if [ -f ".env" ]; then
    cp .env .env.backup
    print_status "Backed up current .env file"
fi

# Temporarily use production environment
cp .env.production .env
print_status "Switched to production environment"

# Function to restore local environment
restore_env() {
    if [ -f ".env.backup" ]; then
        mv .env.backup .env
        print_status "Restored local environment"
    fi
}

# Set trap to restore environment on exit
trap restore_env EXIT

print_status "Checking migration status on production database..."

# Check current migration status
php artisan migrate:status

print_warning "The above shows the current migration status on production."
echo "Migrations marked as 'Pending' will be run."
echo ""
read -p "Do you want to proceed with running pending migrations? (y/n): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Running pending migrations on production..."
    
    # Run migrations with force flag (non-interactive)
    php artisan migrate --force
    
    print_status "✅ Database synchronization completed!"
    print_status "All migrations have been applied to production database"
    
    # Show final status
    print_status "Final migration status:"
    php artisan migrate:status
    
else
    print_warning "Migration sync cancelled by user"
fi

print_status "Database sync process completed"
