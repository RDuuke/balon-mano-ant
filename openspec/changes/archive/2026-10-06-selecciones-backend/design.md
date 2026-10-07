# Design: Backend de fichas de Selecciones

## Technical Approach
Reutilizar `labm_seleccion` y el REST nativo de WordPress. El registro existente ya ofrece título, contenido, extracto, imagen, modalidad, categoría y `labm_modalidad_detalle`. El defecto está en `labm_core_ensure_capabilities()`: su salida temprana comprueba capacidades de otros dominios, pero omite Selecciones. Añadir una reparación aditiva de permisos faltantes antes de esa salida; fijar el contrato existente con pruebas de comportamiento.

## Architecture Decisions

### Decision: Reparación sin incrementar la versión global
| Opción | Compensación | Decisión y justificación |
|---|---|---|
| Incrementar `LABM_CORE_CAPABILITIES_VERSION` | Reejecuta concesiones de todos los dominios; no detecta pérdidas posteriores. | Descartada. |
| Comprobar capacidades reales de Selecciones | Una comprobación pequeña en `init`, sin escrituras cuando están completas. | Elegida: recuperar permisos aunque el marcador siga vigente. |

Obtener el conjunto único de capacidades de `get_post_type_object( 'labm_seleccion' )->cap`, ya registrado en prioridad 5. En prioridad 20 comprobar Administrador y Editor; conceder exclusivamente las capacidades faltantes mediante `WP_Role::add_cap()`. Conservar las demás capacidades y la migración global existente. Si el CPT o un rol no existen, conservar el patrón actual de omitirlos. No cambiar el marcador ni introducir opciones nuevas. La ejecución repetida con roles completos no escribe capacidades de Selecciones.

### Decision: Contrato editorial y privacidad nativos
| Opción | Compensación | Decisión y justificación |
|---|---|---|
| Tablas, controladores o filtros propios | Duplican validaciones y permisos existentes. | Descartada. |
| CPT, taxonomías y REST existentes | Conserva interfaces usadas por el futuro tema. | Elegida: los requisitos están cubiertos por el registro actual. |

Mantener `map_meta_cap`, clasificación extensible, saneamiento `sanitize_text_field` y autorización de metadatos con `current_user_can( 'edit_post', $post_id )`. Las pruebas deben comprobar rechazo real de escrituras y lecturas restringidas; no basta inspeccionar callbacks registrados.

## Data Flow
`init → registro del CPT/taxonomías/metadatos → reparación de capacidades faltantes → editor o REST → permisos y saneamiento WordPress → persistencia → lectura REST pública de publicaciones`.

## File Changes
| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modificar | Reparación aditiva de capacidades de Selecciones antes del guard de versión. |
| `tests/php/DomainModelTest.php` | Modificar | Regresión con versión vigente, roles, contratos editoriales, REST y privacidad. |
| `content-sync/canonical.zip` | Actualizar en APPLY | Estado persistido final mediante sincronización oficial. |
| `content-sync/latest.json` | Actualizar en APPLY | Versión y hash del paquete canónico. |

## Interfaces / Contracts
- Colección y detalle: `/wp/v2/labm_seleccion` y `/wp/v2/labm_seleccion/{id}`.
- Clasificaciones: `/wp/v2/labm_modalidad` y `/wp/v2/labm_categoria`; referencias en los campos homónimos de la ficha.
- Metadato: `meta.labm_modalidad_detalle`, cadena única pública; escritura autorizada para editar la ficha, vaciado permitido.
- Permisos: capacidades generadas por el CPT para Editor y Administrador; visitantes y Suscriptores conservan sus restricciones.
- Colección anónima: solamente publicaciones. Detalle privado o borrador: rechazo sin campos editoriales expuestos. No fijar un código HTTP distinto del contrato nativo.

## Testing Strategy
| Capa | Comportamiento | Enfoque |
|---|---|---|
| Regresión | Versión vigente, pérdida de permisos, preservación e idempotencia | RED retirando capacidades de Selecciones; restauración exacta de roles/opciones al terminar. GREEN después del cambio. |
| Dominio | Soportes y clasificaciones extensibles | Fichas y términos temporales con asignaciones vacías y nuevas. |
| REST | Campos, saneamiento, vaciado, permisos y privacidad | `WP_REST_Request`/`rest_do_request` como usuario autorizado y anónimo; colección acotada a IDs temporales y rechazo de detalle restringido. |
| Calidad | Regresiones globales y LF | Pruebas focales con PHPUnit de integración; `scripts/gate.ps1 -IncludeBrowser`, cobertura mínima 80 %, PHPCS/PHPStan y comprobación LF. |

El bootstrap usa WordPress real en Docker. Cada prueba debe restaurar usuario actual, capacidades y opciones, y limpiar únicamente posts, medios, términos y usuarios creados por ella; evitar contaminar la base sincronizable. Registrar RED/GREEN por tarea de implementación en modo TDD estricto.

## Migration / Rollout
Sin tablas, contenido nuevo ni migración de fichas. Respaldar y revisar `content-sync` antes de mutar el runtime; permitir la reparación en `init`. Después de limpiar pruebas, ejecutar el proceso oficial `scripts/content-sync.ps1 -Action Push`, validar paquete, versión y SHA-256; incluir ambos artefactos canónicos. Excluir credenciales, `wp_users` y `wp_usermeta`. Conservar `review_budget.max_diff_lines=0`; presentar el diff concreto en el gate previo a VERIFY. Rollback: revertir implementación y recuperar los roles respaldados con el procedimiento oficial, preservando capacidades compartidas.

## Open Questions
Ninguna pregunta técnica pendiente. La autorización del diff concreto corresponde al gate de APPLY.
