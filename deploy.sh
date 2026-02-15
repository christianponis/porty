#!/bin/bash

# Script di deploy per Porty
# Uso: ./deploy.sh

set -e

echo "🚀 Deploy di Porty con Docker..."

# Verifica che .env esista
if [ ! -f .env ]; then
    echo "❌ File .env non trovato!"
    echo "Copia .env.docker in .env e configura le variabili:"
    echo "  cp .env.docker .env"
    echo "  nano .env"
    exit 1
fi

# Genera APP_KEY se non presente
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generazione APP_KEY..."
    docker compose run --rm app php artisan key:generate --force
fi

# Build e avvio container
echo "🐳 Build dei container..."
docker compose build --no-cache

echo "🚀 Avvio dei servizi..."
docker compose up -d

# Attendi che i database siano pronti
echo "⏳ Attendo che i database siano pronti..."
sleep 15

# Esegui migrazioni
echo "📦 Esecuzione migrazioni..."
docker compose exec -T app php artisan migrate --force

echo "🌱 Esecuzione seeder..."
docker compose exec -T app php artisan db:seed --force

# Ottimizzazioni
echo "⚡ Ottimizzazione..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo "✅ Deploy completato!"
echo ""
echo "🌐 Porty è disponibile su: http://localhost"
echo ""
echo "📊 Comandi utili:"
echo "  docker compose logs -f app     # Visualizza log"
echo "  docker compose ps              # Stato container"
echo "  docker compose down            # Ferma tutto"
echo "  docker compose restart app     # Riavvia app"
