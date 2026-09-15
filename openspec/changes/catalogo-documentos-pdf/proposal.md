# Proposal: Catálogo público de documentos PDF

## Intent

Completar la página Documentos con un catálogo editable desde WordPress que presente PDF publicados en una lista accesible y paginada conforme a las referencias aprobadas.

## Scope

### In Scope

- Gestionar documentos con título, adjunto PDF y metadatos internos opcionales para ordenación, trazabilidad y seguridad.
- Mostrar una lista de títulos con las acciones «Ver PDF» y «Descargar», estado vacío y paginación de 10 documentos por página.
- Abrir «Ver PDF» mediante la URL directa del adjunto en una pestaña nueva; el navegador o sistema operativo decide si lo visualiza o descarga como alternativa.
- Importar idempotentemente los PDF iniciales de `docs/legal-documents` desde una operación administrativa explícita.
- Validar archivo, MIME y permisos antes de publicar o exponer un PDF.

### Out of Scope

- Buscador, filtros, categorías, referencias, fechas, tamaños u otros metadatos visibles en la página pública.
- Modificar el encabezado, navegación o pie ya aprobados.
- Visor incrustado, búsqueda asíncrona, OCR, versionado o archivos no PDF.

## Approach

Se consolidará el catálogo sobre el CPT `labm_documento`: cada entrada publicada tendrá título y adjunto PDF; sus metadatos administrativos permanecerán disponibles si son necesarios, sin presentarse públicamente. El plugin proveerá el contrato, seguridad y enlaces; el tema únicamente la presentación aislada. La importación administrativa reproducible evitará duplicados.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modified | Consulta, seguridad, enlaces y vista simplificada. |
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modified | Contrato de publicación y metadatos administrativos. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modified | Importación inicial administrativa. |
| `wp-content/themes/labm/{patterns/documentos.php,style.css}` | Modified | Integración y estilos aislados de la lista. |
| `tests/php/{DocumentContactTest.php,PublicExperienceTest.php}` | Modified | Contratos TDD de lista, enlaces y paginación. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| El navegador no puede previsualizar un PDF | Media | «Ver PDF» abre el recurso directo en pestaña nueva y deja el fallback de descarga al navegador/SO; «Descargar» permanece disponible. |
| MIME falsificado o adjunto alterado | Media | Comprobar carga, cabecera y MIME WordPress antes de renderizar enlaces. |
| Cambio visual afecta otras listas | Baja | Selectores exclusivos de Documentos y pruebas de aislamiento. |

## Rollback Plan

Restaurar los archivos listados y eliminar solo entradas/adjuntos del importador mediante su marcador estable. No hay migración ni banderas globales.

## Dependencies

- WordPress 6.8+, `labm-core`, tema LABM y permisos de editor/administrador.
- Los PDFs disponibles en `docs/legal-documents` y títulos editoriales para la importación inicial.

## Success Criteria

- [ ] Un editor autorizado puede publicar un documento con título y PDF válido.
- [ ] La página muestra exclusivamente título, «Ver PDF», «Descargar», vacío y paginación sin alterar secciones ajenas.
- [ ] «Ver PDF» abre el adjunto seguro en nueva pestaña y «Descargar» inicia una descarga segura cuando el navegador lo permite.
- [ ] La lista nunca expone borradores ni adjuntos inválidos, y la importación es idempotente.
- [ ] Las pruebas, calidad y verificación LF pasan.
