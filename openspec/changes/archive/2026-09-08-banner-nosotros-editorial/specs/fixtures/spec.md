# Delta de Fixtures

## ADDED Requirements

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
