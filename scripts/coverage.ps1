$ErrorActionPreference = 'Continue'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$artifactDir = Join-Path $root 'artifacts/coverage'
$clover = Join-Path $artifactDir 'clover.xml'
$runId = [guid]::NewGuid().ToString('N')
$runClover = Join-Path $artifactDir "clover.$runId.xml"
$minimum = 80.0

New-Item -ItemType Directory -Force -Path $artifactDir | Out-Null
if (Test-Path -LiteralPath $clover) {
    # Conservar evidencia previa y evitar sobrescribir un archivo creado por root.
    Move-Item -LiteralPath $clover -Destination (Join-Path $artifactDir "clover.previous.$runId.xml") -ErrorAction Stop
}
docker build --pull=false --tag labm-coverage:php8.3 --file (Join-Path $root 'docker/coverage/Dockerfile') $root
if ($LASTEXITCODE -ne 0) { throw 'No se pudo construir la imagen de cobertura fijada.' }

docker run --rm --user 33:33 --network labm_default --entrypoint php `
    -e WORDPRESS_DB_HOST=db:3306 `
    -e WORDPRESS_DB_NAME=labm_demo `
    -e WORDPRESS_DB_USER=labm_demo `
    -e WORDPRESS_DB_PASSWORD=demo_password_change_me `
    -e WP_TESTS_RUNTIME_ROOT=/wordpress `
    -v "${root}:/app" `
    -v labm_composer_vendor:/app/vendor `
    -v labm_wordpress_core:/wordpress `
    -v labm_wordpress_uploads:/wordpress/wp-content/uploads `
    -v "${root}/wp-content/themes/labm:/wordpress/wp-content/themes/labm:ro" `
    -v "${root}/wp-content/plugins/labm-core:/wordpress/wp-content/plugins/labm-core:ro" `
    -w /app labm-coverage:php8.3 `
    -d pcov.enabled=1 -d pcov.directory=/wordpress/wp-content `
    /app/vendor/bin/phpunit -c phpunit.integration.xml.dist "--coverage-clover=/app/artifacts/coverage/clover.$runId.xml"
$suiteExit = $LASTEXITCODE
if (-not (Test-Path -LiteralPath $runClover)) { throw 'PHPUnit no produjo evidencia Clover de esta ejecucion.' }
Move-Item -LiteralPath $runClover -Destination $clover -ErrorAction Stop

[xml] $report = Get-Content -Raw -LiteralPath $clover
$metrics = $report.coverage.project.metrics
$statements = [double] $metrics.statements
$covered = [double] $metrics.coveredstatements
if ($statements -le 0) { throw 'El reporte Clover no contiene lineas ejecutables.' }
$percentage = [math]::Round(($covered / $statements) * 100, 2)
Write-Output "Cobertura PHP: $percentage% ($covered/$statements lineas)."
if ($suiteExit -ne 0) { throw 'La suite de cobertura fallo; Clover actual conservado para diagnostico.' }
if ($percentage -lt $minimum) { throw "Cobertura PHP $percentage% inferior al minimo $minimum%." }
Write-Output "PASS cobertura PHP >= $minimum%."

# SIG # Begin signature block
# MIIHDgYJKoZIhvcNAQcCoIIG/zCCBvsCAQExCzAJBgUrDgMCGgUAMGkGCisGAQQB
# gjcCAQSgWzBZMDQGCisGAQQBgjcCAR4wJgIDAQAABBAfzDtgWUsITrck0sYpfvNR
# AgEAAgEAAgEAAgEAAgEAMCEwCQYFKw4DAhoFAAQUxrCEy2EGWK8+n3g29/eZQwR2
# QF+gggQeMIIEGjCCAoKgAwIBAgIQJVDX8cHCHYNPXcDGpBMWKTANBgkqhkiG9w0B
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
# KwYBBAGCNwIBCzEOMAwGCisGAQQBgjcCARUwIwYJKoZIhvcNAQkEMRYEFDIUig+0
# 2e2JVIu8djZFLQMIuFKaMA0GCSqGSIb3DQEBAQUABIIBgDpmLDzgR32QabPKCrjI
# yompP/Dlzd14pdJ/PziibCa0gb7fs7pIpNbpUvYGWEZZTa1nyxO1IOBGJxxyhuN4
# nteemAo4dKoQ0I8JMvB7R9+aPOSt7/fcxyd4gE4IodgzHn0v+Qx3pNCYHl2mJIHD
# U/KmCx9tqsaBLhY8rvPhtVqSoXvyuSx52yQEDV1V9sDgUSUDjJlh9E67gnTrFcQy
# NTwV8F0xJOLczJsqI7vGEXxtTMTqOx9Tv4i/AdrqGCD6u//X+m4BK32/7JUWirc6
# Tsn8+BM5vf0ZXtBsC2346BWfVUs3O+dbiCWniH/GUYLhdNSzZRLazy3wn7FDAdUc
# 6+ZV/iNiyh7nIeXi5geqMg8992eyH0yqSZQiJ8Mu3RdKRUG/tmo5SvpCp5gMr1Al
# TASkOHAFc+r8YHgoNTzHrHNzsLOzCd489rbzvGXMrwGrLd8tke2UoVKxhEF9DekG
# 7X+Z1AwTig1cQ++51hPwsRGRBvsvjccLpTjOYb4xEumk1g==
# SIG # End signature block
