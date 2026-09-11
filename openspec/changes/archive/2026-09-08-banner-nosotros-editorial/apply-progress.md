# Progreso de implementación

## Tarea 1.1 — Contrato del fixture
- **RED:** test `FixturesDomainTest::test_about_banner_fixture_is_complete_and_idempotent` falla con: `null no es WP_Post`.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; test pasa.
- **TRIANGULATE:** `test_about_banner_fixture_preserves_foreign_content_conflict` cubre contenido ajeno.

## Tarea 1.2 — Contrato del renderizador
- **RED:** test `PublicExperienceTest::test_about_page_renders_published_editorial_banner` falla con: `función no definida`.
- **GREEN:** implementación en `functions.php`; test pasa.
- **TRIANGULATE:** `test_about_banner_omits_unpublishable_or_incomplete_article` cubre borrador e imagen ausente.

## Tarea 1.3 — Contrato de navegador
- **RED:** test `Nosotros inicia con un banner editorial estático y responsive` falla sin selector ni composición.
- **GREEN:** patrón, helper y CSS pasan en 320, 768, 1024 y 1440 px.

## Tarea 2.1 — Artículo demo
- **RED:** el test del fixture falla sin artículo.
- **GREEN:** definición idempotente añadida; test pasa.
- **REFACTOR:** definición extraída a `about_banner_fixture()` y cargada después de aliados.

## Tarea 2.2 — Carga WordPress
- **RED:** la consulta WP-CLI inicial no encontraba el artículo nuevo.
- **GREEN:** fixtures crean una entrada publicada única ID 2597 con adjunto destacado ID 2594.

## Tarea 3.1 — Renderizado seguro
- **RED:** `labm_theme_render_about_banner()` no existía.
- **GREEN:** helper consulta solo `publish`, limpia el marcador y escapa la salida.
- **TRIANGULATE:** borrador se omite e imagen ausente conserva texto sin medio roto.

## Tarea 3.2 — Integración del patrón
- **RED:** el patrón contenía bloques provisionales y ningún banner editorial.
- **GREEN:** `patterns/nosotros.php` invoca el helper seguro.

## Tarea 3.3 — Composición visual
- **RED:** Playwright no encontraba paneles contiguos/apilados.
- **GREEN:** CSS implementa panel negro 45 %, imagen 55 % y apilado móvil.
- **REFACTOR:** se anuló el padding genérico y la imagen cubre todo el panel.

## Tarea 4.1 — PHPUnit focal
- **RED:** ejecución inicial: 2 errores y 2 fallos.
- **GREEN:** 16 pruebas y 169 aserciones correctas.

## Tarea 4.2 — Calidad PHP
- **RED:** los chequeos estaban pendientes antes del cierre.
- **GREEN:** PHPCS sin hallazgos y PHPStan sin errores.

## Tarea 4.3 — Verificación visual
- **RED:** la primera captura reveló padding externo e imagen sin cubrir el panel.
- **GREEN:** Playwright focal: 8 pruebas correctas; capturas finales en `artifacts/visual/`.
- **REFACTOR:** altura e imagen se alinearon con la referencia.

## Tarea 5.1 — Higiene
- **RED:** la comprobación detectó CRLF en dos archivos modificados.
- **GREEN:** se normalizaron; `git diff --check` y revisión binaria correctos.

## Tarea 5.2 — Evidencia
- **RED:** no existía `apply-progress.md` para este cambio.
- **GREEN:** este documento registra ciclos y resultados de todas las tareas.

## Riesgos residuales

- Ninguno.

## Reintento de cierre VERIFY — Desborde global a 768 px

- **RED:** la suite Playwright completa fallaba en tres contratos de ausencia de desborde de Inicio a 768 px.
- **GREEN:** la cuadrícula de cuatro columnas del pie se activa desde 1024 px; la suite Playwright completa pasa con 88 de 88 pruebas.
- **REFACTOR:** se conservó la composición de escritorio sin ocultar el desborde ni relajar las aserciones.
