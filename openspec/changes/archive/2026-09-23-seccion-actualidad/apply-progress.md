# Progreso de aplicación: Actualidad

## Tarea 1.1 — Contrato editorial PHP

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` falla con: `name="texto"` ausente.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.

## Tarea 2.1 — Consulta pública de actualidad

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` falla con: `name="texto"` ausente en el contrato de consulta y listado.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.

## Tarea 2.2 — Marcado editorial de actualidad

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` falla con: `name="texto"` ausente en el renderer.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.

## Tarea 2.3 — Medio, metadata y paginación

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` falla con: `name="texto"` ausente antes de poder validar fallback y parámetros de paginación.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.

## Tarea 2.4 — Validación focal PHP

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` falla con: `texto=Contrato+actualidad` ausente en el enlace de paginación durante el ajuste GREEN.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa con 1 prueba y 22 aserciones.

## Tarea 1.2 — Regiones e interacciones de actualidad

- **RED:** test `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla con: `img.labm-home-news__featured-image intercepts pointer events` sobre el enlace de la destacada.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; el caso pasa en los proyectos `mobile-320`, `tablet-768`, `desktop-1024` y `wide-1440`.

## Tarea 3.1 — Hero editorial

- **RED:** test `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` confirma que el contrato requiere el hero `actualidad-hero` y su encabezado de nivel 1.
- **GREEN:** implementación en `wp-content/themes/labm/templates/archive-labm_actualidad.html`; el caso pasa en los cuatro viewports objetivo.

## Tarea 3.2 — Estilos aislados de actualidad

- **RED:** test `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla al no poder activar el enlace del titular: la imagen destacada intercepta eventos de puntero.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; los selectores cubren la imagen como hijo directo y como contenido envuelto, y el caso pasa.

## Tarea 3.3 — Retícula fluida

- **RED:** test `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` exige ausencia de desborde en 320, 768 y 1440 px.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; el caso pasa en 320/768/1024/1440 px.

## Tarea 3.4 — Validación focal Playwright

- **RED:** test `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla con la imagen destacada interceptando eventos de puntero.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; `pnpm run test:e2e -- --timeout=20000 --grep "1.2 actualidad reproduce las regiones Pencil"` pasa 4 de 4 pruebas.

## Corrección visual acotada — Composición Pencil Nrclx

- **RED:** `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla en `wide-1440`: los controles difieren 68 px en el eje Y.
- **GREEN:** el renderer agrupa ambos controles, el hero tiene contenedor interior alineado y la destacada usa un wrapper de medio fijo. `wide-1440` pasa con hero de 340 px, filtros de 420×52 y 260×52 con 16 px de separación, y medio destacado de 670×440 px.
- **PENDIENTE:** la ejecución conjunta mobile/tablet no inició por `spawn EPERM`; el propio caso aprobado mantiene verificaciones de desborde para 320, 768 y 1440 px.

## Corrección visual acotada — Continuidad hero y controles

- **RED:** `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla en `wide-1440`: la banda de filtros inicia 49.6 px después del hero; tras eliminar la separación residual, el botón permanece 16 px desalineado en el eje Y.
- **GREEN:** `wp-content/themes/labm/style.css` oculta el salto de línea que Gutenberg inserta tras el hero, elimina los márgenes externos del formulario y normaliza el contenedor del botón. La banda beige inicia junto al hero y buscador, categoría y botón miden 52 px y comparten la coordenada vertical.

## Corrección visual acotada — Paginación Pencil Zka5m/Nrclx

- **RED:** `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla en `wide-1440`: la paginación mide 44 px de alto, no la banda blanca de 100 px requerida.
- **GREEN:** `wp-content/themes/labm/style.css` convierte la paginación existente en una banda blanca de 100 px con controles de 44×44 px, separación de 10 px, página actual `#AECD25`, flechas visuales para anterior/siguiente y foco visible. La lógica y URLs de WordPress no cambian; `wide-1440` pasa.

## Corrección visual acotada — Centrado de flechas

- **RED:** `tests/e2e/public-experience.spec.ts::1.2 actualidad reproduce las regiones Pencil, filtros y navegación responsive` falla en `wide-1440`: el pseudo-elemento de las flechas se calcula como `display: block`, sin una caja de alineación propia.
- **GREEN:** `wp-content/themes/labm/style.css` hace que cada pseudo-elemento de anterior/siguiente sea una rejilla de ancho y alto completos con `place-items: center` y `line-height: 1`; el control mantiene 44×44 px, enlaces y lógica sin cambios. `wide-1440` pasa.

## Tarea 4.1 — Datos de paginación

- **Verificación:** `LABM_Fixtures_Command::home_news_fixtures()` ya define seis fixtures de `labm_actualidad` publicados, categorizados como `Noticias demo` e ilustrados con `labm_demo_image` local. Superan los cinco elementos necesarios para una segunda página; no se crearon duplicados.

## Tarea 4.2 — Refactor del renderer editorial

- **RED:** test `tests/php/PublicExperienceTest.php::test_actualidad_article_content_renders_the_editorial_fields_once` falla con: `Call to undefined function labm_theme_actualidad_article_content()`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; el test pasa con 1 prueba y 3 aserciones.
- **REFACTOR:** `labm_theme_actualidad_article_content()` centraliza metadata, título, enlace y extracto usados por destacada y tarjetas, sin cambiar selectores, URLs ni contratos públicos.
- **Validación focal:** Playwright pasa 4 de 4 proyectos (`mobile-320`, `tablet-768`, `desktop-1024`, `wide-1440`). `PublicExperienceTest.php` ejecuta 17 pruebas correctas de 18; la única falla restante pertenece al patrón de Documentos y es ajena a Actualidad.

## Tarea 4.3 — Calidad local

- **Formato y análisis:** PHPCBF corrigió 15 infracciones en `functions.php`; PHPStan termina sin errores. PHPCS conserva errores históricos de documentación y advertencias en `PublicExperienceTest.php`, sin infracciones en `functions.php`.
- **LF:** se verificaron los archivos del cambio; todos usan LF y no fue necesario convertir CRLF.
