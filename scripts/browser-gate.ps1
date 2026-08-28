param([ValidateSet('playwright', 'lighthouse', 'all')][string] $Task = 'all')
$ErrorActionPreference = 'Stop'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$image = 'labm-browser-gate:node-22.13.1'
docker build --tag $image --file (Join-Path $root 'docker/browser/Dockerfile') $root
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update home http://host.docker.internal:8080 | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update siteurl http://host.docker.internal:8080 | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
try {
    docker run --rm --add-host host.docker.internal:host-gateway `
        -e CI=true -e WP_URL=http://host.docker.internal:8080 -e LABM_BROWSER_TASK=$Task `
        -v "${root}:/app" -v labm_pnpm_store:/pnpm/store -v labm_playwright_browsers:/root/.cache/ms-playwright `
        -w /app $image sh /app/scripts/browser-gate.sh
    $browserExit = $LASTEXITCODE
} finally {
    docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update home http://localhost:8080 | Out-Null
    docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update siteurl http://localhost:8080 | Out-Null
}
exit $browserExit
