# Tareas: filtros y metadata del catálogo público de documentos

## Phase 1: Foundation

- [x] 1.1 `tests/php/DocumentContactTest.php`: añadir casos RED para texto, categoría, año, orden, fechas ausentes y paginación con filtros; dejar criterios observables.
- [x] 1.2 `tests/e2e/public-experience.spec.ts`: añadir casos RED para formulario, limpiar filtros, teclado y viewport estrecho; usar fixtures públicos aislados.

## Phase 2: Core Implementation

- [x] 2.1 `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`: normalizar `texto`, `categoria`, `anio`, `orden` y `pagina`; aplicar filtros combinados antes de paginar. GREEN de 1.1.
- [x] 2.2 Mismo archivo: preparar años/categorías públicas, orden estable y metadata segura de fecha/tamaño; omitir código y manejar PDF/fecha ausentes. GREEN de 1.1.

## Phase 3: Integration

- [x] 3.1 Mismo archivo y `wp-content/themes/labm/patterns/documentos.php`: renderizar formulario GET, resumen, tarjetas, estados vacío y enlaces paginados con filtros conservados.
- [x] 3.2 `wp-content/themes/labm/style.css`: aplicar estilos de controles, metadata, tarjetas, estado vacío y paginación para desktop/móvil, con foco visible.
- [x] 3.3 `tests/e2e/public-experience.spec.ts`: conectar selectores/criterios finales y dejar GREEN los escenarios de 1.2 sin AJAX/REST.

## Phase 4: Testing

- [x] 4.1 Ejecutar PHPUnit focal del catálogo; confirmar GREEN para combinaciones, sanitización, fechas, orden, salida segura y URLs.
- [x] 4.2 Ejecutar E2E focal de documentos en desktop y viewport estrecho, un worker y límite; confirmar filtros, limpiar, teclado y responsive.
- [x] 4.3 Ejecutar PHPCS/validación estática focal y `git diff --check`; corregir hallazgos sin ampliar alcance.

## Phase 5: Cleanup

- [x] 5.1 Verificar LF en todos los archivos modificados y actualizar comentarios/documentación solo si describen el catálogo anterior.
- [x] 5.2 Revisar criterios de aceptación: código documental permanece fuera, no hay endpoints nuevos ni migración, y registrar evidencia en el reporte de VERIFY.

## Dependencias y aceptación

1.1 y 1.2 preceden implementación; 2.1–2.2 preceden integración; 3.1–3.3 preceden pruebas; 4.1–4.3 preceden cleanup. Se acepta cuando los 10 escenarios de SPEC pasan, la paginación conserva parámetros, la salida no expone IDs internos y el responsive mantiene foco y acciones operables.
