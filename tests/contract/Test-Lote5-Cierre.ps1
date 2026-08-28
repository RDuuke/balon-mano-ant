param([ValidateSet('5.1', '5.2', '5.3', '5.5', 'all')][string] $Task = 'all')
$ErrorActionPreference = 'Stop'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path } else { (Resolve-Path '.').Path }
$failures = [System.Collections.Generic.List[string]]::new()

function Assert-Contract([bool] $Condition, [string] $Message) {
    if (-not $Condition) { $failures.Add($Message) }
}

if ($Task -in @('5.1', 'all')) {
    $coverageScriptPath = Join-Path $root 'scripts/coverage.ps1'
    $coverageImagePath = Join-Path $root 'docker/coverage/Dockerfile'
    Assert-Contract (Test-Path -LiteralPath $coverageScriptPath) '5.1: falta el gate reproducible de cobertura'
    Assert-Contract (Test-Path -LiteralPath $coverageImagePath) '5.1: falta la imagen fijada con driver de cobertura'
    if (Test-Path -LiteralPath $coverageScriptPath) {
        $coverageScript = Get-Content -Raw -LiteralPath $coverageScriptPath
        Assert-Contract ($coverageScript -match '80') '5.1: el gate no exige cobertura minima de 80%'
        Assert-Contract ($coverageScript -match 'clover') '5.1: el gate no conserva evidencia Clover'
    }
}

if ($Task -in @('5.2', 'all')) {
    $lighthouse = Get-Content -Raw -LiteralPath (Join-Path $root 'lighthouserc.json')
    Assert-Contract ($lighthouse -match '"minScore"\s*:\s*0\.85') '5.2: Lighthouse no exige Performance >=85'
    Assert-Contract (([regex]::Matches($lighthouse, '"minScore"\s*:\s*0\.9')).Count -ge 3) '5.2: Lighthouse no exige Accessibility, Best Practices y SEO >=90'
}

if ($Task -in @('5.3', 'all')) {
    $workflowPath = Join-Path $root '.github/workflows/quality.yml'
    Assert-Contract (Test-Path -LiteralPath $workflowPath) '5.3: falta .github/workflows/quality.yml'
    if (Test-Path -LiteralPath $workflowPath) {
        $workflow = Get-Content -Raw -LiteralPath $workflowPath
        Assert-Contract ($workflow -match 'scripts/gate\.ps1') '5.3: CI no ejecuta el gate agregado'
        Assert-Contract ($workflow -match 'pnpm install --frozen-lockfile') '5.3: CI no instala pnpm desde lockfile'
        Assert-Contract ($workflow -match 'upload-artifact') '5.3: CI no conserva reportes'
        Assert-Contract ($workflow -notmatch 'continue-on-error:\s*true') '5.3: CI permite fallos silenciosos'
    }
	$gate = Get-Content -Raw -LiteralPath (Join-Path $root 'scripts/gate.ps1')
	Assert-Contract ($gate -match 'if \(\$PSScriptRoot\)') '5.3: el gate no admite ejecución embebida para diagnóstico local'
}

if ($Task -in @('5.5', 'all')) {
    $tracePath = Join-Path $root 'docs/traceability.md'
    Assert-Contract (Test-Path -LiteralPath $tracePath) '5.5: falta docs/traceability.md'
    foreach ($path in @('README.md', 'docs/development.md', 'docs/testing.md')) {
        $fullPath = Join-Path $root $path
        $body = if (Test-Path -LiteralPath $fullPath) { Get-Content -Raw -LiteralPath $fullPath } else { '' }
        Assert-Contract ($body -match 'rollback|reversi') "5.5: $path no documenta rollback o reversión"
    }
    if (Test-Path -LiteralPath $tracePath) {
        $trace = Get-Content -Raw -LiteralPath $tracePath
        foreach ($domain in @('entorno-desarrollo', 'arquitectura-cms', 'experiencia-publica', 'documentos-contacto', 'calidad-seguridad')) {
            Assert-Contract ($trace -match [regex]::Escape($domain)) "5.5: falta trazabilidad para $domain"
        }
    }
}

if ($failures.Count -gt 0) {
    $failures | ForEach-Object { Write-Error $_ -ErrorAction Continue }
    exit 1
}
Write-Output 'Contrato de cierre 5.3/5.5 aprobado.'
