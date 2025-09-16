#!/usr/bin/env bash
set -euo pipefail

# Build script for production deployments
# Usage: ./scripts/deploy/build.sh

PHP_BIN=${PHP_BIN:-php}
NPM_BIN=${NPM_BIN:-npm}
COMPOSER_BIN=${COMPOSER_BIN:-composer}

$COMPOSER_BIN install --no-dev --prefer-dist --optimize-autoloader
$NPM_BIN ci
$NPM_BIN run build
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize

