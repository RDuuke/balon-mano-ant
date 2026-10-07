# Proposal: Backend de fichas de Selecciones

## Intent
Asegurar un contrato editorial para fichas de Selecciones clasificadas por modalidad y categoría, reutilizando el dominio existente. La comprobación actual de capacidades puede omitir `labm_seleccion` si el marcador de versión ya está vigente.

## Scope
### In Scope
- Reparar capacidades de edición y publicación de `labm_seleccion` para editor y administrador.
- Fijar con pruebas el contrato del CPT, taxonomías, metadatos REST y privacidad de publicaciones.

### Out of Scope
- Planteles, deportistas, temporadas, relaciones o endpoints propios.
- Plantillas, estilos, navegación y comportamiento visual; van en `selecciones-frontend`.

## Approach
Reutilizar CPT, taxonomías y API REST nativa. Corregir la comprobación de capacidades en `labm_core_ensure_capabilities()` y probar instalaciones con versión vigente sin capacidades de Selecciones. No añadir tablas ni migrar contenido.

## Affected Areas
| Area | Impact | Description |
|------|--------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modified | Reparación de capacidades y contrato de Selecciones. |
| `tests/php/DomainModelTest.php` | Modified | Pruebas de capacidades, taxonomías, metadatos y REST. |

## Risks
| Risk | Likelihood | Mitigation |
|------|------------|------------|
| `review_budget.max_diff_lines=0` bloquea diffs de implementación si se aplica literalmente. | High | Conservarlo y detenerse si el gate se activa. |
| Reparar capacidades actualiza opciones de roles. | Low | Limitar la reparación y sincronizar el estado canónico antes de cerrar. |

## Rollback Plan
Revertir los dos archivos afectados. No hay tablas ni contenido que migrar. Si la reparación ya actualizó roles, restaurar su estado respaldado en `content-sync` con el proceso oficial y verificarlo; conservar otras capacidades compartidas.

## Dependencies
- WordPress 7.1, PHP 8.3 y plugin `labm-core`.
- El tema consumirá el contrato en el cambio posterior `selecciones-frontend`.

## Success Criteria
- [ ] Editor y administrador pueden crear y publicar Selecciones; sus capacidades se reparan aunque la versión ya esté vigente.
- [ ] Usuarios sin rol editorial no reciben esas capacidades.
- [ ] REST expone fichas y clasificaciones por modalidad/categoría con saneamiento y autorización de metadatos.
- [ ] Las consultas públicas excluyen contenido no publicado.
- [ ] Pruebas focales, análisis estático, formato y LF pasan; si se activa el gate de diff cero, APPLY se detiene.