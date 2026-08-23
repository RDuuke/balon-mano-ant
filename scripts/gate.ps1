param([switch] $IncludeBrowser)
$ErrorActionPreference = 'Continue'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$artifactDir = Join-Path $root 'artifacts/gate'
New-Item -ItemType Directory -Force -Path $artifactDir | Out-Null
$results = @()

function Invoke-Gate([string] $Name, [string] $Tool, [scriptblock] $Command) {
    $available = Get-Command $Tool -ErrorAction SilentlyContinue
    if (-not $available) {
        $script:results += [pscustomobject]@{ Name=$Name; Status='NO EJECUTADA'; ExitCode=$null; Detail="Herramienta ausente: $Tool" }
        return
    }
    $version = & $Tool --version 2>&1 | Select-Object -First 1
    $output = & $Command 2>&1
    $code = $LASTEXITCODE
    $output | Out-File -LiteralPath (Join-Path $artifactDir "$Name.log") -Encoding utf8
    $script:results += [pscustomobject]@{ Name=$Name; Status=$(if($code -eq 0){'PASS'}else{'FAIL'}); ExitCode=$code; Detail="version=$version" }
}

Invoke-Gate 'compose-config' 'docker' { docker compose --env-file .env.example config --quiet }
Invoke-Gate 'composer-test' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 test }
Invoke-Gate 'wordpress-integration' 'docker' { docker run --rm --network labm_default --entrypoint php -e WORDPRESS_DB_HOST=db:3306 -e WORDPRESS_DB_NAME=labm_demo -e WORDPRESS_DB_USER=labm_demo -e WORDPRESS_DB_PASSWORD=demo_password_change_me -e WP_TESTS_RUNTIME_ROOT=/wordpress -v "${root}:/app" -v labm_composer_vendor:/app/vendor -v labm_wordpress_core:/wordpress -v "${root}/wp-content/themes/labm:/wordpress/wp-content/themes/labm:ro" -v "${root}/wp-content/plugins/labm-core:/wordpress/wp-content/plugins/labm-core:ro" -w /app wordpress:cli-2.11.0-php8.3 /app/vendor/bin/phpunit -c phpunit.integration.xml.dist }
Invoke-Gate 'composer-lint' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 lint }
Invoke-Gate 'composer-analyse' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 analyse -- --no-progress }
if ($IncludeBrowser) {
    Invoke-Gate 'playwright' 'pnpm' { pnpm exec playwright test }
    Invoke-Gate 'lighthouse' 'pnpm' { & (Join-Path $PSScriptRoot 'lighthouse.ps1') }
}
$results | ConvertTo-Json -Depth 4 | Out-File -LiteralPath (Join-Path $artifactDir 'summary.json') -Encoding utf8
$results | Format-Table -AutoSize
if (@($results | Where-Object { $_.Status -ne 'PASS' }).Count -gt 0) { exit 1 }
exit 0
