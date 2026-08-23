$ErrorActionPreference = 'Continue'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$outputDir = Join-Path $root 'artifacts/lighthouse'
New-Item -ItemType Directory -Force -Path $outputDir | Out-Null
$report = Join-Path $outputDir 'baseline.report.json'
$log = Join-Path $outputDir 'baseline.log'
$env:CHROME_PATH = node -e "const { chromium }=require('@playwright/test'); process.stdout.write(chromium.executablePath())"
if ($LASTEXITCODE -ne 0 -or -not (Test-Path -LiteralPath $env:CHROME_PATH)) {
    throw 'No se encontro Chromium bloqueado por Playwright.'
}
$result = pnpm exec lighthouse http://localhost:8080 --output=json --output-path=$report --quiet --chrome-flags="--headless --no-sandbox" 2>&1
$code = $LASTEXITCODE
$result | Out-File -LiteralPath $log -Encoding utf8
if (-not (Test-Path -LiteralPath $report)) { throw 'Lighthouse no produjo el reporte baseline.' }
$data = Get-Content -Raw -LiteralPath $report | ConvertFrom-Json
$categories = @('performance','accessibility','best-practices','seo')
$missing = @($categories | Where-Object { $null -eq $data.categories.$_.score })
if ($missing.Count -gt 0 -or $data.runtimeError) { throw 'El reporte Lighthouse es incompleto o contiene error de ejecucion.' }
if ($code -ne 0 -and (($result -join "`n") -notmatch 'EPERM.*[Tt]emp.*lighthouse')) {
    throw "Lighthouse fallo con codigo $code; consulte $log."
}
$scores = $categories | ForEach-Object { "$_=$([math]::Round($data.categories.$_.score * 100))" }
Write-Output "PASS Lighthouse baseline: $($scores -join ', ')"
if ($code -ne 0) { Write-Warning 'Chrome devolvio EPERM al limpiar su temporal en Windows; el reporte fue validado antes de aceptar este fallo de limpieza.' }
exit 0
