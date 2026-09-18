# Design: Experiencia administrativa para documentos PDF

## Enfoque técnico

Incorporar un módulo administrativo en `labm-core` que exponga una sola validación PHP del estado efectivo del Documento y dos adaptadores de interfaz: panel lateral del editor de bloques y metabox para editor clásico. Ambos usan Media Library, los metadatos existentes `labm_documento_pdf_id` y `labm_documento_fecha`, y la taxonomía `labm_documento_categoria`. El frontend no cambia.

## Decisiones de arquitectura

### Decisión: interfaz según editor

| Opción | Tradeoff | Decisión |
|---|---|---|
| Solo metabox | Menos código, peor integración en bloques | Descartada |
| Panel de bloques + metabox clásico | Dos adaptadores, experiencia nativa | Elegida |

`admin-documento.js`, sin compilación, usará APIs públicas expuestas por WordPress (`wp.plugins`, `wp.editPost`, `wp.components`, `wp.data`, `wp.media`). PHP encolará dependencias, configuración y traducciones solo para `labm_documento`. El clásico renderizará campos, nonce y botones equivalentes; el adjunto nunca se elimina al retirarlo.

### Decisión: validación autoritativa y atómica

| Opción | Tradeoff | Decisión |
|---|---|---|
| Validar solo en JavaScript | Se puede omitir | Descartada |
| Contrato PHP compartido | Más integración, misma regla para todos los canales | Elegida |

Un validador puro construirá el estado efectivo combinando valores persistidos y enviados. Comprobará título, fecha calendario, tipo único y adjunto: existencia, permiso `edit_post`, MIME declarado, `wp_check_filetype_and_ext`, legibilidad, firma `%PDF-` y tamaño `min(30 * MB_IN_BYTES, wp_max_upload_size())`. `rest_pre_insert_labm_documento` devolverá `WP_Error` antes de persistir. El clásico usa el filtro real `wp_insert_post_empty_content`: valida solo POST documental con contexto de formulario, capacidad y nonce; devuelve true al rechazar antes de escribir. WordPress retorna `empty_content`; el error específico por campo y las entradas se conservan en el estado fallido para reabrir la edición. `wp_after_insert_post` persiste los metadatos del estado válido. El hook propuesto `pre_wp_insert_post` no existe en el runtime y se reemplaza por esta integración soportada. Las llamadas programáticas sin contexto clásico no activan el guard. Ningún rechazo aplica cambios parciales.

### Decisión: tipo único gobernado por capacidades

| Opción | Tradeoff | Decisión |
|---|---|---|
| Campo de texto | Duplica vocabulario | Descartada |
| Taxonomía existente restringida a uno | Conserva compatibilidad | Elegida |

La taxonomía tendrá UI propia de selección única y capacidades separadas: administradores gestionan términos; editores asignan existentes. Una versión de esquema sembrará idempotentemente Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro. Nuevos documentos sin selección reciben Documento general al guardar; históricos sin tipo solo lo muestran como fallback hasta un guardado válido.

### Decisión: compatibilidad histórica

| Opción | Tradeoff | Decisión |
|---|---|---|
| Migración masiva | Riesgo operativo | Descartada |
| Normalización al editar | Históricos inválidos requieren reparación | Elegida |

Se aceptan IDs numéricos almacenados como texto. El despliegue no despublica ni reescribe documentos. Un histórico con PDF inválido conserva su versión; cualquier actualización debe repararlo.

## Flujo de datos

```text
UI bloques / metabox -> selección wp.media -> borrador local
        -> REST / formulario clásico -> estado efectivo -> validador PHP
        -> error por campo (sin cambios) | persistencia meta + tipo único
```

El panel muestra nombre obtenido del adjunto, tamaño con `size_format`, estado textual y límite efectivo. Los avisos usan componentes/notices con anuncio accesible y foco al primer campo inválido; no dependen del color.

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/labm-core.php` | Modificar | Cargar módulo administrativo. |
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modificar | Capacidades, taxonomía, metadatos y términos iniciales. |
| `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` | Crear | UI, configuración, validación y hooks REST/clásico. |
| `wp-content/plugins/labm-core/assets/js/admin-documento.js` | Crear | Panel, Media Library y feedback accesible. |
| `tests/php/DocumentContactTest.php` | Modificar | Contrato, permisos, atomicidad e históricos. |
| `tests/e2e/document-admin.spec.ts` | Crear | Flujo administrativo, teclado y WCAG. |

## Interfaces / contratos

- Configuración JS: `effectiveMaxBytes`, `effectiveMaxLabel`, `generalTermId`, `restNonce`, textos localizados.
- Metadato PDF: entero REST (`integer`, mínimo 0), persistencia compatible con texto histórico.
- Fecha: cadena vacía o `Y-m-d` real.
- Clasificación: cero o un ID de término existente y asignable; múltiples/desconocidos producen error.
- El cliente anticipa errores, pero nunca reemplaza la decisión PHP.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| PHPUnit | firma/MIME/tamaño, fecha, tipo, permisos, REST y clásico atómicos, fallback histórico | Casos RED/GREEN con adjuntos temporales y usuarios administrador/editor/no autorizado |
| WPCS/PHPStan | saneamiento, hooks y contratos | Gates del proyecto |
| Playwright | seleccionar/subir/reemplazar/retirar, mensajes, persistencia | Bloques y fallback clásico |
| Accesibilidad | teclado, foco, nombres, estados y anuncios | Playwright + axe; comprobación manual focal |

## Migración / despliegue

Incrementar la versión de capacidades/vocabulario y sembrar términos idempotentemente. No migrar posts ni borrar adjuntos. Rollback: retirar módulo/asset y restaurar registro anterior; conservar metadatos, asociaciones y términos para evitar pérdida.

## Preguntas abiertas

- Ninguna bloqueante.
