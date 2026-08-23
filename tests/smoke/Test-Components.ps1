$ErrorActionPreference = 'Continue'
function Assert-Exit([string] $Message) { if ($LASTEXITCODE -ne 0) { throw $Message } }

docker compose --profile tools run --rm --no-deps wp-cli plugin deactivate labm-core | Out-Null
Assert-Exit 'No se pudo desactivar labm-core.'
& (Join-Path $PSScriptRoot 'Test-Http.ps1')

docker compose --profile tools run --rm --no-deps wp-cli plugin activate labm-core | Out-Null
Assert-Exit 'No se pudo reactivar labm-core.'
docker compose --profile tools run --rm --no-deps wp-cli theme activate twentytwentyfive | Out-Null
Assert-Exit 'No se pudo cambiar a un tema independiente.'
$active = docker compose --profile tools run --rm --no-deps wp-cli plugin is-active labm-core
Assert-Exit 'labm-core no persistio al cambiar de tema.'

docker compose --profile tools run --rm --no-deps wp-cli theme activate labm | Out-Null
Assert-Exit 'No se pudo restaurar el tema labm.'
& (Join-Path $PSScriptRoot 'Test-Http.ps1')
Write-Output 'PASS activacion independiente y fallback sin errores fatales'
