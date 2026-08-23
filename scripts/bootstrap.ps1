param(
    [switch] $SkipStart,
    [string] $EnvFile = (Join-Path $PSScriptRoot '../.env')
)
$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Push-Location $root
try {
    & (Join-Path $PSScriptRoot 'validate-env.ps1') -Path $EnvFile
    foreach ($line in Get-Content -LiteralPath $EnvFile) {
        if ($line -match '^\s*([^#=\s]+)=(.*)$') {
            [Environment]::SetEnvironmentVariable($matches[1], $matches[2], 'Process')
        }
    }
    # Windows PowerShell convierte el progreso de Docker en NativeCommandError;
    # los codigos de salida se validan explicitamente tras cada comando.
    $ErrorActionPreference = 'Continue'
    if (-not $SkipStart) {
        docker compose up -d --wait
        if ($LASTEXITCODE -ne 0) { throw 'Docker Compose no alcanzo estado saludable.' }
    }
    docker compose --profile tools run --rm --no-deps wp-cli core is-installed 2>$null
    if ($LASTEXITCODE -ne 0) {
        docker compose --profile tools run --rm --no-deps wp-cli core install --url="$env:WP_URL" --title="$env:WP_TITLE" --admin_user="$env:WP_ADMIN_USER" --admin_password="$env:WP_ADMIN_PASSWORD" --admin_email="$env:WP_ADMIN_EMAIL" --skip-email
        if ($LASTEXITCODE -ne 0) { throw 'No se pudo instalar WordPress.' }
    }
    docker compose --profile tools run --rm --no-deps wp-cli plugin activate labm-core
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo activar labm-core.' }
    docker compose --profile tools run --rm --no-deps wp-cli theme activate labm
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo activar el tema labm.' }
    docker compose --profile tools run --rm --no-deps wp-cli rewrite structure '/%postname%/' --hard
    if ($LASTEXITCODE -ne 0) { throw 'No se pudieron configurar las rutas publicas.' }
    docker compose --profile tools run --rm --no-deps wp-cli labm fixtures load
    if ($LASTEXITCODE -ne 0) { throw 'No se pudieron cargar fixtures.' }
    Write-Output 'Bootstrap completado de forma idempotente.'
} finally {
    Pop-Location
}
