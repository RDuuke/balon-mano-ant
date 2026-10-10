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

### Requirement: Slider principal accesible y estable
La portada SHALL ofrecer bajo el menú un slider administrable con controles operables, pausa y estado perceptible. El slider SHALL mantener una altura exterior estable mientras el usuario navega entre ítems dentro del mismo viewport, aunque varíen el texto o los medios. El contenido esencial y los controles MUST permanecer visibles y operables, y con preferencia de movimiento reducido MUST permanecer sin transición automática.

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

#### Scenario: Navegación entre ítems diferentes
- DADO ítems publicados con distintas cantidades de texto y proporciones de medios
- CUANDO el visitante usa anterior, siguiente e indicadores
- ENTONCES la altura exterior del slider no cambia y el ítem activo resulta perceptible.

#### Scenario: Slider en anchos objetivo
- DADO el slider a 320, 768, 1024, 1200 y 1440 px
- CUANDO se recorren todos sus ítems
- ENTONCES cada ancho mantiene una altura estable sin ocultar contenido esencial ni controles.

#### Scenario: Contenido extremo o medio no disponible
- DADO un ítem con contenido largo o un medio que no puede mostrarse
- CUANDO el ítem se activa
- ENTONCES el slider conserva su altura, ofrece una presentación legible y no bloquea la navegación.

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
Las vistas públicas MUST usar la identidad verde, negro, blanco y neutros aprobada, conservar contraste y foco perceptible, y mantener las alineaciones aprobadas en Pen. Su contenido principal MUST tener un ancho máximo de 1200 px y permanecer centrado cuando el viewport sea mayor; fondos decorativos MAY extenderse al ancho completo. Los gutters y alineaciones MUST ser consistentes a 320, 768, 1024, 1200 y 1440 px.

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

#### Scenario: Pantalla mayor que el ancho máximo
- DADO un viewport de 1440 px y contenido representativo
- CUANDO se presenta una vista pública
- ENTONCES el contenido principal no supera 1200 px y sus márgenes laterales son iguales.

#### Scenario: Cambio entre anchos objetivo
- DADO la misma vista a 320, 768, 1024 y 1200 px
- CUANDO se mide la posición de sus secciones principales
- ENTONCES respetan gutters coherentes, alineación común y controles visibles.

#### Scenario: Contenido que intenta desbordar
- DADO texto largo, medios amplios o ampliación de texto
- CUANDO el contenido excedería el espacio disponible
- ENTONCES no causa desplazamiento horizontal global, solapamientos ni pérdida de controles.

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
Las vistas públicas MUST ser usables a 320, 768, 1024, 1200 y 1440 px, incluyendo teclado, foco, jerarquía, alternativas textuales y movimiento reducido. El centrado, los gutters, la ausencia de desborde y la estabilidad del slider MUST ser verificables mediante recorridos end-to-end en esos anchos. Una desviación geométrica mayor a 1 px entre márgenes que deban ser iguales, o cualquier cambio en la altura exterior del slider durante un recorrido, MUST fallar la comprobación correspondiente. La garantía integral de cumplimiento WCAG 2.2 AA SHALL quedar diferida a un cambio futuro y MUST NOT declararse COMPLIANT en este cambio.

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

#### Scenario: Comprobación geométrica satisfactoria
- DADO una vista pública cargada a 1440 px
- CUANDO se miden contenedor, márgenes y ancho del viewport
- ENTONCES el contenedor mide como máximo 1200 px y la diferencia entre márgenes no supera 1 px.

#### Scenario: Recorrido responsive completo
- DADO contenido representativo en cada ancho objetivo
- CUANDO la comprobación recorre secciones y todos los ítems del slider
- ENTONCES no detecta desborde global, desalineación ni variación de altura del slider.

#### Scenario: Regresión geométrica
- DADO una vista cuyo contenido excede 1200 px, queda descentrado o cuyo slider cambia de altura
- CUANDO se ejecuta la comprobación end-to-end aplicable
- ENTONCES el resultado falla e identifica el ancho y la condición incumplida.

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
La sección de integrantes MUST mostrar únicamente el grupo seleccionado como contenido perceptible y operable, identificar su filtro activo y funcionar sin JavaScript. MUST excluir datos restringidos; la presencia de contenido público no seleccionado en la página MAY variar sin alterar estas garantías.

La secci?n SHALL ofrecer filtros para Comit?, Entrenadores y Representantes. La grilla MUST mantener jerarqu?a, foco, contraste y ausencia de desborde entre 320 y 1440 px.

#### Scenario: Grupo publicado seleccionado
- DADO integrantes públicos de varios grupos
- CUANDO se activa Entrenadores con o sin JavaScript
- ENTONCES solo sus integrantes resultan perceptibles y operables y el filtro activo es identificable.

#### Scenario: Grupo válido vacío
- DADO un grupo sin integrantes publicables
- CUANDO se selecciona
- ENTONCES aparece un estado vacío comprensible y los demás filtros permanecen operables.

#### Scenario: Filtro inválido o contenido restringido
- DADO un filtro desconocido e integrantes privados
- CUANDO una persona anónima consulta la vista
- ENTONCES recibe la colección predeterminada sin datos restringidos ni errores técnicos.

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

### Requirement: CTA de vinculación compartido

La vista “Nosotros” MUST mostrar inmediatamente después de la sección de integrantes el mismo CTA “Haz parte del balonmano antioqueño” usado en la portada. Ambas vistas SHALL reutilizar un único render, conservar el contenido y el destino `/contacto/`, y MUST mantener una presentación responsive y accesible sin duplicar estilos.

#### Scenario: CTA presente en Nosotros
- DADO que un visitante abre la vista “Nosotros”
- CUANDO termina de recorrer la sección de integrantes
- ENTONCES encuentra inmediatamente después el CTA de vinculación con su título, texto y enlace a `/contacto/`

#### Scenario: Reutilización entre vistas
- DADO que la portada y la vista “Nosotros” muestran el CTA de vinculación
- CUANDO se compara su estructura semántica
- ENTONCES ambas vistas conservan el mismo contenido, clases y destino mediante un único render compartido

#### Scenario: Presentación responsive y accesible
- DADO el CTA en viewports de 320, 768, 1024 y 1440 px
- CUANDO se presenta o se recorre con tecnología asistiva
- ENTONCES no presenta recorte, solapamiento ni desborde y mantiene título y enlace accesibles
### Requirement: Encabezado editorial editable de Documentos

El sistema MUST mostrar el encabezado de la pÃ¡gina Documentos a partir de un artÃ­culo editorial publicado y editable, usando su tÃ­tulo y resumen pÃºblico.

#### Scenario: ArtÃ­culo publicado completo

- GIVEN un artÃ­culo editorial publicado con tÃ­tulo y resumen.
- WHEN una persona visita la pÃ¡gina Documentos.
- THEN el encabezado muestra el tÃ­tulo y el resumen del artÃ­culo.

#### Scenario: Contenido alternativo editable

- GIVEN un artÃ­culo publicado sin extracto y con contenido.
- WHEN se muestra el encabezado.
- THEN el contenido pÃºblico del artÃ­culo se usa como resumen.

#### Scenario: ArtÃ­culo incompleto o no pÃºblico

- GIVEN que el artÃ­culo no estÃ¡ publicado, no existe o carece de tÃ­tulo o resumen.
- WHEN se solicita el encabezado.
- THEN el sistema SHALL omitirlo sin revelar datos no pÃºblicos.

### Requirement: PresentaciÃ³n semÃ¡ntica del encabezado

El sistema MUST presentar el encabezado como un artÃ­culo accesible, con ceja editorial, tÃ­tulo principal Ãºnico y resumen legible.

#### Scenario: DiseÃ±o aprobado

- GIVEN el artÃ­culo editorial publicado con el contenido inicial.
- WHEN se muestra la pÃ¡gina Documentos.
- THEN se visualizan Â«TRANSPARENCIA Y CONSULTAÂ», Â«DOCUMENTOSÂ» y el resumen aprobado.

#### Scenario: Pantalla reducida

- GIVEN una pantalla de ancho reducido.
- WHEN se visualiza el encabezado.
- THEN el texto conserva contraste, orden de lectura y no produce desbordamiento horizontal.

#### Scenario: Contenido con marcado o texto hostil

- GIVEN tÃ­tulo o resumen con marcado, enlaces o texto no seguro.
- WHEN se renderiza el encabezado.
- THEN el sistema SHALL exponer solo texto saneado y escapado.

### Requirement: AsociaciÃ³n de la pÃ¡gina Documentos

El sistema MUST asociar la pÃ¡gina Documentos con el encabezado editorial y SHOULD conservar las partes globales de navegaciÃ³n y pie.

#### Scenario: Ruta Documentos

- GIVEN la pÃ¡gina Documentos usa su plantilla pÃºblica.
- WHEN WordPress resuelve la ruta.
- THEN el patrÃ³n de Documentos compone el encabezado editorial.

#### Scenario: Tema sin artÃ­culo editorial

- GIVEN la plantilla pÃºblica estÃ¡ activa pero falta el artÃ­culo editorial.
- WHEN WordPress compone la pÃ¡gina.
- THEN la navegaciÃ³n y el pie se mantienen disponibles.

#### Scenario: PatrÃ³n no disponible

- GIVEN una instalaciÃ³n donde no se carga el patrÃ³n Documentos.
- WHEN se procesa otra pÃ¡gina pÃºblica.
- THEN el sistema SHALL no alterar la composiciÃ³n de esa pÃ¡gina.

### Requirement: Contraste verificable en las vistas afectadas
Las vistas públicas afectadas MUST conservar foco perceptible y contraste WCAG 2.2 AA: al menos 4.5:1 para texto normal, 3:1 para texto grande y componentes sujetos al criterio no textual. La comprobación MUST incluir los fondos y estados usados a 320, 768, 1024, 1200 y 1440 px; MUST NOT atribuir cumplimiento integral al sitio sin auditoría integral.

#### Scenario: Colores y estados habituales
- DADO texto, enlaces, controles y foco en sus fondos habituales
- CUANDO se comprueba su contraste en los anchos objetivo
- ENTONCES cumplen el umbral aplicable y permiten identificar las acciones.

#### Scenario: Fondo alternativo o interacción
- DADO un componente sobre fondo claro alternativo o con foco
- CUANDO se comprueba el estado mostrado
- ENTONCES mantiene el contraste exigido sin depender exclusivamente del color.

#### Scenario: Contraste insuficiente
- DADO una combinación que incumple el umbral aplicable
- CUANDO se ejecuta la comprobación
- ENTONCES falla e identifica componente, estado, fondo y relación observada.

### Requirement: Sección principal
MUST conservar enlace padre Selecciones a /selecciones/, disclosure independiente. MUST ofrecer Balonmano Piso/Playa hacia /selecciones/?modalidad=Piso y /selecciones/?modalidad=Playa. MUST conservar secciones/logotipo. Padre MUST identificar seccion; solo hijo de modalidad efectiva MUST declarar aria-current="page". Fuera del listado MUST NOT marcar hijos actuales.

#### Scenario: 1
- Dado una página pública con navegación compartida
- Cuando se activa el padre o cada hijo
- Entonces padre abre Piso; hijos abren su modalidad.

#### Scenario: 2
- Dado un término adicional seleccionado en el listado
- Cuando se consulta la navegación
- Entonces Selecciones identifica la sección y ninguno de los dos hijos declara página actual.

#### Scenario: 3
- Dado una modalidad inválida en el archivo o una página fuera de Selecciones
- Cuando se presenta la navegación
- Entonces el archivo identifica Piso efectivo; la página externa no marca los hijos ni Selecciones como actuales.

### Requirement: Disclosure accesible
Submenu MUST abrir/cerrar con clic, touch, Enter/Espacio sin depender de hover. MUST usar enlaces normales, nombres accesibles, aria-expanded sincronizado y aria-controls asociado al contenedor. Tab/Shift+Tab MUST seguir orden logico; ocultos MUST excluir foco. Escape MUST cerrar submenu y enfocar disparador. Clic/foco exterior MUST cerrarlo sin robar foco. MUST NOT usar roles de menu de aplicacion.

#### Scenario: 4
- Dado submenú cerrado y foco en el disparador
- Cuando se pulsa Enter o Espacio y se recorre con Tab
- Entonces abre sin navegar, anuncia expansión y permite ambos enlaces.

#### Scenario: 5
- Dado submenú abierto
- Cuando se hace clic fuera o Tab sale de la navegación
- Entonces se cierra y conserva el foco en el destino elegido.

#### Scenario: 6
- Dado foco en un hijo y submenú abierto
- Cuando se pulsa Escape
- Entonces se cierra, anuncia contraído y el foco retorna al disparador sin alcanzar enlaces ocultos.

### Requirement: Panel móvil
Panel movil MUST desplazar contenido segun coPGt, sin overlay modal ni bloqueo de foco. Panel/disclosure MUST tener nombres/estados independientes. Touch en disclosure MUST expandir sin navegar; en enlaces MUST navegar. Escape en submenu MUST cerrar Selecciones y enfocar disparador; posterior o desde resto del panel MUST cerrarlo y enfocar boton. Cerrar panel MUST contraer Selecciones. Destinos MUST ser alcanzables sin JavaScript a 320 px.

#### Scenario: 7
- Dado panel cerrado
- Cuando se abre y se expande Selecciones mediante touch
- Entonces ambos estados anuncian expansión y el panel desplaza el hero con los dos hijos disponibles.

#### Scenario: 8
- Dado JavaScript deshabilitado a 320 px
- Cuando se recorren padre, Piso y Playa
- Entonces son alcanzables y navegan sin panel oculto inaccesible.

#### Scenario: 9
- Dado foco dentro del submenú móvil
- Cuando se pulsa Escape dos veces
- Entonces primero cierra Selecciones y enfoca su disparador; después cierra el panel y enfoca su botón.

### Requirement: Presentación fiel
Desktop MUST conservar jerarquia/opciones de yRBhE y modalidad activa distinguible; movil MUST conservar orden, panel claro y agrupacion de coPGt. Destinos/foco/cierres: decisiones aprobadas. Controles MUST medir minimo 44 x 44 px y tener foco visible no oculto por header. MUST cumplir contraste WCAG 2.2 AA: 4.5:1 texto normal, 3:1 grande/componentes aplicables; estado MUST NOT depender solo del color. MUST evitar solapamientos/desborde entre 320-1440px.

#### Scenario: 10
- Dado escritorio y móvil con Piso activo
- Cuando se observa y recorre el menú
- Entonces mantiene jerarquía aprobada, estado identificable y áreas operables de al menos 44 × 44 px.

#### Scenario: 11
- Dado 320 px y texto al 200 %
- Cuando se abre y recorre la navegación
- Entonces las opciones y el foco permanecen visibles sin desborde ni solapamiento.

#### Scenario: 12
- Dado un estado cuyo contraste incumple su umbral
- Cuando se evalúa su presentación
- Entonces falla el criterio correspondiente sin atribuir cumplimiento integral al sitio.

### Requirement: Colección pública
MUST reutilizar backend sin ampliar REST: solo labm_seleccion publish, filtrado por labm_modalidad. /selecciones/ MUST seleccionar Piso; ?modalidad=Piso y ?modalidad=Playa MUST seleccionar modalidad. Piso/Playa MUST permanecer disponibles vacios. Terminos adicionales con publicaciones MUST ser seleccionables en listado sin ampliar submenu. Contadores MUST totalizar modalidad publica independientemente de pagina; MUST NOT contar Clubes/otros estados.

#### Scenario: 1
- Dado siete Selecciones Piso publicadas y dos Clubes con ese término
- Cuando se abre `/selecciones/`
- Entonces se selecciona Piso, el contador es siete y únicamente aparecen Selecciones Piso publicadas.

#### Scenario: 2
- Dado Playa sin publicaciones y otro término con una Selección publicada
- Cuando se consulta Playa y después el término adicional
- Entonces Playa muestra cero y vacío; el término adicional permite consultar su publicación.

#### Scenario: 3
- Dado Selecciones privadas y borradores junto a publicaciones
- Cuando una persona anónima consulta cada modalidad
- Entonces filas/opciones/contadores excluyen datos restringidos.

### Requirement: Estado de consulta y paginación
MUST aceptar modalidad/pagina escalares validas. Modalidad desconocida, vacia o no escalar MUST resolver a Piso. Pagina ausente, no escalar, no entera positiva o desbordada MUST resolver a uno; superior al maximo MUST resolver a ultima disponible, o uno sin resultados. MUST mostrar hasta tres registros por pagina, fecha descendente y desempate estable. Paginacion MUST conservar modalidad; cambiarla MUST iniciar pagina uno. Parametros ajenos MUST NOT alterar coleccion.

#### Scenario: 4
- Dado siete publicaciones Playa, dos con igual fecha
- Cuando se recorre la paginación y se recarga
- Entonces las páginas contienen tres, tres y una filas, conservan Playa y mantienen orden sin duplicados.

#### Scenario: 5
- Dado dos páginas Piso y una petición de página nueve
- Cuando se abre la consulta y después se cambia a Playa
- Entonces se muestra la última página Piso y Playa inicia en uno.

#### Scenario: 6
- Dado modalidad desconocida o array y página array, negativa, fraccionaria o desbordada
- Cuando se solicita el listado
- Entonces los valores inválidos usan sus predeterminados sin avisos técnicos ni exposición de datos.

### Requirement: Filas editoriales
Filas MUST mostrar titulo, resumen editable (extracto o contenido publico resumido), medios/metadatos existentes. MUST NOT inventar resultados, fechas deportivas ni planteles. MUST NOT enlazar titulo/imagen/fila a fichas ni incluir VER PUBLICACION/CTA individual. MUST NOT crear rutas/plantillas de detalle. Imagen ausente/invalida MUST usar placeholder neutro sin medio roto; vacio MUST ofrecer recuperacion Piso/Playa sin datos ficticios.

#### Scenario: 7
- Dado una publicación con extracto e imagen
- Cuando se editan y recargan
- Entonces refleja cambios sin enlace individual.

#### Scenario: 8
- Dado una publicación sin extracto, imagen ni metadatos opcionales
- Cuando se presenta
- Entonces usa contenido resumido y placeholder; omite metadatos ausentes.

#### Scenario: 9
- Dado una imagen inaccesible o ninguna publicación coincidente
- Cuando se abre el listado
- Entonces no hay imagen rota; la colección vacía identifica la modalidad y permite cambiarla.

### Requirement: Fidelidad visual
MUST conservar hero negro por modalidad, introduccion clara, filas foto izquierda/texto derecha de neIe6/p1GT2, tipografia y colores LABM. Demo/CTA individuales quedan excluidos. Apilado movil, vacio/paginacion son adaptaciones aprobadas, no observadas; MUST conservar lectura, contraste y ausencia de desborde a 320, 768, 1024, 1200 y 1440 px.

#### Scenario: 10
- Dado contenido público completo a 1440 px
- Cuando se compara con los frames
- Entonces hero, introducción y filas conservan composición y jerarquía observadas.

#### Scenario: 11
- Dado el mismo listado a 320 px
- Cuando se presenta
- Entonces las filas se apilan y todos los contenidos y controles permanecen disponibles.

#### Scenario: 12
- Dado títulos largos y ampliación de texto al 200 %
- Cuando se visualiza el listado
- Entonces no aparecen recortes, superposiciones ni desborde horizontal global.
