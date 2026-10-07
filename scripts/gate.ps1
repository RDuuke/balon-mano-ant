param(
    [switch] $IncludeBrowser,
    [switch] $SkipCoverage
)
$ErrorActionPreference = 'Continue'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$artifactDir = Join-Path $root 'artifacts/gate'
New-Item -ItemType Directory -Force -Path $artifactDir | Out-Null
$results = [System.Collections.Generic.List[object]]::new()

function Invoke-Gate([string] $Name, [string] $Tool, [scriptblock] $Command, [string] $IsolatedScript = '', [string[]] $ScriptArguments = @(), [switch] $NonBlocking) {
    $available = Get-Command $Tool -ErrorAction SilentlyContinue
    if (-not $available) {
		$results.Add([pscustomobject]@{ Name=$Name; Status='NO EJECUTADA'; ExitCode=$null; Detail="Herramienta ausente: $Tool" })
        return
    }
    $version = & $Tool --version 2>&1 | Select-Object -First 1
    if ($IsolatedScript) {
        $powershell = [System.Diagnostics.Process]::GetCurrentProcess().MainModule.FileName
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
	$results.Add([pscustomobject]@{ Name=$Name; Status=$(if($code -eq 0){'PASS'}elseif($NonBlocking){'WARN'}else{'FAIL'}); ExitCode=$code; Detail="version=$version" })
}

Invoke-Gate 'compose-config' 'docker' { docker compose --env-file .env.example config --quiet }
Invoke-Gate 'composer-test' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 test }
if ($SkipCoverage) {
    # Sin cobertura, la capa de integracion conserva su ejecucion normal.
    Invoke-Gate 'wordpress-integration' 'docker' { docker compose run --rm -T --user 33:33 -e WP_TESTS_RUNTIME_ROOT=/var/www/html -v "${root}:/work:ro" -v labm_composer_vendor:/work/vendor -w /work wordpress php vendor/bin/phpunit -c phpunit.integration.xml.dist }
} else {
    # Coverage ejecuta la misma suite de integracion y aporta tambien su evidencia.
    Invoke-Gate 'php-coverage' 'docker' {} -IsolatedScript (Join-Path $root 'scripts/coverage.ps1')
}
Invoke-Gate 'composer-lint' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 lint }
Invoke-Gate 'composer-analyse' 'docker' { docker run --rm -v "${root}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 analyse -- --no-progress }
if ($IncludeBrowser) {
    Invoke-Gate 'browser-portable' 'docker' { & (Join-Path $PSScriptRoot 'browser-gate.ps1') -Task playwright } -IsolatedScript (Join-Path $PSScriptRoot 'browser-gate.ps1') -ScriptArguments @('-Task', 'playwright')
}
$results | ConvertTo-Json -Depth 4 | Out-File -LiteralPath (Join-Path $artifactDir 'summary.json') -Encoding utf8
$results | Format-Table -AutoSize
if (@($results | Where-Object { $_.Status -eq 'FAIL' }).Count -gt 0) { exit 1 }
exit 0

# SIG # Begin signature block
# MIIHDgYJKoZIhvcNAQcCoIIG/zCCBvsCAQExCzAJBgUrDgMCGgUAMGkGCisGAQQB
# gjcCAQSgWzBZMDQGCisGAQQBgjcCAR4wJgIDAQAABBAfzDtgWUsITrck0sYpfvNR
# AgEAAgEAAgEAAgEAAgEAMCEwCQYFKw4DAhoFAAQUWCW2KHA+SwBknq8gJ6a6yng2
# 7aWgggQeMIIEGjCCAoKgAwIBAgIQJVDX8cHCHYNPXcDGpBMWKTANBgkqhkiG9w0B
# AQsFADAlMSMwIQYDVQQDDBpDb250ZW50IFN5bmMgTG9jYWwgU2lnbmluZzAeFw0y
# NjEwMDIwMjI2MThaFw0yODEwMDIwMjM2MTdaMCUxIzAhBgNVBAMMGkNvbnRlbnQg
# U3luYyBMb2NhbCBTaWduaW5nMIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKC
# AYEAsVL4jZOImZAW8MIHJPouSFGZvj3ptOebnnqyr2NFjhOHqrpmH/cR16xOZXg6
# 16+9Z4CmZO02oIlWiNO5Sl3rAWXU+MBytzZe1ljWVEyj3+8bzs+LwklQHB6vLr4K
# 9PDyokCiiW6k+U3rk3V8SA+bJX2woTqlBqeWdZulHnpYCwz32rCBQiOLA4KI/hK9
# voKvsJnAp0n/9ZjRFz2bCEnLvC3lCOMQY7sGPKZkHfPF6z/8mCdywKYfaIDW+Z7C
# wFEvdWbCxfMy4Y22htrOteMgT9hyQ7jWvTvasQziT5QR3WRi77bRnYgvAAQbgmYX
# +xG3oa/KkLplnAfkcAMH4MfdhbhuIyqujQWksRSlXKM/63FbJuyq7O6FGLFlQ0SY
# AFG+vXKrekMalDkKTAAxp6uLrbnerHEUngujpDc2SR7r3xetZ8Zjanjduw4QGaaS
# ahcYG8jeafUXWprcsel3/jtaoKg5FIkUykxWzP11saR/UQbz1prJ8V1Gr/icoVcp
# s5gtAgMBAAGjRjBEMA4GA1UdDwEB/wQEAwIHgDATBgNVHSUEDDAKBggrBgEFBQcD
# AzAdBgNVHQ4EFgQUh085m4j0du0f0mDh+LrKaJVsaUwwDQYJKoZIhvcNAQELBQAD
# ggGBAB1tEDBmgLPZ/hBApclHgxezqLDhl8scUG+xLjEsXpKUuLn6An8tNBkKzw0g
# dgp5w3akVlXetMJjc2HBe1HVyE5rXAca92G1FHuSredSjLjtC+XgU3ireh+lNBle
# p3Zzfpoi1VmBiXy541Z559ZKNoXswFkEB98hmJb28B+/3rhK2IEZzT+EBXAT7OTN
# jT51bZCL7lsgJVrGzOg2GXBe/DO4eINp6DZDWcxTtOcWy9w44rOHe9d4dfS2SbLS
# ix0MsKOTE1UkJPrKk+wlFNGC3dJpZ3YBwHR/vPaUc03vBqV/3T4SML2hnjGn+FfG
# d2xjaYR66QU2EgfXgdYbQdorf9kLEWGGEPBd+EqE8yHWmqmqtofuPyBDNkEfPHqa
# tRgLPA58og7zzpcAOCQPYIBSNou1ug8QwmlZBMuHeUqySZLgZRLruyxvd1U4PGSS
# gBNwa5vHxwpLbcNJbDpUacKSdjKlg7jJkgru1cQeuTbPBdJo920Qc+90j1P9rKbw
# hWMOzDGCAlowggJWAgEBMDkwJTEjMCEGA1UEAwwaQ29udGVudCBTeW5jIExvY2Fs
# IFNpZ25pbmcCECVQ1/HBwh2DT13AxqQTFikwCQYFKw4DAhoFAKB4MBgGCisGAQQB
# gjcCAQwxCjAIoAKAAKECgAAwGQYJKoZIhvcNAQkDMQwGCisGAQQBgjcCAQQwHAYK
# KwYBBAGCNwIBCzEOMAwGCisGAQQBgjcCARUwIwYJKoZIhvcNAQkEMRYEFHJvvKgY
# nCZ7m+VfuaUVUvdclrHHMA0GCSqGSIb3DQEBAQUABIIBgCCscgwbbk6afLGV6w3w
# sEuAykOI3GGKgfpRW+GRGdWtMdmlnsobcL3EnRcWDuWszGcHB/ns9VTKZ8j/uWqo
# gIqd76ye0o6AEa5DPY29gA2xwreGtRKxYhPk2G5Q0VtyZ41YXXBBAIlbX60isAfT
# Hzm2nDYWN1O14ciLRS0UVke0543VdEOzB+SPLxHkjJhco+NSMysVYi4b18SZcvAQ
# O+trywSsbHtU51TtiKNQwBw671s+7nMY60Mqt433EW5yAouQLeeuYE7SEE/AdDem
# YiOE+FKcPyaNYgAFQLSb6HVSTUX3K++mO17f73GDYYwKMjAloh23vxx1rDTtvnTV
# O0vio6uvkNe+tVs8HhNMERqVKvYL+TGJht4rdVkPGsXwNQV0DFVIqc9L0wLK/3nU
# fONXl3ANLmQeL/BJJpyf53GDU2BoVvB/QLh3MOeKOE9NuHTmoLtdQplXmVCwJERQ
# WoLPk7HXA2ZKrvfNni9igtQHk1bjUoXDQinNb1rK22lRQA==
# SIG # End signature block
