# Especificación completa: Experiencia pública

## Requisitos

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

### Requirement: Experiencia responsive y accesible
Las vistas públicas MUST cumplir WCAG 2.2 AA y MUST ser usables a 320, 768, 1024 y 1440 px, incluyendo teclado, foco, jerarquía, alternativas textuales y movimiento reducido.

#### Scenario: Recorrido accesible
- DADO una página representativa y navegación solo por teclado
- CUANDO se recorren controles, enlaces y formularios
- ENTONCES el orden, nombres accesibles, foco y mensajes permiten completar la tarea.

#### Scenario: Cambio de tamaño
- DADO contenido representativo en cada ancho objetivo
- CUANDO se redimensiona la vista
- ENTONCES no hay pérdida de información, solapamientos ni desplazamiento horizontal funcionalmente innecesario.

#### Scenario: Incumplimiento AA
- DADO una auditoría que detecta contraste, semántica o interacción no conforme
- CUANDO se evalúa el criterio de aceptación
- ENTONCES la entrega falla y registra el elemento y la vista afectados.
