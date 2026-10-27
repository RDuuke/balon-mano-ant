$ErrorActionPreference = 'Continue'

$slug = 'demo-labm-club-frontera'
$before = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=labm_club --name=$slug --post_status=any --fields=ID,post_title,post_status --format=json
if ($LASTEXITCODE -ne 0 -or -not $before) { throw 'No se encontro el contenido de dominio antes del cambio de tema.' }

docker compose --profile tools run --rm --no-deps wp-cli theme activate twentytwentyfive | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'No se pudo activar el tema alternativo.' }
$during = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=labm_club --name=$slug --post_status=any --fields=ID,post_title,post_status --format=json
if (($before | Select-Object -Last 1) -ne ($during | Select-Object -Last 1)) { throw 'El contenido de dominio cambio al sustituir el tema.' }

$foreignSlug = 'club-editorial-ajeno'
$foreignId = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=labm_club --name=$foreignSlug --post_status=any --field=ID
if (-not $foreignId) {
    $foreignId = docker compose --profile tools run --rm --no-deps wp-cli post create --post_type=labm_club --post_status=draft --post_name=$foreignSlug --post_title='Club editorial ajeno' --porcelain
}
$foreignId = ($foreignId | Select-Object -Last 1).Trim()
$foreignBefore = docker compose --profile tools run --rm --no-deps wp-cli post get $foreignId --field=post_title
docker compose --profile tools run --rm --no-deps wp-cli labm fixtures load | Out-Null
$foreignAfter = docker compose --profile tools run --rm --no-deps wp-cli post get $foreignId --field=post_title
if (($foreignBefore | Select-Object -Last 1) -ne ($foreignAfter | Select-Object -Last 1)) { throw 'Fixtures modificaron contenido de dominio ajeno.' }

docker compose --profile tools run --rm --no-deps wp-cli theme activate labm | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'No se pudo restaurar el tema LABM.' }
Write-Output 'PASS dominio persistente entre temas y contenido editorial ajeno preservado'
