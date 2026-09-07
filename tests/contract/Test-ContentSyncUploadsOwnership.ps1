$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$scriptPath = Join-Path $root 'scripts/content-sync.ps1'
$source = Get-Content -Raw -LiteralPath $scriptPath

if ($source -notmatch 'function\s+Restore-WordPressUploadsOwnership') {
    throw 'content-sync no define la restauracion de propietario para uploads.'
}
if ($source -notmatch "Restore-WordPressUploadsOwnership\s*\r?\n\s*Set-LocalVersion") {
    throw 'content-sync no restaura el propietario inmediatamente despues de importar uploads.'
}
if ($source -notmatch 'chown\s+-R\s+33:33') {
    throw 'content-sync no asigna uploads recursivamente al usuario de WordPress.'
}

Write-Output 'PASS content-sync restaura ownership recursivo de uploads despues de importar.'
