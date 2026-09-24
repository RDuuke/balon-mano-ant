# Exploración: filtros del catálogo público de documentos

## Objetivo

Preparar el ajuste del catálogo público para presentar la metadata editorial disponible y permitir filtrar por categoría y/o año, conservando los filtros durante la paginación.

## Referencia visual

La referencia `design/exports/documentos-desktop.png` fija una dirección visual para la siguiente fase:

- Hero negro con eyebrow “Transparencia y consulta”, título “Documentos” y resumen.
- Barra de consulta con texto libre, selectores de Categoría, Año y orden.
- Resumen de resultados y tarjetas de PDF sobre fondo neutro, con etiqueta de categoría, título, línea secundaria de código/fecha/tamaño y acciones “Ver PDF” y “Descargar”.
- Estado vacío específico para filtros activos, con instrucción para limpiar la consulta.
- Paginación visible y foco/contraste conservados.
- Aunque la referencia es desktop, el diseño debe evaluarse en móvil: controles apilables, tarjetas con acciones que no desborden y paginación navegable por teclado.

El hero ya existe en el patrón actual. La barra de filtros, el resumen, el tratamiento visual de tarjetas y el estado vacío filtrado son gaps de implementación.

## Hallazgos

- La metadata ya se guarda en `labm_documento_fecha` con formato ISO `Y-m-d`.
- La clasificación pública está registrada como la taxonomía `labm_documento_categoria`; el guardado administrativo limita el documento a un solo término y aplica `documento-general` como respaldo.
- El catálogo público se compone de `templates/page-documentos.html`, el patrón `wp-content/themes/labm/patterns/documentos.php` y el helper `labm_core_render_document_catalog()` del plugin.
- `labm_core_document_catalog_query()` actualmente filtra únicamente documentos publicados con PDF asociado y ordena por fecha de publicación. Recibe filtros, pero los ignora.
- `labm_core_document_page_url()` recibe filtros, pero solo conserva `pagina`, por lo que cualquier formulario GET perdería categoría y año al paginar.
- Las tarjetas actuales muestran título y acciones PDF; no presentan fecha ni categoría. El CSS ya concentra estilos bajo `.labm-documents-*`, por lo que puede ampliarse sin alterar otras vistas.
- La referencia visual solicita código y tamaño. El código no existe como campo editorial público identificado en el modelo actual; el tamaño sí puede derivarse del adjunto validado mediante `filesize()`/metadatos de WordPress, evitando rutas internas. La siguiente fase debe decidir si el código se incorpora como nueva metadata o se omite hasta que exista una fuente editorial.
- La página pública no necesita JavaScript para esta interacción: un formulario GET y renderizado server-side mantienen enlaces compartibles, accesibilidad y funcionamiento progresivo.

## Alcance técnico probable

1. Ampliar la consulta del plugin con filtros normalizados de categoría y año, usando `tax_query` para la taxonomía y `meta_query` acotada al año de `labm_documento_fecha`, además de mantener el orden por fecha editorial descendente con una alternativa estable para fechas ausentes.
2. Exponer las opciones de categoría y los años disponibles de forma segura, y renderizar un formulario accesible con botón de aplicar y acción para limpiar filtros.
3. Mostrar en cada resultado la categoría y la fecha editorial cuando existan, sin exponer claves internas ni romper documentos antiguos sin metadata.
4. Hacer que las URLs de paginación conserven todos los filtros activos y que el estado vacío ofrezca una acción clara para limpiar la consulta.
5. Ajustar estilos del catálogo y las pruebas focales existentes; las pruebas actuales contienen aserciones que documentan deliberadamente el comportamiento simple y deberán evolucionar con la especificación nueva.

## Opciones consideradas

- **GET server-side (recomendada):** encaja con el renderizado PHP actual, no añade dependencia de JavaScript y permite indexación/enlaces compartibles.
- **Filtrado REST/AJAX:** ofrece cambios sin recarga, pero añade estado cliente, manejo de accesibilidad y más superficie de pruebas para una interacción que ya puede resolverse con la consulta pública.
- **Filtrado solo en plantilla:** evitaría tocar el helper, pero no permitiría que la consulta y la paginación compartieran una fuente de verdad y sería más difícil validar el total de páginas.

## Riesgos y decisiones pendientes

- Debe definirse si el año se deriva exclusivamente de `labm_documento_fecha` o si se ofrece también el año de publicación cuando la metadata editorial está vacía. La opción más coherente con el nuevo campo es usar solo la fecha editorial y tratar la ausencia como “sin año”.
- Las categorías deben obtenerse de `labm_documento_categoria` con términos disponibles para visitantes; no se deben reutilizar capacidades administrativas para ocultar términos públicos.
- La consulta por año sobre meta serializada como fecha requiere una estrategia verificable para no incluir valores inválidos.
- Debe resolverse el gap del código mostrado en la referencia: introducir una metadata pública exige contrato, edición administrativa, REST y validación adicionales; derivarlo desde el ID del documento expondría un identificador interno y no es una presentación estable.
- Hay pruebas heredadas que esperan que los filtros sean ignorados; deberán actualizarse en SPEC/TASKS antes de implementar.

## Archivos afectados previsibles

- `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`
- `wp-content/themes/labm/patterns/documentos.php` (solo si se separa la preparación de datos del helper)
- `wp-content/themes/labm/style.css`
- `tests/php/DocumentContactTest.php` y pruebas públicas E2E focales
- Artefactos OpenSpec del cambio.
