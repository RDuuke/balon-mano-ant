# Progreso de APPLY - Selecciones y submenu

Zona horaria de ejecucion: America/Bogota.

## Tarea 1.1 - Inspeccion de limites

- **Estado:** completada.
- **Fecha:** 2026-10-06.
- **Evidencia:** se inspeccionaron `wp-content/themes/labm/`, `wp-content/plugins/labm-core/`, las pruebas publicas y los artefactos OpenSpec.
- **Limites confirmados:** el tema ya registra el shortcode de Selecciones y una consulta publica generica; el header aun usa `core/navigation`; existe plantilla de archivo y plantilla single, pero el alcance solo modifica el archivo; `labm-core` registra el CPT, la taxonomia `labm_modalidad` y los metadatos existentes; no se encontraron migraciones ni overrides persistidos del tema que deban modificarse.
- **Riesgo detectado:** la implementacion existente enlaza fichas individuales y usa terminos globales; las tareas 2.x y 3.x deben aislar Selecciones sin alterar Actualidad.

## Tarea 1.2 — Pruebas de consulta y filtros

- **Fecha/hora:** 2026-10-07 00:02:48 -05:00 (America/Bogota).
- **RED:** test `tests/php/PublicExperienceTest.php::test_selection_query_normalizes_filters_and_counts_only_public_posts` falló en la ejecución focal previa del estado parcial con `Failed asserting that actual size 3 matches expected size 1` para una página fuera de rango; la cobertura de términos se incorporó en `tests/php/PublicExperienceTest.php::test_selection_terms_exclude_global_club_terms_and_keep_base_modalities`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; comando `docker compose run --rm -T --no-deps --user 33:33 -e WP_TESTS_RUNTIME_ROOT=/var/www/html -v "${PWD}:/work:ro" -v labm_composer_vendor:/work/vendor -w /work wordpress php vendor/bin/phpunit -c phpunit.integration.xml.dist --filter "test_selection_query_normalizes_filters_and_counts_only_public_posts|test_selection_terms_exclude_global_club_terms_and_keep_base_modalities|test_selection_rows_render_editorial_content_without_individual_links"`; salida `OK (3 tests, 27 assertions)`, exit status 0.
- **TRIANGULATE:** `test_selection_terms_exclude_global_club_terms_and_keep_base_modalities` cubre términos base, término adicional de Selecciones y término usado solo por Clubes.

## Tarea 1.3 — Pruebas de filas editoriales

- **Fecha/hora:** 2026-10-07 00:02:48 -05:00 (America/Bogota).
- **RED:** test `tests/php/PublicExperienceTest.php::test_selection_rows_render_editorial_content_without_individual_links` falló tras añadir la aserción del metadato `Noticias`, con `Failed asserting ... contains "Noticias"` porque el renderer aún no mostraba la categoría existente.
- **GREEN:** se añadió el render seguro de términos `labm_categoria` en `wp-content/themes/labm/functions.php`; el mismo comando focal terminó con `OK (3 tests, 27 assertions)`, exit status 0.
- **TRIANGULATE:** el test cubre extracto editable, metadato `labm_modalidad_detalle`, placeholder neutro, ausencia de enlaces individuales y ausencia de CTA `VER PUBLICACION`.

## Tarea 2.1 — Normalización y consulta pública

- **Fecha/hora:** 2026-10-07 00:06:12 -05:00 (America/Bogota).
- **RED:** la cobertura focal del estado parcial `tests/php/PublicExperienceTest.php::test_selection_query_normalizes_filters_and_counts_only_public_posts` falló con `Failed asserting that actual size 3 matches expected size 1` al resolver una página fuera de rango; el caso también ejercita la modalidad escalar, el estado publish y el filtro taxonómico exclusivo.
- **GREEN:** se consolidó la normalización escalar, la lista de términos basada en Selecciones publicadas y la consulta `publish` de `labm_seleccion` en `wp-content/themes/labm/functions.php`; la ejecución focal del bloque terminó con `OK (3 tests, 29 assertions)`, exit status 0.
- **TRIANGULATE:** `test_selection_terms_exclude_global_club_terms_and_keep_base_modalities` cubre términos base, término adicional de Selecciones y exclusión del término usado solo por Clubes.

## Tarea 2.2 — Conteos y paginación

- **Fecha/hora:** 2026-10-07 00:06:12 -05:00 (America/Bogota).
- **RED:** el mismo fallo RED previo evidenció que la página superior devolvía tres filas en lugar de la última página disponible.
- **GREEN:** `labm_theme_selection_query()` calcula `found_posts`, `max_num_pages`, orden fecha/ID y corta en la página efectiva; el test también verifica enlaces que conservan `modalidad=Playa+TDD` y generan `pagina=2`. Comando focal del bloque: `docker compose run --rm -T --no-deps --user 33:33 -e WP_TESTS_RUNTIME_ROOT=/var/www/html -v "${PWD}:/work:ro" -v labm_composer_vendor:/work/vendor -w /work wordpress php vendor/bin/phpunit -c phpunit.integration.xml.dist --filter "test_selection_query_normalizes_filters_and_counts_only_public_posts|test_selection_terms_exclude_global_club_terms_and_keep_base_modalities|test_selection_rows_render_editorial_content_without_individual_links"`; exit status 0.
- **TRIANGULATE:** se comprobó que la paginación mantiene la modalidad y que la petición desbordada muestra una sola fila de la última página.

## Tarea 2.3 — Helpers editoriales seguros

- **Fecha/hora:** 2026-10-07 00:06:12 -05:00 (America/Bogota).
- **RED:** la prueba de filas del estado parcial falló al no encontrar el metadato editorial `Noticias`; el renderer no exponía todavía la categoría existente.
- **GREEN:** los helpers de resumen saneado, categoría/metadato, miniatura segura y placeholder neutro quedan cubiertos en `wp-content/themes/labm/functions.php`; la prueba focal terminó `OK (3 tests, 29 assertions)`, exit status 0.
- **TRIANGULATE:** `test_selection_rows_render_editorial_content_without_individual_links` verifica extracto, categoría, `labm_modalidad_detalle`, placeholder y ausencia de enlaces individuales/CTA.

## Tarea 3.1 — Plantilla y composición editorial

- **Fecha/hora:** 2026-10-07 00:11:14 -05:00 (America/Bogota).
- **RED:** `tests/php/PublicExperienceTest.php::test_selection_archive_uses_editorial_template_and_term_description` falló porque la introducción mostraba texto fijo y no la descripción editable del término.
- **GREEN:** `archive-labm_seleccion.html` conserva header/main/footer y shortcode; `wp-content/themes/labm/functions.php` usa la descripción saneada de `labm_modalidad` con fallback traducible. La suite focal final pasó `OK (7 tests, 56 assertions)`, exit status 0.
- **TRIANGULATE:** el test verifica ausencia de contenido `[DEMO LABM]` y mantiene la composición delegada al listado público.

## Tarea 3.2 — Header y estados de modalidad

- **Fecha/hora:** 2026-10-07 00:11:14 -05:00 (America/Bogota).
- **RED:** `tests/php/PublicExperienceTest.php::test_selection_header_navigation_orders_parent_and_marks_effective_child` falló porque Selecciones se renderizaba después de Contacto.
- **GREEN:** `wp-content/themes/labm/functions.php` ordena Inicio, Nosotros, Actualidad, Selecciones, Documentos y Contacto; conserva padre navegable, destinos Piso/Playa y `aria-current` solo para la modalidad efectiva. La suite focal final pasó `OK (7 tests, 56 assertions)`, exit status 0.
- **TRIANGULATE:** el test verifica que fuera del archivo ningún hijo de Selecciones declara página actual.

## Tarea 3.3 — Disclosure progresivo

- **Fecha/hora:** 2026-10-07 00:11:14 -05:00 (America/Bogota).
- **RED:** se ejecutó el test contractual `tests/php/PublicExperienceTest.php::test_selection_navigation_asset_declares_accessible_disclosure_contract` sobre el estado parcial; el contrato ya estaba satisfecho, por lo que no hubo fallo de producción en esta tarea y no se inventa una RED inexistente.
- **GREEN:** `wp-content/themes/labm/assets/navigation.js` conserva controles independientes, `aria-expanded`/`hidden`, Escape, foco, clic exterior, `focusout`, resize y ausencia de roles `menu`; el test pasa dentro de `OK (7 tests, 56 assertions)`, exit status 0.
- **TRIANGULATE:** se verifican simultáneamente el control del panel móvil y el disclosure de Selecciones.

## Tarea 3.4 — Estilos responsive y accesibles

- **Fecha/hora:** 2026-10-07 00:11:14 -05:00 (America/Bogota).
- **RED:** se ejecutó el test contractual `tests/php/PublicExperienceTest.php::test_selection_styles_declare_responsive_accessible_surfaces` sobre el estado parcial; los estilos focales ya estaban presentes, por lo que no hubo fallo de producción en esta tarea y no se inventa una RED inexistente.
- **GREEN:** `wp-content/themes/labm/style.css` conserva hero, filas, controles mínimos de 44 px, foco visible, breakpoint móvil y reduced motion; el test pasa dentro de `OK (7 tests, 56 assertions)`, exit status 0.
- **TRIANGULATE:** la prueba cubre superficies de listado y navegación sin ampliar el alcance a otras secciones.

## Tarea 4.1 — Pruebas PHP de paginación y estados

- **Fecha/hora:** 2026-10-07 00:16:45 -05:00 (America/Bogota).
- **RED:** el nuevo contrato focal de pruebas E2E falló inicialmente por no declarar los escenarios de vacío, touch y texto al 200%; la prueba de paginación quedó en verde sobre la implementación existente.
- **GREEN:** `test_selection_pagination_covers_pages_empty_and_invalid_requests` verifica páginas 1/2/3, página 99 resuelta a la última página, arrays normalizados, posts privados excluidos, estado vacío recuperable y ausencia de paginación en vacío. Suite focal PHP: `OK (9 tests, 85 assertions)`, exit status 0.
- **TRIANGULATE:** se combinan la prueba previa de consulta pública y la nueva prueba de estados para cubrir términos ausentes, restringidos y desborde.

## Tarea 4.2 — Cobertura E2E pública declarada

- **Fecha/hora:** 2026-10-07 00:16:45 -05:00 (America/Bogota).
- **GREEN:** `tests/e2e/public-experience.spec.ts` incorpora navegación de Selecciones, filas sin enlaces individuales, página fuera de rango, vacío condicional, teclado/Escape, touch y contexto sin JavaScript. El contrato estático `test_selection_e2e_contracts_cover_testing_block` pasó dentro de `OK (9 tests, 85 assertions)`.
- **EVIDENCIA:** Playwright no se ejecutó por el alcance explícito de este bloque; queda pendiente la ejecución browser real.

## Tarea 4.3 — Correctivos de header y navegación

- **Fecha/hora:** 2026-10-07 00:16:45 -05:00 (America/Bogota).
- **GREEN:** `tests/e2e/verify-correctives.spec.ts` comprueba jerarquía padre/hijos y `aria-current` en Inicio y Actualidad, sticky, foco visible, disclosure, Escape y retorno de foco en móvil. El contrato estático focal pasó dentro de `OK (9 tests, 85 assertions)`.
- **EVIDENCIA:** no se ejecutó Playwright; la validación browser permanece fuera de este bloque.

## Tarea 4.4 — Matriz responsive y accesibilidad

- **Fecha/hora:** 2026-10-07 00:16:45 -05:00 (America/Bogota).
- **RED:** la primera invocación con `pnpm` no pudo iniciar porque el ejecutable no estaba instalado; la invocación con `npx` quedó bloqueada por la política de scripts de PowerShell. No constituyen fallos del cambio.
- **GREEN:** se instalaron las dependencias fijadas por `pnpm@11.17.0` con `--frozen-lockfile` y se ejecutó `.\node_modules\.bin\playwright.cmd test tests/e2e/public-experience.spec.ts --grep "4\.4"`; resultado `4 passed` en 40.0 s, exit status 0. Los cuatro proyectos recorrieron 320, 768, 1024, 1200 y 1440 px, texto al 200 %, axe/contraste y ausencia de desborde.
- **TRIANGULATE:** la matriz se ejecutó en `mobile-320`, `tablet-768`, `desktop-1024` y `wide-1440`; no hubo fallos que clasificar como propios o ajenos al cambio.

## Tarea 5.1 — Calidad, regresión y formato

- **Fecha/hora:** 2026-10-07 00:34:39 -05:00 (America/Bogota).
- **RED:** `./scripts/gate.ps1 -SkipCoverage` falló inicialmente por hallazgos atribuibles al cambio: `VerifyCorrectivesTest.php:176` esperaba etiquetas inline en `header.html`; PHPCS reportó 15 errores/32 warnings en `functions.php`; PHPStan reportó 5 diagnósticos en `functions.php`.
- **GREEN:** se ajustó `tests/php/VerifyCorrectivesTest.php` para consumir `[labm_header_navigation]`, y se corrigieron PHPDoc, precedencia, superglobales y formato WPCS en `wp-content/themes/labm/functions.php` con `apply_patch`. El comando final `./scripts/gate.ps1 -SkipCoverage` terminó exit status 0: `compose-config PASS`, `composer-test PASS`, `wordpress-integration PASS` (185 tests), `composer-lint PASS` (PHPCS) y `composer-analyse PASS` (PHPStan).
- **TRIANGULATE:** regresión focal ejecutada con PHPUnit sobre las pruebas de Selecciones y navegación: `OK (10 tests, 97 assertions)`, exit status 0.
- **FORMATO:** LF verificado en todos los archivos modificados y `git diff --check` sin salida.

## Tarea 5.2 — Estado persistido y sincronización

- **Fecha/hora:** 2026-10-07 00:34:39 -05:00 (America/Bogota).
- **GREEN:** no aplica `scripts/content-sync.ps1`: APPLY modificó código, pruebas y artefactos OpenSpec, sin contenido WordPress, uploads, fixtures persistentes ni migraciones. Las pruebas PHP usan datos temporales y los eliminan en sus bloques `finally`.
- **EVIDENCIA:** no se modificaron `content-sync/canonical.zip` ni `content-sync/latest.json`; no se exportaron credenciales ni datos de usuarios.

## Tarea 5.3 — Revisión final y rollback

- **Fecha/hora:** 2026-10-07 00:34:39 -05:00 (America/Bogota).
- **GREEN:** `git cat-file -t ecab4ca` devolvió `commit`; `git show -s --format='%H %ad %s' ecab4ca` confirmó `ecab4cae066f74eb4c9c382b38773e69154a286d`. Se revisó `git diff --stat ecab4ca`/`git diff --name-status ecab4ca` contra el alcance declarado y se confirmó que no se ejecutó ningún rollback destructivo.
- **EVIDENCIA:** `git diff --check` terminó limpio; la fase persistida continúa en `APPLY` y no se avanzó a VERIFY ni ARCHIVE.
