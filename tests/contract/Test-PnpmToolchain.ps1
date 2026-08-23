$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$packagePath = Join-Path $root 'package.json'
$package = Get-Content -Raw -LiteralPath $packagePath | ConvertFrom-Json

if ($package.packageManager -notmatch '^pnpm@\d+\.\d+\.\d+$') {
    throw 'packageManager debe fijar una version concreta de pnpm.'
}
if (Test-Path -LiteralPath (Join-Path $root 'package-lock.json')) {
    throw 'package-lock.json no puede coexistir con pnpm.'
}
if (-not (Test-Path -LiteralPath (Join-Path $root 'pnpm-lock.yaml'))) {
    throw 'Falta pnpm-lock.yaml.'
}

$operationalFiles = @(
    'README.md',
    'docs/testing.md',
    'scripts/gate.ps1',
    'scripts/lighthouse.ps1',
    'package.json'
)
foreach ($relative in $operationalFiles) {
    $content = Get-Content -Raw -LiteralPath (Join-Path $root $relative)
    if ($content -match '(?im)(^|[\s`])(?:npm|npx)(?:\s|$)') {
        throw "Referencia operativa npm/npx detectada en $relative."
    }
}

Write-Output "PASS pnpm exclusivo $($package.packageManager)"
