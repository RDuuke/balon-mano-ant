# Progreso de aplicación

## Tarea 1.1 — Taxonomía de grupos

- **RED:** test `DomainModelTest::test_taxonomies_are_extensible_and_rest_enabled` falla porque `labm_grupo_integrante` no está registrado.
- **GREEN:** registro jerárquico y REST en `class-labm-domain.php`; test pasa.

## Tarea 1.2 — Retratos demo

- **RED:** validación inicial no encontraba los cuatro retratos requeridos en el tema.
- **GREEN:** cuatro PNG ficticios RGB de 1024×1536 disponibles en `assets/images/quienes-demo/`.

## Tarea 2.1 — Fixtures de integrantes

- **RED:** test `FixturesDomainTest::test_about_team_fixtures_are_complete_categorized_and_idempotent` falla al no encontrar el primer integrante.
- **GREEN:** cuatro definiciones con cargo, grupo, orden y adjunto; test pasa tras dos cargas.

## Tarea 2.2 — Render público

- **RED:** tests `PublicExperienceTest::test_about_team_*` fallan porque `labm_theme_render_about_team()` no existe.
- **GREEN:** consulta pública, elegibilidad, filtro permitido y salida segura implementados; tests pasan.
- **TRIANGULATE:** prueba adicional omite borradores y tarjetas sin imagen.

## Tarea 3.1 — Integración visual

- **RED:** Playwright detecta contraste insuficiente de 3.13:1 y 3.4:1 en filtros y cargos.
- **GREEN:** inserción tras Misión/Visión, grilla y contraste corregido; Axe pasa.
- **REFACTOR:** estilos responsive consolidados con los breakpoints existentes.

## Tarea 4.1 — Navegador

- **RED:** prueba `Nosotros presenta integrantes editables, filtrables y responsive` falla por contraste Axe.
- **GREEN:** Playwright pasa 100 pruebas en cuatro proyectos de viewport.
- **TRIANGULATE:** se cubren filtro, texto largo, orden, columnas y desborde.

## Tarea 4.2 — Calidad PHP

- **RED:** PHPCS informa dos arrays asociativos sin formato multilínea.
- **GREEN:** PHPCS y PHPStan finalizan sin errores; PHPUnit focal pasa 33 tests y 286 aserciones.
- **REFACTOR:** arrays ajustados al estándar WordPress.

## Tarea 5.1 — Cierre técnico

- **RED:** auditoría detecta CRLF en `tests/php/DomainModelTest.php`.
- **GREEN:** archivo normalizado; auditoría del alcance y `git diff --check` quedan limpios.
