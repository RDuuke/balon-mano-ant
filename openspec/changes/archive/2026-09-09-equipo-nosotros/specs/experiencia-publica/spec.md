# Delta: Experiencia pública

## ADDED Requirements

### Requirement: Sección editorial de integrantes

La vista “Nosotros” MUST mostrar después de Misión/Visión una sección titulada “Quiénes hacen posible la Liga”, alimentada por integrantes publicados y administrables, con imagen destacada, nombre, cargo y grupo editorial.

#### Scenario: Colección publicada
- DADO al menos cuatro integrantes publicados y completos
- CUANDO un visitante abre “Nosotros”
- ENTONCES ve cuatro tarjetas ordenadas con imagen, nombre y cargo

#### Scenario: Integrante incompleto
- DADO un integrante publicado sin imagen, nombre o cargo
- CUANDO se construye la colección
- ENTONCES su tarjeta se omite sin dejar un medio roto o hueco visual

#### Scenario: Contenido restringido
- DADO integrantes en borrador o privados
- CUANDO un visitante anónimo abre “Nosotros”
- ENTONCES sus datos no aparecen en la sección ni en sus filtros

### Requirement: Filtros accesibles y composición responsive

La sección SHALL ofrecer filtros para Comité, Entrenadores y Representantes; MUST conservar el grupo seleccionado de forma perceptible y funcionar sin JavaScript. La grilla MUST mantener jerarquía, foco, contraste y ausencia de desborde entre 320 y 1440 px.

#### Scenario: Filtrado por grupo
- DADO integrantes publicados en los tres grupos
- CUANDO el visitante activa “Entrenadores”
- ENTONCES ve solo integrantes de ese grupo y el filtro queda identificado

#### Scenario: Grupo sin resultados
- DADO un grupo válido sin integrantes publicables
- CUANDO el visitante activa su filtro
- ENTONCES ve un estado vacío comprensible y puede cambiar de grupo

#### Scenario: Filtro inválido
- DADO un valor de filtro no reconocido
- CUANDO se abre “Nosotros” con ese valor
- ENTONCES la vista usa la colección predeterminada sin revelar contenido restringido

#### Scenario: Anchos objetivo
- DADO cuatro tarjetas con contenido representativo
- CUANDO se presentan a 320, 768, 1024, 1200 y 1440 px
- ENTONCES la grilla se adapta sin solapamiento ni desplazamiento horizontal global

#### Scenario: Contenido largo
- DADO nombres y cargos mayores que los datos demo
- CUANDO se renderizan las tarjetas
- ENTONCES el texto crece o ajusta sin recorte, superposición ni pérdida de foco

#### Scenario: Navegación asistida
- DADO un visitante que usa teclado o tecnología asistiva
- CUANDO recorre filtros y tarjetas
- ENTONCES percibe el título, el filtro activo y contenido en orden lógico sin violaciones automatizadas
