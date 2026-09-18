# Propuesta: filtros y metadata del catálogo público de documentos

## Intento

El catálogo ya guarda categoría y fecha editorial, pero las ignora: no permite localizar por texto, categoría o año, pierde filtros al paginar y no presenta la metadata. Se alineará con `design/exports/documentos-desktop.png`, conservando el hero y un flujo accesible en móvil.

## Alcance

### Incluido

- Formulario GET con texto, categoría, año y orden.
- Consulta server-side, paginación que conserva parámetros y estado vacío con limpieza.
- Tarjetas responsive con categoría, título, fecha editorial y tamaño PDF disponible, más Ver PDF y Descargar.
- Opciones derivadas de datos públicos, foco y contraste accesibles.
- Pruebas focales del helper, renderizado y experiencia pública.

### Excluido

- AJAX/REST, cambios al hero y cambios al modelo administrativo.
- Código documental: no existe metadata pública; no se inventará ni se expondrá un ID interno.
- Año de publicación alternativo: el filtro usará `labm_documento_fecha`; documentos sin fecha se omiten del filtro por año y conservan su presentación.

## Enfoque

Normalizar parámetros en `labm_core_document_catalog_query()`, aplicar taxonomía y meta de fecha, centralizar la URL paginada y ampliar el renderizado. El patrón seguirá componiendo el catálogo; el tema aportará estilos de controles, tarjetas y responsive. Toda metadata se escapará y los valores ausentes se omitirán.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificado | Filtros, metadata y paginación. |
| `wp-content/themes/labm/style.css` | Modificado | Controles, tarjetas, estados y responsive. |
| `tests/php/DocumentContactTest.php` y E2E público | Modificado | Contratos de consulta y presentación. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Fechas inválidas o ausentes | Media | Normalizar ISO y probar límites. |
| Código solicitado sin fuente | Alta | Mantenerlo fuera y registrar decisión. |
| Enlaces existentes afectados | Baja | Parámetros GET opcionales y pruebas. |

## Rollback

Revertir plugin, tema, pruebas y artefactos; restaurar consulta simple y paginación con `pagina`. No hay migraciones ni flags.

## Dependencias

Metadata, taxonomía, página pública y estilos existentes.

## Criterios de éxito

- [ ] Texto, categoría, año y orden combinan resultados correctamente.
- [ ] Paginación conserva filtros y limpiar restaura el catálogo.
- [ ] Tarjetas muestran metadata disponible sin datos internos y funcionan en móvil/teclado.
- [ ] Estado vacío filtrado es claro y accionable.
- [ ] Pruebas focales y validaciones de formato pasan.
