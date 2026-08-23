$ErrorActionPreference = 'Continue'
$marker = 'labm-persistence-smoke'
docker compose --profile tools run --rm --no-deps wp-cli option update labm_persistence_smoke $marker | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'No se pudo guardar marcador de persistencia.' }
docker compose restart db wordpress | Out-Null
docker compose up -d --wait
if ($LASTEXITCODE -ne 0) { throw 'El entorno no recupero salud tras reinicio.' }
$value = docker compose --profile tools run --rm --no-deps wp-cli option get labm_persistence_smoke
if (($value | Select-Object -Last 1) -ne $marker) { throw 'El marcador no persistio tras reinicio.' }
Write-Output 'PASS persistencia no destructiva'
