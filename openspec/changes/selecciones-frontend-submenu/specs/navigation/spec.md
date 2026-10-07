# Delta: navegación de Selecciones

RFC2119: MUST obligatorio; MUST NOT prohibido.

## ADDED Requirements

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
- Entonces abre sin navegar, anuncia expansi?n y permite ambos enlaces.

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
