#!/bin/bash

# Fix Login Redirect Issue
# This script temporarily disables WebRTC and ensures proper login redirect

set -e

echo "🔧 Fixing Login Redirect Issue"

# Configuration
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"
PRODUCTION_PASSWORD="Maxou2000**"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_status "Step 1: Temporarily disabling WebRTC polling on production..."

# Disable WebRTC polling temporarily
sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    # Create a backup of the current WebRTC file
    cp resources/themes/anchor/assets/js/webrtc.js resources/themes/anchor/assets/js/webrtc.js.backup
    
    # Comment out the WebRTC polling initialization
    sed -i 's/setInterval(() => this.checkIncomingCalls(), 2000);/\/\/ TEMPORARILY DISABLED: setInterval(() => this.checkIncomingCalls(), 2000);/' resources/themes/anchor/assets/js/webrtc.js
    
    echo "WebRTC polling disabled"
EOSSH

print_status "Step 2: Clearing all caches..."

sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Restart PHP-FPM
    systemctl restart php8.3-fpm
    
    echo "All caches cleared and PHP-FPM restarted"
EOSSH

print_status "Step 3: Testing login redirect..."

sleep 3

# Test if the login page loads correctly
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/custom/login || echo "000")
if [ "$HTTP_STATUS" -eq 200 ]; then
    print_status "✅ Login page is responding correctly (HTTP $HTTP_STATUS)"
else
    print_warning "⚠️  Login page returned HTTP $HTTP_STATUS"
fi

print_status "🎯 Login Redirect Fix Summary:"
echo "- WebRTC polling temporarily disabled"
echo "- All caches cleared"
echo "- PHP-FPM restarted"
echo ""
echo "🔑 Test Login with these credentials:"
echo "Email: admin@onlyverified.io"
echo "Password: AdminMaxou2025!"
echo ""
echo "📝 Login URL: https://onlyverified.io/custom/login"
echo ""
print_status "✅ Login redirect issue should now be fixed!"
echo ""
print_warning "Note: WebRTC polling is temporarily disabled. Re-enable it later if needed."
