param([ValidateSet('playwright', 'lighthouse', 'all')][string] $Task = 'all')
$ErrorActionPreference = 'Stop'
$root = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Resolve-Path '.').Path }
$image = 'labm-browser-gate:node-22.13.1'
docker build --tag $image --file (Join-Path $root 'docker/browser/Dockerfile') $root
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update home http://host.docker.internal:8080 | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update siteurl http://host.docker.internal:8080 | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli labm fixtures load | Out-Null
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
try {
    docker run --rm --add-host host.docker.internal:host-gateway `
        -e CI=true -e WP_URL=http://host.docker.internal:8080 -e LABM_BROWSER_TASK=$Task `
        -v "${root}:/app" -v labm_pnpm_store:/pnpm/store -v labm_playwright_browsers:/root/.cache/ms-playwright `
        -w /app $image sh /app/scripts/browser-gate.sh
    $browserExit = $LASTEXITCODE
} finally {
    docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update home http://localhost:8080 | Out-Null
    docker compose --env-file (Join-Path $root '.env.example') run --rm wp-cli option update siteurl http://localhost:8080 | Out-Null
}
exit $browserExit

# SIG # Begin signature block
# MIIHDgYJKoZIhvcNAQcCoIIG/zCCBvsCAQExCzAJBgUrDgMCGgUAMGkGCisGAQQB
# gjcCAQSgWzBZMDQGCisGAQQBgjcCAR4wJgIDAQAABBAfzDtgWUsITrck0sYpfvNR
# AgEAAgEAAgEAAgEAAgEAMCEwCQYFKw4DAhoFAAQUz1aEAPNvI6IEl2SMf0Bf1e8w
# ZBWgggQeMIIEGjCCAoKgAwIBAgIQJVDX8cHCHYNPXcDGpBMWKTANBgkqhkiG9w0B
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
# KwYBBAGCNwIBCzEOMAwGCisGAQQBgjcCARUwIwYJKoZIhvcNAQkEMRYEFDiHGtX4
# aGCm8I80qOZWwG1emRHpMA0GCSqGSIb3DQEBAQUABIIBgJWk82YXtQEoI/rHHeqn
# ljxoc3S9UqCNno5h9cZ3bd5wkHkNZz+4bF2SU9HnEj0EMePjfDOCS27i4A5vkUXy
# s+aUjUtHA00fCEWJYHPBs+6IFiFTejf61YJfRVk0/bRfin1fP0ZpIIZKCe2mhdod
# Q6jHzB88UUrLx5xIYQOeJxIWJqfaHyngPRQuRKKpxybS9CeDBMsRNMQOafW+N4rd
# gtY3hQ/yqoT2EOzUClzGktVCwaODWteyYJyfD4ZMooG52Bo/8V5fDea6YDHULyum
# qXjhYCajV9fxAYy3+VpUxKGvqYHLfwOIJd2fcgV+15XF3px3w/YnGhkgW7rffrat
# BOBhz8hPzHeJcP1RBFD/VFm39HO11LffMxYjRlB4cn3FUutBWB4Xv5WwwVHMekjn
# 9oXmM/NfZ1WTZzj5fGP7syeLUz2CMWAiDyFA+kDPiu+K+68ML20O/k2hDW0j/lt7
# XOywOcMFSHUTfTbZ89xjc6Su+vvm5aLkfLpAFhGTxKI7Zg==
# SIG # End signature block
