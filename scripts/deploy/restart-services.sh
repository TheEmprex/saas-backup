#!/usr/bin/env bash
set -euo pipefail

# Restart services helper
# Usage: ./scripts/deploy/restart-services.sh horizon queue reverb

SERVICES=("$@")
if [ ${#SERVICES[@]} -eq 0 ]; then
  SERVICES=("horizon" "queue@1" "queue@2" "reverb")
fi

for svc in "${SERVICES[@]}"; do
  echo "Restarting $svc..."
  sudo systemctl restart "$svc" || true
  sudo systemctl status "$svc" --no-pager -l | sed -n '1,10p' || true
  echo "---"
done

