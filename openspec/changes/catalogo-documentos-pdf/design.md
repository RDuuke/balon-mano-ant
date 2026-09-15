# Design: Catálogo público de documentos PDF

## Technical Approach

Se completará el dominio existente `labm_documento` en `labm-core` y se integrará su renderizador en el patrón Documentos. El plugin será la fuente de datos, autorización, validación, consulta y enlaces; el tema solo tendrá clases `labm-documents-*` para la composición visual aislada. La primera carga será un comando administrativo explícito, nunca una importación automática en solicitudes públicas.

## Architecture Decisions

### Decision: Reutilizar el CPT documental existente

| Opción | Trade-off | Decisión |
|---|---|---|
| `labm_documento` y sus datos internos | Aprovecha capacidades, REST y consultas ya presentes | Elegida |
| Entradas convencionales | Pierde permisos y aislamiento de dominio | Descartada |
| Tabla propia | Aumenta migración y operación | Descartada |

Rationale: el CPT ya separa documentos del contenido editorial y permite mantener metadatos sin exponerlos.

### Decision: Publicar título y PDF; conservar metadatos internos opcionales

| Opción | Trade-off | Decisión |
|---|---|---|
| Título + `pdf_id`; metadatos internos opcionales | UI simple sin perder trazabilidad | Elegida |
| Exigir y visualizar todos los metadatos | Añade fricción editorial y elementos descartados | Descartada |

Rationale: el ID de adjunto permite validar MIME y URL al renderizar. Categoría, referencia o fechas podrán conservarse para gestión, ordenación o seguridad, pero no forman parte del contrato visual.

### Decision: Delegar la previsualización al agente de usuario

| Opción | Trade-off | Decisión |
|---|---|---|
| URL directa, `target="_blank"`, `rel="noopener"` | Depende de soporte del navegador, sin visor propio | Elegida |
| Visor incrustado | Mayor complejidad, privacidad y accesibilidad | Descartada |

Rationale: «Ver PDF» abre el adjunto en una pestaña independiente y el navegador/SO decide si lo muestra o lo descarga. `noopener` evita acceso de la pestaña nueva a la ventana de origen; `noreferrer` se añadirá solo si la política lo exige. «Descargar» usará atributo `download` cuando el origen y navegador lo permitan, respaldado por una respuesta segura de descarga de WordPress con `Content-Disposition: attachment` cuando sea necesaria.

### Decision: Importador WP-CLI idempotente con mapa declarado

| Opción | Trade-off | Decisión |
|---|---|---|
| Comando con clave de origen estable | Requiere ejecución administrativa y títulos | Elegida |
| Carga automática en activación | Riesgo de duplicados y acceso a archivos | Descartada |

Rationale: permite repetir, actualizar y revertir selectivamente los PDFs sin afectar material editorial ajeno.

## Data Flow

```text
Editor / importador
  → CPT + título + adjunto PDF + metadatos internos opcionales
  → validación de publicación y MIME
  → consulta pública paginada (10 por página)
  → patrón Documentos → título + Ver PDF / Descargar, vacío o paginador
```

## File Changes

| File | Action | Description |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modify | Validar título, PDF y metadatos internos opcionales. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modify | Consulta paginada, enlaces seguros y renderizado semántico simplificado. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modify | Comando de importación y marcador de origen. |
| `wp-content/themes/labm/patterns/documentos.php` | Modify | Insertar el catálogo debajo del encabezado. |
| `wp-content/themes/labm/style.css` | Modify | Estilos exclusivos de filas, vacío y paginación. |
| `tests/php/DocumentContactTest.php` | Modify | Pruebas de seguridad, consulta, enlaces e importación. |
| `tests/php/PublicExperienceTest.php` | Modify | Pruebas de composición, ausencia de filtros y aislamiento visual. |

## Interfaces / Contracts

- Registro: título y `labm_documento_pdf_id` (ID de adjunto) requeridos; metadatos como referencia, categoría o fecha son opcionales e internos.
- Consulta: acepta únicamente `pagina`; devuelve documentos publicados con PDF válido, en orden descendente de publicación y 10 por página.
- Vista: fila `<article>` con título y enlaces nombrados «Ver PDF» y «Descargar»; no contiene buscador, filtros ni metadatos visibles.
- Enlaces: «Ver PDF» usa URL validada, `target="_blank"` y `rel="noopener"`. «Descargar» solicita descarga usando `download` cuando aplica y un endpoint/encabezado `Content-Disposition: attachment` seguro como respaldo.
- Importación: cada fuente recibe una clave persistente; valida antes de insertar y solo modifica entradas con esa clave.

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| PHP unit/integration | publicación, PDF, consulta, vacío, paginación, enlaces e idempotencia | RED → GREEN → REFACTOR focal. |
| Browser/a11y | orden de foco, etiquetas, contraste, nueva pestaña y paginador | Playwright y auditoría WCAG 2.2 AA. |
| Calidad | WPCS, PHPStan, LF y diff | Puertas `quick_local` y `verify_pr`. |

## Migration / Rollout

No hay migración de base de datos. Tras desplegar, un administrador define o revisa los títulos y ejecuta el importador. Un registro previo se actualiza por la clave de origen; un fallo no borra ni publica elementos inválidos.

## Open Questions

- [ ] Confirmar los títulos editoriales de los PDFs iniciales si los nombres de archivo no son apropiados para mostrarse.
- [ ] Confirmar si la política de privacidad exige `noreferrer` además de `noopener` en «Ver PDF».
