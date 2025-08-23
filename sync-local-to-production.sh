#!/bin/bash

# Sync Local Database to Production
# This script exports your complete local database and imports it to production

set -e  # Exit on any error

echo "🔄 Syncing Local Database to Production"
echo "This will replace ALL production data with your local database"

# Configuration
LOCAL_DB_NAME="onlyverified_saas"
LOCAL_DB_USER="root"
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"
PRODUCTION_PASSWORD="Maxou2000**"
PRODUCTION_DB_NAME="saas_app"
PRODUCTION_DB_USER="saasuser"
PRODUCTION_DB_PASS="MaxouSAAS2000**"

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

print_warning "⚠️  WARNING: This will COMPLETELY REPLACE all production data!"
print_warning "Production database will be wiped and replaced with local data."
echo ""
read -p "Are you absolutely sure you want to continue? (type 'YES' to confirm): " -r
echo

if [ "$REPLY" != "YES" ]; then
    print_error "Operation cancelled. You must type 'YES' to confirm."
    exit 1
fi

# Step 1: Create local database backup
print_status "Step 1: Creating local database backup..."
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
LOCAL_BACKUP="/Users/Maxou/backups/local_full_backup_$TIMESTAMP.sql"
mysqldump -u $LOCAL_DB_USER $LOCAL_DB_NAME > $LOCAL_BACKUP
print_status "✅ Local backup created: $LOCAL_BACKUP"

# Step 2: Create production database backup before sync
print_status "Step 2: Creating production database backup..."
PROD_BACKUP_FILE="production_backup_before_sync_$TIMESTAMP.sql"
sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
    mysqldump -u $PRODUCTION_DB_USER -p'$PRODUCTION_DB_PASS' $PRODUCTION_DB_NAME > /tmp/$PROD_BACKUP_FILE
    echo "Production backup created: /tmp/$PROD_BACKUP_FILE"
EOF

# Download production backup locally
sshpass -p "$PRODUCTION_PASSWORD" scp -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER:/tmp/$PROD_BACKUP_FILE /Users/Maxou/backups/
print_status "✅ Production backup downloaded: /Users/Maxou/backups/$PROD_BACKUP_FILE"

# Step 3: Upload local database to production server
print_status "Step 3: Uploading local database to production server..."
sshpass -p "$PRODUCTION_PASSWORD" scp -o StrictHostKeyChecking=no $LOCAL_BACKUP $PRODUCTION_USER@$PRODUCTION_SERVER:/tmp/local_database.sql
print_status "✅ Local database uploaded to production server"

# Step 4: Import local database to production
print_status "Step 4: Importing local database to production..."
sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
    set -e
    cd $PRODUCTION_PATH
    
    echo "🗄️  Dropping and recreating production database..."
    mysql -u $PRODUCTION_DB_USER -p'$PRODUCTION_DB_PASS' -e "DROP DATABASE IF EXISTS $PRODUCTION_DB_NAME;"
    mysql -u $PRODUCTION_DB_USER -p'$PRODUCTION_DB_PASS' -e "CREATE DATABASE $PRODUCTION_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    echo "📥 Importing local database to production..."
    mysql -u $PRODUCTION_DB_USER -p'$PRODUCTION_DB_PASS' $PRODUCTION_DB_NAME < /tmp/local_database.sql
    
    echo "🧹 Clearing application caches..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    echo "🔄 Running any pending migrations..."
    php artisan migrate --force || echo "No new migrations to run"
    
    echo "📊 Checking imported data..."
    php artisan tinker --execute "
        echo 'IMPORTED DATA COUNT:';
        echo 'Users: ' . \App\Models\User::count();
        echo 'JobPosts: ' . (\class_exists('\App\Models\JobPost') ? \App\Models\JobPost::count() : 'N/A');
        echo 'UserTypes: ' . \App\Models\UserType::count();
    "
    
    echo "🧹 Cleaning up temporary files..."
    rm -f /tmp/local_database.sql
    rm -f /tmp/$PROD_BACKUP_FILE
    
    echo "✅ Database sync completed successfully!"
EOF

print_status "✅ Local database successfully synced to production!"

# Step 5: Test the production site
print_status "Step 5: Testing production site..."
sleep 3
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/custom/register || echo "000")
if [ "$HTTP_STATUS" -eq 200 ]; then
    print_status "✅ Registration page is responding correctly (HTTP $HTTP_STATUS)"
else
    print_warning "⚠️  Registration page returned HTTP $HTTP_STATUS - please check manually"
fi

print_status "🎯 Database Sync Summary:"
echo "- Local database backed up: $LOCAL_BACKUP"
echo "- Production database backed up: /Users/Maxou/backups/$PROD_BACKUP_FILE"
echo "- Local data imported to production successfully"
echo "- Production site is now using your local database"
echo ""
echo "🌐 Production Site: https://onlyverified.io"
echo "📝 Registration: https://onlyverified.io/custom/register"
echo "👨‍💼 Admin Panel: https://onlyverified.io/filament-admin"
echo ""
print_status "🎉 Your local database is now live on production!"
