# Delta: noticias de portada

## MODIFIED Requirements

### Requirement: Metadatos, medios y adaptación
Cada noticia MUST conservar título, categoría y fecha semántica cuando estén disponibles. Su imagen MUST priorizar la destacada válida, después un medio editorial permitido y finalmente un respaldo institucional seguro coherente con el diseño vigente. El respaldo MUST NOT sugerir una imagen específica de la noticia ni exponer medios restringidos.

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
