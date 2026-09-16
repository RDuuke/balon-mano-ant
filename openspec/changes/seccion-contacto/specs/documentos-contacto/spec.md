# Delta para Documentos y contacto

## MODIFIED Requirements

### Requirement: Contacto privado y resiliente
El sitio MUST validar nombre, apellidos, correo, asunto, mensaje y consentimiento explícito; MAY aceptar teléfono. SHALL enviar las solicitudes válidas a `info@balonmanoantioquia.com`, sin retener su contenido fuera de lo necesario para la entrega. Solo en entornos no productivos, podrá enviar copias a destinatarios definidos mediante configuración segura no versionada; MUST NOT mostrar ni incorporar esos destinatarios en el HTML ni activarlos en producción.

#### Scenario: Envío válido en producción
- DADO campos obligatorios, consentimiento y datos válidos en producción
- CUANDO el visitante envía el formulario una vez
- ENTONCES la solicitud se entrega al correo institucional y recibe confirmación anunciable sin duplicarse al recargar.

#### Scenario: Envío no productivo con copias configuradas
- DADO un entorno no productivo y destinatarios de prueba definidos de forma segura
- CUANDO se procesa una solicitud válida
- ENTONCES se entrega al correo institucional y las copias operan sin exponerse en la interfaz ni en el repositorio.

#### Scenario: Falla de entrega o configuración inválida
- DADO un servicio de correo no disponible o una configuración de destinatarios inválida
- CUANDO se procesa una solicitud válida
- ENTONCES no se confirma el envío, se comunica un error accionable y no se revelan datos técnicos ni personales innecesarios.

### Requirement: Validación, privacidad y antispam de Contacto
El formulario MUST rechazar entradas ausentes, malformadas o manipuladas antes de entregarlas; SHALL aplicar una medida antispam que no impida el uso con teclado o tecnología asistiva. Los datos enviados MUST usarse exclusivamente para responder la consulta y la página SHALL enlazar la información de privacidad aplicable.

#### Scenario: Validación de campos y consentimiento
- DADO un visitante con valores válidos y consentimiento marcado
- CUANDO envía el formulario
- ENTONCES la solicitud avanza a entrega y los datos se tratan solo para la consulta.

#### Scenario: Datos incompletos o no válidos
- DADO uno o más campos obligatorios inválidos o consentimiento ausente
- CUANDO el visitante intenta enviar
- ENTONCES no se transmite la solicitud y cada error se asocia al campo correspondiente.

#### Scenario: Solicitud automatizada o manipulada
- DADO una solicitud que incumple la protección antispam o integridad esperada
- CUANDO intenta procesarse
- ENTONCES se rechaza sin entregar correo, revelar la regla de protección ni confirmar un envío inexistente.
