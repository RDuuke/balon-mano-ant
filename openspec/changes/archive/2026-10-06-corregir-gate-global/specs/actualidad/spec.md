# Delta: actualidad

## MODIFIED Requirements

### Requirement: Presentación y navegación de resultados
El listado MUST presentar cada publicación una sola vez: primera como destacada y restantes como tarjetas, respetando el tamaño de página vigente, orden cronológico, filtros y privacidad. La comprobación MUST distinguir roles editoriales y publicaciones, sin confundir la destacada con una tarjeta adicional.

#### Scenario: Página completa filtrada
- DADO suficientes noticias públicas para una página y filtros válidos
- CUANDO se abre el listado
- ENTONCES aparece una destacada y las restantes tarjetas sin duplicados y todas cumplen los filtros.

#### Scenario: Última página parcial
- DADO una última página con menos publicaciones que el tamaño vigente
- CUANDO se consulta
- ENTONCES todas aparecen una sola vez sin relleno y la paginación conserva los filtros.

#### Scenario: Página inexistente o contenido privado
- DADO una página fuera de rango y publicaciones restringidas
- CUANDO una persona anónima solicita el listado
- ENTONCES ve un estado comprensible sin publicaciones restringidas ni paginación inválida.

## ADDED Requirements

### Requirement: Reconciliación previa de composición y detalle
Las correcciones del listado MUST contrastarse con el frame Pencil `Nrclx` citado en la especificación vigente y registrar las medidas aprobadas por viewport. Antes de modificar contratos de Detalle, MUST existir una reconciliación explícita en su cambio propio que conserve contenido Gutenberg público, galería accesible y orden semántico; este cambio MUST preservar sus tareas pendientes. El handoff MUST identificar artefactos reconciliados, referencia visual, medidas verificables y tareas que siguen pendientes.

#### Scenario: Contrato reconciliado
- DADO referencia editorial y contrato de Detalle resueltos en su cambio propio
- CUANDO se valida una corrección global asociada
- ENTONCES la presentación cumple ese contrato y su evidencia identifica referencia y alcance.

#### Scenario: Medio ausente o contenido largo
- DADO contenido público largo o una imagen no disponible
- CUANDO se presenta la vista conforme al contrato reconciliado
- ENTONCES conserva lectura, navegación y alternativa segura sin desborde ni imagen rota.

#### Scenario: Contratos contradictorios
- DADO decisiones incompatibles pendientes entre los artefactos de Detalle
- CUANDO se plantea modificar su plantilla o comprobaciones
- ENTONCES el trabajo asociado queda bloqueado hasta reconciliarlas sin eliminar garantías funcionales.
