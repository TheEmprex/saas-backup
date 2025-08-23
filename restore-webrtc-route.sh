#!/bin/bash

# Restore WebRTC route after testing
# This will re-enable the WebRTC route

set -e

echo "🔧 Restoring WebRTC Route"

# Configuration
PRODUCTION_SERVER="46.62.150.27"
PRODUCTION_USER="root"
PRODUCTION_PATH="/var/www/saas"
PRODUCTION_PASSWORD="Maxou2000**"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
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

print_status "Step 1: Restoring WebRTC route from backup..."

sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    # Restore routes file from backup
    if [ -f routes/web.php.backup ]; then
        cp routes/web.php.backup routes/web.php
        echo "WebRTC route restored from backup"
        
        # Verify the route is back
        grep -n "webrtc/incoming-calls" routes/web.php | head -1
    else
        echo "No backup found, manually uncommenting..."
        # Uncomment the route
        sed -i 's|// TEMP DISABLED: Route::get(\x27/api/webrtc/incoming-calls\x27|Route::get(\x27/api/webrtc/incoming-calls\x27|' routes/web.php
        echo "WebRTC route uncommented"
    fi
EOSSH

print_status "Step 2: Clearing route cache..."

sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    php artisan route:clear
    php artisan config:clear
    
    echo "Route cache cleared"
EOSSH

print_status "Step 3: Testing WebRTC route restoration..."

sleep 3

# Test if the WebRTC route is back
WEBRTC_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/api/webrtc/incoming-calls || echo "000")
if [ "$WEBRTC_STATUS" -eq 200 ] || [ "$WEBRTC_STATUS" -eq 401 ]; then
    print_status "✅ WebRTC route is restored (HTTP $WEBRTC_STATUS)"
else
    print_warning "⚠️  WebRTC route returned HTTP $WEBRTC_STATUS"
fi

print_status "🎯 WebRTC Route Restoration Complete:"
echo "- WebRTC incoming-calls route re-enabled"
echo "- Route cache cleared"
echo ""
print_status "✅ WebRTC functionality should now be restored"
