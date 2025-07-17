#!/bin/bash

# Get local IP address
LOCAL_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)

echo "🚀 Starting your SaaS publicly accessible with localtunnel..."
echo "🔐 Tunnel password will be: $LOCAL_IP"
echo ""

# Start Laravel server in background
echo "📱 Starting Laravel server on port 8080..."
php artisan serve --host=0.0.0.0 --port=8080 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Generate a unique subdomain
SUBDOMAIN="mysaas-$(date +%s)"

# Start localtunnel
echo "🌐 Creating public tunnel..."
echo "📡 Your public URL will be: https://$SUBDOMAIN.loca.lt"
echo "🔐 Tunnel password: $LOCAL_IP"
echo ""

lt --port 8080 --subdomain "$SUBDOMAIN" &
TUNNEL_PID=$!

echo "✅ Your app is now publicly accessible!"
echo "🌐 Public URL: https://$SUBDOMAIN.loca.lt"
echo "🔐 Password: $LOCAL_IP"
echo "📱 Local URL: http://localhost:8080"
echo ""
echo "Share this info with your users:"
echo "URL: https://$SUBDOMAIN.loca.lt"
echo "Password: $LOCAL_IP"
echo ""
echo "Press Ctrl+C to stop both servers"

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping servers..."
    kill $LARAVEL_PID 2>/dev/null
    kill $TUNNEL_PID 2>/dev/null
    echo "✅ Servers stopped"
    exit 0
}

# Set trap to cleanup on exit
trap cleanup INT TERM

# Wait for user to stop
wait
