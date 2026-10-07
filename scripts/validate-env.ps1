param([string] $Path = (Join-Path $PSScriptRoot '../.env'))
$ErrorActionPreference = 'Stop'
$required = @('DB_NAME','DB_USER','DB_PASSWORD','DB_ROOT_PASSWORD','WP_PORT','WP_URL','WP_TITLE','WP_ADMIN_USER','WP_ADMIN_PASSWORD','WP_ADMIN_EMAIL')
if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
    throw 'Valor ausente: archivo de entorno. Copie .env.example como .env.'
}
$keys = @{}
foreach ($line in Get-Content -LiteralPath $Path) {
    if ($line -match '^\s*([^#=\s]+)=(.*)$') { $keys[$matches[1]] = $matches[2] }
}
$missing = @($required | Where-Object { -not $keys.ContainsKey($_) -or [string]::IsNullOrWhiteSpace($keys[$_]) })
if ($missing.Count -gt 0) { throw "Valor ausente en variables requeridas: $($missing -join ', ')" }
if ($keys['WP_PORT'] -notmatch '^\d{2,5}$') { throw 'WP_PORT debe ser numerico.' }
Write-Output "Configuracion valida: $($required.Count) variables requeridas presentes; valores ocultos."


# SIG # Begin signature block
# MIIHDgYJKoZIhvcNAQcCoIIG/zCCBvsCAQExCzAJBgUrDgMCGgUAMGkGCisGAQQB
# gjcCAQSgWzBZMDQGCisGAQQBgjcCAR4wJgIDAQAABBAfzDtgWUsITrck0sYpfvNR
# AgEAAgEAAgEAAgEAAgEAMCEwCQYFKw4DAhoFAAQU2mswztP9i64ly8VHGbnuufnj
# LlygggQeMIIEGjCCAoKgAwIBAgIQJVDX8cHCHYNPXcDGpBMWKTANBgkqhkiG9w0B
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
# KwYBBAGCNwIBCzEOMAwGCisGAQQBgjcCARUwIwYJKoZIhvcNAQkEMRYEFODyqt8N
# ae3GGSiWQBZEnFtvWVUwMA0GCSqGSIb3DQEBAQUABIIBgDt3WvxisfFKClXvIvLp
# kPCxt/UuZcnprp2xWK2cBgSIfhNDCgeoIb1MzjbiFGjHDIiqWm7/w2FA8Aw0BxSn
# uU0yg9rai3SNKwAkOXz7Kx56haR9XKcKr8+Day59MGbRn6s3LFu9IX60j02NFmdz
# tcCIM8LFAl2YnRanuUZET5ZxDBIHPQkZzAEo8JiD+DGr5aKbPu/lADEZNrdkBTcC
# 5aa+CZ0byRRME9rTG4jxaUyzJ7YbQjKou0Qxj3IAdtUvGRBHBmx7/fMxTp9RM2Qx
# nG8JUNuwtFuQKCChl1gEV3UNcOFivaIs4B7Bn7crH03qFDHsa+yVN5KU+N2PPdXk
# SvV0+bci6N00XkuWqr0UvHmlH9na62xqfbQ0papoLVyPx7C/zvXToZJzaK34cjfb
# FQao2DZ/ZBv/exLV9NuC0aAYy/aVfKGbifP0/1j6y0+OyfwyT1HbaxHUf2onHcJt
# MNVYr+iiq8vcez7KQih5y+BEXxm53EwTxl7loLmSjsEFPw==
# SIG # End signature block
