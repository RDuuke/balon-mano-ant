$ErrorActionPreference = 'Continue'
function Invoke-Fixtures {
    docker compose --profile tools run --rm --no-deps wp-cli labm fixtures load | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Fallo el comando de fixtures.' }
}

$foreignSlug = 'editorial-ajeno-fixture'
$foreignId = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=page --name=$foreignSlug --post_status=any --field=ID
if (-not $foreignId) {
    $foreignId = docker compose --profile tools run --rm --no-deps wp-cli post create --post_type=page --post_status=draft --post_name=$foreignSlug --post_title='Contenido editorial ajeno' --porcelain
}
$foreignId = ($foreignId | Select-Object -Last 1).Trim()
$before = docker compose --profile tools run --rm --no-deps wp-cli post get $foreignId --field=post_title
Invoke-Fixtures
Invoke-Fixtures
$after = docker compose --profile tools run --rm --no-deps wp-cli post get $foreignId --field=post_title
if (($before | Select-Object -Last 1) -ne ($after | Select-Object -Last 1)) { throw 'Fixtures modificaron contenido editorial ajeno.' }

foreach ($slug in @('demo-labm-inicio','demo-labm-nosotros')) {
    $count = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=page --name=$slug --post_status=any --format=count
    if (($count | Select-Object -Last 1) -ne '1') { throw "Fixture duplicado o ausente: $slug" }
}
$pdfCount = docker compose --profile tools run --rm --no-deps wp-cli post list --post_type=attachment --post_mime_type=application/pdf --post_status=inherit --format=count
if (($pdfCount | Select-Object -Last 1) -ne '0') { throw 'Fixtures publicaron adjuntos PDF.' }
Write-Output 'PASS fixtures idempotentes, contenido ajeno preservado y cero PDF'
