#!/bin/bash

# OnlyVerified Production Deployment Script
# This script prepares and deploys the application to production

set -e  # Exit on any error

echo "🚀 Starting OnlyVerified Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"
BACKUP_PATH="/var/backups/saas"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 1. Pre-deployment checks
print_status "Running pre-deployment checks..."

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    print_error "Not in Laravel project directory"
    exit 1
fi

# Check if .env.production exists
if [ ! -f ".env.production" ]; then
    print_error ".env.production file not found"
    exit 1
fi

# 2. Run tests
print_status "Running tests..."
php artisan test --env=testing || {
    print_warning "Some tests failed. Continue anyway? (y/n)"
    read -r answer
    if [ "$answer" != "y" ]; then
        exit 1
    fi
}

# 3. Build assets
print_status "Building production assets..."
npm run build

# 4. Clear and optimize caches locally
print_status "Optimizing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Create deployment package
print_status "Creating deployment package..."
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PACKAGE_NAME="onlyverified_${TIMESTAMP}.tar.gz"

# Create temporary directory for packaging
TEMP_DIR="/tmp/onlyverified_deployment"
rm -rf $TEMP_DIR
mkdir -p $TEMP_DIR

# Copy application files (excluding development files)
rsync -av --progress \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.env' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='tests' \
    --exclude='.phpunit.result.cache' \
    --exclude='*.log' \
    ./ $TEMP_DIR/

# Copy production environment file
cp .env.production $TEMP_DIR/.env

# Create package
cd /tmp
tar -czf $PACKAGE_NAME onlyverified_deployment/
print_status "Package created: $PACKAGE_NAME"

# 6. Upload to server
print_status "Uploading to production server..."
sshpass -p 'Maxou2000**' scp -o StrictHostKeyChecking=no $PACKAGE_NAME $PRODUCTION_USER@$PRODUCTION_SERVER:/tmp/

# 7. Deploy on server
print_status "Deploying on production server..."
sshpass -p 'Maxou2000**' ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
    set -e
    
    # Create backup of current deployment
    if [ -d "$PRODUCTION_PATH" ]; then
        echo "Creating backup..."
        mkdir -p $BACKUP_PATH
        cp -r $PRODUCTION_PATH $BACKUP_PATH/backup_$TIMESTAMP
    fi
    
    # Extract new version
    cd /tmp
    tar -xzf $PACKAGE_NAME
    
    # Replace production files
    rm -rf $PRODUCTION_PATH
    mv onlyverified_deployment $PRODUCTION_PATH
    
    # Set correct permissions
    chown -R www-data:www-data $PRODUCTION_PATH
    chmod -R 755 $PRODUCTION_PATH
    chmod -R 775 $PRODUCTION_PATH/storage
    chmod -R 775 $PRODUCTION_PATH/bootstrap/cache
    
    # Create necessary directories
    mkdir -p $PRODUCTION_PATH/storage/logs
    mkdir -p $PRODUCTION_PATH/storage/framework/cache
    mkdir -p $PRODUCTION_PATH/storage/framework/sessions
    mkdir -p $PRODUCTION_PATH/storage/framework/views
    
    # Set correct permissions for storage directories
    chmod -R 775 $PRODUCTION_PATH/storage
    chown -R www-data:www-data $PRODUCTION_PATH/storage
    
    cd $PRODUCTION_PATH
    
    # Install/update composer dependencies (production)
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Run database migrations
    php artisan migrate --force
    
    # Clear and cache config
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    php artisan view:clear
    php artisan view:cache
    
    # Restart services
    systemctl reload nginx
    systemctl restart php8.3-fpm
    
    # Clean up
    rm -f /tmp/$PACKAGE_NAME
    rm -rf /tmp/onlyverified_deployment
    
    echo "✅ Deployment completed successfully!"
EOF

# 8. Clean up local files
rm -f /tmp/$PACKAGE_NAME
rm -rf $TEMP_DIR

# 9. Run post-deployment checks
print_status "Running post-deployment checks..."
sleep 5

# Check if site is responding
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/)
if [ "$HTTP_STATUS" -eq 200 ]; then
    print_status "✅ Site is responding correctly (HTTP $HTTP_STATUS)"
else
    print_warning "⚠️  Site returned HTTP $HTTP_STATUS"
fi

print_status "🎉 Production deployment completed!"
print_status "Site URL: https://onlyverified.io"
print_status "Admin Panel: https://onlyverified.io/filament-admin"

echo
print_warning "Post-deployment checklist:"
echo "1. Verify all critical features are working"
echo "2. Check error logs: tail -f $PRODUCTION_PATH/storage/logs/laravel.log"
echo "3. Monitor server resources"
echo "4. Test payment processing"
echo "5. Verify email delivery"
