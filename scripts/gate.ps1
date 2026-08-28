param([switch] $IncludeBrowser)
$ErrorActionPreference = 'Continue'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$artifactDir = Join-Path $root 'artifacts/gate'
New-Item -ItemType Directory -Force -Path $artifactDir | Out-Null
$results = [System.Collections.Generic.List[object]]::new()

function Invoke-Gate([string] $Name, [string] $Tool, [scriptblock] $Command, [string] $IsolatedScript = '', [string[]] $ScriptArguments = @()) {
    $available = Get-Command $Tool -ErrorAction SilentlyContinue
    if (-not $available) {
		$results.Add([pscustomobject]@{ Name=$Name; Status='NO EJECUTADA'; ExitCode=$null; Detail="Herramienta ausente: $Tool" })
        return
    }
    $version = & $Tool --version 2>&1 | Select-Object -First 1
    if ($IsolatedScript) {
        $powershell = Join-Path $PSHOME 'powershell.exe'
        $startInfo = New-Object System.Diagnostics.ProcessStartInfo
        $startInfo.FileName = $powershell
        $startInfo.WorkingDirectory = $root
        $startInfo.UseShellExecute = $false
        $startInfo.CreateNoWindow = $true
        $startInfo.RedirectStandardOutput = $true
        $startInfo.RedirectStandardError = $true
        $escapedScript = $IsolatedScript.Replace('"', '\"')
        $escapedArguments = @($ScriptArguments | ForEach-Object {
            if ($_ -match '\s') { '`"' + $_.Replace('"', '\"') + '`"' } else { $_ }
        })
        $startInfo.Arguments = "-NoLogo -NoProfile -NonInteractive -ExecutionPolicy Bypass -File `"$escapedScript`" $($escapedArguments -join ' ')"
        $process = New-Object System.Diagnostics.Process
        $process.StartInfo = $startInfo
        [void] $process.Start()
        $stdoutTask = $process.StandardOutput.ReadToEndAsync()
        $stderrTask = $process.StandardError.ReadToEndAsync()
        $process.WaitForExit()
        $output = @()
        if ($stdoutTask.Result) { $output += $stdoutTask.Result -split "`r?`n" }
        if ($stderrTask.Result) { $output += $stderrTask.Result -split "`r?`n" }
        $commandSucceeded = ($process.ExitCode -eq 0)
        $code = $process.ExitCode
    } else {
        $output = & $Command 2>&1
        $commandSucceeded = $?
        $code = $LASTEXITCODE
    }
    if (-not $commandSucceeded -and ($null -eq $code -or $code -eq 0)) { $code = 1 }
    $output | Out-File -LiteralPath (Join-Path $artifactDir "$Name.log") -Encoding utf8
	$results.Add([pscustomobject]@{ Name=$Name; Status=$(if($code -eq 0){'PASS'}else{'FAIL'}); ExitCode=$code; Detail="version=$version" })
}

Invoke-Gate 'compose-config' 'docker' { docker compose --env-file .env.example config --quiet }
Invoke-Gate 'composer-test' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 test }
Invoke-Gate 'wordpress-integration' 'docker' { docker run --rm --network labm_default --entrypoint php -e WORDPRESS_DB_HOST=db:3306 -e WORDPRESS_DB_NAME=labm_demo -e WORDPRESS_DB_USER=labm_demo -e WORDPRESS_DB_PASSWORD=demo_password_change_me -e WP_TESTS_RUNTIME_ROOT=/wordpress -v "${root}:/app" -v labm_composer_vendor:/app/vendor -v labm_wordpress_core:/wordpress -v "${root}/wp-content/themes/labm:/wordpress/wp-content/themes/labm:ro" -v "${root}/wp-content/plugins/labm-core:/wordpress/wp-content/plugins/labm-core:ro" -w /app wordpress:cli-2.11.0-php8.3 /app/vendor/bin/phpunit -c phpunit.integration.xml.dist }
Invoke-Gate 'php-coverage' 'docker' { & (Join-Path $root 'scripts/coverage.ps1') } -IsolatedScript (Join-Path $root 'scripts/coverage.ps1')
Invoke-Gate 'composer-lint' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 lint }
Invoke-Gate 'composer-analyse' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 analyse -- --no-progress }
if ($IncludeBrowser) {
    Invoke-Gate 'browser-portable' 'docker' { & (Join-Path $PSScriptRoot 'browser-gate.ps1') -Task playwright } -IsolatedScript (Join-Path $PSScriptRoot 'browser-gate.ps1') -ScriptArguments @('-Task', 'playwright')
}
$results | ConvertTo-Json -Depth 4 | Out-File -LiteralPath (Join-Path $artifactDir 'summary.json') -Encoding utf8
$results | Format-Table -AutoSize
if (@($results | Where-Object { $_.Status -ne 'PASS' }).Count -gt 0) { exit 1 }
exit 0
