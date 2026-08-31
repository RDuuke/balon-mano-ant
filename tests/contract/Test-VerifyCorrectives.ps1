param([ValidateSet('functional', 'toolchain', 'all')][string] $Task = 'all')
$ErrorActionPreference = 'Stop'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path } else { (Resolve-Path '.').Path }
$failures = [System.Collections.Generic.List[string]]::new()

function Assert-Contract([bool] $Condition, [string] $Message) {
    if (-not $Condition) { $failures.Add($Message) }
}

if ($Task -in @('functional', 'all')) {
    $testPath = Join-Path $root 'tests/php/VerifyCorrectivesTest.php'
    Assert-Contract (Test-Path -LiteralPath $testPath) 'Falta la suite ejecutable de escenarios correctivos'
    if (Test-Path -LiteralPath $testPath) {
        $test = Get-Content -Raw -LiteralPath $testPath
        foreach ($scenario in @(
            'publicacion_autorizada', 'presentacion_generica', 'identificador_invalido',
            'actualizacion_incompatible', 'documento_publicado', 'consulta_combinada_paginada',
            'consulta_vacia', 'archivo_exclusivo', 'validacion_accesible', 'error_entrega',
            'navegacion_completa', 'portada_completa', 'seccion_opcional', 'contenido_incompleto'
        )) {
            Assert-Contract ($test -match $scenario) "Falta evidencia ejecutable para $scenario"
        }
    }
    $e2ePath = Join-Path $root 'tests/e2e/verify-correctives.spec.ts'
    Assert-Contract (Test-Path -LiteralPath $e2ePath) 'Falta evidencia E2E de navegacion escritorio/movil y recorrido accesible'
}

if ($Task -in @('toolchain', 'all')) {
    $scriptPath = Join-Path $root 'scripts/browser-gate.ps1'
    Assert-Contract (Test-Path -LiteralPath $scriptPath) 'Falta el gate portable de navegador'
    if (Test-Path -LiteralPath $scriptPath) {
        $script = (Get-Content -Raw -LiteralPath $scriptPath) + (Get-Content -Raw -LiteralPath (Join-Path $root 'scripts/browser-gate.sh')) + (Get-Content -Raw -LiteralPath (Join-Path $root 'docker/browser/Dockerfile'))
        Assert-Contract ($script -match 'node:22\.13\.1') 'El runtime Node portable no esta fijado en 22.13.1'
        Assert-Contract ($script -match 'pnpm@11\.17\.0') 'pnpm portable no esta fijado en 11.17.0'
        Assert-Contract ($script -match 'docker run') 'El gate portable no se ejecuta aislado en Docker'
    }
    $gate = Get-Content -Raw -LiteralPath (Join-Path $root 'scripts/gate.ps1')
    Assert-Contract ($gate -match 'browser-gate\.ps1') 'El gate agregado aun depende del pnpm/Node del host'
    Assert-Contract ($gate -notmatch "Invoke-Gate 'playwright' 'pnpm'") 'Playwright aun exige pnpm del host'
}

if ($failures.Count -gt 0) {
    $failures | ForEach-Object { Write-Error $_ -ErrorAction Continue }
    exit 1
}
Write-Output 'Contratos correctivos VERIFY aprobados.'
