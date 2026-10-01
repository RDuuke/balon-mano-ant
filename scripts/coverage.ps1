$ErrorActionPreference = 'Continue'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$artifactDir = Join-Path $root 'artifacts/coverage'
$clover = Join-Path $artifactDir 'clover.xml'
$runId = [guid]::NewGuid().ToString('N')
$runClover = Join-Path $artifactDir "clover.$runId.xml"
$minimum = 80.0

New-Item -ItemType Directory -Force -Path $artifactDir | Out-Null
if (Test-Path -LiteralPath $clover) {
    # Conservar evidencia previa y evitar sobrescribir un archivo creado por root.
    Move-Item -LiteralPath $clover -Destination (Join-Path $artifactDir "clover.previous.$runId.xml") -ErrorAction Stop
}
docker build --pull=false --tag labm-coverage:php8.3 --file (Join-Path $root 'docker/coverage/Dockerfile') $root
if ($LASTEXITCODE -ne 0) { throw 'No se pudo construir la imagen de cobertura fijada.' }

docker run --rm --user 33:33 --network labm_default --entrypoint php `
    -e WORDPRESS_DB_HOST=db:3306 `
    -e WORDPRESS_DB_NAME=labm_demo `
    -e WORDPRESS_DB_USER=labm_demo `
    -e WORDPRESS_DB_PASSWORD=demo_password_change_me `
    -e WP_TESTS_RUNTIME_ROOT=/wordpress `
    -v "${root}:/app" `
    -v labm_composer_vendor:/app/vendor `
    -v labm_wordpress_core:/wordpress `
    -v labm_wordpress_uploads:/wordpress/wp-content/uploads `
    -v "${root}/wp-content/themes/labm:/wordpress/wp-content/themes/labm:ro" `
    -v "${root}/wp-content/plugins/labm-core:/wordpress/wp-content/plugins/labm-core:ro" `
    -w /app labm-coverage:php8.3 `
    -d pcov.enabled=1 -d pcov.directory=/wordpress/wp-content `
    /app/vendor/bin/phpunit -c phpunit.integration.xml.dist "--coverage-clover=/app/artifacts/coverage/clover.$runId.xml"
$suiteExit = $LASTEXITCODE
if (-not (Test-Path -LiteralPath $runClover)) { throw 'PHPUnit no produjo evidencia Clover de esta ejecucion.' }
Move-Item -LiteralPath $runClover -Destination $clover -ErrorAction Stop

[xml] $report = Get-Content -Raw -LiteralPath $clover
$metrics = $report.coverage.project.metrics
$statements = [double] $metrics.statements
$covered = [double] $metrics.coveredstatements
if ($statements -le 0) { throw 'El reporte Clover no contiene lineas ejecutables.' }
$percentage = [math]::Round(($covered / $statements) * 100, 2)
Write-Output "Cobertura PHP: $percentage% ($covered/$statements lineas)."
if ($suiteExit -ne 0) { throw 'La suite de cobertura fallo; Clover actual conservado para diagnostico.' }
if ($percentage -lt $minimum) { throw "Cobertura PHP $percentage% inferior al minimo $minimum%." }
Write-Output "PASS cobertura PHP >= $minimum%."
