# Delta: fixtures

## ADDED Requirements

### Requirement: Fallos de medios diagnosticables y recuperables
La carga de fixtures MUST informar el recurso previsto que falla sin depender del orden circunstancial de otros medios. MUST conservar contenido ajeno, evitar asociaciones inválidas y permitir recuperación idempotente al restablecer la disponibilidad del recurso.

#### Scenario: Medios disponibles
- DADO recursos demo disponibles y contenido ajeno existente
- CUANDO se cargan los fixtures
- ENTONCES los medios quedan asociados a sus entidades previstas sin modificar contenido ajeno.

#### Scenario: Carga repetida tras recuperación
- DADO un recurso antes no disponible que vuelve a estar disponible
- CUANDO se repite la carga
- ENTONCES se completa su asociación sin duplicar entidades ni adjuntos demo.

#### Scenario: Recurso previsto no disponible
- DADO un único recurso demo aislado que no puede importarse
- CUANDO se solicita la carga
- ENTONCES se informa ese recurso y no queda una asociación inválida ni se altera contenido ajeno.
