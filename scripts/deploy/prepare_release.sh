#!/usr/bin/env bash
set -euo pipefail

# Usage: ./scripts/deploy/prepare_release.sh
# Runs on server inside the app directory to prepare a new release

PHP=${PHP:-/usr/bin/php}
COMPOSER=${COMPOSER:-/usr/bin/composer}
NPM=${NPM:-/usr/bin/npm}

echo "[deploy] Installing PHP deps (no-dev)"
$COMPOSER install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if [ ! -L public/storage ]; then
  echo "[deploy] Linking storage"
  $PHP artisan storage:link || true
fi

echo "[deploy] Building assets"
$NPM ci
$NPM run build

echo "[deploy] Caching config/routes/views/events"
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

echo "[deploy] Running migrations"
$PHP artisan migrate --force

echo "[deploy] Restarting services"
sudo systemctl reload php8.3-fpm || true
sudo systemctl restart horizon || true
sudo systemctl restart reverb || true

echo "[deploy] Done"
