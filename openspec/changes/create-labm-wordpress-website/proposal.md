# Proposal: Sitio WordPress de LABM

## Intent

Crear una base WordPress institucional reproducible. El remoto público `https://github.com/RDuuke/balon-mano-ant` está vacío; el workspace será la fuente inicial, sin commits ni push.

## Scope

### In Scope

- Docker Compose con WordPress y base de datos, configuración sin secretos y guía.
- Tema de bloques `labm` y plugin `labm-core` para dominio persistente.
- Fixtures ficticios etiquetados, sin datos personales reales ni extraídos de los PDF.
- Navegación, inicio, nosotros, actualidad, selecciones, documentos y contacto.
- Pruebas: smoke; unitarias/integración PHP-WordPress; consultas, permisos, PDF y contacto; estándares estáticos; E2E; accesibilidad/responsive; rendimiento.

### Out of Scope

- Despliegue, hosting, dominio, SMTP, analítica y contenido oficial.
- Publicar activos sensibles, hacer commits/push o completar todo el sitio en el primer lote.

## Approach

Aplicar por lotes. El primero entregará Docker, tema/plugin mínimos, dummy y base de pruebas. Después se cubrirán dominio, frontend y endurecimiento. `docs/*.pdf` requiere revisión previa.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| `compose.yaml` | New | WordPress y base de datos reproducibles. |
| `.env.example` | New | Variables sin secretos. |
| `wp-content/themes/labm/` | New | Tema de bloques. |
| `wp-content/plugins/labm-core/` | New | Dominio y seguridad. |
| `tests/` | New | Pruebas y fixtures ficticios. |
| `docs/` | Modified | Guías; PDF bajo revisión. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Hosting incompatible | Med | Fijar localmente y validar matriz antes de desplegar. |
| Filtrar secretos o PDF sensibles | Med | Exclusiones Git, ejemplos y revisión explícita. |
| Alcance transversal | High | Lotes pequeños con gates y evidencia. |

## Rollback Plan

Detener contenedores conservando volúmenes y restaurar archivos. El primer lote no tendrá migraciones irreversibles. Antes de cambios de esquema, respaldar base/medios y revertir el lote afectado.

## Dependencies

- Docker instalado; versiones WordPress/PHP/DB por decidir en DESIGN.
- Revisión de ambos PDF y confirmación posterior de hosting, SMTP y datos oficiales.

## Success Criteria

- [ ] Un entorno nuevo levanta WordPress sin secretos versionados.
- [ ] Tema y plugin se activan independientemente; el dominio persiste al cambiar de tema.
- [ ] Fixtures ficticios cubren vistas críticas.
- [ ] Smoke, análisis estático, integración y E2E base producen evidencia.
- [ ] QA cubre WCAG 2.2 AA, 320/768/1024/1440 px y Lighthouse.
