[CmdletBinding()]
param([string] $RootPath = (Join-Path $PSScriptRoot '../..'))

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path -LiteralPath $RootPath).Path
$workflow = Get-Content -Raw -LiteralPath (Join-Path $root '.github/workflows/quality.yml')
$scriptPath = Join-Path $root 'scripts/bootstrap-hostinger-content.ps1'
$documentation = Join-Path $root 'docs/ci-cd-hostinger.md'

function Assert-Contains {
    param([string] $Text, [string] $Expected, [string] $Description)
    if (-not $Text.Contains($Expected)) { throw "Contrato incumplido: $Description" }
}

if (-not (Test-Path -LiteralPath $scriptPath)) { throw 'Contrato incumplido: falta el bootstrap de Hostinger.' }
if (-not (Test-Path -LiteralPath $documentation)) { throw 'Contrato incumplido: falta la documentacion de Hostinger.' }
[scriptblock]::Create((Get-Content -Raw -LiteralPath $scriptPath)) | Out-Null

foreach ($secret in @('DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'FTP_SERVER', 'FTP_USERNAME', 'FTP_PASSWORD', 'WP_USER', 'WP_PASSWORD')) {
    Assert-Contains -Text $workflow -Expected ("secrets.$secret") -Description "el workflow no inyecta $secret"
    Assert-Contains -Text (Get-Content -Raw -LiteralPath $scriptPath) -Expected ("'$secret'") -Description "el script no exige $secret"
}
foreach ($required in @('needs: quality', 'needs: deploy-code', 'needs: bootstrap-content', 'refs/heads/main', 'SamKirkland/FTP-Deploy-Action@v4.4.0', 'protocol: ftp', 'port: 21', 'dangerous-clean-slate: false', '/public_html/wp-content/themes/labm/', '/public_html/wp-content/plugins/labm-core/', 'wp-login.php', 'wp-admin/profile.php')) {
    Assert-Contains -Text $workflow -Expected $required -Description "falta la proteccion o ruta $required"
}
if ($workflow.Contains('deploy-hostinger-sftp.sh') -or $workflow.Contains('sshpass')) { throw 'Contrato incumplido: el workflow conserva dependencias SFTP.' }
foreach ($required in @("ValidateSet('Preflight', 'Import', 'Finalize')", 'labm_content_sync_version', '--precise', '--recurse-objects', 'database-before-import.sql.gz', 'ruta ZIP insegura', 'GITHUB_OUTPUT')) {
    Assert-Contains -Text (Get-Content -Raw -LiteralPath $scriptPath) -Expected $required -Description "falta el contrato de bootstrap $required"
}

foreach ($required in @('set -euo pipefail', '--cookie-jar', '--output /dev/null', '--data-urlencode "pwd=$WP_PASSWORD"', 'test "$status" -lt 300')) {
    Assert-Contains -Text $workflow -Expected $required -Description "el smoke no conserva la salvaguarda $required"
}

$pointer = Get-Content -Raw -LiteralPath (Join-Path $root 'content-sync/latest.json') | ConvertFrom-Json
$package = Join-Path $root ('content-sync/' + $pointer.package)
if ((Get-FileHash -LiteralPath $package -Algorithm SHA256).Hash.ToLowerInvariant() -ne $pointer.sha256.ToLowerInvariant()) { throw 'Contrato incumplido: checksum del paquete canónico.' }
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [IO.Compression.ZipFile]::OpenRead($package)
try {
    foreach ($entry in $zip.Entries) {
        $name = $entry.FullName.Replace('\', '/')
        if ($name.StartsWith('/') -or $name -match '(^|/)\.\.(/|$)' -or $name -match '^[A-Za-z]:') { throw 'Contrato incumplido: ZIP canónico contiene una ruta insegura.' }
    }
} finally { $zip.Dispose() }

$fixtureScript = Get-Content -Raw -LiteralPath (Join-Path $root 'tests/contract/Test-HostingerBootstrapFixtures.ps1')
& ([scriptblock]::Create($fixtureScript)) -RootPath $root
Write-Output 'PASS contrato CI/CD Hostinger'
