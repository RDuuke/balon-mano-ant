# Especificación completa: Experiencia pública

## Requisitos

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

### Requirement: Navegación institucional
El sitio completo SHALL ofrecer Inicio, Nosotros, Eventos y noticias, Selecciones de Piso y Playa, Documentos y Contacto desde una navegación global accesible.

#### Scenario: Navegación de escritorio
- DADO un visitante en una pantalla amplia
- CUANDO usa la navegación principal
- ENTONCES alcanza todas las secciones y reconoce la ubicación activa.

#### Scenario: Navegación móvil
- DADO un viewport de 320 px y navegación por teclado
- CUANDO se abre y cierra el menú
- ENTONCES todos los enlaces son alcanzables, el foco es visible y retorna a un lugar predecible.

#### Scenario: Destino no disponible
- DADO que una sección todavía no está publicada
- CUANDO un visitante intenta acceder desde una ruta conocida
- ENTONCES recibe un estado comprensible y una vía de regreso, sin errores técnicos expuestos.

### Requirement: Contenido institucional administrable
El sitio completo MUST permitir administrar las secciones de portada, Nosotros, actualidad, selecciones, clubes, integrantes, horarios y datos de contacto definidas en la fuente funcional.

#### Scenario: Portada completa
- DADO contenido publicado para las secciones configuradas
- CUANDO se visita Inicio
- ENTONCES se muestran únicamente las secciones habilitadas, sin espacios vacíos ni contenido ficticio presentado como oficial.

#### Scenario: Sección opcional oculta
- DADO que un editor deshabilita una sección de portada
- CUANDO se vuelve a cargar la página
- ENTONCES la sección desaparece y las restantes conservan orden y continuidad visual.

#### Scenario: Contenido incompleto
- DADO que faltan campos necesarios para publicar una entidad
- CUANDO un editor intenta publicarla
- ENTONCES se impide la publicación o se identifica claramente qué debe corregirse.

### Requirement: Actualidad y selecciones
El sitio SHALL mostrar actualidad cronológica con detalle, categorías, paginación y estados vacíos, y SHALL agrupar participaciones por modalidades extensibles.

#### Scenario: Consulta publicada
- DADO noticias, eventos y participaciones publicadas
- CUANDO un visitante consulta un listado o detalle
- ENTONCES ve título, fecha pertinente, categoría, extracto o contenido y medios disponibles.

#### Scenario: Filtro sin coincidencias
- DADO una combinación válida sin resultados
- CUANDO se aplica el filtro
- ENTONCES aparece un estado vacío claro y una acción para restablecer la consulta.

#### Scenario: Contenido privado
- DADO una entrada en borrador o privada
- CUANDO un visitante no autenticado consulta listados, búsqueda o URL directa
- ENTONCES el sitio no revela su contenido ni metadatos restringidos.

### Requirement: Experiencia responsive y accesibilidad verificable
Las vistas públicas MUST ser usables a 320, 768, 1024 y 1440 px, incluyendo teclado, foco, jerarquía, alternativas textuales y movimiento reducido. La garantía integral de cumplimiento WCAG 2.2 AA SHALL quedar diferida a un cambio futuro y MUST NOT declararse COMPLIANT en este cambio.

#### Scenario: Recorrido accesible
- DADO una página representativa y navegación solo por teclado
- CUANDO se recorren controles, enlaces y formularios
- ENTONCES el orden, nombres accesibles, foco y mensajes permiten completar la tarea.

#### Scenario: Cambio de tamaño
- DADO contenido representativo en cada ancho objetivo
- CUANDO se redimensiona la vista
- ENTONCES no hay pérdida de información, solapamientos ni desplazamiento horizontal funcionalmente innecesario.

#### Scenario: Hallazgo o afirmación no sustentada
- DADO una comprobación concreta que falla o un informe que afirma cumplimiento integral WCAG 2.2 AA
- CUANDO se evalúa el criterio de aceptación de este cambio
- ENTONCES falla el criterio concreto afectado o se retira la afirmación global, y la garantía integral permanece declarada como diferida.
