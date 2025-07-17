#!/bin/bash

echo "🚀 Making your SaaS app live on the internet..."

# Kill any existing processes on port 8080
if lsof -Pi :8080 -sTCP:LISTEN -t >/dev/null ; then
    echo "🔄 Killing existing processes on port 8080..."
    kill $(lsof -t -i:8080) 2>/dev/null
    sleep 2
fi

# Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Start Laravel server in background
echo "📱 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8080 > /dev/null 2>&1 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Check if Laravel is running
if ! curl -s http://localhost:8080 > /dev/null; then
    echo "❌ Laravel server failed to start"
    exit 1
fi

echo "✅ Laravel server is running"

# Start ngrok tunnel
echo "🌐 Creating public tunnel..."
echo ""

# Use a more stable SSH connection
ssh -o StrictHostKeyChecking=no -o ServerAliveInterval=30 -R 80:localhost:8080 serveo.net 2>&1 | grep -E "(Forwarding|serveo\.net)" &
TUNNEL_PID=$!

# Wait a bit for tunnel to establish
sleep 3

echo ""
echo "✅ Your app should now be live!"
echo "📱 Local URL: http://localhost:8080"
echo "🌐 Public URL is shown above"
echo ""
echo "🔍 If you don't see the URL, check the output above for 'serveo.net'"
echo "📋 Copy that URL and share it with anyone!"
echo ""
echo "Press Ctrl+C to stop and go offline"

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Taking your app offline..."
    kill $LARAVEL_PID 2>/dev/null
    kill $TUNNEL_PID 2>/dev/null
    # Kill any remaining SSH connections
    pkill -f "ssh.*serveo.net" 2>/dev/null
    echo "✅ App is now offline"
    exit 0
}

# Set trap to cleanup on exit
trap cleanup INT TERM

# Wait for user to stop
wait
