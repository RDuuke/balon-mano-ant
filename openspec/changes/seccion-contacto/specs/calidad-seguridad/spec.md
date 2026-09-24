# Delta para Calidad y seguridad

## ADDED Requirements

### Requirement: Evidencia automatizada de Contacto
La verificación MUST cubrir la ruta pública, datos institucionales, responsividad a 320, 768, 1024, 1200 y 1440 px, recorrido por teclado, estados anunciables y reglas de entrega para producción y no producción. SHALL verificar que los destinatarios de prueba no se exponen en salida pública ni configuración versionada.

#### Scenario: Flujo público satisfactorio
- DADO datos institucionales y un formulario válido
- CUANDO se ejecutan las comprobaciones focales de Contacto
- ENTONCES la ruta, contenido, envío y mensajes cumplen el contrato declarado.

#### Scenario: Anchos y controles representativos
- DADO Contacto en cada ancho objetivo
- CUANDO se recorren sus enlaces, formulario y mensajes
- ENTONCES no hay desborde horizontal, solapamientos, foco perdido ni controles inaccesibles.

#### Scenario: Regresión de seguridad o accesibilidad
- DADO una copia de prueba expuesta, un envío no autorizado o un error inaccesible
- CUANDO se ejecuta la comprobación aplicable
- ENTONCES falla e identifica el contrato incumplido sin revelar contenido sensible.

### Requirement: Configuración segura de destinatarios
El sistema MUST determinar el destinatario institucional de forma estable y los destinatarios de prueba exclusivamente desde configuración segura de entornos no productivos. La verificación MUST rechazar la activación de copias de prueba en producción y cualquier presencia de sus direcciones en contenido público.

#### Scenario: Producción sin copias de prueba
- DADO el entorno marcado como productivo
- CUANDO se procesa o verifica un envío de Contacto
- ENTONCES solo se autoriza la entrega al destinatario institucional.

#### Scenario: Desarrollo con configuración segura
- DADO un entorno no productivo con copias de prueba configuradas fuera del código versionado
- CUANDO se verifica el flujo de entrega
- ENTONCES las copias son elegibles sin aparecer en archivos públicos o de frontend.

#### Scenario: Entorno o configuración ambiguos
- DADO que no puede determinarse el entorno o falta configuración segura
- CUANDO se intenta habilitar copias de prueba
- ENTONCES se deshabilitan las copias y la verificación informa la condición sin filtrar direcciones.
