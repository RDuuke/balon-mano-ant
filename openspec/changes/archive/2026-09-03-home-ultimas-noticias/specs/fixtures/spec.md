# Especificación completa: Fixtures de noticias

## ADDED Requirements

### Requirement: Colección demo identificable

El sistema MUST disponer de una categoría de demostración y exactamente seis noticias publicadas, identificables como ficticias, con título, contenido, fecha editorial e imagen asociada.

#### Scenario: Carga inicial completa

- DADO un entorno sin la colección demo
- CUANDO se solicita cargar los datos de demostración
- ENTONCES existen una categoría demo y seis noticias publicadas asociadas a ella

#### Scenario: Carga repetida

- DADO que la colección demo ya existe completa
- CUANDO se solicita cargarla nuevamente
- ENTONCES permanecen exactamente seis noticias demo sin duplicados

#### Scenario: Conflicto con contenido ajeno

- DADO que un identificador reservado para una noticia demo pertenece a contenido no ficticio
- CUANDO se solicita cargar la colección
- ENTONCES el contenido ajeno se conserva y el conflicto se informa

### Requirement: Datos editoriales verificables

Cada noticia demo SHALL tener valores estables y suficientes para verificar la presentación de portada, incluyendo categoría, fecha e imagen; ningún dato MUST afirmar hechos reales de la Liga.

#### Scenario: Datos completos

- DADO que las seis noticias demo fueron cargadas
- CUANDO se consultan sus datos editoriales
- ENTONCES todas tienen categoría demo, fecha válida, imagen y aviso de contenido ficticio

#### Scenario: Medio previamente disponible

- DADO que la imagen prevista ya está disponible
- CUANDO se recarga la colección demo
- ENTONCES la noticia conserva una única asociación válida con esa imagen

#### Scenario: Medio no disponible

- DADO que una imagen demo no está disponible
- CUANDO se carga la colección
- ENTONCES la noticia sigue siendo consultable y la ausencia queda apta para el fallback de presentación
