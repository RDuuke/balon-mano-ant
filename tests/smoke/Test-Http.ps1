$ErrorActionPreference = 'Stop'
$url = if ($env:WP_URL) { $env:WP_URL } else { 'http://localhost:8080' }
$response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 20
if ($response.StatusCode -ne 200) { throw "HTTP inesperado: $($response.StatusCode)" }
if ($response.BaseResponse.ResponseUri.AbsolutePath -match 'wp-admin/(install|setup-config)\.php') {
    throw "WordPress redirigio al asistente de instalacion: $($response.BaseResponse.ResponseUri.AbsolutePath)"
}
if ($response.Content -match 'Instalar WordPress|Welcome to WordPress.*installation') {
    throw 'El asistente de instalacion sigue visible.'
}
if ($response.Content -notmatch 'LABM Demo Ficticio') { throw 'La portada instalada no muestra el titulo esperado.' }
Write-Output "PASS HTTP $($response.StatusCode)"
