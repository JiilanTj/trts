#!/bin/bash

# Laravel Chat System Startup Script
# This script starts both Laravel app server and Reverb WebSocket server

echo "🚀 Starting Laravel Chat System..."
echo "=================================="

# Function to cleanup processes on exit
cleanup() {
    echo ""
    echo "🛑 Shutting down servers..."
    kill $APP_PID $REVERB_PID 2>/dev/null
    exit 0
}

# Set trap for cleanup on script exit
trap cleanup SIGINT SIGTERM

# Start Laravel application server
echo "📱 Starting Laravel App Server on http://localhost:8000..."
php artisan serve --host=localhost --port=8000 &
APP_PID=$!

# Wait a moment for the app server to start
sleep 2

# Start Laravel Reverb WebSocket server
echo "🔌 Starting Laravel Reverb WebSocket Server on ws://localhost:8080..."
php artisan reverb:start --host=localhost --port=8080 &
REVERB_PID=$!

# Wait a moment for Reverb to start
sleep 2

echo ""
echo "✅ Both servers are running!"
echo "=================================="
echo "🌐 Laravel App: http://localhost:8000"
echo "🔌 WebSocket:   ws://localhost:8080" 
echo "📊 Admin Chat:  http://localhost:8000/admin/chat"
echo ""
echo "Press Ctrl+C to stop both servers"
echo ""

# Keep script running and show logs
while true; do
    if ! kill -0 $APP_PID 2>/dev/null; then
        echo "❌ Laravel app server stopped unexpectedly"
        break
    fi
    if ! kill -0 $REVERB_PID 2>/dev/null; then
        echo "❌ Reverb WebSocket server stopped unexpectedly"
        break
    fi
    sleep 5
done

cleanup
