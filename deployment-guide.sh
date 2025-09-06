#!/bin/bash

# Production Chat Setup Guide for AAPanel/Shared Hosting
# =======================================================

echo "📋 PRODUCTION DEPLOYMENT GUIDE"
echo "==============================="
echo ""
echo "🏠 For AAPanel/Shared Hosting (No CLI Access):"
echo ""
echo "1. 📁 Upload all files to public_html/"
echo "2. 🔧 Set up .htaccess for Laravel routing"
echo "3. 🌐 Use HTTP Polling instead of WebSocket"
echo ""
echo "⚠️  WebSocket (Reverb) NOT SUPPORTED on shared hosting!"
echo "✅ Use polling-based chat system instead"
echo ""
echo "🔄 Alternative: Use Pusher.com (free tier available)"
echo "   - Sign up at pusher.com"
echo "   - Get API keys"
echo "   - Update .env with Pusher credentials"
echo ""
echo "📝 Files to configure:"
echo "   - .env (database, broadcast driver)"
echo "   - resources/js/chat.js (polling mode)"
echo "   - AdminChatController.php (polling endpoints)"
echo ""

# Check if we're in development or production
if [ -f "artisan" ]; then
    echo "🧪 DEVELOPMENT MODE DETECTED"
    echo "=========================="
    echo ""
    echo "Run these commands:"
    echo "1. php artisan serve (Terminal 1)"
    echo "2. php artisan reverb:start (Terminal 2)"
    echo ""
    echo "Or use: ./start-chat.sh"
else
    echo "🌐 PRODUCTION MODE"
    echo "=================="
    echo ""
    echo "WebSocket not available in shared hosting."
    echo "Use polling-based chat system instead."
fi
