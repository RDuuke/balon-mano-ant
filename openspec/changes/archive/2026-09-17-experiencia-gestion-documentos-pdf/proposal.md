# Proposal: Experiencia administrativa para documentos PDF

## Intent

Eliminar la copia de IDs o URLs. Personas con poca experiencia deben asociar un PDF, fecha y tipo desde controles claros de `wp-admin`.

## Scope

### In Scope

- Panel para seleccionar/subir, reemplazar o quitar PDF mediante Media Library, sin exponer IDs; muestra nombre, peso y estado.
- Límite efectivo de 30 MB o el menor permitido por WordPress.
- Título y PDF válido obligatorios, con bloqueo accesible en cliente y validación autoritativa en servidor.
- «Fecha del documento» opcional, elegida por el usuario y persistida como `Y-m-d`.
- Un único tipo sobre `labm_documento_categoria`: Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro; administradores gestionan y editores seleccionan.
- Compatibilidad REST/editor clásico, permisos, nonces, sanitización, registros existentes y fallback «Documento general»; pruebas correspondientes.

### Out of Scope

- Frontend, catálogo público, Pencil, paginación, iconos, estilos o render de metadatos.

## Approach

Reutilizar metadatos y taxonomía. Incorporar UI nativa y centralizar en PHP la validación. El cliente anticipará errores; REST y guardado clásico aplicarán el contrato. Sin despublicación ni migración masiva: los registros históricos se normalizarán al editarse.

## Affected Areas

| Área | Impacto | Descripción |
|------|---------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modificado | Esquema y taxonomía. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificado | Validación/guardado. |
| `wp-content/plugins/labm-core/assets/js/admin-documento.js` | Nuevo | Panel. |
| `tests/php/DocumentContactTest.php` | Modificado | PHP/REST. |
| `tests/e2e/document-admin.spec.ts` | Nuevo | UX accesible. |

## Risks

| Riesgo | Probabilidad | Mitigación |
|--------|--------------|------------|
| Divergencia cliente/servidor | Media | PHP autoritativo y pruebas contractuales. |
| Histórico inválido bloquea edición | Media | Mensaje reparable, sin despublicación. |
| APIs administrativas cambian | Baja | Usar APIs públicas compatibles con WordPress 6.8+. |

## Rollback Plan

Restaurar ambos PHP y retirar archivos nuevos. No hay tablas ni migraciones; datos previos permanecen intactos. Conservar términos sembrados evita perder asociaciones.

## Dependencies

- WordPress 6.8+, Media Library, REST y editor clásico; PHPUnit, WPCS/PHPStan y Playwright.

## Success Criteria

- [ ] Un editor completa el flujo sin conocer IDs ni URLs.
- [ ] Publicar falla accesiblemente ante título/PDF/fecha inválidos sin mutación indebida.
- [ ] Fecha, tipo, nombre, peso y estado se muestran y persisten según contrato.
- [ ] Documentos históricos e importador conservan compatibilidad.
- [ ] Pruebas PHP/REST, calidad, navegador, teclado, WCAG y LF pasan.
