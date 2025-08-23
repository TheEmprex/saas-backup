#!/bin/bash

# Temporarily disable WebRTC route to test login redirect
# This will help identify if the route itself is causing issues

set -e

echo "🔧 Temporarily Disabling WebRTC Route to Test Login"

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

print_status "Step 1: Creating backup and temporarily commenting out WebRTC route..."

sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    # Backup routes file
    cp routes/web.php routes/web.php.backup
    
    # Comment out the WebRTC incoming-calls route
    sed -i 's|Route::get(\x27/api/webrtc/incoming-calls\x27|// TEMP DISABLED: Route::get(\x27/api/webrtc/incoming-calls\x27|' routes/web.php
    
    echo "WebRTC incoming-calls route temporarily disabled"
    
    # Show the change
    grep -n "webrtc/incoming-calls" routes/web.php || echo "Route successfully commented out"
EOSSH

print_status "Step 2: Clearing route cache..."

sshpass -p "$PRODUCTION_PASSWORD" ssh -o StrictHostKeyChecking=no $PRODUCTION_USER@$PRODUCTION_SERVER << 'EOSSH'
    cd /var/www/saas
    
    php artisan route:clear
    php artisan route:cache
    php artisan config:clear
    
    echo "Route cache cleared and rebuilt"
EOSSH

print_status "Step 3: Testing login without WebRTC route..."

sleep 3

# Test login page
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/custom/login || echo "000")
if [ "$HTTP_STATUS" -eq 200 ]; then
    print_status "✅ Login page is responding correctly (HTTP $HTTP_STATUS)"
else
    print_error "⚠️  Login page returned HTTP $HTTP_STATUS"
fi

# Test if the problematic URL now returns 404
WEBRTC_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://onlyverified.io/api/webrtc/incoming-calls || echo "000")
if [ "$WEBRTC_STATUS" -eq 404 ]; then
    print_status "✅ WebRTC route is disabled (HTTP $WEBRTC_STATUS)"
else
    print_warning "⚠️  WebRTC route returned HTTP $WEBRTC_STATUS (expected 404)"
fi

echo ""
print_status "🧪 Test Results:"
echo "- WebRTC incoming-calls route temporarily disabled"
echo "- Route cache cleared and rebuilt"
echo ""
print_warning "NOW TEST LOGIN:"
echo "1. Go to: https://onlyverified.io/custom/login"
echo "2. Login with: admin@onlyverified.io / AdminMaxou2025!"
echo "3. Check if you get redirected to dashboard or still to WebRTC URL"
echo ""
print_status "If login works now, the issue is with the WebRTC route/controller"
print_status "If login still redirects to WebRTC, the issue is elsewhere"
echo ""
print_warning "After testing, run the restore script to re-enable WebRTC route"
