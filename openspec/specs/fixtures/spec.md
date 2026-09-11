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

### Requirement: Artículo demo del banner Nosotros

Los fixtures MUST crear o actualizar un artículo estándar publicado, identificado como ficticio, con slug estable, título, resumen, contenido e imagen destacada suficientes para administrar y comprobar el banner de “Nosotros”.

#### Scenario: Carga inicial

- DADO un entorno sin el artículo reservado
- CUANDO se cargan los fixtures
- ENTONCES existe un único artículo publicado con todos los datos editoriales requeridos

#### Scenario: Carga repetida

- DADO que el artículo demo ya existe
- CUANDO se cargan nuevamente los fixtures
- ENTONCES el artículo se actualiza sin duplicarse y conserva una única imagen destacada válida

#### Scenario: Conflicto con contenido ajeno

- DADO que el slug reservado pertenece a un artículo sin marcador ficticio
- CUANDO se cargan los fixtures
- ENTONCES el contenido ajeno no se sobrescribe y el conflicto se informa

### Requirement: Artículos demo independientes de Misión y Visión

Los fixtures MUST crear o actualizar dos entradas estándar publicadas con slugs estables, marcador ficticio y contenido suficiente, sin compartir identidad ni sobrescribir contenido ajeno.

#### Scenario: Carga inicial

- DADO un entorno sin las entradas reservadas
- CUANDO se cargan los fixtures
- ENTONCES existen exactamente una Misión y una Visión publicadas y completas

#### Scenario: Carga repetida

- DADO que ambas entradas demo ya existen
- CUANDO se cargan nuevamente los fixtures
- ENTONCES conservan sus identificadores y no se duplican

#### Scenario: Conflicto independiente

- DADO que uno de los slugs pertenece a contenido sin marcador ficticio
- CUANDO se cargan los fixtures
- ENTONCES ese contenido se preserva, se informa el conflicto y el otro artículo se procesa normalmente

### Requirement: Integrantes demo categorizados e idempotentes

Los fixtures MUST crear o actualizar cuatro integrantes ficticios publicados, completos y ordenados, distribuidos entre Comité, Entrenadores y Representantes, con nombres, cargos e imágenes destacadas originales; MUST preservar contenido ajeno y no duplicar artículos, términos ni adjuntos al repetirse.

#### Scenario: Carga inicial completa
- DADO un entorno sin integrantes demo
- CUANDO se cargan los fixtures
- ENTONCES existen cuatro integrantes completos, ordenados y asignados a grupos válidos

#### Scenario: Carga repetida
- DADO que los integrantes, términos y adjuntos demo ya existen
- CUANDO se cargan nuevamente los fixtures
- ENTONCES conservan sus identificadores y cantidades sin duplicados

#### Scenario: Conflicto con contenido ajeno
- DADO que un slug reservado pertenece a un integrante sin marcador ficticio
- CUANDO se cargan los fixtures
- ENTONCES el contenido ajeno se preserva, se informa el conflicto y los demás integrantes se procesan
