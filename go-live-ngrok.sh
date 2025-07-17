#!/bin/bash

echo "🚀 Making your SaaS app live with ngrok..."

# Kill any existing processes on port 8080
pkill -f "php artisan serve"
pkill -f "ngrok"
sleep 2

# Start Laravel server in background
echo "📱 Starting Laravel server..."
php artisan serve --host=127.0.0.1 --port=8080 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Test if Laravel is running
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ Laravel server is running"
else
    echo "❌ Laravel server failed to start"
    exit 1
fi

# Start ngrok tunnel
echo "🌐 Starting ngrok tunnel..."
ngrok http 8080 --log=stdout &
NGROK_PID=$!

echo ""
echo "✅ Your app is now live!"
echo "📱 Local URL: http://localhost:8080"
echo "🌐 Public URL will be shown above"
echo ""
echo "📋 Look for the https://....ngrok.io URL above and share it!"
echo ""
echo "Press Ctrl+C to stop and go offline"

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Taking your app offline..."
    kill $LARAVEL_PID 2>/dev/null
    kill $NGROK_PID 2>/dev/null
    pkill -f "php artisan serve" 2>/dev/null
    pkill -f "ngrok" 2>/dev/null
    echo "✅ App is now offline"
    exit 0
}

# Set trap to cleanup on exit
trap cleanup INT TERM

# Wait for user to stop
wait
