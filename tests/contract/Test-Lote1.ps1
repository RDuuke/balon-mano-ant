param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('1.1','1.2','1.3','1.4','1.5','1.6','1.7','1.8','1.9','1.10','1.11','1.12','1.13','1.14')]
    [string] $Task
)

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path

function Assert-True([bool] $Condition, [string] $Message) {
    if (-not $Condition) { throw $Message }
}

function Read-ProjectFile([string] $RelativePath) {
    $path = Join-Path $root $RelativePath
    Assert-True (Test-Path -LiteralPath $path -PathType Leaf) "Falta $RelativePath"
    return Get-Content -Raw -LiteralPath $path
}

switch ($Task) {
    '1.1' {
        $ignore = Read-ProjectFile '.gitignore'
        @('.env','*.sql','wp-content/uploads/','vendor/','node_modules/','artifacts/','wordpress/') |
            ForEach-Object { Assert-True ($ignore -match [regex]::Escape($_)) "Falta ignorar $_" }
        Assert-True (Test-Path -LiteralPath (Join-Path $root 'scripts/test-repository-hygiene.ps1')) 'Falta prueba de higiene'
    }
    '1.2' {
        $example = Read-ProjectFile '.env.example'
        $validator = Read-ProjectFile 'scripts/validate-env.ps1'
        @('DB_NAME','DB_USER','DB_PASSWORD','DB_ROOT_PASSWORD','WP_PORT','WP_URL','WP_TITLE','WP_ADMIN_USER','WP_ADMIN_PASSWORD','WP_ADMIN_EMAIL') |
            ForEach-Object { Assert-True ($example -match "(?m)^$($_)=") "Falta variable $_" }
        Assert-True ($example -match 'FICTICIO') 'El ejemplo debe declarar datos ficticios'
        Assert-True ($validator -match 'Valor ausente') 'El validador debe informar variables ausentes'
        Assert-True ($validator -notmatch 'Write-.*\$value') 'El validador no debe imprimir valores'
    }
    '1.3' {
        $compose = Read-ProjectFile 'compose.yaml'
        @('db:','wordpress:','wp-cli:','healthcheck:','volumes:','profiles:') |
            ForEach-Object { Assert-True ($compose -match [regex]::Escape($_)) "Compose no contiene $_" }
        Assert-True ($compose -notmatch '(?m)image:\s*[^#\r\n]+:latest') 'No se permite latest'
    }
    '1.4' {
        @('tests/smoke/Test-Http.ps1','tests/smoke/Test-Database.ps1','tests/smoke/Test-Failure.ps1','tests/smoke/Test-Persistence.ps1') |
            ForEach-Object { Assert-True (Test-Path -LiteralPath (Join-Path $root $_)) "Falta $_" }
    }
    '1.5' {
        $theme = Read-ProjectFile 'wp-content/themes/labm/theme.json'
        $style = Read-ProjectFile 'wp-content/themes/labm/style.css'
        Assert-True ($theme -match '"version"\s*:\s*3') 'theme.json debe usar version 3'
        Assert-True ($style -match 'Theme Name:\s*LABM') 'Falta cabecera del tema'
        Assert-True (Test-Path -LiteralPath (Join-Path $root 'wp-content/themes/labm/templates/index.html')) 'Falta plantilla index'
    }
    '1.6' {
        $plugin = Read-ProjectFile 'wp-content/plugins/labm-core/labm-core.php'
        Assert-True ($plugin -match 'Plugin Name:\s*LABM Core') 'Falta cabecera del plugin'
        Assert-True ($plugin -match 'WP_CLI') 'El plugin debe integrar WP-CLI'
    }
    '1.7' {
        $functions = Read-ProjectFile 'wp-content/themes/labm/functions.php'
        Assert-True ($functions -match 'function_exists') 'El fallback debe comprobar disponibilidad del plugin'
        Assert-True ($functions -match 'LABM Core no esta activo') 'Falta mensaje seguro traducible'
    }
    '1.8' {
        $fixtures = Read-ProjectFile 'wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php'
        Assert-True ($fixtures -match 'DEMO LABM .* FICTICIO') 'Falta marcador ficticio'
        Assert-True ($fixtures -match 'post_name') 'El upsert debe usar una clave estable'
        Assert-True ($fixtures -notmatch 'docs[/\\].*\.pdf|file_get_contents\s*\(') 'Fixtures no deben leer PDF'
    }
    '1.9' {
        $bootstrap = Read-ProjectFile 'scripts/bootstrap.ps1'
        Assert-True ($bootstrap -match 'is-installed') 'Bootstrap debe detectar instalacion previa'
        Assert-True ($bootstrap -match 'plugin activate') 'Bootstrap debe activar plugin'
        Assert-True ($bootstrap -match 'theme activate') 'Bootstrap debe activar tema'
        Assert-True ($bootstrap -match 'labm fixtures load') 'Bootstrap debe cargar fixtures'
    }
    '1.10' {
        @('composer.json','composer.lock','phpcs.xml.dist','phpstan.neon.dist','phpunit.xml.dist','tests/php/bootstrap.php') |
            ForEach-Object { Assert-True (Test-Path -LiteralPath (Join-Path $root $_)) "Falta $_" }
        $composer = Read-ProjectFile 'composer.json'
        @('"test"','"lint"','"analyse"') | ForEach-Object { Assert-True ($composer -match $_) "Composer no define $_" }
    }
    '1.11' {
        @('package.json','pnpm-lock.yaml','playwright.config.ts','tests/e2e/home.spec.ts') |
            ForEach-Object { Assert-True (Test-Path -LiteralPath (Join-Path $root $_)) "Falta $_" }
        $config = Read-ProjectFile 'playwright.config.ts'
        @('320','768','1024','1440') | ForEach-Object { Assert-True ($config -match $_) "Falta viewport $_" }
        Assert-True ((Read-ProjectFile 'tests/e2e/home.spec.ts') -match 'axe') 'Falta axe en E2E'
    }
    '1.12' {
        @('lighthouserc.json','scripts/lighthouse.ps1') |
            ForEach-Object { Assert-True (Test-Path -LiteralPath (Join-Path $root $_)) "Falta $_" }
        Assert-True ((Read-ProjectFile 'lighthouserc.json') -match 'temporary-public-storage') 'Lighthouse debe evitar almacenamiento publico'
    }
    '1.13' {
        $gate = Read-ProjectFile 'scripts/gate.ps1'
        @('version','ExitCode','NO EJECUTADA','artifacts') | ForEach-Object { Assert-True ($gate -match $_) "Gate no registra $_" }
    }
    '1.14' {
        @('README.md','docs/development.md','docs/testing.md') |
            ForEach-Object { Assert-True (Test-Path -LiteralPath (Join-Path $root $_)) "Falta $_" }
        $docs = (Read-ProjectFile 'README.md') + (Read-ProjectFile 'docs/development.md') + (Read-ProjectFile 'docs/testing.md')
        @('diagnostico','confirmacion','entorno limpio','docker compose') | ForEach-Object { Assert-True ($docs -match $_) "Documentacion no cubre $_" }
    }
}

Write-Output "PASS tarea $Task"
