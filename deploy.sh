#!/usr/bin/env bash
# =============================================================
# ASMS Production Deployment Script
# Usage: bash deploy.sh
# =============================================================

set -e

echo "▶ Putting app into maintenance mode..."
php artisan down --refresh=15 --retry=10

echo "▶ Pulling latest code..."
git pull origin main

echo "▶ Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

echo "▶ Clearing old caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear

echo "▶ Running database migrations..."
php artisan migrate --force

echo "▶ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "▶ Linking storage..."
php artisan storage:link || true

echo "▶ Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "▶ Restarting queue workers..."
php artisan queue:restart

echo "▶ Bringing app back online..."
php artisan up

echo ""
echo "✅ Deployment complete."
