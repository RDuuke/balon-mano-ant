# Especificación completa: Últimas noticias de portada

## ADDED Requirements

### Requirement: Selección editorial acotada

La portada MUST seleccionar como máximo cuatro noticias publicadas en un orden estable y MUST excluir borradores, contenido privado y tipos ajenos.

#### Scenario: Cuatro o más noticias publicadas

- DADO que existen al menos cuatro noticias publicadas
- CUANDO se abre la portada
- ENTONCES se presentan exactamente las primeras cuatro según el orden editorial

#### Scenario: Menos de cuatro noticias

- DADO que existen entre una y tres noticias publicadas
- CUANDO se abre la portada
- ENTONCES se presentan todas una sola vez sin espacios interactivos vacíos

#### Scenario: Sin noticias publicadas

- DADO que no existe ninguna noticia publicada
- CUANDO se abre la portada
- ENTONCES la sección se omite sin error ni marcado incompleto

### Requirement: Jerarquía visual y navegación

La sección MUST mostrar la primera noticia como destacada y hasta tres noticias laterales. Cada noticia MUST enlazar a su detalle, y la cabecera MUST ofrecer un enlace al archivo completo de actualidad.

#### Scenario: Colección completa

- DADO que fueron seleccionadas cuatro noticias
- CUANDO se renderiza la sección
- ENTONCES hay una pieza destacada, tres laterales y un CTA al archivo

#### Scenario: Colección parcial

- DADO que fueron seleccionadas menos de cuatro noticias
- CUANDO se renderiza la sección
- ENTONCES la primera conserva jerarquía destacada y las restantes se presentan como laterales

#### Scenario: Enlace de archivo no disponible

- DADO que el archivo de actualidad no tiene una URL válida
- CUANDO se renderiza la sección
- ENTONCES no se emite un CTA roto y las noticias conservan enlaces válidos

### Requirement: Metadatos, medios y adaptación

Cada pieza MUST exponer título, categoría y fecha semántica. La destacada y las laterales SHOULD mostrar imagen; ante su ausencia MUST existir un fallback no engañoso. La sección MUST adaptarse sin scroll horizontal y conservar orden de lectura y foco visibles.

#### Scenario: Datos editoriales completos

- DADO una noticia con imagen, categoría y fecha
- CUANDO se presenta en cualquier posición
- ENTONCES sus datos visibles y semánticos coinciden con la noticia

#### Scenario: Imagen o categoría ausente

- DADO una noticia publicada con imagen o categoría ausente
- CUANDO se presenta en la portada
- ENTONCES usa un fallback seguro sin enlaces rotos ni texto inventado

#### Scenario: Ancho móvil extremo

- DADO un viewport móvil de 320 píxeles
- CUANDO se navega la sección con teclado
- ENTONCES el contenido sigue el orden visual, el foco es visible y no hay scroll horizontal
