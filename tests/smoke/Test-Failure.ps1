$ErrorActionPreference = 'Continue'
$json = docker compose ps --format json | ConvertFrom-Json
$bad = @($json | Where-Object { $_.State -ne 'running' -or ($_.Health -and $_.Health -ne 'healthy') })
if ($bad.Count -gt 0) { throw "Servicios con fallo visible: $($bad.Service -join ', ')" }
Write-Output 'PASS sin fallos ocultos'
