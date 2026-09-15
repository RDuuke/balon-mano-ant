## Tarea 1.1 — Contrato editorial

- **RED:** test `PublicExperienceTest.php::test_documents_page_renders_editable_editorial_banner` falló al no existir el renderizador.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.
- **TRIANGULATE:** `test_documents_banner_omits_unpublishable_content_and_escapes_text` cubre borrador, contenido alternativo, marcado e incompletitud.

## Tarea 1.2 — Ejecución RED

- **RED:** la prueba focal se ejecutó antes de la implementación y detectó la función inexistente.
- **GREEN:** la ejecución focal posterior confirma el contrato en verde.

## Tarea 2.1 — Renderizador editable

- **RED:** `PublicExperienceTest.php::test_documents_page_renders_editable_editorial_banner` exigió la función y falló.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; test pasa.

## Tarea 2.2 — Patrón Documentos

- **RED:** `PublicExperienceTest.php::test_documents_pattern_and_template_compose_the_editorial_banner` falló por patrón ausente.
- **GREEN:** implementación en `wp-content/themes/labm/patterns/documentos.php`; test pasa.

## Tarea 2.3 — Plantilla Documentos

- **RED:** `PublicExperienceTest.php::test_documents_pattern_and_template_compose_the_editorial_banner` falló por plantilla ausente.
- **GREEN:** implementación en `wp-content/themes/labm/templates/page-documentos.html`; test pasa.

## Tarea 2.4 — Presentación visual

- **RED:** `PublicExperienceTest.php::test_documents_pattern_and_template_compose_the_editorial_banner` falló por reglas CSS ausentes.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; test pasa.

## Tarea 3.1 — Composición aislada

- **RED:** el contrato de composición se añadió antes de patrón, plantilla y estilos.
- **GREEN:** `PublicExperienceTest.php::test_documents_pattern_and_template_compose_the_editorial_banner` pasa y confirma que Nosotros no usa el patrón.

## Tarea 3.2 — Prueba GREEN

- **RED:** los contratos nuevos fallaron antes de las implementaciones requeridas.
- **GREEN:** la prueba focal finalizó con 4 pruebas y 28 aserciones correctas.
- **REFACTOR:** se restauró el docblock de `labm_theme_render_about_purpose()` detectado por PHPCS.

## Tarea 4.1 — Calidad focal

- **RED:** PHPCS detectó el docblock omitido de `labm_theme_render_about_purpose()`.
- **GREEN:** se restauró el docblock; PHPCS y PHPStan finalizan correctamente.

## Tarea 4.2 — Higiene del cambio

- **RED:** se comprobó el requisito de LF antes del cierre.
- **GREEN:** la comprobación de LF y revisión de diff no detectaron cambios ajenos dentro del alcance.

## Ajuste visual — Hero de Documentos

- **RED:** `PublicExperienceTest.php::test_documents_pattern_and_template_compose_the_editorial_banner` falló al exigir una altura de 357 px y un contenedor de ancho completo alineado al margen editorial.
- **GREEN:** `wp-content/themes/labm/style.css` elimina el padding global del hero, fija el alto y aplica el relleno lateral propio; la prueba focal pasa.
