#!/bin/bash

# Get public IP
PUBLIC_IP=$(curl -s https://ipinfo.io/ip)

echo "🚀 Lancement de votre SaaS sur internet..."
echo "🔐 Mot de passe du tunnel : $PUBLIC_IP"
echo ""

# Kill existing processes
pkill -f "php artisan serve" 2>/dev/null
pkill -f "lt --port" 2>/dev/null

# Start Laravel server
echo "📱 Démarrage du serveur Laravel..."
php artisan serve --host=127.0.0.1 --port=8080 &
LARAVEL_PID=$!

sleep 5

# Start tunnel
echo "🌐 Création du tunnel public..."
lt --port 8080 &
TUNNEL_PID=$!

echo ""
echo "✅ Votre app est maintenant en ligne !"
echo "🔐 Mot de passe : $PUBLIC_IP"
echo "📱 URL locale : http://localhost:8080"
echo "🌐 URL publique : Regardez ci-dessus pour l'URL .loca.lt"
echo ""
echo "📋 Partagez l'URL .loca.lt avec le mot de passe : $PUBLIC_IP"
echo ""
echo "Appuyez sur Ctrl+C pour arrêter"

# Cleanup function
cleanup() {
    echo ""
    echo "🛑 Arrêt de l'application..."
    kill $LARAVEL_PID 2>/dev/null
    kill $TUNNEL_PID 2>/dev/null
    pkill -f "php artisan serve" 2>/dev/null
    pkill -f "lt --port" 2>/dev/null
    echo "✅ Application arrêtée"
    exit 0
}

trap cleanup INT TERM
wait
