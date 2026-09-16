[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('Preflight', 'Import', 'Finalize')]
    [string] $Phase,
    [Parameter(Mandatory = $true)][string] $Package,
    [Parameter(Mandatory = $true)][string] $Pointer,
    [Parameter(Mandatory = $true)][string] $TargetUrl,
    [string] $WorkRoot = (Join-Path $PSScriptRoot '../artifacts/hostinger-bootstrap'),
    [switch] $UploadsTransferred
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$script:RequiredSecrets = @('DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'FTP_SERVER', 'FTP_USERNAME', 'FTP_PASSWORD', 'WP_USER', 'WP_PASSWORD')
$script:StateFileName = 'state.json'
$script:ReportFileName = 'report.txt'

function Write-Utf8Lf {
    param([string] $Path, [string] $Content)
    $normalized = $Content.Replace("`r`n", "`n").Replace("`r", "`n")
    if (-not $normalized.EndsWith("`n")) { $normalized += "`n" }
    [IO.File]::WriteAllText($Path, $normalized, [Text.UTF8Encoding]::new($false))
}

function Write-Report {
    param([string] $Message)
    Add-Content -LiteralPath (Join-Path $script:WorkRoot $script:ReportFileName) -Value ("[{0}] {1}" -f (Get-Date).ToUniversalTime().ToString('o'), $Message) -Encoding utf8
}

function Throw-Safe {
    param([string] $Message)
    Write-Report -Message "ERROR: $Message"
    throw $Message
}

function Initialize-WorkRoot {
    $script:WorkRoot = [IO.Path]::GetFullPath($WorkRoot)
    New-Item -ItemType Directory -Path $script:WorkRoot -Force | Out-Null
    $report = Join-Path $script:WorkRoot $script:ReportFileName
    if (-not (Test-Path -LiteralPath $report)) { Write-Utf8Lf -Path $report -Content '# Informe de bootstrap Hostinger' }
}

function Assert-RequiredSecrets {
    foreach ($name in $script:RequiredSecrets) {
        if ([string]::IsNullOrWhiteSpace([string] [Environment]::GetEnvironmentVariable($name))) { Throw-Safe "Falta el secreto requerido: $name." }
    }
}

function Get-DbEndpoint {
    $value = [Environment]::GetEnvironmentVariable('DB_HOST').Trim()
    if ($value -match '^\[(?<host>[^\]]+)\]:(?<port>\d+)$') { return [pscustomobject]@{ Host = $matches.host; Port = $matches.port } }
    if ($value -match '^(?<host>[^:]+):(?<port>\d+)$') { return [pscustomobject]@{ Host = $matches.host; Port = $matches.port } }
    return [pscustomobject]@{ Host = $value; Port = '3306' }
}

function New-MySqlDefaultsFile {
    $endpoint = Get-DbEndpoint
    $path = Join-Path $script:WorkRoot ('.mysql-{0}.cnf' -f [guid]::NewGuid().ToString('N'))
    $content = @"
[client]
host=$($endpoint.Host)
port=$($endpoint.Port)
user=$([Environment]::GetEnvironmentVariable('DB_USERNAME'))
password=$([Environment]::GetEnvironmentVariable('DB_PASSWORD'))
database=$([Environment]::GetEnvironmentVariable('DB_NAME'))
"@
    Write-Utf8Lf -Path $path -Content $content
    if ([Environment]::OSVersion.Platform -ne [PlatformID]::Win32NT) { & chmod 600 -- $path }
    return $path
}

function Remove-SecretFile { param([string] $Path); if ($Path -and (Test-Path -LiteralPath $Path)) { Remove-Item -LiteralPath $Path -Force } }

function Invoke-MySql {
    param([string] $DefaultsFile, [string] $Query, [switch] $AllowFailure)
    $output = @(& mysql "--defaults-extra-file=$DefaultsFile" --batch --skip-column-names --execute=$Query 2>&1)
    if ($LASTEXITCODE -ne 0 -and -not $AllowFailure) { Throw-Safe 'No fue posible conectar o consultar MySQL remoto. Revise DB_HOST y el acceso remoto desde GitHub Actions.' }
    return @($output | ForEach-Object { $_.ToString().Trim() } | Where-Object { $_ })
}

function Get-QuotedIdentifier {
    param([string] $Name)
    if ($Name -notmatch '^[A-Za-z0-9_]+$') { Throw-Safe 'Se detecto un prefijo de tabla inseguro.' }
    return ('`{0}`' -f $Name)
}

function Get-TargetPrefix {
    param([string] $DefaultsFile)
    $tables = Invoke-MySql -DefaultsFile $DefaultsFile -Query 'SHOW TABLES'
    $candidates = @($tables | Where-Object { $_ -match '^(?<prefix>[A-Za-z0-9_]+)options$' } | ForEach-Object { $matches.prefix } | Where-Object { $tables -contains ("{0}users" -f $_) } | Select-Object -Unique)
    if ($candidates.Count -ne 1) { Throw-Safe 'No se pudo identificar un unico prefijo WordPress destino mediante las tablas options y users.' }
    return [string] $candidates[0]
}

function Read-JsonFile {
    param([string] $Path)
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) { Throw-Safe "Archivo requerido inexistente: $Path" }
    try { return Get-Content -Raw -LiteralPath $Path | ConvertFrom-Json } catch { Throw-Safe "JSON invalido en $Path." }
}

function Assert-SafeZipEntry {
    param([string] $EntryName)
    $normalized = $EntryName.Replace('\', '/')
    if ($normalized.StartsWith('/') -or $normalized -match '(^|/)\.\.(/|$)' -or $normalized -match '^[A-Za-z]:') { Throw-Safe 'El paquete contiene una ruta ZIP insegura.' }
}

function Get-ValidatedPayload {
    param([string] $PackagePath, [object] $PointerData)
    $packageFullPath = [IO.Path]::GetFullPath($PackagePath)
    $actualHash = (Get-FileHash -LiteralPath $packageFullPath -Algorithm SHA256).Hash.ToLowerInvariant()
    if ($actualHash -ne ([string] $PointerData.sha256).ToLowerInvariant()) { Throw-Safe 'El checksum del paquete no coincide con latest.json.' }
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [IO.Compression.ZipFile]::OpenRead($packageFullPath)
    try { foreach ($entry in $archive.Entries) { Assert-SafeZipEntry -EntryName $entry.FullName } } finally { $archive.Dispose() }
    $payloadPath = Join-Path $script:WorkRoot 'payload'
    if (Test-Path -LiteralPath $payloadPath) { Remove-Item -LiteralPath $payloadPath -Recurse -Force }
    Expand-Archive -LiteralPath $packageFullPath -DestinationPath $payloadPath -Force
    $manifest = Read-JsonFile -Path (Join-Path $payloadPath 'manifest.json')
    foreach ($field in @('schemaVersion', 'contentVersion', 'sourceUrl', 'wordpressVersion', 'databaseFile', 'uploadsDirectory', 'excludedTables', 'files')) {
        $value = $manifest.$field
        if ($null -eq $value -or ($value -is [string] -and [string]::IsNullOrWhiteSpace($value)) -or ($value -is [System.Collections.IEnumerable] -and $value -isnot [string] -and @($value).Count -eq 0)) { Throw-Safe "Falta el campo requerido '$field' en manifest.json." }
    }
    if ([int] $manifest.schemaVersion -ne 1) { Throw-Safe 'La version del manifiesto no es compatible.' }
    if ([string] $manifest.contentVersion -ne [string] $PointerData.version) { Throw-Safe 'La version del paquete no coincide con latest.json.' }
    foreach ($file in @($manifest.files)) {
        Assert-SafeZipEntry -EntryName ([string] $file.path)
        $candidate = [IO.Path]::GetFullPath((Join-Path $payloadPath (([string] $file.path).Replace('/', [IO.Path]::DirectorySeparatorChar))))
        if (-not $candidate.StartsWith(([IO.Path]::GetFullPath($payloadPath) + [IO.Path]::DirectorySeparatorChar), [StringComparison]::OrdinalIgnoreCase)) { Throw-Safe 'El manifiesto apunta fuera del paquete.' }
        if (-not (Test-Path -LiteralPath $candidate -PathType Leaf)) { Throw-Safe 'El manifiesto referencia un archivo inexistente.' }
        if ((Get-Item -LiteralPath $candidate).Length -ne [long] $file.size -or (Get-FileHash -LiteralPath $candidate -Algorithm SHA256).Hash.ToLowerInvariant() -ne ([string] $file.sha256).ToLowerInvariant()) { Throw-Safe 'La integridad de un archivo del paquete no es valida.' }
    }
    $databasePath = Join-Path $payloadPath ([string] $manifest.databaseFile)
    $uploadsPath = Join-Path $payloadPath ([string] $manifest.uploadsDirectory)
    if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf) -or -not (Test-Path -LiteralPath $uploadsPath -PathType Container)) { Throw-Safe 'El paquete no contiene la base de datos o uploads declarados.' }
    return [pscustomobject]@{ Manifest = $manifest; DatabasePath = $databasePath; UploadsPath = $uploadsPath }
}

function Get-SourcePrefix {
    param([object] $Manifest)
    $users = @($Manifest.excludedTables | Where-Object { $_ -match '^(?<prefix>[A-Za-z0-9_]+)users$' })
    if ($users.Count -ne 1) { Throw-Safe 'No se pudo deducir el prefijo fuente desde excludedTables.' }
    $prefix = ([string] $users[0] -replace 'users$', '')
    if ($Manifest.excludedTables -notcontains ("{0}usermeta" -f $prefix) -or $prefix -notmatch '^[A-Za-z0-9_]+$') { Throw-Safe 'excludedTables no es seguro o no conserva usermeta.' }
    return $prefix
}

function Get-RemoteWordPressVersion {
    try { $rest = Invoke-RestMethod -Uri ('{0}/wp-json/' -f $TargetUrl.TrimEnd('/')) -Method Get -TimeoutSec 30 } catch { Throw-Safe 'No fue posible consultar el indice REST de WordPress destino.' }
    if ([string] $rest.generator -notmatch '[?&]v=(?<version>\d+\.\d+(?:\.\d+)?)') { Throw-Safe 'El indice REST no informa una version de WordPress verificable.' }
    return $matches.version
}

function Assert-CompatibleWordPress {
    param([string] $SourceVersion, [string] $TargetVersion)
    if ((($SourceVersion -split '\.')[0..1] -join '.') -ne (($TargetVersion -split '\.')[0..1] -join '.')) { Throw-Safe "WordPress incompatible: paquete $SourceVersion y destino $TargetVersion." }
}

function Get-StatePath { return Join-Path $script:WorkRoot $script:StateFileName }
function Save-State { param([hashtable] $State); Write-Utf8Lf -Path (Get-StatePath) -Content ($State | ConvertTo-Json -Depth 6) }
function Get-State {
    $state = Read-JsonFile -Path (Get-StatePath)
    if ($null -eq $state.contentVersion -or $null -eq $state.targetPrefix) { Throw-Safe 'El estado de bootstrap es incompleto.' }
    return $state
}
function Write-GitHubOutput { param([string] $Name, [string] $Value); if ($env:GITHUB_OUTPUT) { Add-Content -LiteralPath $env:GITHUB_OUTPUT -Value ("{0}={1}" -f $Name, $Value) -Encoding utf8 } }

function New-TemporaryWordPress {
    param([object] $State)
    $root = Join-Path $script:WorkRoot ('.wp-cli-{0}' -f [guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Path $root | Out-Null
    try {
        $zip = Join-Path $root 'wordpress.zip'; $cli = Join-Path $root 'wp-cli.phar'
        Invoke-WebRequest -Uri ("https://wordpress.org/wordpress-{0}.zip" -f $State.remoteWordPressVersion) -OutFile $zip -UseBasicParsing
        Expand-Archive -LiteralPath $zip -DestinationPath $root
        Invoke-WebRequest -Uri 'https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar' -OutFile $cli -UseBasicParsing
        $config = @"
<?php
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
`$table_prefix = '$($State.targetPrefix)';
define('WP_HOME', '$($TargetUrl.TrimEnd('/'))');
define('WP_SITEURL', '$($TargetUrl.TrimEnd('/'))');
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';
"@
        Write-Utf8Lf -Path (Join-Path $root 'wordpress/wp-config.php') -Content $config
        return [pscustomobject]@{ Root = $root; Path = (Join-Path $root 'wordpress'); Cli = $cli }
    } catch { if (Test-Path -LiteralPath $root) { Remove-Item -LiteralPath $root -Recurse -Force -ErrorAction SilentlyContinue }; Throw-Safe 'No fue posible preparar WP-CLI temporal compatible.' }
}

function Invoke-WpCli {
    param([object] $TemporaryWordPress, [string[]] $Arguments, [switch] $AllowFailure)
    $output = @(& php $TemporaryWordpress.Cli "--path=$($TemporaryWordpress.Path)" --allow-root @Arguments 2>&1)
    if ($LASTEXITCODE -ne 0 -and -not $AllowFailure) { Throw-Safe 'WP-CLI no pudo completar la operacion de WordPress.' }
    return $output
}

function Invoke-Preflight {
    Assert-RequiredSecrets
    $pointerData = Read-JsonFile -Path $Pointer
    foreach ($field in @('version', 'package', 'sha256')) { if ([string]::IsNullOrWhiteSpace([string] $pointerData.$field)) { Throw-Safe "Falta el campo '$field' en latest.json." } }
    $payload = Get-ValidatedPayload -PackagePath $Package -PointerData $pointerData
    $defaults = New-MySqlDefaultsFile
    try {
        $targetPrefix = Get-TargetPrefix -DefaultsFile $defaults
        $remoteVersion = Get-RemoteWordPressVersion
        Assert-CompatibleWordPress -SourceVersion ([string] $payload.Manifest.wordpressVersion) -TargetVersion $remoteVersion
        $table = Get-QuotedIdentifier -Name ("{0}options" -f $targetPrefix)
        $marker = @(Invoke-MySql -DefaultsFile $defaults -Query "SELECT option_value FROM $table WHERE option_name = 'labm_content_sync_version' LIMIT 1" | Select-Object -First 1)
        Write-GitHubOutput -Name 'content_version' -Value ([string] $pointerData.version)
        if ($marker.Count -gt 0) { Write-GitHubOutput -Name 'should_migrate' -Value 'false'; Write-Report -Message 'Migracion omitida: existe el marcador persistente.'; return }
        Save-State -State ([ordered]@{ contentVersion = [string] $pointerData.version; sourceUrl = [string] $payload.Manifest.sourceUrl; sourcePrefix = Get-SourcePrefix -Manifest $payload.Manifest; targetPrefix = $targetPrefix; remoteWordPressVersion = $remoteVersion; package = [IO.Path]::GetFullPath($Package); pointer = [IO.Path]::GetFullPath($Pointer); importCompleted = $false })
        Write-GitHubOutput -Name 'should_migrate' -Value 'true'
        Write-Report -Message 'Preflight correcto; migracion inicial preparada.'
    } finally { Remove-SecretFile -Path $defaults }
}

function Invoke-Import {
    Assert-RequiredSecrets; $state = Get-State; $pointerData = Read-JsonFile -Path $state.pointer; $payload = Get-ValidatedPayload -PackagePath $state.package -PointerData $pointerData; $defaults = New-MySqlDefaultsFile
    try {
        $backup = Join-Path $script:WorkRoot 'database-before-import.sql.gz'
        $rawBackup = Join-Path $script:WorkRoot '.database-before-import.sql'
        & mysqldump "--defaults-extra-file=$defaults" --single-transaction --routines --triggers --events "--result-file=$rawBackup"
        if ($LASTEXITCODE -ne 0 -or -not (Test-Path -LiteralPath $rawBackup) -or (Get-Item -LiteralPath $rawBackup).Length -eq 0) { Throw-Safe 'No fue posible crear el respaldo MySQL previo a la importacion.' }
        & gzip -c -- $rawBackup > $backup
        $gzipExitCode = $LASTEXITCODE
        Remove-Item -LiteralPath $rawBackup -Force -ErrorAction SilentlyContinue
        if ($gzipExitCode -ne 0 -or -not (Test-Path -LiteralPath $backup) -or (Get-Item -LiteralPath $backup).Length -eq 0) { Throw-Safe 'No fue posible comprimir el respaldo MySQL previo a la importacion.' }
        $rewritten = Join-Path $script:WorkRoot 'database-target.sql'
        $sourceToken = [string]::Concat([char] 96, [string] $state.sourcePrefix); $targetToken = [string]::Concat([char] 96, [string] $state.targetPrefix)
        Write-Utf8Lf -Path $rewritten -Content ((Get-Content -Raw -LiteralPath $payload.DatabasePath).Replace($sourceToken, $targetToken))
        Get-Content -Raw -LiteralPath $rewritten | & mysql "--defaults-extra-file=$defaults"
        if ($LASTEXITCODE -ne 0) { Throw-Safe 'La importacion MySQL no se completo; restaure el artefacto database-before-import.sql.gz antes de reintentar.' }
        $table = Get-QuotedIdentifier -Name ("{0}options" -f $state.targetPrefix)
        Invoke-MySql -DefaultsFile $defaults -Query "UPDATE $table SET option_name = '$($state.targetPrefix)user_roles' WHERE option_name = '$($state.sourcePrefix)user_roles'" | Out-Null
        Invoke-MySql -DefaultsFile $defaults -Query "DELETE FROM $table WHERE option_name = 'labm_content_sync_version'" | Out-Null
        $hashState = $state | ConvertTo-Json -Depth 6 | ConvertFrom-Json -AsHashtable; $hashState.importCompleted = $true; Save-State -State $hashState
        Remove-Item -LiteralPath $rewritten -Force
        Write-Report -Message 'Base de datos importada; uploads pendientes de transferencia FTP.'
    } finally {
        Remove-Item -LiteralPath (Join-Path $script:WorkRoot '.database-before-import.sql') -Force -ErrorAction SilentlyContinue
        Remove-Item -LiteralPath (Join-Path $script:WorkRoot 'database-target.sql') -Force -ErrorAction SilentlyContinue
        Remove-SecretFile -Path $defaults
    }
}

function Invoke-Finalize {
    if (-not $UploadsTransferred) { Throw-Safe 'Finalize requiere confirmar la transferencia FTP de uploads.' }
    Assert-RequiredSecrets; $state = Get-State
    if (-not [bool] $state.importCompleted) { Throw-Safe 'Finalize requiere una importacion completada.' }
    $temporary = New-TemporaryWordPress -State $state
    try {
        Invoke-WpCli -TemporaryWordPress $temporary -Arguments @('search-replace', [string] $state.sourceUrl, $TargetUrl.TrimEnd('/'), '--all-tables-with-prefix', '--precise', '--recurse-objects') | Out-Null
        Invoke-WpCli -TemporaryWordPress $temporary -Arguments @('option', 'update', 'home', $TargetUrl.TrimEnd('/')) | Out-Null
        Invoke-WpCli -TemporaryWordPress $temporary -Arguments @('option', 'update', 'siteurl', $TargetUrl.TrimEnd('/')) | Out-Null
        Invoke-WpCli -TemporaryWordPress $temporary -Arguments @('cache', 'flush') -AllowFailure | Out-Null
        Invoke-WpCli -TemporaryWordPress $temporary -Arguments @('option', 'update', 'labm_content_sync_version', [string] $state.contentVersion) | Out-Null
        Write-Report -Message 'Migracion finalizada; marcador persistente creado.'
        Remove-Item -LiteralPath (Join-Path $script:WorkRoot 'payload') -Recurse -Force -ErrorAction SilentlyContinue
        Remove-Item -LiteralPath (Get-StatePath) -Force -ErrorAction SilentlyContinue
    } finally { if (Test-Path -LiteralPath $temporary.Root) { Remove-Item -LiteralPath $temporary.Root -Recurse -Force } }
}

Initialize-WorkRoot
try { switch ($Phase) { 'Preflight' { Invoke-Preflight }; 'Import' { Invoke-Import }; 'Finalize' { Invoke-Finalize } } }
catch {
    if ($_.Exception.Message -notmatch '^(Falta el secreto requerido|El checksum|El paquete|La version|No fue posible|No se pudo|WordPress incompatible|El manifiesto|La integridad|La importacion|Finalize requiere|El estado)') { Write-Report -Message 'ERROR: Fallo de bootstrap redactado; consulte la configuracion sin exponer secretos.'; throw 'Fallo de bootstrap Hostinger. Consulte el informe redactado y la configuracion.' }
    throw
}
