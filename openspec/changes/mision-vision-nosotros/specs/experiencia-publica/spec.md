# Delta de Experiencia pública

## ADDED Requirements

### Requirement: Sección editorial de Misión y Visión

La vista “Nosotros” MUST mostrar después del banner los contenidos públicos e independientes de Misión y Visión, en ese orden, identificados como 01 y 02 y con tratamientos claro y oscuro diferenciados.

#### Scenario: Ambos artículos disponibles

- DADO artículos publicados y completos para Misión y Visión
- CUANDO un visitante abre “Nosotros”
- ENTONCES ve 01 Misión seguido de 02 Visión con sus textos independientes

#### Scenario: Un artículo no está disponible

- DADO que solo uno de los dos artículos está publicado y completo
- CUANDO se presenta la sección
- ENTONCES se muestra únicamente el contenido válido sin revelar el otro

#### Scenario: Ningún artículo es publicable

- DADO que ambos artículos faltan, están restringidos o vacíos
- CUANDO un visitante abre “Nosotros”
- ENTONCES la sección se omite sin marcadores vacíos

### Requirement: Composición responsive y accesible

La sección SHALL usar jerarquía semántica, contraste perceptible y paneles contiguos en escritorio; MUST apilarse sin desborde ni solapamiento en pantallas estrechas.

#### Scenario: Presentación de escritorio

- DADO ambos artículos y un viewport amplio
- CUANDO se renderiza la sección
- ENTONCES los paneles claro y oscuro aparecen contiguos y equilibrados

#### Scenario: Presentación móvil

- DADO ambos artículos y un viewport de 320 px
- CUANDO se renderiza la sección
- ENTONCES los paneles se apilan en orden y no generan desborde horizontal

#### Scenario: Contenido editorial largo

- DADO títulos o textos más extensos que los demo
- CUANDO se presenta la sección
- ENTONCES el contenido se adapta sin recorte, solapamiento ni pérdida de jerarquía
