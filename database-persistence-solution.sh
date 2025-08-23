#!/bin/bash

# Database Persistence Solution
# This script creates automated backups and ensures data persistence across deployments

set -e  # Exit on any error

echo "🗄️  Database Persistence Solution"
echo "This script creates automated backups and ensures data persistence"

# Configuration
LOCAL_DB_NAME="onlyverified_saas"
LOCAL_DB_USER="root"
LOCAL_DB_PASSWORD=""
BACKUP_DIR="/Users/Maxou/backups"
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

# Function to create local backup
create_local_backup() {
    print_status "Creating local database backup..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="$BACKUP_DIR/saas_app_backup_$TIMESTAMP.sql"
    
    mysqldump -u $LOCAL_DB_USER $LOCAL_DB_NAME > $BACKUP_FILE
    
    print_status "✅ Local backup created: $BACKUP_FILE"
    
    # Keep only last 10 backups
    ls -t $BACKUP_DIR/saas_app_backup_*.sql | tail -n +11 | xargs -r rm
    print_status "Old backups cleaned up (keeping last 10)"
}

# Function to create production backup
create_production_backup() {
    print_status "Creating production database backup..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    PROD_BACKUP_FILE="saas_production_backup_$TIMESTAMP.sql"
    
    sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << EOF
        cd /var/www/saas
        mysqldump -u saasuser -p'MaxouSAAS2000**' saas_app > /tmp/$PROD_BACKUP_FILE
        echo "Production backup created: /tmp/$PROD_BACKUP_FILE"
EOF
    
    # Download production backup
    sshpass -p "$PRODUCTION_PASSWORD" scp -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER:/tmp/$PROD_BACKUP_FILE $BACKUP_DIR/
    
    print_status "✅ Production backup downloaded: $BACKUP_DIR/$PROD_BACKUP_FILE"
}

# Function to deploy with data persistence
deploy_with_persistence() {
    print_status "Deploying with data persistence..."
    
    # Create backups first
    create_local_backup
    create_production_backup
    
    # Upload files and run critical seeder
    print_status "Uploading migration and seeder files..."
    sshpass -p "$PRODUCTION_PASSWORD" scp -r -o StrictHostKeyChecking=no database/migrations/ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH/database/
    sshpass -p "$PRODUCTION_PASSWORD" scp -r -o StrictHostKeyChecking=no database/seeders/ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH/database/
    
    # Run migrations and seed critical data
    print_status "Running migrations and seeding critical data..."
    sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
        set -e
        cd /var/www/saas
        
        echo "🔄 Running migrations..."
        php artisan migrate --force
        
        echo "🌱 Seeding critical data..."
        php artisan db:seed --class=CriticalDataSeeder --force
        
        echo "🧹 Clearing caches..."
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        
        echo "✅ Deployment with persistence completed!"
EOSSH
    
    print_status "✅ Deployment with data persistence completed!"
}

# Function to setup automated backups (cron job)
setup_automated_backups() {
    print_status "Setting up automated backups..."
    
    # Create backup script
    BACKUP_SCRIPT="/Users/Maxou/saas/automated-backup.sh"
    cat > $BACKUP_SCRIPT << 'EOF'
#!/bin/bash
# Automated Database Backup Script
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/Users/Maxou/backups"
mysqldump -u root onlyverified_saas > $BACKUP_DIR/auto_backup_$TIMESTAMP.sql
# Keep only last 20 automated backups
ls -t $BACKUP_DIR/auto_backup_*.sql | tail -n +21 | xargs -r rm
EOF
    
    chmod +x $BACKUP_SCRIPT
    
    # Add to cron (daily at 2 AM)
    (crontab -l 2>/dev/null; echo "0 2 * * * $BACKUP_SCRIPT") | crontab -
    
    print_status "✅ Automated daily backups setup at 2 AM"
}

# Main menu
echo ""
echo "Choose an option:"
echo "1. Create local backup only"
echo "2. Create production backup only"
echo "3. Deploy with data persistence"
echo "4. Setup automated backups"
echo "5. Full solution (backup + deploy + automation)"
echo ""
read -p "Enter your choice (1-5): " choice

case $choice in
    1)
        create_local_backup
        ;;
    2)
        create_production_backup
        ;;
    3)
        deploy_with_persistence
        ;;
    4)
        setup_automated_backups
        ;;
    5)
        print_status "Running full database persistence solution..."
        create_local_backup
        deploy_with_persistence
        setup_automated_backups
        ;;
    *)
        print_error "Invalid choice. Please run the script again."
        exit 1
        ;;
esac

print_status "🎯 Summary of actions completed:"
echo "- Database backups created"
echo "- Critical data deployed to production"
echo "- Site should now work with login/registration"
echo ""
echo "🔑 Admin Login Details:"
echo "Email: admin@onlyverified.io"
echo "Password: AdminMaxou2025!"
echo ""
echo "🌐 Production Site: https://onlyverified.io"
echo "👨‍💼 Admin Panel: https://onlyverified.io/filament-admin"
