[CmdletBinding()]
param([string] $RootPath = (Join-Path $PSScriptRoot '../..'))

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$root = (Resolve-Path -LiteralPath $RootPath).Path
$bootstrap = Join-Path $root 'scripts/bootstrap-hostinger-content.ps1'
$bootstrapBlock = $null
$secrets = @('DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'FTP_SERVER', 'FTP_USERNAME', 'FTP_PASSWORD', 'WP_USER', 'WP_PASSWORD')
$temporaryRoot = Join-Path ([IO.Path]::GetTempPath()) ('labm-hostinger-fixtures-' + [guid]::NewGuid().ToString('N'))
$savedEnvironment = @{}

function Assert-True {
    param([bool] $Condition, [string] $Message)
    if (-not $Condition) { throw "Prueba de fixtures incumplida: $Message" }
}

function Write-Utf8Lf {
    param([string] $Path, [string] $Content)
    $normalized = $Content.Replace("`r`n", "`n").Replace("`r", "`n")
    if (-not $normalized.EndsWith("`n")) { $normalized += "`n" }
    [IO.File]::WriteAllText($Path, $normalized, [Text.UTF8Encoding]::new($false))
}

function Invoke-ExpectFailure {
    param([scriptblock] $Action, [string] $ExpectedMessage)
    try {
        & $Action
    } catch {
        Assert-True -Condition ($_.Exception.Message -eq $ExpectedMessage) -Message "se esperaba '$ExpectedMessage' y se obtuvo '$($_.Exception.Message)'."
        return
    }
    throw "Prueba de fixtures incumplida: no fallo con '$ExpectedMessage'."
}

function Set-FixtureSecrets {
    foreach ($secret in $secrets) { [Environment]::SetEnvironmentVariable($secret, "fixture-$secret", 'Process') }
}

function New-Pointer {
    param([string] $Path, [string] $PackagePath, [string] $Version = 'fixture-v1')
    $pointer = [ordered]@{
        version = $Version
        package = [IO.Path]::GetFileName($PackagePath)
        sha256 = (Get-FileHash -LiteralPath $PackagePath -Algorithm SHA256).Hash.ToLowerInvariant()
    }
    Write-Utf8Lf -Path $Path -Content (ConvertTo-Json -InputObject $pointer)
}

function New-ValidPackage {
    param([string] $Path)
    $payload = Join-Path $temporaryRoot 'valid-payload'
    $uploads = Join-Path $payload 'uploads'
    New-Item -ItemType Directory -Path $uploads -Force | Out-Null
    $database = Join-Path $payload 'database.sql'
    $uploadFile = Join-Path $uploads 'fixture.txt'
    [IO.File]::WriteAllText($database, "-- fixture`n", [Text.UTF8Encoding]::new($false))
    [IO.File]::WriteAllText($uploadFile, "fixture`n", [Text.UTF8Encoding]::new($false))
    $fileHash = (Get-FileHash -LiteralPath $database -Algorithm SHA256).Hash.ToLowerInvariant()
    $uploadHash = (Get-FileHash -LiteralPath $uploadFile -Algorithm SHA256).Hash.ToLowerInvariant()
    $manifest = [ordered]@{
        schemaVersion = 1
        contentVersion = 'fixture-v1'
        sourceUrl = 'http://source.fixture.invalid'
        wordpressVersion = '6.8.3'
        databaseFile = 'database.sql'
        uploadsDirectory = 'uploads'
        excludedTables = @('wp_users', 'wp_usermeta')
        files = @(
            [ordered]@{ path = 'database.sql'; size = (Get-Item -LiteralPath $database).Length; sha256 = $fileHash },
            [ordered]@{ path = 'uploads/fixture.txt'; size = (Get-Item -LiteralPath $uploadFile).Length; sha256 = $uploadHash }
        )
    }
    Write-Utf8Lf -Path (Join-Path $payload 'manifest.json') -Content (ConvertTo-Json -InputObject $manifest -Depth 5)
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [IO.Compression.ZipFile]::CreateFromDirectory($payload, $Path)
}

function New-UnsafePackage {
    param([string] $Path)
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [IO.Compression.ZipFile]::Open($Path, [IO.Compression.ZipArchiveMode]::Create)
    try {
        $entry = $archive.CreateEntry('../fuera-del-paquete.txt')
        $writer = [IO.StreamWriter]::new($entry.Open())
        try { $writer.Write('fixture') } finally { $writer.Dispose() }
    } finally { $archive.Dispose() }
}

try {
    if (-not (Test-Path -LiteralPath $bootstrap)) { throw 'Prueba de fixtures incumplida: falta el script de bootstrap.' }
    $bootstrapBlock = [scriptblock]::Create((Get-Content -Raw -LiteralPath $bootstrap))
    New-Item -ItemType Directory -Path $temporaryRoot | Out-Null
    foreach ($secret in $secrets) { $savedEnvironment[$secret] = [Environment]::GetEnvironmentVariable($secret, 'Process') }
    $savedEnvironment['GITHUB_OUTPUT'] = [Environment]::GetEnvironmentVariable('GITHUB_OUTPUT', 'Process')
    $savedEnvironment['PATH'] = [Environment]::GetEnvironmentVariable('PATH', 'Process')

    # Un secreto faltante debe abortar antes de leer el puntero y sin escribir valores sensibles.
    foreach ($secret in $secrets) { [Environment]::SetEnvironmentVariable($secret, $null, 'Process') }
    [Environment]::SetEnvironmentVariable('DB_PASSWORD', 'no-debe-aparecer-en-el-informe', 'Process')
    $withoutSecrets = Join-Path $temporaryRoot 'without-secrets'
    Invoke-ExpectFailure -ExpectedMessage 'Falta el secreto requerido: DB_HOST.' -Action {
        & $bootstrapBlock -Phase Preflight -Package (Join-Path $temporaryRoot 'inexistente.zip') -Pointer (Join-Path $temporaryRoot 'inexistente.json') -TargetUrl 'http://127.0.0.1:1' -WorkRoot $withoutSecrets
    }
    $withoutSecretsReport = Get-Content -Raw -LiteralPath (Join-Path $withoutSecrets 'report.txt')
    Assert-True -Condition (-not $withoutSecretsReport.Contains('no-debe-aparecer-en-el-informe')) -Message 'el informe redactado expone el valor de un secreto.'

    # Un puntero inexistente y un ZIP con traversal fallan antes de acceder a MySQL o expandir archivos.
    Set-FixtureSecrets
    $invalidPointerRoot = Join-Path $temporaryRoot 'invalid-pointer'
    Invoke-ExpectFailure -ExpectedMessage 'Fallo de bootstrap Hostinger. Consulte el informe redactado y la configuracion.' -Action {
        & $bootstrapBlock -Phase Preflight -Package (Join-Path $temporaryRoot 'missing.zip') -Pointer (Join-Path $temporaryRoot 'missing.json') -TargetUrl 'http://127.0.0.1:1' -WorkRoot $invalidPointerRoot
    }
    Assert-True -Condition ((Get-Content -Raw -LiteralPath (Join-Path $invalidPointerRoot 'report.txt')).Contains('Archivo requerido inexistente:')) -Message 'el informe no explica el puntero invalido de forma redactada.'
    Assert-True -Condition (-not (Test-Path -LiteralPath (Join-Path $invalidPointerRoot 'payload'))) -Message 'un puntero invalido creo payload.'

    $unsafePackage = Join-Path $temporaryRoot 'unsafe.zip'
    $unsafePointer = Join-Path $temporaryRoot 'unsafe.json'
    New-UnsafePackage -Path $unsafePackage
    New-Pointer -Path $unsafePointer -PackagePath $unsafePackage
    $unsafeRoot = Join-Path $temporaryRoot 'unsafe-package'
    Invoke-ExpectFailure -ExpectedMessage 'El paquete contiene una ruta ZIP insegura.' -Action {
        & $bootstrapBlock -Phase Preflight -Package $unsafePackage -Pointer $unsafePointer -TargetUrl 'http://127.0.0.1:1' -WorkRoot $unsafeRoot
    }
    Assert-True -Condition (-not (Test-Path -LiteralPath (Join-Path $temporaryRoot 'fuera-del-paquete.txt'))) -Message 'un ZIP inseguro escribio fuera del directorio temporal.'
    Assert-True -Condition (-not (Test-Path -LiteralPath (Join-Path $unsafeRoot 'payload'))) -Message 'un ZIP inseguro fue expandido.'

    # Un checksum alterado debe abortar antes de descomprimir o acceder a MySQL.
    $checksumPackage = Join-Path $temporaryRoot 'checksum.zip'
    $checksumPointer = Join-Path $temporaryRoot 'checksum.json'
    New-ValidPackage -Path $checksumPackage
    New-Pointer -Path $checksumPointer -PackagePath $checksumPackage
    $checksumData = Get-Content -Raw -LiteralPath $checksumPointer | ConvertFrom-Json
    $checksumData.sha256 = ('0' * 64)
    Write-Utf8Lf -Path $checksumPointer -Content ($checksumData | ConvertTo-Json)
    $checksumRoot = Join-Path $temporaryRoot 'checksum'
    Invoke-ExpectFailure -ExpectedMessage 'El checksum del paquete no coincide con latest.json.' -Action {
        & $bootstrapBlock -Phase Preflight -Package $checksumPackage -Pointer $checksumPointer -TargetUrl 'http://127.0.0.1:1' -WorkRoot $checksumRoot
    }
    Assert-True -Condition (-not (Test-Path -LiteralPath (Join-Path $checksumRoot 'payload'))) -Message 'un checksum invalido creo payload.'

    # Finalize no puede continuar sin la transferencia de uploads ni sin una importacion completada.
    $finalizeWithoutUploads = Join-Path $temporaryRoot 'finalize-without-uploads'
    Invoke-ExpectFailure -ExpectedMessage 'Finalize requiere confirmar la transferencia FTP de uploads.' -Action {
        & $bootstrapBlock -Phase Finalize -Package $checksumPackage -Pointer $checksumPointer -TargetUrl 'http://127.0.0.1:1' -WorkRoot $finalizeWithoutUploads
    }
    $finalizeWithoutImport = Join-Path $temporaryRoot 'finalize-without-import'
    New-Item -ItemType Directory -Path $finalizeWithoutImport | Out-Null
    Write-Utf8Lf -Path (Join-Path $finalizeWithoutImport 'state.json') -Content (@{
        contentVersion = 'fixture-v1'
        targetPrefix = 'wp_'
        importCompleted = $false
    } | ConvertTo-Json)
    Invoke-ExpectFailure -ExpectedMessage 'Finalize requiere una importacion completada.' -Action {
        & $bootstrapBlock -Phase Finalize -Package $checksumPackage -Pointer $checksumPointer -TargetUrl 'http://127.0.0.1:1' -WorkRoot $finalizeWithoutImport -UploadsTransferred
    }

    # Un marcador realista simulado debe emitir should_migrate=false y no crear estado de importacion.
    $bin = Join-Path $temporaryRoot 'bin'
    New-Item -ItemType Directory -Path $bin | Out-Null
    if ($env:OS -eq 'Windows_NT') {
        @'
@echo off
echo %* | findstr /c:"SHOW TABLES" >nul
if not errorlevel 1 (
  echo wp_options
  echo wp_users
  exit /b 0
)
echo %* | findstr /c:"labm_content_sync_version" >nul
if not errorlevel 1 (
  echo fixture-marker
  exit /b 0
)
exit /b 0
'@ | Set-Content -LiteralPath (Join-Path $bin 'mysql.cmd') -Encoding ascii
    } else {
        @'
#!/usr/bin/env bash
args="$*"
if [[ "$args" == *"SHOW TABLES"* ]]; then
  printf 'wp_options\nwp_users\n'
  exit 0
fi
if [[ "$args" == *"labm_content_sync_version"* ]]; then
  printf 'fixture-marker\n'
fi
'@ | ForEach-Object { Write-Utf8Lf -Path (Join-Path $bin 'mysql') -Content $_ }
        & chmod +x -- (Join-Path $bin 'mysql')
    }
    [Environment]::SetEnvironmentVariable('PATH', ($bin + [IO.Path]::PathSeparator + $savedEnvironment['PATH']), 'Process')
    $validPackage = Join-Path $temporaryRoot 'valid.zip'
    $validPointer = Join-Path $temporaryRoot 'valid.json'
    New-ValidPackage -Path $validPackage
    New-Pointer -Path $validPointer -PackagePath $validPackage
    $fixtureZip = [IO.Compression.ZipFile]::OpenRead($validPackage)
    try {
        $fixtureManifestEntry = $fixtureZip.GetEntry('manifest.json')
        $fixtureReader = [IO.StreamReader]::new($fixtureManifestEntry.Open())
        try { $fixtureManifest = $fixtureReader.ReadToEnd() | ConvertFrom-Json } finally { $fixtureReader.Dispose() }
    } finally { $fixtureZip.Dispose() }
    Assert-True -Condition ($null -ne $fixtureManifest.files) -Message 'el paquete valido no contiene files en manifest.json.'
    function Invoke-RestMethod {
        param([string] $Uri, [string] $Method, [int] $TimeoutSec)
        if ($Uri -ne 'http://fixture.invalid/wp-json/') { throw 'El fixture REST recibio una URL inesperada.' }
        return [pscustomobject]@{ generator = 'https://wordpress.org/?v=6.8.3' }
    }
    $markerRoot = Join-Path $temporaryRoot 'marker'
    $githubOutput = Join-Path $temporaryRoot 'github-output.txt'
    [Environment]::SetEnvironmentVariable('GITHUB_OUTPUT', $githubOutput, 'Process')
    try {
        & $bootstrapBlock -Phase Preflight -Package $validPackage -Pointer $validPointer -TargetUrl 'http://fixture.invalid' -WorkRoot $markerRoot
    } catch {
        $markerReport = Get-Content -Raw -LiteralPath (Join-Path $markerRoot 'report.txt') -ErrorAction SilentlyContinue
        throw "Prueba de fixtures incumplida: el preflight con marcador fallo. Informe redactado: $markerReport"
    }
    $outputs = Get-Content -Raw -LiteralPath $githubOutput
    Assert-True -Condition ($outputs -match '(?m)^content_version=fixture-v1\r?$') -Message 'el preflight no informa la version del contenido.'
    Assert-True -Condition ($outputs -match '(?m)^should_migrate=false\r?$') -Message 'el marcador no omite la migracion.'
    Assert-True -Condition (-not (Test-Path -LiteralPath (Join-Path $markerRoot 'state.json'))) -Message 'el marcador permitio preparar una importacion.'
    Assert-True -Condition ((Get-Content -Raw -LiteralPath (Join-Path $markerRoot 'report.txt')).Contains('Migracion omitida: existe el marcador persistente.')) -Message 'el informe no registra la omision idempotente.'

    Write-Output 'PASS fixtures bootstrap Hostinger: secretos, puntero, ZIP seguro y marcador idempotente'
} finally {
    foreach ($name in $savedEnvironment.Keys) { [Environment]::SetEnvironmentVariable($name, $savedEnvironment[$name], 'Process') }
    if (Test-Path -LiteralPath $temporaryRoot) { Remove-Item -LiteralPath $temporaryRoot -Recurse -Force }
}
