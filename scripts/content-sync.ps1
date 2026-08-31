[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('Status', 'Backup', 'Begin', 'Pull', 'Push', 'Restore')]
    [string] $Action,

    [string] $EnvFile = (Join-Path $PSScriptRoot '../.env'),
    [string] $RepositoryStore = (Join-Path $PSScriptRoot '../content-sync'),
    [string] $Owner,
    [string] $Package,
    [string] $Backup,
    [switch] $ConfirmReplace,
    [switch] $ForceStale,
    [switch] $AllowVersionMismatch
)

$ErrorActionPreference = 'Stop'
$script:Root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$script:EnvFile = [IO.Path]::GetFullPath($EnvFile)
$script:LocalRoot = Join-Path $script:Root '.content-sync'
$script:BackupRoot = Join-Path $script:LocalRoot 'backups'
$script:RepositoryStore = [IO.Path]::GetFullPath($RepositoryStore)
$script:ContainerWork = '/var/www/html/.content-sync'
$script:UploadsPath = '/var/www/html/wp-content/uploads'
$script:ManifestSchema = 1

function Write-Utf8Lf {
    param([string] $Path, [string] $Content)
    $normalized = $Content.Replace("`r`n", "`n").Replace("`r", "`n")
    if (-not $normalized.EndsWith("`n")) { $normalized += "`n" }
    [IO.File]::WriteAllText($Path, $normalized, [Text.UTF8Encoding]::new($false))
}

function Write-JsonFile {
    param([string] $Path, [object] $Value)
    Write-Utf8Lf -Path $Path -Content ($Value | ConvertTo-Json -Depth 12)
}

function Read-JsonFile {
    param([string] $Path)
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) { return $null }
    return Get-Content -Raw -LiteralPath $Path | ConvertFrom-Json
}

function Read-EnvFile {
    param([string] $Path)
    & (Join-Path $PSScriptRoot 'validate-env.ps1') -Path $Path | Out-Null
    $values = @{}
    foreach ($line in Get-Content -LiteralPath $Path) {
        if ($line -match '^\s*([^#=\s]+)=(.*)$') { $values[$matches[1]] = $matches[2] }
    }
    return $values
}

function Resolve-ConfiguredPath {
    param([string] $Value)
    if ([string]::IsNullOrWhiteSpace($Value)) { return $null }
    if ([IO.Path]::IsPathRooted($Value)) { return [IO.Path]::GetFullPath($Value) }
    return [IO.Path]::GetFullPath((Join-Path $script:Root $Value))
}

function Invoke-Docker {
    param(
        [string[]] $Arguments,
        [switch] $Capture,
        [switch] $AllowFailure
    )
    $previousPreference = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        if ($Capture) {
            $lines = @(& docker @Arguments 2>&1)
            $exitCode = $LASTEXITCODE
            $output = ($lines | ForEach-Object { $_.ToString() }) -join "`n"
        } else {
            & docker @Arguments
            $exitCode = $LASTEXITCODE
            $output = ''
        }
    } finally {
        $ErrorActionPreference = $previousPreference
    }
    if ($exitCode -ne 0 -and -not $AllowFailure) {
        throw "Docker fallo (codigo $exitCode). Revise 'docker compose ps' y los logs del servicio afectado."
    }
    return [pscustomobject]@{ ExitCode = $exitCode; Output = $output.Trim() }
}

function Get-ComposeArguments {
    param([string[]] $Tail)
    return @('compose', '--env-file', $script:EnvFile) + $Tail
}

function Invoke-WpCli {
    param(
        [string[]] $Arguments,
        [switch] $Capture,
        [switch] $AllowFailure
    )
    $tail = @('--profile', 'tools', 'run', '--rm', '--no-deps', 'wp-cli') + $Arguments
    return Invoke-Docker -Arguments (Get-ComposeArguments -Tail $tail) -Capture:$Capture -AllowFailure:$AllowFailure
}

function Assert-ServicesReady {
    Invoke-Docker -Arguments @('info', '--format', '{{.ServerVersion}}') -Capture | Out-Null
    $running = (Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('ps', '--status', 'running', '--services')) -Capture).Output -split "`n"
    foreach ($service in @('db', 'wordpress')) {
        if ($running -notcontains $service) {
            throw "El servicio Docker '$service' no esta activo. Ejecute docker compose up -d --wait."
        }
    }
}

function Get-LocalVersion {
    $result = Invoke-WpCli -Arguments @('option', 'get', 'labm_content_sync_version') -Capture -AllowFailure
    if ($result.ExitCode -ne 0 -or [string]::IsNullOrWhiteSpace($result.Output)) { return 'unversioned' }
    return ($result.Output -split "`n")[-1].Trim()
}

function Set-LocalVersion {
    param([string] $Version)
    if ($Version -eq 'unversioned') {
        Invoke-WpCli -Arguments @('option', 'delete', 'labm_content_sync_version') -AllowFailure | Out-Null
        return
    }
    Invoke-WpCli -Arguments @('option', 'update', 'labm_content_sync_version', $Version) | Out-Null
}

function Get-SafeOwner {
    param([string] $Value)
    if ([string]::IsNullOrWhiteSpace($Value)) {
        $Value = "$env:USERNAME@$env:COMPUTERNAME"
    }
    if ([string]::IsNullOrWhiteSpace($Value)) { throw 'Defina -Owner o CONTENT_SYNC_OWNER.' }
    return $Value.Trim()
}

function Get-VersionId {
    param([string] $OwnerName)
    $safe = ($OwnerName -replace '[^A-Za-z0-9._-]', '-').Trim('-')
    if ([string]::IsNullOrWhiteSpace($safe)) { $safe = 'developer' }
    return "{0}-{1}" -f (Get-Date).ToUniversalTime().ToString('yyyyMMddTHHmmssfffZ'), $safe
}

function Get-RelativeManifestPath {
    param([string] $BasePath, [string] $FilePath)
    $baseUri = [Uri]::new(($BasePath.TrimEnd('\', '/') + [IO.Path]::DirectorySeparatorChar))
    $fileUri = [Uri]::new($FilePath)
    return [Uri]::UnescapeDataString($baseUri.MakeRelativeUri($fileUri).ToString())
}

function New-TemporaryDirectory {
    $path = Join-Path ([IO.Path]::GetTempPath()) ("labm-content-sync-{0}" -f [guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Path $path | Out-Null
    return $path
}

function New-PortableZip {
    param([string] $SourceDirectory, [string] $DestinationZip)
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $fileStream = [IO.File]::Open($DestinationZip, [IO.FileMode]::CreateNew, [IO.FileAccess]::Write, [IO.FileShare]::None)
    try {
        $archive = [IO.Compression.ZipArchive]::new($fileStream, [IO.Compression.ZipArchiveMode]::Create, $false)
        try {
            foreach ($file in Get-ChildItem -LiteralPath $SourceDirectory -File -Recurse -Force | Sort-Object FullName) {
                $entryName = Get-RelativeManifestPath -BasePath $SourceDirectory -FilePath $file.FullName
                $entry = $archive.CreateEntry($entryName, [IO.Compression.CompressionLevel]::Optimal)
                $entryStream = $entry.Open()
                $sourceStream = [IO.File]::OpenRead($file.FullName)
                try { $sourceStream.CopyTo($entryStream) } finally { $sourceStream.Dispose(); $entryStream.Dispose() }
            }
        } finally {
            $archive.Dispose()
        }
    } finally {
        $fileStream.Dispose()
    }
}

function Remove-TemporaryDirectory {
    param([string] $Path)
    if ([string]::IsNullOrWhiteSpace($Path) -or -not (Test-Path -LiteralPath $Path)) { return }
    $resolved = (Resolve-Path -LiteralPath $Path).Path
    $tempRoot = [IO.Path]::GetFullPath([IO.Path]::GetTempPath())
    if (-not $resolved.StartsWith($tempRoot, [StringComparison]::OrdinalIgnoreCase) -or
        -not ([IO.Path]::GetFileName($resolved)).StartsWith('labm-content-sync-')) {
        throw "Se rechazo limpiar una ruta temporal inesperada: $resolved"
    }
    Remove-Item -LiteralPath $resolved -Recurse -Force
}

function Export-WordPressPayload {
    param([string] $Destination)
    $databasePath = Join-Path $Destination 'database.sql'
    $uploadsPath = Join-Path $Destination 'uploads'
    $containerDatabase = "$script:ContainerWork/database-$([guid]::NewGuid().ToString('N')).sql"
    New-Item -ItemType Directory -Path $uploadsPath -Force | Out-Null
    Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('exec', '-T', 'wordpress', 'sh', '-c', "mkdir -p '$script:ContainerWork' && chown 33:33 '$script:ContainerWork'")) | Out-Null
    try {
        $tablePrefix = (Invoke-WpCli -Arguments @('db', 'prefix') -Capture).Output.Split("`n")[-1].Trim()
        $excludedTables = "${tablePrefix}users,${tablePrefix}usermeta"
        Invoke-WpCli -Arguments @('db', 'export', $containerDatabase, '--add-drop-table', "--exclude_tables=$excludedTables") | Out-Null
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('cp', "wordpress:$containerDatabase", $databasePath)) | Out-Null
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('cp', "wordpress:$script:UploadsPath/.", $uploadsPath)) | Out-Null
    } finally {
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('exec', '-T', 'wordpress', 'rm', '-f', $containerDatabase)) -AllowFailure | Out-Null
    }
    if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf)) { throw 'No se genero database.sql.' }
    if (@(Get-ChildItem -LiteralPath $uploadsPath -File -Recurse -Force).Count -eq 0) {
        Write-Utf8Lf -Path (Join-Path $uploadsPath '.content-sync-empty') -Content 'placeholder'
    }
    return $tablePrefix
}

function New-ContentArchive {
    param(
        [string] $DestinationZip,
        [string] $Kind,
        [string] $ContentVersion,
        [string] $BaseVersion,
        [string] $OwnerName,
        [hashtable] $EnvironmentValues
    )
    $temp = New-TemporaryDirectory
    try {
        $staging = Join-Path $temp 'payload'
        New-Item -ItemType Directory -Path $staging | Out-Null
        $tablePrefix = Export-WordPressPayload -Destination $staging
        $wordpressVersion = (Invoke-WpCli -Arguments @('core', 'version') -Capture).Output.Split("`n")[-1].Trim()
        $files = @(
            Get-ChildItem -LiteralPath $staging -File -Recurse -Force |
                Sort-Object FullName |
                ForEach-Object {
                    [ordered]@{
                        path = Get-RelativeManifestPath -BasePath $staging -FilePath $_.FullName
                        size = $_.Length
                        sha256 = (Get-FileHash -LiteralPath $_.FullName -Algorithm SHA256).Hash.ToLowerInvariant()
                    }
                }
        )
        $manifest = [ordered]@{
            schemaVersion = $script:ManifestSchema
            packageKind = $Kind
            contentVersion = $ContentVersion
            baseVersion = $BaseVersion
            createdAtUtc = (Get-Date).ToUniversalTime().ToString('o')
            sourceOwner = $OwnerName
            sourceUrl = $EnvironmentValues['WP_URL']
            wordpressVersion = $wordpressVersion
            databaseFile = 'database.sql'
            uploadsDirectory = 'uploads'
            excludedTables = @("${tablePrefix}users", "${tablePrefix}usermeta")
            files = $files
        }
        Write-JsonFile -Path (Join-Path $staging 'manifest.json') -Value $manifest
        $destinationDirectory = Split-Path -Parent $DestinationZip
        New-Item -ItemType Directory -Path $destinationDirectory -Force | Out-Null
        if (Test-Path -LiteralPath $DestinationZip) { throw "El archivo ya existe: $DestinationZip" }
        New-PortableZip -SourceDirectory $staging -DestinationZip $DestinationZip
        return [pscustomobject]@{
            Path = $DestinationZip
            Manifest = $manifest
            Sha256 = (Get-FileHash -LiteralPath $DestinationZip -Algorithm SHA256).Hash.ToLowerInvariant()
        }
    } finally {
        Remove-TemporaryDirectory -Path $temp
    }
}

function Expand-ValidatedArchive {
    param([string] $ArchivePath, [string] $ExpectedSha256)
    if (-not (Test-Path -LiteralPath $ArchivePath -PathType Leaf)) { throw "Paquete inexistente: $ArchivePath" }
    if (-not [string]::IsNullOrWhiteSpace($ExpectedSha256)) {
        $actualArchiveHash = (Get-FileHash -LiteralPath $ArchivePath -Algorithm SHA256).Hash.ToLowerInvariant()
        if ($actualArchiveHash -ne $ExpectedSha256.ToLowerInvariant()) { throw 'El checksum del paquete no coincide con latest.json.' }
    }
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $zip = [IO.Compression.ZipFile]::OpenRead($ArchivePath)
    try {
        foreach ($entry in $zip.Entries) {
            $name = $entry.FullName.Replace('\', '/')
            if ($name.StartsWith('/') -or $name -match '(^|/)\.\.(/|$)' -or $name -match '^[A-Za-z]:') {
                throw "El paquete contiene una ruta insegura: $name"
            }
        }
    } finally {
        $zip.Dispose()
    }
    $temp = New-TemporaryDirectory
    Expand-Archive -LiteralPath $ArchivePath -DestinationPath $temp
    try {
        $manifestPath = Join-Path $temp 'manifest.json'
        $manifest = Read-JsonFile -Path $manifestPath
        if ($null -eq $manifest) { throw 'El paquete no contiene manifest.json.' }
        if ([int]$manifest.schemaVersion -ne $script:ManifestSchema) { throw "Version de manifiesto incompatible: $($manifest.schemaVersion)." }
        foreach ($required in @('contentVersion', 'sourceUrl', 'wordpressVersion', 'databaseFile', 'uploadsDirectory', 'files')) {
            if ($null -eq $manifest.$required -or [string]::IsNullOrWhiteSpace([string]$manifest.$required)) {
                throw "Campo requerido ausente en manifest.json: $required"
            }
        }
        foreach ($file in @($manifest.files)) {
            $relative = ([string]$file.path).Replace('/', [IO.Path]::DirectorySeparatorChar)
            $candidate = [IO.Path]::GetFullPath((Join-Path $temp $relative))
            if (-not $candidate.StartsWith([IO.Path]::GetFullPath($temp), [StringComparison]::OrdinalIgnoreCase)) {
                throw "Ruta de manifiesto fuera del paquete: $($file.path)"
            }
            if (-not (Test-Path -LiteralPath $candidate -PathType Leaf)) { throw "Archivo faltante en paquete: $($file.path)" }
            $actual = (Get-FileHash -LiteralPath $candidate -Algorithm SHA256).Hash.ToLowerInvariant()
            if ($actual -ne ([string]$file.sha256).ToLowerInvariant()) { throw "Checksum invalido: $($file.path)" }
            if ((Get-Item -LiteralPath $candidate).Length -ne [long]$file.size) { throw "Tamano invalido: $($file.path)" }
        }
        $databasePath = Join-Path $temp ([string]$manifest.databaseFile)
        $uploadsPath = Join-Path $temp ([string]$manifest.uploadsDirectory)
        if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf)) { throw 'database.sql no esta presente.' }
        if (-not (Test-Path -LiteralPath $uploadsPath -PathType Container)) { throw 'El directorio uploads no esta presente.' }
        return [pscustomobject]@{ Directory = $temp; Manifest = $manifest; Database = $databasePath; Uploads = $uploadsPath }
    } catch {
        Remove-TemporaryDirectory -Path $temp
        throw
    }
}

function Assert-CompatibleWordPress {
    param([string] $SourceVersion)
    $targetVersion = (Invoke-WpCli -Arguments @('core', 'version') -Capture).Output.Split("`n")[-1].Trim()
    $sourceMajorMinor = (($SourceVersion -split '\.')[0..1] -join '.')
    $targetMajorMinor = (($targetVersion -split '\.')[0..1] -join '.')
    if ($sourceMajorMinor -ne $targetMajorMinor -and -not $AllowVersionMismatch) {
        throw "WordPress incompatible: paquete $SourceVersion, destino $targetVersion. Use -AllowVersionMismatch solo tras revisar migraciones."
    }
}

function Import-ValidatedPayload {
    param(
        [pscustomobject] $Validated,
        [hashtable] $EnvironmentValues,
        [string] $SafetyBackup
    )
    if (-not $ConfirmReplace) {
        throw "La importacion reemplaza base de datos y uploads. Repita con -ConfirmReplace. Respaldo previsto: $SafetyBackup"
    }
    Assert-CompatibleWordPress -SourceVersion ([string]$Validated.Manifest.wordpressVersion)
    $targetUrl = [string]$EnvironmentValues['WP_URL']
    $sourceUrl = [string]$Validated.Manifest.sourceUrl
    $containerDatabase = "$script:ContainerWork/import-$([guid]::NewGuid().ToString('N')).sql"
    Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('exec', '-T', 'wordpress', 'sh', '-c', "mkdir -p '$script:ContainerWork' && chown 33:33 '$script:ContainerWork'")) | Out-Null
    try {
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('cp', $Validated.Database, "wordpress:$containerDatabase")) | Out-Null
        Invoke-WpCli -Arguments @('db', 'import', $containerDatabase) | Out-Null
        if ($sourceUrl.TrimEnd('/') -ne $targetUrl.TrimEnd('/')) {
            Invoke-WpCli -Arguments @('search-replace', $sourceUrl, $targetUrl, '--all-tables-with-prefix', '--precise', '--recurse-objects') | Out-Null
        }
        Invoke-WpCli -Arguments @('option', 'update', 'home', $targetUrl) | Out-Null
        Invoke-WpCli -Arguments @('option', 'update', 'siteurl', $targetUrl) | Out-Null
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('exec', '-T', 'wordpress', 'sh', '-c', "find '$script:UploadsPath' -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +")) | Out-Null
        $placeholder = Join-Path $Validated.Uploads '.content-sync-empty'
        if (Test-Path -LiteralPath $placeholder) { Remove-Item -LiteralPath $placeholder -Force }
        if (@(Get-ChildItem -LiteralPath $Validated.Uploads -Force).Count -gt 0) {
            Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('cp', ((Join-Path $Validated.Uploads '.') + [IO.Path]::DirectorySeparatorChar), "wordpress:$script:UploadsPath")) | Out-Null
        }
        Set-LocalVersion -Version ([string]$Validated.Manifest.contentVersion)
        Invoke-WpCli -Arguments @('cache', 'flush') -AllowFailure | Out-Null
    } catch {
        throw "Importacion incompleta. No borre volumenes. Restaure el respaldo '$SafetyBackup'. Detalle: $($_.Exception.Message)"
    } finally {
        Invoke-Docker -Arguments (Get-ComposeArguments -Tail @('exec', '-T', 'wordpress', 'rm', '-f', $containerDatabase)) -AllowFailure | Out-Null
    }
}

function Get-LatestPointer {
    return Read-JsonFile -Path (Join-Path $script:RepositoryStore 'latest.json')
}

function New-LocalBackup {
    param([hashtable] $EnvironmentValues, [string] $Reason)
    New-Item -ItemType Directory -Path $script:BackupRoot -Force | Out-Null
    $localVersion = Get-LocalVersion
    $id = Get-VersionId -OwnerName $script:OwnerName
    $path = Join-Path $script:BackupRoot ("backup-{0}-{1}.zip" -f $Reason, $id)
    $archive = New-ContentArchive -DestinationZip $path -Kind 'local-backup' -ContentVersion $localVersion -BaseVersion $localVersion -OwnerName $script:OwnerName -EnvironmentValues $EnvironmentValues
    Write-Output "Respaldo creado y verificado: $($archive.Path)"
    return $archive.Path
}

function Invoke-Pull {
    param([hashtable] $EnvironmentValues)
    $latest = Get-LatestPointer
    if ($null -eq $latest) { Write-Output 'No existe un paquete canonico publicado.'; return }
    $localVersion = Get-LocalVersion
    if ($localVersion -eq [string]$latest.version) { Write-Output "Contenido ya sincronizado: $localVersion"; return }
    $packagePath = Join-Path $script:RepositoryStore ([string]$latest.package)
    $validated = Expand-ValidatedArchive -ArchivePath $packagePath -ExpectedSha256 ([string]$latest.sha256)
    try {
        if ($localVersion -ne 'unversioned' -and $localVersion -ne [string]$validated.Manifest.baseVersion -and -not $ForceStale) {
            throw "El entorno local ($localVersion) no parte de la base esperada ($($validated.Manifest.baseVersion)). Use -ForceStale solo despues de respaldar y revisar contenido divergente."
        }
        if ($localVersion -eq 'unversioned') {
            Write-Warning 'Primera importacion sobre un entorno sin version. Se creara un respaldo completo antes de reemplazar contenido.'
        }
        $backupPath = New-LocalBackup -EnvironmentValues $EnvironmentValues -Reason 'pre-pull'
        Import-ValidatedPayload -Validated $validated -EnvironmentValues $EnvironmentValues -SafetyBackup $backupPath
        Write-Output "Contenido importado: $($validated.Manifest.contentVersion). Respaldo previo: $backupPath"
    } finally {
        Remove-TemporaryDirectory -Path $validated.Directory
    }
}

function Invoke-Push {
    param([hashtable] $EnvironmentValues)
    $latest = Get-LatestPointer
    $localVersion = Get-LocalVersion
    if ($null -ne $latest -and $localVersion -ne [string]$latest.version -and -not $ForceStale) {
        throw "Push obsoleto bloqueado: local=$localVersion, canonico=$($latest.version). Ejecute Pull o use -ForceStale tras consolidacion manual."
    }
    $version = Get-VersionId -OwnerName $script:OwnerName
    New-Item -ItemType Directory -Path $script:RepositoryStore -Force | Out-Null
    $relativePackage = 'canonical.zip'
    $packagePath = Join-Path $script:RepositoryStore $relativePackage
    $temporaryPackage = Join-Path $script:RepositoryStore ("canonical.$([guid]::NewGuid().ToString('N')).tmp.zip")
    $archive = New-ContentArchive -DestinationZip $temporaryPackage -Kind 'canonical-content' -ContentVersion $version -BaseVersion $localVersion -OwnerName $script:OwnerName -EnvironmentValues $EnvironmentValues
    $pointer = [ordered]@{
        schemaVersion = $script:ManifestSchema
        version = $version
        baseVersion = $localVersion
        package = $relativePackage
        sha256 = $archive.Sha256
        publishedAtUtc = (Get-Date).ToUniversalTime().ToString('o')
        publishedBy = $script:OwnerName
    }
    $latestPath = Join-Path $script:RepositoryStore 'latest.json'
    $temporaryLatest = "$latestPath.$([guid]::NewGuid().ToString('N')).tmp"
    Write-JsonFile -Path $temporaryLatest -Value $pointer
    Move-Item -LiteralPath $temporaryPackage -Destination $packagePath -Force
    Move-Item -LiteralPath $temporaryLatest -Destination $latestPath -Force
    Set-LocalVersion -Version $version
    Write-Output "Paquete canonico preparado para Git: $version ($packagePath)"
    Write-Output "Incluya content-sync/canonical.zip y content-sync/latest.json en el proximo commit."
}

function Show-Status {
    $localVersion = Get-LocalVersion
    $latest = Get-LatestPointer
    Write-Output "Version local: $localVersion"
    Write-Output "Version canonica: $(if ($null -eq $latest) { 'no publicada' } else { $latest.version })"
    Write-Output "Paquete Git: $(Join-Path $script:RepositoryStore 'canonical.zip')"
    Write-Output 'Coordine un unico editor de contenido; Git rechazara o marcara publicaciones concurrentes.'
}

Push-Location $script:Root
try {
    $environmentValues = Read-EnvFile -Path $script:EnvFile
    if ([string]::IsNullOrWhiteSpace($Owner)) { $Owner = $environmentValues['CONTENT_SYNC_OWNER'] }
    $script:OwnerName = Get-SafeOwner -Value $Owner
    New-Item -ItemType Directory -Path $script:RepositoryStore -Force | Out-Null
    Assert-ServicesReady

    switch ($Action) {
        'Status' { Show-Status }
        'Backup' { New-LocalBackup -EnvironmentValues $environmentValues -Reason 'manual' | Out-Null }
        'Begin' {
            Invoke-Pull -EnvironmentValues $environmentValues
            Write-Output 'Sesion de edicion iniciada. Coordine un unico escritor y publique al terminar con -Action Push.'
        }
        'Pull' { Invoke-Pull -EnvironmentValues $environmentValues }
        'Push' { Invoke-Push -EnvironmentValues $environmentValues }
        'Restore' {
            if ([string]::IsNullOrWhiteSpace($Backup)) { throw 'Restore requiere -Backup <ruta-al-zip>.' }
            $backupPath = Resolve-ConfiguredPath -Value $Backup
            $validated = Expand-ValidatedArchive -ArchivePath $backupPath -ExpectedSha256 ''
            try {
                $safety = New-LocalBackup -EnvironmentValues $environmentValues -Reason 'pre-restore'
                Import-ValidatedPayload -Validated $validated -EnvironmentValues $environmentValues -SafetyBackup $safety
                Write-Output "Respaldo restaurado: $backupPath. Respaldo del estado reemplazado: $safety"
            } finally {
                Remove-TemporaryDirectory -Path $validated.Directory
            }
        }
    }
} finally {
    Pop-Location
}
