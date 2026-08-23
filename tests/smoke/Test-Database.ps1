$ErrorActionPreference = 'Continue'
docker compose exec -T db healthcheck.sh --connect --innodb_initialized
if ($LASTEXITCODE -ne 0) { throw 'MariaDB no esta saludable.' }
Write-Output 'PASS DB saludable'
