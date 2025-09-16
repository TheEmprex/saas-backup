#!/usr/bin/env bash
set -euo pipefail

# Fix permissions for storage and cache directories
# Usage: ./scripts/deploy/permissions.sh deploy www-data

USER=${1:-deploy}
GROUP=${2:-www-data}

sudo chown -R $USER:$GROUP storage bootstrap/cache
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
sudo chmod -R ug+rwx bootstrap/cache

