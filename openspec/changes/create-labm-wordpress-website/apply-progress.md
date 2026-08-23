# Progreso APPLY: Lote 1

## Tarea 1.1 — Higiene del repositorio

- **RED:** test `tests/contract/Test-Lote1.ps1::1.1` falla con: `Falta .gitignore`.
- **GREEN:** implementación en `.gitignore` y `scripts/test-repository-hygiene.ps1`; contrato y prueba de higiene pasan.
- **REFACTOR:** la detección evita imprimir rutas o contenido sensible.

## Tarea 1.2 — Entorno ficticio validado

- **RED:** test `tests/contract/Test-Lote1.ps1::1.2` falla con: `Falta .env.example`.
- **GREEN:** implementación en `.env.example`, `.env` ignorado y `scripts/validate-env.ps1`; el ejemplo pasa y un archivo ausente falla sin revelar valores.

## Tarea 1.3 — Docker Compose reproducible

- **RED:** test `tests/contract/Test-Lote1.ps1::1.3` falla con: `Falta compose.yaml`.
- **GREEN:** implementación en `compose.yaml`; `docker compose --env-file .env.example config --quiet` pasa y las tres imágenes usan tag y digest.
- **TRIANGULATE:** `db` y `wordpress` alcanzan estado saludable mediante `docker compose up -d --wait`.

## Tarea 1.4 — Smoke de infraestructura

- **RED:** test `tests/contract/Test-Lote1.ps1::1.4` falla con: `Falta tests/smoke/Test-Http.ps1`.
- **GREEN:** implementación en `tests/smoke/`; HTTP, DB, fallos visibles y persistencia no destructiva pasan.
- **TRIANGULATE:** el primer smoke de persistencia falla con `El marcador no persistio`; se cambia a una opción idempotente y pasa tras reiniciar ambos servicios.

## Tarea 1.5 — Tema de bloques mínimo

- **RED:** test `tests/contract/Test-Lote1.ps1::1.5` falla con: `Falta wp-content/themes/labm/theme.json`.
- **GREEN:** implementación en `wp-content/themes/labm/`; el tema se activa de forma aislada y la portada responde HTTP 200.

## Tarea 1.6 — Plugin persistente mínimo

- **RED:** test `tests/contract/Test-Lote1.ps1::1.6` falla con: `Falta wp-content/plugins/labm-core/labm-core.php`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/`; activación aislada y persistencia al cambiar de tema pasan sin errores fatales.

## Tarea 1.7 — Fallback seguro

- **RED:** test `tests/contract/Test-Lote1.ps1::1.7` falla con: `Falta wp-content/themes/labm/functions.php`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; con `labm-core` desactivado la portada responde HTTP 200 y el plugin puede reactivarse.
- **REFACTOR:** el tema consulta `function_exists` y usa una cadena traducible y escapada.

## Tarea 1.8 — Fixtures idempotentes

- **RED:** test `tests/contract/Test-Lote1.ps1::1.8` falla con: `Falta wp-content/plugins/labm-core/includes/class-fixtures-command.php`.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; dos cargas conservan un registro por slug, preservan contenido ajeno y mantienen cero adjuntos PDF.
- **TRIANGULATE:** test `tests/smoke/Test-Fixtures.ps1::fixtures` cubre repetición, contenido editorial ajeno y MIME PDF.

## Tarea 1.9 — Bootstrap idempotente

- **RED:** test `tests/contract/Test-Lote1.ps1::1.9` falla con: `Falta scripts/bootstrap.ps1`; además, el usuario observa el asistente de instalación.
- **GREEN:** implementación en `scripts/bootstrap.ps1`; dos ejecuciones terminan con código 0, `wp core is-installed` devuelve 0 y `home/siteurl` son `http://localhost:8080`.
- **TRIANGULATE:** test `tests/smoke/Test-Http.ps1::installer` rechaza `install.php` y el texto del asistente; la portada instalada pasa.

## Tarea 1.10 — Gates PHP y WordPress

- **RED:** test `tests/contract/Test-Lote1.ps1::1.10` falla con: `Falta composer.json`; la primera integración falla porque Composer no incluye `mysqli`, y el agregador detecta mezcla de suites.
- **GREEN:** implementación en `composer.json`, `composer.lock`, configuraciones y `tests/php/`; PHPUnit unitario pasa 1 prueba/2 aserciones, integración WordPress pasa 2 pruebas/7 aserciones, WPCS y PHPStan pasan.
- **REFACTOR:** unitarias e integración usan suites separadas y la integración ejecuta PHP 8.3 con `mysqli`.

## Tarea 1.11 — Playwright y axe

- **RED:** test `tests/contract/Test-Lote1.ps1::1.11` falla con: `Falta package.json`; luego Playwright detecta selector ambiguo y axe detecta una lista de navegación inválida.
- **GREEN:** implementación vigente en `package.json`, `pnpm-lock.yaml`, `playwright.config.ts` y `tests/e2e/home.spec.ts`; pasan 4 pruebas en 320, 768, 1024 y 1440.
- **REFACTOR:** la navegación usa enlaces explícitos con estructura de lista válida.

## Tarea 1.12 — Lighthouse baseline

- **RED:** test `tests/contract/Test-Lote1.ps1::1.12` falla con: `Falta lighthouserc.json`; el primer gate falla por aserciones finales prematuras y por `EPERM` al limpiar temporales en Windows.
- **GREEN:** implementación en `lighthouserc.json` y `scripts/lighthouse.ps1`; reporte validado con Performance 100, Accessibility 100, Best Practices 100 y SEO 82, sin imponer umbrales finales.
- **REFACTOR:** el wrapper solo tolera el `EPERM` de limpieza si existe un reporte completo sin `runtimeError`; cualquier otro fallo conserva código no exitoso.

## Tarea 1.13 — Gate agregado honesto

- **RED:** test `tests/contract/Test-Lote1.ps1::1.13` falla con: `Falta scripts/gate.ps1`; una ejecución intermedia registra Lighthouse `FAIL`.
- **GREEN:** implementación en `scripts/gate.ps1`; el resumen final registra PASS para Compose, PHPUnit, integración WordPress, WPCS, PHPStan, Playwright y Lighthouse.
- **TRIANGULATE:** una herramienta ausente queda `NO EJECUTADA` y hace fallar el gate por contrato.

## Tarea 1.14 — Documentación operativa

- **RED:** test `tests/contract/Test-Lote1.ps1::1.14` falla con: `Falta README.md`.
- **GREEN:** implementación en `README.md`, `docs/development.md` y `docs/testing.md`; el contrato cubre ciclo de vida, diagnóstico, pruebas, confirmación de reset y entorno limpio.

## Tarea PNPM — Migración exclusiva del toolchain JS

- **RED:** test `tests/contract/Test-PnpmToolchain.ps1::gestor_exclusivo` falla con: `packageManager debe fijar una version concreta de pnpm`.
- **GREEN:** implementación en `package.json`, `pnpm-lock.yaml`, scripts, gates, documentación y contratos; `pnpm install --frozen-lockfile` y el contrato pnpm pasan sin referencias operativas obsoletas.
- **TRIANGULATE:** `pnpm run test:e2e` pasa 4/4 viewports y `pnpm run lighthouse` valida baseline 100/100/100/82.
- **REFACTOR:** se valida y retira exclusivamente `C:\Users\rduuqe\Documents\BalonManoAnt\node_modules` generado por npm antes de reconstruirlo con pnpm; `package-lock.json` queda eliminado.

## Tarea 2.1 — RED del modelo de dominio

- **RED:** test `tests/php/DomainModelTest.php::modelo_dominio` y `tests/php/FixturesDomainTest.php::fixtures_dominio` falla con 11 fallos: cinco CPT ausentes, taxonomías/metadatos/capacidades/i18n sin registrar y fixtures inexistentes.
- **GREEN:** implementación posterior en `labm-core`; la suite completa pasa 16 pruebas y 82 aserciones.
- **TRIANGULATE:** las pruebas cubren usuario suscriptor sin capacidades, fecha ISO inválida y borrador excluido de consulta pública.

## Tarea 2.2 — Dominio persistente en labm-core

- **RED:** test `tests/php/DomainModelTest.php::capacidades` falla después del primer registro porque los nombres de capacidades no conservan el prefijo `labm_`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php`; cinco CPT, dos taxonomías, metadatos REST, validación, capacidades e i18n pasan.
- **TRIANGULATE:** test `tests/smoke/Test-DomainPersistence.ps1::cambio_tema` conserva contenido y metadatos al activar Twenty Twenty-Five y restaurar LABM.
- **REFACTOR:** capacidades idempotentes versionadas y separación del dominio respecto del tema.

## Tarea 2.3 — Fixtures de dominio y bordes

- **RED:** test `tests/php/FixturesDomainTest.php::estados_y_terminos` falla porque los fixtures de dominio no existen; la primera implementación deja `Noticias` sin asignar por usar nombres en una taxonomía jerárquica.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; fixtures públicos, borradores, privados, categorías, modalidades y metadatos pasan 16 pruebas/82 aserciones tras asignar IDs de término.
- **TRIANGULATE:** test `tests/smoke/Test-DomainPersistence.ps1::contenido_ajeno` preserva contenido editorial ajeno; dos cargas no duplican slugs y la suite confirma cero adjuntos PDF.
- **REFACTOR:** upsert común por slug, tipo y marcador ficticio, sin acceso a `docs/*.pdf`.

## Tarea 3.1 — Navegación, Inicio y Nosotros

- **RED:** `pnpm run test:e2e -- tests/e2e/public-experience.spec.ts --project=mobile-320` termina con código 1; `3.1 navegación global...` no encuentra el enlace Inicio en la navegación y las rutas/patrones institucionales aún no existen.
- **GREEN:** patrones `labm/inicio` y `labm/nosotros`, plantillas `front-page`, `page-nosotros` y `404`, navegación global y skip link implementados; `pnpm run test:e2e` pasa 20/20 pruebas en 320/768/1024/1440.
- **TRIANGULATE:** teclado produce un `:focus-visible`; `/nosotros/` muestra H1 y `/ruta-ficticia-ausente/` responde 404 con enlace de retorno.
- **REFACTOR:** todas las cadenas de patrones PHP son traducibles y el contenido de muestra conserva `[DEMO LABM — FICTICIO]`.

## Tarea 3.2 — Actualidad y selecciones públicas

- **RED:** integración WordPress termina con código 2: 20 pruebas, 1 error y 3 fallos por templates ausentes, slug `seleccion` en vez de `selecciones` y funciones públicas inexistentes; el E2E móvil adicional falla al no encontrar tarjetas de actualidad/selecciones.
- **GREEN:** templates de archivo/detalle, consultas solo `publish`, filtros de categoría/modalidad, tres elementos por página, navegación de páginas, vacíos y fallback seguro implementados; integración pasa 22 pruebas/115 aserciones y Playwright pasa 20/20.
- **TRIANGULATE:** Piso y Playa devuelven fixtures públicos separados; borrador y selección privada no aparecen; el filtro inexistente ofrece “Limpiar filtros” y el detalle conserva retorno al archivo.
- **REFACTOR:** el plugin mantiene CPT, taxonomías, rutas y fixtures persistentes; el tema limita su responsabilidad a consulta/renderizado y responde con fallback si el tipo no está registrado.

## Tarea 3.3 — Tokens, responsive y WCAG

- **RED:** `FrontendTokensTest::test_responsive_design_tokens_are_declared_in_theme_json` termina con código 1 (22 pruebas, 1 fallo) porque `settings.custom.labm.spacing.small` era `null`.
- **GREEN:** tokens fluidos y radio declarados en `theme.json`, layouts `minmax(min(100%, ...))`, foco visible y `prefers-reduced-motion` implementados; integración pasa 22/115 y E2E/axe pasa 20/20 en 320/768/1024/1440.
- **TRIANGULATE:** las cuatro rutas públicas no presentan desborde horizontal con movimiento reducido; capturas reales `inicio-320.png` y `actualidad-1440.png` se inspeccionaron sin desbordes, contraste defectuoso ni foco truncado visibles.
- **REFACTOR:** se retiró el contenido predeterminado de WordPress de la portada y la paginación se compactó como lista flexible.

## Gates finales del Lote 3

- `scripts/gate.ps1 -IncludeBrowser`: 7/7 PASS (Compose, PHPUnit, integración, WPCS, PHPStan, Playwright y Lighthouse), código 0.
- Smokes HTTP, DB, fallos visibles, fixtures sin PDF y activación/fallback: PASS; contrato pnpm exclusivo: PASS.
- Lighthouse baseline: Performance 100, Accessibility 100, Best Practices 100 y SEO 91; se conserva la tolerancia documentada al `EPERM` de limpieza solo después de validar el reporte.
- Auditorías informativas aceptadas: `pnpm audit` código 1 con 6 avisos (3 altos, 1 moderado, 2 bajos); Composer audit código 1 con 2 avisos altos. No se hicieron upgrades mayores.
