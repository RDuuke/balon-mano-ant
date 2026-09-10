# Delta: Fixtures

## ADDED Requirements

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
