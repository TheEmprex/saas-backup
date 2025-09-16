#!/usr/bin/env bash
set -euo pipefail

# Usage: ./scripts/deploy/finalize_release.sh
# Optional post-deploy verification steps

PHP=${PHP:-/usr/bin/php}

# Health check
STATUS=$($PHP -r "echo file_get_contents(getenv('APP_URL').'/health');" || echo '')
if [[ "$STATUS" == *"ok"* ]]; then
  echo "[deploy] Health check OK"
else
  echo "[deploy] Health check FAILED: $STATUS" >&2
  exit 1
fi

echo "[deploy] Horizon status:"
$PHP artisan horizon:status || true

echo "[deploy] Completed finalize_release"

