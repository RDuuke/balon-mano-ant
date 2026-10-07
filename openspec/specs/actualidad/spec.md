# Especificación: Actualidad

## Requisitos

### Requisito: Composición editorial fiel al diseño canónico

El sistema DEBE presentar en `/actualidad/` la jerarquía editorial del frame Pencil `Nrclx`: barra institucional y navegación, hero, controles de filtro, una noticia destacada, tarjetas de noticias, paginación y pie. DEBE usar los colores, contrastes, proporciones tipográficas y estados de los componentes definidos en dicho frame.

#### Escenario: Composición de escritorio
- DADO que una persona visita `/actualidad/` en escritorio
- CUANDO la página termina de cargar
- ENTONCES observa las regiones y el orden visual definidos por `Nrclx`.

#### Escenario: Adaptación de viewport
- DADO un viewport de 320 px, tableta o escritorio
- CUANDO se visualiza el listado
- ENTONCES los controles, la destacada, las tarjetas y la paginación conservan la jerarquía y no producen desplazamiento horizontal.

#### Escenario: Recurso visual no disponible
- DADO que un recurso de imagen de una noticia no está disponible
- CUANDO se muestra la composición
- ENTONCES la página conserva una superficie de imagen identificable y texto alternativo útil, sin iconos de imagen rota.

### Requisito: Controles de descubrimiento accesibles

El sistema DEBE ofrecer búsqueda por el parámetro público `texto` y filtro por categoría. Los controles DEBEN tener etiqueta accesible, foco visible y contraste suficiente; el valor seleccionado DEBE persistir tras filtrar y paginar.

#### Escenario: Búsqueda y categoría válidas
- DADO que existen noticias que coinciden con texto y categoría
- CUANDO la persona aplica ambos filtros
- ENTONCES se muestran únicamente resultados que cumplen ambos criterios y la URL conserva los valores aplicados.

#### Escenario: Filtro sin coincidencias
- DADO una combinación válida sin resultados
- CUANDO la persona la aplica
- ENTONCES se presenta un estado vacío claro, con los filtros visibles para poder modificarlos.

#### Escenario: Parámetros no válidos
- DADO una URL con valores de filtro no reconocidos o texto vacío
- CUANDO la página procesa la solicitud
- ENTONCES no expone errores técnicos ni resultados no publicados y mantiene una interfaz utilizable.

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

#### Escenario: Página con resultados
- DADO que una página de resultados contiene publicaciones
- CUANDO se muestra el listado
- ENTONCES la primera es destacada y ninguna publicación se repite entre la destacada y las tarjetas.

#### Escenario: Página solicitada inexistente
- DADO que la URL solicita una página fuera del rango disponible
- CUANDO se procesa el listado
- ENTONCES se comunica el estado sin resultados de forma clara y sin enlaces de paginación inválidos.

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
