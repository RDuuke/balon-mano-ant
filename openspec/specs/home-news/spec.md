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
Cada noticia MUST conservar título, categoría y fecha semántica cuando estén disponibles. Su imagen MUST priorizar la destacada válida, después un medio editorial permitido y finalmente un respaldo institucional seguro coherente con el diseño vigente. El respaldo MUST NOT sugerir una imagen específica de la noticia ni exponer medios restringidos.

La secci?n MUST adaptarse sin scroll horizontal y conservar orden de lectura y foco visibles.

#### Scenario: Imagen destacada válida
- DADO una noticia pública con destacada válida y medio alternativo
- CUANDO aparece en portada como destacada o lateral
- ENTONCES usa su destacada y conserva metadatos y enlace al detalle.

#### Scenario: Destacada ausente
- DADO una noticia pública sin destacada y con medio editorial permitido
- CUANDO se presenta
- ENTONCES usa ese medio; si tampoco existe, presenta el respaldo institucional seguro.

#### Scenario: Medio inválido o restringido
- DADO referencias de medios inexistentes, inseguras o no públicas
- CUANDO se presenta la noticia
- ENTONCES no expone esas referencias ni imágenes rotas y conserva texto y navegación con respaldo seguro.

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
