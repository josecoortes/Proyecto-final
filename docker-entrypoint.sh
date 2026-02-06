#!/bin/bash
set -e

echo "🚀 Iniciando configuración del entorno..."

# 1. Backend Setup
echo "🔧 Configurando Backend (Laravel)..."
cd /var/www/html/backend

if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de PHP..."
    composer install --no-interaction --optimize-autoloade
fi

echo "⏳ Esperando a la base de datos..."
sleep 10 # Espera simple para dar tiempo a MySQL (idealmente usar wait-for-it)

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

echo "🐘 Arrancando servidor Laravel..."
php artisan serve --host=0.0.0.0 --port=8000 &

# 2. Frontend Setup
echo "🔧 Configurando Frontend (Angular)..."
cd /var/www/html/frontend

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias de Node..."
    npm install
fi

echo "🅰️ Arrancando servidor Angular..."
# Deshabilitar host check para permitir acceso desde fuera
ng serve --host 0.0.0.0 --port 4200 --poll 2000
