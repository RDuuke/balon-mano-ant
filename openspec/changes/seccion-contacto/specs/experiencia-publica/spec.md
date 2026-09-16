# Delta para Experiencia pública

## ADDED Requirements

### Requirement: Página institucional de Contacto
El sitio MUST publicar la ruta `/contacto/` con el encabezado editorial aprobado, datos institucionales, redes disponibles, acceso a la ubicación y el formulario de contacto. La página SHALL reutilizar el header y footer globales ya publicados, sin requerir su creación ni alteración.

#### Scenario: Vista completa publicada
- DADO un visitante anónimo y la ruta `/contacto/`
- CUANDO carga la página
- ENTONCES ve el encabezado, correo, teléfono, dirección, redes, ubicación y formulario en el orden aprobado.

#### Scenario: Pantalla estrecha o contenido largo
- DADO un viewport de 320 px o una ampliación de texto
- CUANDO se presenta Contacto
- ENTONCES el contenido se adapta sin desborde horizontal, solapamiento ni pérdida de acciones.

#### Scenario: Ruta o recurso público no disponible
- DADO que la ruta no puede resolverse o falta un recurso enlazado
- CUANDO un visitante intenta abrirlo
- ENTONCES recibe un estado comprensible sin errores técnicos, secretos ni enlaces rotos.

### Requirement: Datos de contacto veraces y accionables
La página MUST mostrar `info@balonmanoantioquia.com`, `3233212981` y `Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos, Medellín, Colombia`; SHALL ofrecer solo las redes sociales institucionales disponibles y un enlace accionable de ubicación.

#### Scenario: Datos y enlaces correctos
- DADO la página de Contacto publicada
- CUANDO un visitante revisa los datos institucionales
- ENTONCES encuentra los valores establecidos y enlaces con destino y nombre accesible.

#### Scenario: Red social no disponible
- DADO que una red no está definida como disponible
- CUANDO se muestra la lista de redes
- ENTONCES no aparece un icono, enlace ni marcador vacío para ella.

#### Scenario: Destino de enlace inválido
- DADO un destino de red o ubicación inválido
- CUANDO se genera la página
- ENTONCES el enlace se omite o queda inactivo de forma comprensible sin dirigir a una ubicación insegura.

### Requirement: Accesibilidad de Contacto
La página SHALL conservar estructura semántica, contraste perceptible, foco visible, orden de tabulación lógico y nombres accesibles; sus mensajes de formulario MUST anunciarse sin depender solo del color.

#### Scenario: Recorrido por teclado
- DADO un visitante que navega solo con teclado
- CUANDO recorre Contacto de inicio a fin
- ENTONCES alcanza enlaces, campos y acciones en orden lógico con foco perceptible.

#### Scenario: Tecnología asistiva
- DADO un lector de pantalla y un mensaje de estado
- CUANDO se completa o rechaza un envío
- ENTONCES el resultado se anuncia con texto comprensible y asociado al contexto.

#### Scenario: Control sin nombre o contraste insuficiente
- DADO un control sin nombre accesible o un estado perceptible solo por color
- CUANDO se ejecuta la comprobación de accesibilidad aplicable
- ENTONCES la comprobación falla e identifica el requisito incumplido.
