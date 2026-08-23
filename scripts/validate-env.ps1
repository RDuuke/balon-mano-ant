param([string] $Path = (Join-Path $PSScriptRoot '../.env'))
$ErrorActionPreference = 'Stop'
$required = @('DB_NAME','DB_USER','DB_PASSWORD','DB_ROOT_PASSWORD','WP_PORT','WP_URL','WP_TITLE','WP_ADMIN_USER','WP_ADMIN_PASSWORD','WP_ADMIN_EMAIL')
if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
    throw 'Valor ausente: archivo de entorno. Copie .env.example como .env.'
}
$keys = @{}
foreach ($line in Get-Content -LiteralPath $Path) {
    if ($line -match '^\s*([^#=\s]+)=(.*)$') { $keys[$matches[1]] = $matches[2] }
}
$missing = @($required | Where-Object { -not $keys.ContainsKey($_) -or [string]::IsNullOrWhiteSpace($keys[$_]) })
if ($missing.Count -gt 0) { throw "Valor ausente en variables requeridas: $($missing -join ', ')" }
if ($keys['WP_PORT'] -notmatch '^\d{2,5}$') { throw 'WP_PORT debe ser numerico.' }
Write-Output "Configuracion valida: $($required.Count) variables requeridas presentes; valores ocultos."

