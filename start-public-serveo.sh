#!/bin/bash

echo "🚀 Starting your SaaS publicly accessible with serveo.net..."

# Start Laravel server in background
echo "📱 Starting Laravel server on port 8080..."
php artisan serve --host=0.0.0.0 --port=8080 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Generate a unique subdomain
SUBDOMAIN="mysaas-$(date +%s)"

# Start serveo tunnel
echo "🌐 Creating public tunnel..."
echo "📡 Your public URL will be: https://$SUBDOMAIN.serveo.net"
echo ""

ssh -o StrictHostKeyChecking=no -R $SUBDOMAIN:80:localhost:8080 serveo.net &
TUNNEL_PID=$!

echo "✅ Your app is now publicly accessible!"
echo "🌐 Public URL: https://$SUBDOMAIN.serveo.net"
echo "📱 Local URL: http://localhost:8080"
echo ""
echo "Share this URL with your users: https://$SUBDOMAIN.serveo.net"
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
