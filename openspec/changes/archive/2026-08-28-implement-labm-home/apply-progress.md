# Progreso de APPLY

## Tarea 1.1 — Contrato editorial RED
- **RED:** test `tests/php/HomeContentTest.php` falla con: `5 fallos y 1 error; CPT y contratos ausentes`.
- **GREEN:** implementacion en `tests/php/HomeContentTest.php`; test pasa.
- **REFACTOR:** `6 pruebas y 36 aserciones en verde`.

## Tarea 1.2 — Tipos y metadatos
- **RED:** test `HomeContentTest::test_home_content_is_editorial_rest_enabled_and_not_publicly_queryable` falla con: `tipos nulos`.
- **GREEN:** implementacion en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; test pasa.
- **TRIANGULATE:** test `HomeContentTest::test_invalid_home_items_are_rejected_before_publish` cubre `datos y URL inválidos`.
- **REFACTOR:** `PHPCS limpio`.

## Tarea 1.3 — Capacidades y carga
- **RED:** test `HomeContentTest::test_only_editors_and_administrators_receive_home_capabilities` falla con: `capacidades ausentes`.
- **GREEN:** implementacion en `labm-core.php` y `class-labm-domain.php`; test pasa.
- **REFACTOR:** `versión de capacidades elevada a 5`.

## Tarea 1.4 — Fixtures idempotentes
- **RED:** test `HomeContentTest::test_home_fixtures_are_idempotent_and_marked_as_fictitious` falla con: `fixtures ausentes`.
- **GREEN:** implementacion en `class-labm-fixtures-command.php`; test pasa.
- **TRIANGULATE:** test `ClosingCoverageTest::test_fixture_command_is_idempotent_and_preserves_foreign_content` cubre `caché y contenido ajeno`.
- **REFACTOR:** `49 pruebas de integración en verde`.

## Tarea 2.1 — Consultas públicas
- **RED:** test `HomePresentationTest::test_consultas_de_portada_solo_publican_en_orden_editorial_y_con_limite` falla con: `helper inexistente`.
- **GREEN:** implementacion en `wp-content/themes/labm/functions.php`; test pasa.
- **REFACTOR:** `consulta acotada y reutilizable`.

## Tarea 2.2 — Renderizadores
- **RED:** test `HomePresentationTest::test_renderizadores_de_portada_tienen_fallback_y_salida_segura` falla con: `renderizador de slider ausente`.
- **GREEN:** implementacion en `wp-content/themes/labm/functions.php`; test pasa.
- **TRIANGULATE:** test `HomePresentationTest::test_renderizadores_de_portada_componen_contenido_publicado` cubre `contenido real y fallbacks`.
- **REFACTOR:** `metadatos alineados con labm-core`.

## Tarea 2.3 — Patrón de Inicio
- **RED:** test `HomePresentationTest::test_patron_compone_el_orden_aprobado_y_excluye_secciones_retiradas` falla con: `falta slider`.
- **GREEN:** implementacion en `patterns/inicio.php` y `templates/front-page.html`; test pasa.
- **REFACTOR:** `patrón renderizable cubierto`.

## Tarea 2.4 — Contrato legado
- **RED:** test `VerifyCorrectivesTest::test_navegacion_completa_portada_completa_y_seccion_opcional` falla con: `esperaba modalidades y contacto`.
- **GREEN:** implementacion en `functions.php` y `VerifyCorrectivesTest.php`; test pasa.
- **REFACTOR:** `Selecciones y su slug permanecen intactos`.

## Tarea 3.1 — Contratos E2E de Inicio
- **RED:** test `tests/e2e/home.spec.ts` falla con: `secciones y controles no disponibles`.
- **GREEN:** implementacion en `patterns/inicio.php`, `functions.php` y `assets/home.js`; test pasa.
- **REFACTOR:** `caso vacío sin controles cubierto`.

## Tarea 3.2 — Tokens visuales
- **RED:** test `FrontendTokensTest` falla con: `2 fallos; paleta y pausa ausentes`.
- **GREEN:** implementacion en `theme.json` y `style.css`; test pasa.
- **REFACTOR:** `2 pruebas y 13 aserciones en verde`.

## Tarea 3.3 — Encabezado y pie
- **RED:** test `home.spec.ts::inicio conserva el orden aprobado` falla con: `composición anterior`.
- **GREEN:** implementacion en `parts/header.html` y `parts/footer.html`; test pasa.
- **REFACTOR:** `navegación existente conservada`.

## Tarea 3.4 — Movimiento progresivo
- **RED:** test `home.spec.ts::slider y aliados ofrecen controles accesibles y pausa` falla con: `controles ausentes`.
- **GREEN:** implementacion en `assets/home.js` y `functions.php`; test pasa.
- **TRIANGULATE:** test `public-experience.spec.ts::3.3` cubre `movimiento reducido`.
- **REFACTOR:** `etiquetas de pausa y copia inerte unificadas`.

## Tarea 3.5 — CSS responsive
- **RED:** test `public-experience.spec.ts::3.3` falla con: `contraste 3.4:1`.
- **GREEN:** implementacion en `theme.json` y `style.css`; test pasa.
- **TRIANGULATE:** test `portada y Selecciones conservan contenido en los anchos objetivo` cubre `320, 768, 1024 y 1440`.
- **REFACTOR:** `enlaces globales usan negro accesible`.

## Tarea 3.6 — Semántica final
- **RED:** test `home.spec.ts::slider y aliados ofrecen controles accesibles y pausa` falla con: `duplicación visual sin contrato`.
- **GREEN:** implementacion en `functions.php` y `assets/home.js`; test pasa.
- **REFACTOR:** `copia visual aria-hidden e inert; controles vacíos omitidos`.

## Tarea 4.1 — PHPUnit focal e integración
- **RED:** test `phpunit.integration.xml.dist` falla con: `fixture ajeno dependiente de caché`.
- **GREEN:** implementacion en `class-labm-fixtures-command.php` y `ClosingCoverageTest.php`; test pasa.
- **REFACTOR:** `49 pruebas y 266 aserciones en verde`.

## Tarea 4.2 — Viewports y Selecciones
- **RED:** test `public-experience.spec.ts::3.3` falla con: `contraste en todos los proyectos responsive`.
- **GREEN:** implementacion en `tests/e2e/public-experience.spec.ts`, `theme.json` y `style.css`; test pasa.
- **TRIANGULATE:** test `portada y Selecciones conservan contenido en los anchos objetivo` cubre `rutas Piso y Playa`.
- **REFACTOR:** `matriz de anchos consolidada`.

## Tarea 4.3 — Playwright y axe
- **RED:** test `tests/e2e/public-experience.spec.ts` falla con: `4 violaciones de contraste`.
- **GREEN:** implementacion en `theme.json`; test pasa.
- **REFACTOR:** `44 pruebas en 4 viewports en verde`.

## Tarea 4.4 — Estática PHP
- **RED:** test `composer lint` falla con: `finales de línea y documentación WPCS`.
- **GREEN:** implementacion en archivos PHP modificados; test pasa.
- **TRIANGULATE:** test `composer analyse -- --no-progress` cubre `análisis estático`.
- **REFACTOR:** `WPCS y PHPStan sin errores`.

## Tarea 5.1 — Gate completo
- **RED:** test `scripts/gate.ps1` falla con: `cobertura 74.87% y política de ejecución`.
- **GREEN:** implementacion en `HomePresentationTest.php`, `phpunit.integration.xml.dist`, `gate.ps1` y `coverage.ps1`; test pasa.
- **REFACTOR:** `cobertura 85.48%; seis componentes del gate PASS`.

## Tarea 5.2 — Revisión de alcance
- **RED:** test `VerifyCorrectivesTest` falla con: `contrato legado del Home`.
- **GREEN:** implementacion revisada contra `specs/` y `design.md`; test pasa.
- **TRIANGULATE:** test `public-experience.spec.ts` cubre `conservación de Piso y Playa`.
- **REFACTOR:** `sin eliminación de CPT, taxonomías, datos ni rutas`.

## Tarea 6.1 — Persistencia al cambiar de tema
- **RED:** test `HomeEditorialFlowsTest::test_slides_y_aliados_persisten_al_cambiar_de_tema` faltaba y el escenario figuraba `UNTESTED`.
- **GREEN:** implementación en `tests/php/HomeEditorialFlowsTest.php`; conserva entradas, estados, metadatos y capacidades tras `switch_theme`.
- **REFACTOR:** usuario, tema y contenido se restauran de forma aislada.

## Tarea 6.2 — Fallback sin registros de labm-core
- **RED:** test `HomePresentationTest::test_portada_degrada_sin_registros_de_labm_core` no existía y el escenario figuraba `PARTIAL`.
- **GREEN:** implementación en `tests/php/HomePresentationTest.php`; la portada mantiene presentación y omite slider/aliados sin fatal.
- **REFACTOR:** los tipos se vuelven a registrar en `finally`.

## Tarea 6.3 — Operaciones editoriales autorizadas y denegadas
- **RED:** test `HomeEditorialFlowsTest::test_editor_realiza_flujo_editorial_completo_por_rest` falla con: `El editor carece de edit_labm_slides` al reutilizar usuarios persistentes.
- **GREEN:** implementación en `tests/php/HomeEditorialFlowsTest.php`; usuarios aislados ejercen crear, publicar, editar y eliminar por REST, y el suscriptor recibe 401/403 sin mutación.
- **TRIANGULATE:** test `HomeEditorialFlowsTest::test_suscriptor_no_puede_mutar_contenido_por_rest` cubre el rechazo completo.
- **REFACTOR:** usuarios temporales se eliminan al terminar.

## Tarea 6.4 — Valores límite y entradas manipuladas
- **RED:** test `HomeEditorialFlowsTest::test_limites_y_datos_no_publicos_no_se_exponen` faltaba y los escenarios figuraban `PARTIAL`.
- **GREEN:** implementación en `tests/php/HomeEditorialFlowsTest.php`; texto largo, medio ausente, borrador, privado y URL manipulada tienen resultado seguro.
- **REFACTOR:** 10 pruebas focales y 76 aserciones en verde.

## Tarea 6.5 — E2E con fixtures publicados
- **RED:** test `home.spec.ts::slider y aliados ofrecen controles accesibles y pausa` falla en cuatro viewports por locator mutable; axe detecta enlaces enfocables bajo `aria-hidden`.
- **GREEN:** implementación en `tests/e2e/home.spec.ts`, `functions.php` y `assets/home.js`; se ejercen slider, indicadores, pausa, movimiento reducido y aliados.
- **TRIANGULATE:** axe cubre que la copia visual sea inerte y que sus enlaces no sean enfocables.
- **REFACTOR:** locator estable y defensa HTML/JavaScript para la copia visual.

## Tarea 6.6 — Gates y remapeo
- **RED:** test `scripts/browser-gate.ps1 -Task playwright` falla con: `12 fallos; aria-hidden-focus y locator de pausa mutable`.
- **GREEN:** implementación validada por `scripts/gate.ps1` con seis componentes PASS y `scripts/browser-gate.ps1` con 48/48 pruebas PASS.
- **TRIANGULATE:** matriz de `verify-report.md` remapeada a 24 escenarios con evidencia correctiva.
- **REFACTOR:** APPLY queda sin tareas pendientes y recomienda VERIFY.
