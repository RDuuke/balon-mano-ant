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

### Requisito: Presentación y navegación de resultados

El sistema DEBE mostrar solamente noticias publicadas, ordenadas de la más reciente a la menos reciente. El primer resultado de cada página DEBE mostrarse una sola vez como destacada; los restantes como tarjetas, con categoría, fecha, título, resumen y enlace al detalle. La paginación DEBE ser operable por teclado y preservar filtros activos.

#### Escenario: Página con resultados
- DADO que una página de resultados contiene publicaciones
- CUANDO se muestra el listado
- ENTONCES la primera es destacada y ninguna publicación se repite entre la destacada y las tarjetas.

#### Escenario: Última página parcial
- DADO que la última página contiene menos resultados que una página completa
- CUANDO la persona la abre
- ENTONCES se muestran todos los resultados disponibles sin tarjetas de relleno ni duplicados.

#### Escenario: Página solicitada inexistente
- DADO que la URL solicita una página fuera del rango disponible
- CUANDO se procesa el listado
- ENTONCES se comunica el estado sin resultados de forma clara y sin enlaces de paginación inválidos.
