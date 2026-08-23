$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$ignore = Get-Content -Raw -LiteralPath (Join-Path $root '.gitignore')
$required = @('.env', '*.sql', 'wp-content/uploads/', 'vendor/', 'node_modules/', 'artifacts/', 'wordpress/')
$missing = @($required | Where-Object { $ignore -notmatch [regex]::Escape($_) })
if ($missing.Count -gt 0) {
    throw "Patrones prohibidos ausentes: $($missing -join ', ')"
}

$trackedCandidates = Get-ChildItem -LiteralPath $root -Force -File -Recurse |
    Where-Object {
        $_.FullName -notmatch '[\\/](openspec|\.git|node_modules|vendor)[\\/]' -and
        $_.Extension -in @('.sql', '.dump', '.key', '.pem')
    }
if ($trackedCandidates) {
    throw "Archivos sensibles presentes; revise la higiene antes de publicar (no se imprimen rutas ni contenido)."
}
Write-Output 'PASS higiene del repositorio'
