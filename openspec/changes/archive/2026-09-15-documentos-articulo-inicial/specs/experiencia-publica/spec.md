# Especificación de Experiencia pública

## ADDED Requirements

### Requirement: Encabezado editorial editable de Documentos

El sistema MUST mostrar el encabezado de la página Documentos a partir de un artículo editorial publicado y editable, usando su título y resumen público.

#### Scenario: Artículo publicado completo

- GIVEN un artículo editorial publicado con título y resumen.
- WHEN una persona visita la página Documentos.
- THEN el encabezado muestra el título y el resumen del artículo.

#### Scenario: Contenido alternativo editable

- GIVEN un artículo publicado sin extracto y con contenido.
- WHEN se muestra el encabezado.
- THEN el contenido público del artículo se usa como resumen.

#### Scenario: Artículo incompleto o no público

- GIVEN que el artículo no está publicado, no existe o carece de título o resumen.
- WHEN se solicita el encabezado.
- THEN el sistema SHALL omitirlo sin revelar datos no públicos.

### Requirement: Presentación semántica del encabezado

El sistema MUST presentar el encabezado como un artículo accesible, con ceja editorial, título principal único y resumen legible.

#### Scenario: Diseño aprobado

- GIVEN el artículo editorial publicado con el contenido inicial.
- WHEN se muestra la página Documentos.
- THEN se visualizan «TRANSPARENCIA Y CONSULTA», «DOCUMENTOS» y el resumen aprobado.

#### Scenario: Pantalla reducida

- GIVEN una pantalla de ancho reducido.
- WHEN se visualiza el encabezado.
- THEN el texto conserva contraste, orden de lectura y no produce desbordamiento horizontal.

#### Scenario: Contenido con marcado o texto hostil

- GIVEN título o resumen con marcado, enlaces o texto no seguro.
- WHEN se renderiza el encabezado.
- THEN el sistema SHALL exponer solo texto saneado y escapado.

### Requirement: Asociación de la página Documentos

El sistema MUST asociar la página Documentos con el encabezado editorial y SHOULD conservar las partes globales de navegación y pie.

#### Scenario: Ruta Documentos

- GIVEN la página Documentos usa su plantilla pública.
- WHEN WordPress resuelve la ruta.
- THEN el patrón de Documentos compone el encabezado editorial.

#### Scenario: Tema sin artículo editorial

- GIVEN la plantilla pública está activa pero falta el artículo editorial.
- WHEN WordPress compone la página.
- THEN la navegación y el pie se mantienen disponibles.

#### Scenario: Patrón no disponible

- GIVEN una instalación donde no se carga el patrón Documentos.
- WHEN se procesa otra página pública.
- THEN el sistema SHALL no alterar la composición de esa página.
