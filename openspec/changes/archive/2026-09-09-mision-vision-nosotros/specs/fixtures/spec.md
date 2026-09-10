# Delta de Fixtures

## ADDED Requirements

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
