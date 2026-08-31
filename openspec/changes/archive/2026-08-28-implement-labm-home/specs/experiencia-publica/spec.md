# Delta para Experiencia pública

## MODIFIED Requirements

### Requirement: Portada institucional completa
La portada MUST presentar, en este orden, navegación, slider principal, presentación de la Liga, clubes asociados, evento destacado, actualidad, llamada a vinculación, Aliados Oficiales y pie; MUST excluir secciones de Piso, Playa, horarios y escenarios sin afectar sus rutas fuera de Inicio.

#### Scenario: Portada con contenido publicado
- DADO contenido público disponible para las secciones de Inicio
- CUANDO un visitante abre la portada
- ENTONCES ve las secciones en el orden definido y accede a sus destinos.

#### Scenario: Sección sin contenido
- DADO que una sección opcional no tiene contenido publicable
- CUANDO se carga la portada
- ENTONCES se oculta o muestra un estado ficticio inequívoco, sin dejar huecos.

#### Scenario: Contenido no público
- DADO contenido en borrador, privado o inválido
- CUANDO un visitante anónimo abre Inicio
- ENTONCES ese contenido no se revela ni altera la continuidad de la página.

### Requirement: Slider principal accesible
La portada SHALL ofrecer bajo el menú un slider administrable con controles operables, pausa y estado perceptible; con preferencia de movimiento reducido MUST permanecer sin transición automática.

#### Scenario: Recorrido normal
- DADO al menos dos slides publicados
- CUANDO el visitante usa los controles
- ENTONCES cambia de slide y percibe cuál está activo.

#### Scenario: Movimiento reducido
- DADO que el visitante prefiere movimiento reducido
- CUANDO abre la portada
- ENTONCES el contenido permanece estable y los controles siguen disponibles.

#### Scenario: Slide inválido o ausente
- DADO que no existe ningún slide publicable
- CUANDO se carga Inicio
- ENTONCES no aparece un control inoperable ni contenido privado o incompleto.

### Requirement: Aliados Oficiales accesibles
La portada SHALL mostrar Aliados Oficiales antes del pie con movimiento continuo de derecha a izquierda, opción de pausa y alternativa estática; las repeticiones visuales MUST NOT crear contenido accesible duplicado.

#### Scenario: Secuencia disponible
- DADO varios aliados publicados
- CUANDO el visitante observa la sección
- ENTONCES los logos recorren una secuencia continua y cada aliado conserva nombre accesible.

#### Scenario: Pausa o movimiento reducido
- DADO foco dentro de la sección, pausa activada o preferencia de movimiento reducido
- CUANDO se presenta la lista
- ENTONCES el movimiento se detiene y los aliados permanecen disponibles.

#### Scenario: Datos insuficientes
- DADO que no hay aliados publicables o un logo carece de datos obligatorios
- CUANDO se carga la sección
- ENTONCES se omite el elemento inválido y no aparece un carrusel vacío o roto.

### Requirement: Identidad visual responsive
La portada MUST usar la identidad verde, negro, blanco y neutros aprobada, conservar contraste y foco perceptible, y ser usable a 320, 768, 1024 y 1440 px sin desplazamiento horizontal funcionalmente innecesario.

#### Scenario: Anchos objetivo
- DADO contenido representativo en cada ancho objetivo
- CUANDO se recorre la portada
- ENTONCES la jerarquía, controles y textos permanecen visibles y operables.

#### Scenario: Texto sobre color de marca
- DADO un componente con fondo verde
- CUANDO se calcula su presentación
- ENTONCES el color del texto conserva contraste verificable y el foco es visible.

#### Scenario: Desborde o solapamiento
- DADO contenido largo o ampliación de texto
- CUANDO un elemento excedería su contenedor
- ENTONCES se adapta sin ocultar controles, superponer información ni crear desplazamiento global.
