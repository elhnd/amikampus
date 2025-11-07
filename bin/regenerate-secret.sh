#!/bin/bash

# Script de régénération du APP_SECRET
# Usage: ./bin/regenerate-secret.sh

set -e

echo "🔐 Régénération du APP_SECRET..."

# Vérifier que le fichier .env.dev existe
if [ ! -f .env.dev ]; then
    echo "⚠️  Le fichier .env.dev n'existe pas."
    echo "📋 Copie du template..."
    cp .env.dev.dist .env.dev
fi

# Générer un nouveau secret
NEW_SECRET=$(openssl rand -hex 32)

echo "✨ Nouveau secret généré: $NEW_SECRET"

# Mettre à jour le fichier .env.dev
if grep -q "APP_SECRET=" .env.dev; then
    # Remplacer l'ancien secret
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        sed -i '' "s/APP_SECRET=.*/APP_SECRET=$NEW_SECRET/" .env.dev
    else
        # Linux
        sed -i "s/APP_SECRET=.*/APP_SECRET=$NEW_SECRET/" .env.dev
    fi
    echo "✅ Fichier .env.dev mis à jour"
else
    # Ajouter le secret
    echo "APP_SECRET=$NEW_SECRET" >> .env.dev
    echo "✅ APP_SECRET ajouté à .env.dev"
fi

# Nettoyer le cache Symfony
if [ -f bin/console ]; then
    echo "🧹 Nettoyage du cache..."
    php bin/console cache:clear --no-warmup
    echo "✅ Cache nettoyé"
fi

echo ""
echo "✅ Régénération terminée avec succès !"
echo ""
echo "⚠️  IMPORTANT :"
echo "   - Le nouveau secret a été appliqué à .env.dev"
echo "   - N'oubliez pas de redémarrer votre serveur/conteneurs"
echo "   - Ne partagez jamais ce fichier ou ce secret"
echo ""

# Si dans Docker, proposer de redémarrer
if [ -f compose.yaml ]; then
    read -p "🐳 Voulez-vous redémarrer les conteneurs Docker ? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker compose restart
        echo "✅ Conteneurs redémarrés"
    fi
fi
