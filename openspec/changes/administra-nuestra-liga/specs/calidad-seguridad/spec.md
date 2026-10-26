# Delta para Calidad y seguridad

## ADDED Requirements

### Requirement: Protección de la configuración de Nuestra Liga

El sistema MUST aplicar mínimo privilegio a la configuración editorial de Nuestra Liga, MUST validar y sanear cada valor antes de conservarlo y MUST escapar cada valor según el contexto público donde se presenta. Las acciones de guardado MUST rechazar solicitudes no autorizadas o no auténticas sin modificar la configuración válida existente. Las URLs públicas MUST limitarse a destinos seguros, incluyendo rutas internas válidas y protocolos web permitidos.

#### Scenario: Guardado autorizado y válido

- GIVEN (DADO) un usuario autorizado y valores válidos para los siete campos.
- WHEN (CUANDO) envía la configuración de Nuestra Liga.
- THEN (ENTONCES) se conservan los valores permitidos y la presentación pública los muestra sin contenido ejecutable ni datos ajenos al bloque.

#### Scenario: Solicitud no autorizada o no auténtica

- GIVEN (DADO) una solicitud de guardado proveniente de un usuario sin la capacidad requerida o sin la protección de autenticidad esperada.
- WHEN (CUANDO) intenta modificar Nuestra Liga.
- THEN (ENTONCES) la operación se rechaza, la configuración previa permanece intacta y no se revela información sensible.

#### Scenario: Saneamiento de texto

- GIVEN (DADO) texto con HTML, atributos, secuencias de control o contenido que excede las reglas editoriales.
- WHEN (CUANDO) se guarda y después se muestra el valor.
- THEN (ENTONCES) el valor conservado queda reducido a contenido permitido y la salida pública no ejecuta ni interpreta el contenido rechazado.

#### Scenario: Validación de destino

- GIVEN (DADO) una URL externa con protocolo no permitido, una URL malformada, una ruta interna válida y una URL web válida.
- WHEN (CUANDO) se validan y presentan sucesivamente esos destinos.
- THEN (ENTONCES) solo la ruta interna y la URL web permitida pueden producir un enlace público; los demás destinos no se imprimen como enlaces.

### Requirement: Evidencia focal de la entrega

La entrega SHALL conservar evidencia reproducible de la configuración editorial, permisos, saneamiento, fallback, renderizado público, destino del botón, accesibilidad y documentación. Cada escenario requerido MUST poder relacionarse con una comprobación observable y un resultado explícito; una herramienta no disponible MUST quedar registrada como no ejecutada, nunca como aprobada.

#### Scenario: Cobertura focal completa

- GIVEN (DADO) una ejecución que cubre administración autorizada, rechazo no autorizado, entradas límite, fallback y presentación pública.
- WHEN (CUANDO) se revisan sus resultados y referencias.
- THEN (ENTONCES) cada escenario requerido tiene evidencia trazable y el resultado distingue cumplimiento de cualquier comprobación no ejecutada.

#### Scenario: Fallo de una comprobación requerida

- GIVEN (DADO) que una comprobación de seguridad, fallback, accesibilidad o renderizado falla.
- WHEN (CUANDO) se evalúa la entrega.
- THEN (ENTONCES) el cambio no se considera completo y el resultado identifica el escenario incumplido sin convertirlo en advertencia informativa.

#### Scenario: Verificación de documentación

- GIVEN (DADO) el manual escrito y la cartilla visual actualizados.
- WHEN (CUANDO) se comparan con la pantalla LABM → Inicio y con el resultado público.
- THEN (ENTONCES) ambos documentos describen los mismos campos, ruta, fallback y forma de comprobación, sin instrucciones inexistentes.
