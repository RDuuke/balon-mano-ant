# Delta de Experiencia pública

## ADDED Requirements

### Requirement: Banner institucional estático y administrable

La vista “Nosotros” MUST iniciar con un único banner estático alimentado por un artículo publicado y administrable, y MUST presentar su título, resumen e imagen destacada sin controles ni comportamiento de slider.

#### Scenario: Artículo publicado completo

- DADO un artículo de banner publicado con título, resumen e imagen destacada
- CUANDO un visitante abre “Nosotros”
- ENTONCES ve el banner como primera sección, con texto e imagen y sin controles de slider

#### Scenario: Adaptación a pantalla estrecha

- DADO el banner completo en un viewport de 320 px
- CUANDO se presenta la vista “Nosotros”
- ENTONCES texto e imagen se apilan, permanecen legibles y no generan desborde horizontal

#### Scenario: Artículo ausente o no público

- DADO que el artículo reservado no existe, está en borrador o es privado
- CUANDO un visitante anónimo abre “Nosotros”
- ENTONCES el banner se omite sin revelar contenido restringido ni mostrar controles vacíos

### Requirement: Presentación editorial fiel y accesible

El banner SHALL usar un panel de texto negro, acento verde, texto blanco e imagen contigua; MUST conservar jerarquía semántica, contraste perceptible y alternativa textual adecuada.

#### Scenario: Presentación de escritorio

- DADO un viewport amplio y contenido completo
- CUANDO se renderiza el banner
- ENTONCES texto e imagen ocupan paneles contiguos y equilibrados según el diseño aprobado

#### Scenario: Contenido editorial largo

- DADO un título o resumen mayor al contenido demo
- CUANDO se presenta el banner
- ENTONCES el contenido se adapta sin solaparse, recortarse ni invadir la imagen

#### Scenario: Imagen destacada no disponible

- DADO un artículo publicado sin imagen destacada válida
- CUANDO se abre “Nosotros”
- ENTONCES no aparece un medio roto y la composición restante conserva una lectura comprensible
