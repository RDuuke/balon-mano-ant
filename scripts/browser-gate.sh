#!/bin/sh
set -eu
pnpm install --frozen-lockfile
pnpm exec playwright install chromium
if [ "$LABM_BROWSER_TASK" = playwright ] || [ "$LABM_BROWSER_TASK" = all ]; then
  pnpm exec playwright test
fi
if [ "$LABM_BROWSER_TASK" = lighthouse ] || [ "$LABM_BROWSER_TASK" = all ]; then
  CHROME_PATH="$(find /root/.cache/ms-playwright -type f -path '*/chrome-linux/chrome' | head -n 1)"
  export CHROME_PATH
  sed 's#http://localhost:8080#http://host.docker.internal:8080#g' lighthouserc.json > /tmp/lighthouserc.json
  pnpm exec lhci autorun --config=/tmp/lighthouserc.json
fi
