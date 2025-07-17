#!/bin/bash

echo "🚀 Starting your SaaS publicly accessible..."

# Start Laravel server in background
echo "📱 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8080 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 5

# Start ngrok tunnel
echo "🌐 Creating public tunnel..."
ngrok http 8080 &
NGROK_PID=$!

echo "✅ Your app is now publicly accessible!"
echo "📱 Local URL: http://localhost:8080"
echo "🌐 Public URL: Check the ngrok terminal or go to http://localhost:4040"
echo ""
echo "Press Ctrl+C to stop both servers"

# Function to cleanup on exit
cleanup() {
    echo "🛑 Stopping servers..."
    kill $LARAVEL_PID 2>/dev/null
    kill $NGROK_PID 2>/dev/null
    exit 0
}

# Set trap to cleanup on exit
trap cleanup INT TERM

# Wait for user to stop
wait
