## Tarea 1.1 — Contrato PHP de filtros del catálogo

- **RED:** test `DocumentContactTest::test_catalog_applies_text_category_and_year_filters` falla con: se esperaba un único documento filtrado y la consulta devolvía diez resultados.
- **GREEN:** implementación en `tests/php/DocumentContactTest.php` y `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; test pasa (`OK (1 test, 2 assertions)`).
- **TRIANGULATE:** el fixture se corrigió para declarar `labm_documento_pdf_id`, respetando el requisito de documentos publicables con PDF asociado.
- **REFACTOR:** se añadió normalización centralizada de filtros y valores de orden permitidos.

## Tarea 2.1 — Consulta server-side normalizada

- **RED:** cubierta por `DocumentContactTest::test_catalog_applies_text_category_and_year_filters` antes de escribir producción.
- **GREEN:** `labm_core_document_catalog_normalize_filters()` y filtros `s`, `tax_query`, `meta_query` de año, paginación y orden; test focal pasa.
- **REFACTOR:** se conservaron documentos con PDF asociado y se descartaron categorías/años/órdenes inválidos sin ampliar el alcance.

## Diagnóstico APPLY-2 — Test de metadata y URLs

- **Estado:** bloqueado; `DocumentContactTest::test_document_catalog_renders_public_metadata_and_preserves_filters` superó 60 segundos sin producir salida, incluso ejecutado de forma aislada con `max_execution_time=30`.
- **Hallazgo:** el test de consulta 1.1/2.1 termina en 0.08 s; el bloqueo aparece al crear/renderizar la tarjeta y construir la paginación. No se puede atribuir aún a una aserción concreta sin trazas.
- **Decisión:** no marcar 1.2/2.2 ni implementar renderizado hasta aislar el costo del runtime, fixtures publicados o bucle de paginación.

## Tarea 2.2 — Metadata pública y opciones de filtro

- **RED:** test `DocumentContactTest::test_document_catalog_renders_public_metadata_and_preserves_filters` bloqueaba cuando el render ignoraba filtros y recorría el catálogo completo.
- **GREEN:** implementación en `class-labm-documents-contact.php`; ejecución focal del usuario con `WP_TESTS_RUNTIME_ROOT=/var/www/html` pasa `OK (1 test, 11 assertions)`.
- **TRIANGULATE:** se cubren categoría, fecha, tamaño disponible, ausencia de código y URL con filtros.
- **REFACTOR:** opciones públicas de categorías/años y metadata ausente se omiten sin exponer IDs internos.

## Tarea 3.1 — Render público y paginación persistente

- **RED:** el mismo test no podía encontrar metadata ni parámetros en la URL porque el render descartaba filtros.
- **GREEN:** formulario GET, tarjetas, estado vacío, limpiar filtros y enlaces paginados reciben el conjunto normalizado; evidencia focal GREEN del usuario: `OK (1 test, 11 assertions)`.
- **REFACTOR:** el render conserva el hero/patrón existente y deja estilos responsive para la tarea 3.2.

## Tarea 3.2 — Estilos responsive del catálogo

- **RED:** pendiente de prueba visual previa; la tarea se implementó sobre clases HTML públicas ya existentes y las nuevas del catálogo.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`: controles, metadata, tarjetas, estado vacío y paginación con breakpoints de 48rem y 30rem, foco global visible.
- **REFACTOR:** acciones apilables en móvil y sin dependencia de hover.

## Tareas 1.2/3.3 — E2E público

- **Estado:** pendientes; el test `3.4 documentos ofrece filtros, estado vacío y composición responsive` quedó añadido en `tests/e2e/public-experience.spec.ts`, pero el contenedor E2E no contiene `/app/node_modules/@playwright/test/cli.js`.
- **Evidencia:** la prueba no alcanzó ejecución; no se declara GREEN ni se repite hasta disponer del runtime correcto.

## Tarea 4.1 — Validación PHP focal parcial

- **GREEN:** `DocumentContactTest::test_document_catalog_empty_filter_offers_clear_action` pasa con `OK (1 test, 3 assertions)` en menos de un segundo de PHPUnit; cubre estado vacío, limpieza y formulario.
- **Pendiente:** aún falta ejecutar el conjunto focal de combinaciones, sanitización, fechas, orden, salida segura y URLs antes de marcar 4.1 completa.

## APPLY-6 — Correcciones PHPCS de producción

- **Cambios:** `current_filters` sanea valores escalares después de `wp_unslash`; el resumen `_n()` incluye comentario de traductor.
- **Alcance:** solo `class-labm-documents-contact.php`; no se modificó el test con errores históricos de estilo.
- **Evidencia:** pendiente de rerun PHPCS/PHPStan por el usuario; PHPStan previo terminó por límite de memoria de 128M.

## APPLY-7 — Corrección estática focal

- **Cambios:** se eliminó la rama inalcanzable indicada por PHPStan usando conversión explícita a array; se añadió un ignore PHPCS puntual sobre la lectura dinámica de `$_GET`, documentando que el valor se desescapa, valida como escalar y sanea inmediatamente.
- **Alcance:** solo `class-labm-documents-contact.php`; no se alteran warnings baseline del test.
- **Evidencia:** pendiente de rerun PHPCS/PHPStan por el usuario.

## Tareas 4.1/4.3 — Evidencia de cierre

- **4.1 GREEN:** usuario confirmó PHPUnit focal PASS; evidencias registradas para los focales de metadata/URLs (`1 test, 11 assertions`) y estado vacío (`1 test, 3 assertions`), además del filtro combinado.
- **4.3 GREEN con warnings:** usuario confirmó PHPCS con `0 errors, 23 warnings` y PHPStan `No errors`; los warnings corresponden a alineación baseline, consultas tax/meta intencionales y heurística de GET público saneado. `git diff --check` y LF pasan.

## Mini-corrección VERIFY — Fecha editorial ausente

- **Cambio:** se añadió únicamente `DocumentContactTest::test_document_without_editorial_date_is_visible_without_year_and_excluded_by_year`.
- **Contrato:** el documento publicado con PDF y fecha ausente aparece sin filtro de año y queda excluido cuando se aplica `anio`.
- **Evidencia:** usuario ejecutó el único test permitido: `OK (1 test, 6 assertions)`.

## Diagnóstico E2E 3.4 — Estado de filtros en URL

- **Causa:** `patterns/documentos.php` invocaba el render con `array()`, por lo que el formulario recibía valores vacíos aunque `$_GET` contuviera `texto` y `orden`.
- **Corrección:** se añadió `labm_core_document_catalog_current_filters()` y el patrón ahora transfiere esos parámetros al render normalizado.
- **Evidencia:** pendiente de ejecución del comando E2E del usuario; no se ejecutaron pruebas en este diagnóstico.

## Tareas 1.2/3.3/4.2 — E2E público

- **GREEN:** el usuario ejecutó `3.4 documentos ofrece filtros, estado vacío y composición responsive` y confirmó PASS; cubre formulario, limpiar filtros, persistencia de Buscar, viewport 320 y foco.
- **Alcance:** no se añadió AJAX/REST; se conserva el flujo server-side.

## APPLY-VISUAL — Corrección de composición pública

- **Cambios:** se agruparon etiquetas y controles en campos independientes; el formulario usa una fila desktop de cuatro controles más acciones y columnas responsive; se evitó el salto vertical de `Año`; las tarjetas incorporan distintivo PDF y una estructura de cuerpo/acciones para categoría, título y metadata.
- **Alcance:** solo `class-labm-documents-contact.php` y `style.css`; se preservan filtros server-side, parámetros y metadata existente.
- **Evidencia:** revisión estática y `git diff --check`; no se ejecutaron pruebas por instrucción.
- **Pendiente:** validación visual/E2E focal por el usuario.

## APPLY-VISUAL-2 — Banda de filtros a ancho completo

- **Cambios:** la banda ahora usa ancho de viewport completo con sangrado controlado desde el contenedor padre; el formulario se limita al mismo ancho máximo del catálogo, usa columnas flexibles y reserva una columna mínima para acciones. El botón conserva su texto horizontal.
- **Alcance:** solo `style.css`; no cambia el HTML ni el comportamiento de filtros.
- **Evidencia:** revisión estática y verificación LF; no se ejecutaron pruebas.
- **Pendiente:** nueva captura visual del usuario.

## APPLY-VISUAL-3 — Estado vacío y jerarquía de acciones

- **Cambios:** el distintivo PDF usa fondo negro y texto verde; `Ver PDF` queda blanco con borde oscuro y `Descargar` conserva el verde de acento. El estado vacío ahora tiene icono contrastado, mensaje específico y acción visible para limpiar filtros cuando hay filtros activos.
- **Alcance:** `class-labm-documents-contact.php` y `style.css`; no cambia la consulta ni la lógica de filtrado.
- **Evidencia:** revisión estática y LF; no se ejecutaron pruebas.
- **Pendiente:** nueva captura visual del usuario.

## APPLY-VISUAL-4 — Corrección de cascada del contenedor

- **Diagnóstico:** el patrón se renderiza dentro de `main.wp-block-group.is-layout-constrained`; las reglas globales de bloques estaban limitando el `section` y colapsando el `form`, por eso la banda quedaba centrada y los controles medían pocos píxeles.
- **Cambios:** se añadió `alignfull` al bloque de filtros y selectores scoped de mayor prioridad para ancho de viewport, grid del formulario y campos; se añadieron overrides responsive equivalentes.
- **Alcance:** `class-labm-documents-contact.php` y `style.css`; no cambia la lógica server-side.
- **Evidencia:** revisión estática y LF; no se ejecutaron pruebas.
- **Pendiente:** hard-refresh y nueva captura visual del usuario.

## APPLY-FINAL-POLISH — Espaciado y PDF no disponible

- **Cambios:** se igualó el margen inferior del catálogo y del estado vacío antes del footer; documentos con PDF ausente o inválido muestran `PDF no disponible` con `role="status"` y no renderizan enlaces muertos.
- **Prueba focal añadida:** `test_document_catalog_renders_unavailable_pdf_state` cubre mensaje accesible y ausencia de enlace `Ver PDF`.
- **Alcance:** `class-labm-documents-contact.php`, `style.css` y `DocumentContactTest.php`.
- **Evidencia:** código aplicado y LF verificado; la prueba queda pendiente de ejecución por el usuario.

## APPLY-FINAL-POLISH-2 — Espacio de página y acciones explícitas

- **Cambios:** el `main` de Documentos recibe padding inferior scoped para garantizar separación antes del footer; las acciones `Ver PDF` y `Descargar` tienen reglas explícitas de color y fondo con prioridad dentro de la tarjeta.
- **Alcance:** `page-documentos.html` y `style.css`; sin cambios de filtros ni consultas.
- **Evidencia:** revisión estática y LF; no se ejecutaron pruebas.
- **Pendiente:** confirmar visualmente con hard-refresh.

## Tareas 5.1/5.2 — Cierre de alcance

- **GREEN:** todos los archivos modificados del alcance verificaron `LF=True`; `git diff --check` no reportó errores.
- **GREEN:** revisión confirma código documental fuera de alcance, sin endpoints nuevos ni migraciones.
