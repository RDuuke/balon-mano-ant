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

### Requirement: Catálogo público filtrable y accesible
El catálogo público MUST permitir consultar documentos publicados por texto libre, categoría, año y orden; MUST conservar los criterios al cambiar de página; y MUST presentar acciones diferenciadas para ver y descargar el PDF.

#### Scenario: Consulta combinada con resultados
- DADO documentos publicados que difieren en título, categoría y fecha editorial
- CUANDO el visitante combina texto, categoría, año y un orden disponible
- ENTONCES solo se muestran coincidencias de todos los criterios en el orden elegido.

#### Scenario: Consulta sin filtros
- DADO documentos publicados válidos y ningún criterio seleccionado
- CUANDO el visitante abre el catálogo
- ENTONCES se muestran los documentos según el orden predeterminado y con paginación cuando corresponda.

#### Scenario: Filtros conservados al paginar
- DADO una consulta con texto, categoría, año y orden que ocupa varias páginas
- CUANDO el visitante navega a otra página
- ENTONCES la URL y los resultados conservan todos los criterios activos.

#### Scenario: Categoría o año sin coincidencias
- DADO criterios válidos que no coinciden con ningún documento publicado
- CUANDO el visitante procesa la consulta
- ENTONCES aparece un estado vacío que explica el resultado y ofrece limpiar los filtros.

#### Scenario: Documento sin fecha editorial
- DADO un documento publicado con PDF válido, categoría y fecha editorial ausente
- CUANDO se muestra el catálogo sin filtro de año
- ENTONCES el documento permanece visible, omite la fecha y conserva sus acciones disponibles.

#### Scenario: Documento sin fecha en filtro por año
- DADO un documento publicado cuya fecha editorial está ausente
- CUANDO el visitante aplica un año
- ENTONCES el documento no se presenta como coincidencia de ese año.

#### Scenario: Metadata pública de la tarjeta
- DADO un documento publicado con categoría, fecha editorial y PDF cuyo tamaño está disponible
- CUANDO se muestra su tarjeta
- ENTONCES presenta categoría, título, fecha y tamaño legibles, además de “Ver PDF” y “Descargar”.

#### Scenario: Código documental no disponible
- DADO un documento publicado sin código editorial definido en la metadata pública
- CUANDO se muestra su tarjeta
- ENTONCES no inventa un código ni expone identificadores internos, y presenta el resto de metadata disponible.

#### Scenario: Diseño responsive y teclado
- DADO un visitante que usa una pantalla estrecha o navegación por teclado
- CUANDO consulta filtros, tarjetas o paginación
- ENTONCES los controles se pueden usar sin desbordamiento, el foco es visible y las acciones siguen siendo operables.

#### Scenario: Archivo PDF no disponible
- DADO un documento publicado cuya referencia PDF falta, no es pública o no es válida
- CUANDO se genera el catálogo
- ENTONCES no se muestra un enlace roto ni una ruta interna, y el documento no ofrece acciones PDF inseguras.

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
