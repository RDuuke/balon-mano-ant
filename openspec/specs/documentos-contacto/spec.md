# Especificación completa: Documentos y contacto

## Requisitos

### Requirement: Gestión segura de PDF
El sitio completo SHALL permitir a usuarios autorizados gestionar documentos con PDF obligatorio, título, categoría, fecha, descripción y referencia opcionales, estado editorial y prioridad opcional.

#### Scenario: Publicación válida
- DADO un usuario autorizado, campos obligatorios y un PDF válido
- CUANDO publica el documento
- ENTONCES aparece en el catálogo con sus metadatos públicos y acciones correctas.

#### Scenario: Borrador
- DADO un documento guardado como borrador
- CUANDO un visitante consulta catálogo, búsqueda o URL pública
- ENTONCES el documento no aparece ni queda enlazado por el sitio.

#### Scenario: Archivo inválido
- DADO un archivo cuya extensión o tipo real no es PDF, o excede el límite configurado
- CUANDO se intenta guardar o publicar
- ENTONCES se rechaza con un mensaje claro, sin exponer rutas ni detalles internos.

### Requirement: Catálogo público de documentos
El catálogo MUST combinar texto, categoría y año, conservar filtros al paginar, ordenar por fecha descendente por defecto y diferenciar las acciones Ver PDF y Descargar.

#### Scenario: Consulta combinada
- DADO documentos publicados de categorías y años distintos
- CUANDO un visitante combina texto, categoría y año
- ENTONCES solo obtiene coincidencias de todos los criterios y la paginación conserva la consulta.

#### Scenario: Consulta vacía
- DADO filtros válidos sin coincidencias
- CUANDO se procesa la búsqueda
- ENTONCES se muestra un estado vacío comprensible y una acción para limpiar filtros.

#### Scenario: Enlace no seguro
- DADO un documento con referencia de archivo ausente o no pública
- CUANDO se genera el catálogo
- ENTONCES no se expone una ruta interna ni una acción rota y se registra el problema para administración.

### Requirement: Ciclo de vida del adjunto
Eliminar un registro MUST NOT borrar silenciosamente un archivo compartido; cualquier eliminación física SHALL obedecer una política explícita y verificable.

#### Scenario: Archivo exclusivo
- DADO un adjunto usado únicamente por un documento y una política que permite borrarlo
- CUANDO un usuario autorizado confirma la eliminación
- ENTONCES registro y archivo siguen la política y queda evidencia del resultado.

#### Scenario: Archivo compartido
- DADO un adjunto referenciado por otro contenido
- CUANDO se elimina uno de sus registros
- ENTONCES el archivo permanece disponible para las referencias restantes.

#### Scenario: Usuario no autorizado
- DADO un usuario sin permiso de eliminación
- CUANDO intenta borrar registro o adjunto
- ENTONCES la acción se rechaza sin cambios ni revelación de rutas internas.

### Requirement: Contacto privado y resiliente
El sitio completo SHALL validar nombre, apellidos, correo, asunto y mensaje; MAY aceptar teléfono; SHALL aplicar antispam accesible y enviar al destinatario configurado sin retención indefinida no autorizada.

#### Scenario: Envío exitoso
- DADO datos válidos y un servicio de entrega disponible
- CUANDO el visitante envía el formulario
- ENTONCES recibe confirmación anunciable y recargar no duplica el envío.

#### Scenario: Validación accesible
- DADO campos ausentes o inválidos
- CUANDO se intenta enviar
- ENTONCES cada error queda asociado a su campo, el foco es predecible y no se transmite información.

#### Scenario: Error de entrega
- DADO un fallo del servicio de correo
- CUANDO se procesa una solicitud válida
- ENTONCES se muestra un mensaje accionable sin detalles técnicos y se registra el fallo sin el cuerpo personal innecesario.
