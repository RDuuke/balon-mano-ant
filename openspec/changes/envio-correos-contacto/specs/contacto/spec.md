# Especificación: Entrega y administración SMTP de Contacto

## Requisitos

### Requisito: Configuración SMTP híbrida segura

El sistema DEBE permitir que una persona con capacidad administrativa configure los valores SMTP no secretos y el destino de Contacto desde el panel de LABM. La contraseña de aplicación DEBE residir exclusivamente en la constante `LABM_SMTP_PASSWORD` de `wp-config.php`; el sistema NO DEBE guardarla, mostrarla ni aceptarla desde el panel, opciones, repositorio o registros.

#### Escenario: Guardado de valores no secretos

- DADO un administrador autorizado y una configuración SMTP válida sin contraseña
- CUANDO guarda el servidor, puerto, cifrado, remitente, nombre y destinatario
- ENTONCES el sistema conserva únicamente esos valores no secretos.

#### Escenario: Contraseña disponible fuera de la base de datos

- DADO una constante `LABM_SMTP_PASSWORD` no vacía en `wp-config.php`
- CUANDO el administrador abre la configuración SMTP
- ENTONCES el sistema indica que la contraseña está disponible sin revelarla.

#### Escenario: Acceso o configuración incompleta

- DADO un usuario sin la capacidad requerida o una configuración sin contraseña válida
- CUANDO intenta guardar o utilizar SMTP
- ENTONCES no se guardan secretos ni se revela la causa sensible.

### Requisito: Destinatarios canonicos en SMTP

El sistema DEBE usar `labm_smtp_settings.recipients` como unica fuente de destinatarios privados de Contacto. El panel del footer NO DEBE mostrar ni editar destinatarios. En la primera carga de SMTP, el sistema DEBE migrar sin perdida los destinatarios almacenados en el footer y el destinatario SMTP singular de la version anterior.

#### Escenario: Migracion segura del footer

- DADO que el footer contiene destinatarios historicos o SMTP contiene el campo singular heredado
- CUANDO se carga la configuracion SMTP actualizada
- ENTONCES todos los correos validos permanecen disponibles en `recipients` y el footer deja de ofrecer esa interfaz.

### Requisito: Transporte y encabezados del formulario

El sistema DEBE usar SMTP solo cuando exista una configuración híbrida completa. DEBE autenticar el remitente configurado y DEBE usar el correo válido de quien diligencia Contacto exclusivamente como `Reply-To`.

#### Escenario: Entrega desde un formulario válido

- DADO SMTP completo y un formulario válido con un correo de contacto
- CUANDO el transporte acepta el mensaje
- ENTONCES se envía al destinatario configurado con el remitente autenticado y el `Reply-To` del formulario.

#### Escenario: Correo de respuesta inválido

- DADO un formulario con un correo de contacto inválido
- CUANDO se intenta enviar el formulario
- ENTONCES el sistema lo rechaza sin intentar la entrega.

#### Escenario: Fallo de transporte

- DADO SMTP incompleto o un transporte que rechaza el mensaje
- CUANDO se procesa un formulario válido
- ENTONCES el usuario recibe un error seguro y puede reintentar sin duplicar una entrega confirmada.

#### Escenario: Aviso de entrega diferenciado

- DADO un formulario válido cuyo envío no puede completarse
- CUANDO la respuesta POST-Redirect-GET devuelve el estado `delivery`
- ENTONCES el formulario DEBE mostrar «No pudimos enviar el mensaje en este momento. Inténtalo de nuevo más tarde.», sin detallar SMTP, proveedor ni configuración, y NO DEBE presentar ese fallo como campos inválidos.

### Requisito: Plantilla institucional de correo de Contacto

El sistema DEBE entregar los mensajes de Contacto en HTML compatible con clientes de correo, usando tablas y estilos en línea. DEBE emplear la paleta institucional (verde `#AECD25`, negro `#202020` y fondo claro `#F3F6E8`), incluir el logo oficial como imagen MIME incrustada con Content-ID (CID), sin depender de que el destinatario alcance la URL del sitio, y conservar un encabezado textual de LABM como alternativa si la imagen no se puede mostrar. La plantilla DEBE escapar los datos remitidos, conservar sus saltos de línea y mantener el encabezado `Reply-To` de la persona remitente.

#### Escenario: Entrega HTML institucional

- DADO un formulario válido y un transporte disponible
- CUANDO Contacto construye el correo
- ENTONCES envía un cuerpo HTML con la marca LABM, los datos saneados del remitente y el mensaje, junto con `Content-Type: text/html; charset=UTF-8` y el `Reply-To` validado.

#### Escenario: Logo visible fuera del entorno local

- DADO un correo de Contacto con el logo institucional disponible en el servidor
- CUANDO PHPMailer compone el mensaje
- ENTONCES la plantilla referencia `cid:labm-contact-logo` y adjunta la imagen JPEG con ese CID, sin utilizar una URL local o privada; si el adjunto no está disponible, permanece visible el nombre institucional textual.

#### Escenario: Vista previa administrativa sin datos personales

- DADO un administrador autorizado que abre LABM > SMTP
- CUANDO consulta la vista previa de la plantilla
- ENTONCES puede ver una muestra estática protegida de la composición del correo sin realizar un envío, consultar secretos ni incluir datos de formularios reales.

### Requisito: Estado visible durante el envío público

El formulario de Contacto DEBE informar que la solicitud se está procesando desde que se confirma un envío válido en el navegador hasta que llega la respuesta. DEBE deshabilitar el control de envío durante ese intervalo para impedir solicitudes repetidas, sin alterar el POST, los campos ni el contrato de respuesta del servidor. El indicador DEBE estar dentro del botón y sustituir visualmente su etiqueta normal.

#### Escenario: Envío en curso

- DADO una persona que activa «Enviar mensaje»
- CUANDO el navegador inicia la solicitud del formulario
- ENTONCES el formulario expone `aria-busy="true"`, anuncia «Enviando mensaje…» dentro del botón, oculta la etiqueta «Enviar mensaje» y lo deshabilita hasta la respuesta.

#### Escenario: Validación o error de entrega

- DADO un formulario que el navegador rechaza por validación o el servidor devuelve un estado de error
- CUANDO la persona vuelve a ver el formulario
- ENTONCES no se activa el indicador para la validación local; tras la respuesta PRG el control vuelve a renderizarse habilitado con «Enviar mensaje» y permite corregir o reintentar la solicitud.

### Requisito: Prueba administrativa controlada

El sistema DEBE ofrecer a un administrador autorizado una acción para enviar una prueba al destinatario SMTP configurado. La acción DEBE requerir confirmación contra falsificación de solicitud y mostrar un resultado seguro.

#### Escenario: Prueba aceptada

- DADO SMTP completo, una contraseña disponible y un administrador autorizado
- CUANDO solicita la prueba
- ENTONCES el sistema intenta enviar un mensaje de prueba al destinatario configurado.

#### Escenario: Solicitud sin autorización

- DADO una solicitud sin capacidad administrativa o sin confirmación válida
- CUANDO intenta ejecutar la prueba
- ENTONCES el sistema no intenta enviar correo.

#### Escenario: Prueba fallida

- DADO una configuración incompleta o un rechazo del transporte
- CUANDO solicita la prueba
- ENTONCES el panel informa un fallo genérico sin incluir secretos, direcciones ni detalles del proveedor.

### Requisito: Protección operativa

El sistema NO DEBE exponer secretos SMTP ni contenido de Contacto en HTML público, respuestas, avisos administrativos o registros operativos.

#### Escenario: Inspección de valores y resultados

- DADO una configuración guardada o una prueba ejecutada
- CUANDO se inspeccionan el panel, la respuesta y los eventos permitidos
- ENTONCES no contienen la contraseña, el contenido del mensaje ni detalles SMTP sensibles.

#### Escenario: Guía de despliegue

- DADO un operador de Hostinger autorizado
- CUANDO sigue la guía de configuración
- ENTONCES define la contraseña solo en `wp-config.php` y no la añade a Git ni a la base de datos.

#### Escenario: Rotación de contraseña

- DADO que se rota la contraseña de aplicación de Gmail
- CUANDO el operador actualiza la constante del servidor
- ENTONCES las opciones no secretas existentes se conservan y ninguna contraseña queda persistida.
