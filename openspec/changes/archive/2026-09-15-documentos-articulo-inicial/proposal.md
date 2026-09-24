# Proposal: Artículo inicial de Documentos

## Intent

Incorporar el encabezado editorial de la página Documentos conforme al diseño aprobado y permitir que su contenido se gestione desde WordPress.

## Scope

### In Scope

- Crear una plantilla y patrón propios para la ruta Documentos.
- Renderizar un artículo publicado y editable como encabezado, con ceja, título y resumen.
- Aplicar estilos responsivos, accesibles y fieles al bloque negro del diseño.
- Cubrir el contrato con pruebas PHP focales.

### Out of Scope

- Catálogo, filtros, descargas o administración de documentos.
- Cambios al artículo editorial de Nosotros.

## Approach

El tema consultará un artículo publicado con un slug reservado para el encabezado de Documentos. El título y el extracto o contenido se mostrarán saneados; la ceja usará el texto editorial definido. Un patrón y una plantilla FSE asociarán el componente con la página, y CSS reutilizable definirá su presentación.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| wp-content/themes/labm/functions.php | Modified | Renderizador seguro del encabezado editable. |
| wp-content/themes/labm/patterns/documentos.php | New | Patrón que compone el artículo. |
| wp-content/themes/labm/templates/page-documentos.html | New | Plantilla de la página. |
| wp-content/themes/labm/style.css | Modified | Estilos del encabezado. |
| tests/php/PublicExperienceTest.php | Modified | Contrato del componente y de su degradación segura. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Falta el artículo editorial | Media | No renderizar contenido vacío ni privado. |
| Texto largo altera el diseño | Baja | Limitar ancho y usar tipografía fluida. |

## Rollback Plan

Restaurar `functions.php`, `style.css` y `tests/php/PublicExperienceTest.php`; eliminar el patrón y la plantilla nuevos. No hay migraciones ni banderas que revertir.

## Dependencies

- WordPress 6.8+ y el tema LABM activos.

## Success Criteria

- [ ] La ruta Documentos compone el encabezado desde un artículo publicado editable.
- [ ] Se muestran la ceja, título y resumen aprobados en la semilla editorial.
- [ ] Contenido no publicado o incompleto no se expone.
- [ ] Prueba PHP focal, análisis y verificación LF finalizan correctamente.
