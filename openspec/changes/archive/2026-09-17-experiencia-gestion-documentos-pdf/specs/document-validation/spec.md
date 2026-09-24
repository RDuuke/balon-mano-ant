# Especificación completa: Validación administrativa de documentos

## Requisitos

### Requisito: Integridad obligatoria al publicar o actualizar

El sistema MUST exigir título no vacío y PDF válido para publicar o actualizar un Documento, tanto en solicitudes REST como en el editor clásico; la decisión autoritativa MUST ocurrir en el servidor.

#### Escenario: Guardado válido
- DADO un usuario autorizado, un título y un PDF válido
- CUANDO publica o actualiza por cualquier canal administrativo admitido
- ENTONCES el servidor acepta el estado solicitado y persiste los datos validados

#### Escenario: Actualización parcial
- DADO un Documento que ya tiene título y PDF válidos
- CUANDO una solicitud modifica solo otro dato permitido
- ENTONCES se evalúa el estado efectivo y se conserva lo no enviado

#### Escenario: Dato obligatorio ausente
- DADO que falta el título o el PDF válido
- CUANDO se intenta publicar o actualizar
- ENTONCES se bloquea la operación con errores accionables por campo y sin mutación parcial

### Requisito: Validez verificable del PDF

El sistema MUST aceptar solo un adjunto PDF accesible por el usuario, con tipo declarado de PDF, archivo legible, firma PDF válida y tamaño no superior al límite efectivo.

#### Escenario: PDF auténtico
- DADO un adjunto legible que cumple tipo, firma y tamaño
- CUANDO el servidor lo valida
- ENTONCES lo reconoce como PDF válido

#### Escenario: Adjunto inaccesible
- DADO un PDF válido al que el usuario no tiene acceso
- CUANDO intenta asociarlo
- ENTONCES la operación se rechaza sin revelar información restringida

#### Escenario: Contenido o archivo inválido
- DADO un ID inexistente, archivo ilegible, tipo distinto, firma falsa o exceso de tamaño
- CUANDO se valida la asociación
- ENTONCES se rechaza y se explica una acción segura para corregirla

### Requisito: Autorización y protección del guardado

El sistema MUST verificar capacidad, intención autenticada de la solicitud y datos saneados antes de aceptar cambios administrativos.

#### Escenario: Solicitud autorizada
- DADO un usuario con capacidad y una solicitud administrativa auténtica
- CUANDO envía valores válidos
- ENTONCES solo los valores saneados se consideran para guardar

#### Escenario: Valor inesperado
- DADO un usuario autorizado que envía valores con formato inesperado
- CUANDO se validan
- ENTONCES no se ejecuta contenido activo ni se persiste información no saneada

#### Escenario: Solicitud no autorizada
- DADO un usuario sin capacidad o una solicitud sin protección antifalsificación válida
- CUANDO intenta modificar el Documento
- ENTONCES se rechaza sin cambiar sus datos

### Requisito: Fecha opcional del documento

El sistema MUST ofrecer «Fecha del documento» como dato opcional elegido por el usuario y, si existe, MUST validar una fecha calendario y persistirla como `Y-m-d`.

#### Escenario: Fecha válida
- DADO una fecha calendario seleccionada
- CUANDO se guarda el Documento
- ENTONCES se persiste exactamente en formato `Y-m-d`

#### Escenario: Fecha vacía
- DADO que el usuario no define fecha
- CUANDO guarda datos por lo demás válidos
- ENTONCES se acepta sin sustituirla por publicación o carga

#### Escenario: Fecha imposible
- DADO un valor que no representa una fecha calendario real
- CUANDO se intenta guardar
- ENTONCES se bloquea la mutación y se muestra un mensaje asociado al campo

## Límites

No se especifican cambios de presentación pública.
