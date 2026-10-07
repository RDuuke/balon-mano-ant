# Informe VERIFY - corregir-gate-global

## Sobre auditable

- Fecha de ejecucion: 2026-10-05.
- Estado inicial: APPLY, 18/26 tareas completas; pendientes 2.8, 2.9, 3.1, 3.2, 6.1, 6.2, 7.1 y 7.2; `awaiting_approval: false`.
- Autorizacion: el usuario autorizo explicitamente superar `review_budget`; no se solicito aprobacion adicional.
- Estado de aplicacion final: las ocho tareas autorizadas quedan marcadas y el estado persistido pasa a VERIFY con 26/26 tareas marcadas.
- Estado VERIFY: `no ok`. No se ejecuta `spec-sync` ni `archive`.

## Cambios propios

- `wp-content/themes/labm/functions.php`: identidad `data-labm-actualidad-post-id` en destacada y tarjetas.
- `wp-content/themes/labm/style.css`: token `--labm-green-dark: #3f5200`, ajuste de salto en banner Documentos y ancho responsivo del logo dentro del shell.
- `tests/php/PublicExperienceTest.php`: listado editorial por paginas, filtros, privacidad y ultima pagina.
- `tests/e2e/public-experience.spec.ts`: identidad editorial, contraste/foco/axe en cinco anchos y texto largo sin overflow.
- `openspec/changes/corregir-gate-global/tasks.md`, `apply-progress.md`, `verify-report.md` y `.status.yaml`: trazabilidad del flujo.

## Pruebas focales

1. PHPUnit RED 2.8 antes del hook: comando focal de `PublicExperienceTest`; exit1, IDs esperados `[9730,9729,9728,9727]`, IDs recibidos `[]`.
2. PHPUnit GREEN 2.8/2.9: mismo comando con ambos filtros; exit0, `2 tests, 40 assertions`.
3. Browser GREEN 2.9: CLI Playwright en `labm-browser-gate:node-22.13.1`, `--grep "3.2 actualidad ofrece"`, proyecto `wide-1440`; exit0, `1 passed (33.3s)`.
4. Browser GREEN 3.1/3.2: CLI Playwright, `--grep "3.1 contraste"`, proyecto `desktop-1024`, `--timeout 120000`; exit0, `1 passed (52.9s)`. Incluye ratios 4.5:1, foco 3:1 y axe.
5. Browser RED 6.2: CLI Playwright, `--grep "6.2 Documentos"`; exit1 a 320px con `scrollWidth=1275` por `.labm-header-logo` heredando `max-width:720px`.
6. Browser GREEN 6.2: mismo CLI despues del ajuste de CSS; exit0, `1 passed (41.1s)`; anchos 320/768/1024/1200/1440, texto largo, visibilidad y ancho del banner pasan.

## Gate integral

Comando exacto: `./scripts/gate.ps1 -IncludeBrowser`.

`artifacts/gate/summary.json` registra:

- `compose-config`: PASS, exit0.
- `composer-test`: PASS, exit0.
- `php-coverage`: PASS, exit0.
- `composer-lint`: PASS, exit0.
- `composer-analyse`: PASS, exit0.
- `browser-portable`: FAIL, exit137.

La ejecucion envolvente no devolvio normalmente porque el proceso browser quedo sin contenedor visible despues del exit137; se interrumpio con Ctrl+C para evitar espera indefinida. El resultado no se eleva a VERIFY ok. La cobertura fresca registrada por el gate es 82,74 %, superior al umbral 80 %.

## Persistencia, limpieza y LF

- Backup previo: `.content-sync/backups/backup-manual-20261005T045626532Z-User-DESKTOP-UOCJQQ8.zip`.
- Restore oficial: `./scripts/content-sync.ps1 -Action Restore -Backup .content-sync/backups/backup-manual-20261005T045626532Z-User-DESKTOP-UOCJQQ8.zip -ConfirmReplace`; salida final exit0; safety backup `backup-pre-restore-20261005T060137546Z-User-DESKTOP-UOCJQQ8.zip`.
- Se restauraron `home` y `siteurl` con WP-CLI oficial a `http://localhost:8080`; ambos reportaron valor sin cambios.
- `content-sync/latest.json`: version `20261001T194755441Z-rduuqe-RDUUQE`; SHA256 `ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e`, coincidente con `canonical.zip`.
- No se actualizaron datos persistidos intencionalmente; no se incluyeron credenciales, `wp_users` ni `wp_usermeta`.
- LF verificado solo en los archivos modificados por esta continuacion: `functions.php`, `style.css`, `PublicExperienceTest.php`, `public-experience.spec.ts`, `tasks.md`, `apply-progress.md`, `verify-report.md` y `.status.yaml`.

## Cierre

Las ocho tareas solicitadas fueron implementadas y sus pruebas focales pasan. El gate integral no es aprobable por `browser-portable` exit137; por el contrato de VERIFY no se ejecutan `spec-sync` ni `archive`. No se modificaron tareas completadas ni se realizaron operaciones destructivas.
