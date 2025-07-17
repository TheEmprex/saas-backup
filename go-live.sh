#!/bin/bash

echo "🚀 Making your SaaS app live on the internet..."

# Check if port 8080 is available
if lsof -Pi :8080 -sTCP:LISTEN -t >/dev/null ; then
    echo "🔄 Port 8080 is busy, killing existing processes..."
    kill $(lsof -t -i:8080) 2>/dev/null
    sleep 2
fi

# Start Laravel server in background
echo "📱 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8080 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Start serveo tunnel (auto-assigned URL)
echo "🌐 Creating public tunnel..."
echo ""

ssh -o StrictHostKeyChecking=no -R 80:localhost:8080 serveo.net &
TUNNEL_PID=$!

echo "✅ Your app is now live!"
echo "📱 Local URL: http://localhost:8080"
echo ""
echo "🌐 Your public URL will be shown above ^"
echo "📋 Copy the serveo.net URL and share it with anyone!"
echo ""
echo "Press Ctrl+C to stop and go offline"

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Taking your app offline..."
    kill $LARAVEL_PID 2>/dev/null
    kill $TUNNEL_PID 2>/dev/null
    echo "✅ App is now offline"
    exit 0
}

# Set trap to cleanup on exit
trap cleanup INT TERM

# Wait for user to stop
wait
