# Delta: documentos y contacto

## ADDED Requirements

### Requirement: Restablecimiento inequívoco del catálogo
Cada acción visible para limpiar filtros MUST restablecer la consulta predeterminada, incluidos texto, categoría, año, orden y página. Las acciones del formulario y del estado vacío MUST ser distinguibles por su región y operables por teclado.

#### Scenario: Formulario con resultados
- DADO una consulta con filtros y página distintos de los predeterminados
- CUANDO se activa limpiar filtros en el formulario
- ENTONCES controles, URL y resultados reflejan la consulta predeterminada.

#### Scenario: Estado vacío con dos acciones
- DADO una consulta vacía con acciones de limpieza en formulario y estado vacío
- CUANDO se activa cualquiera por teclado
- ENTONCES ambas restablecen la consulta sin ambigüedad en su región.

#### Scenario: Parámetros manipulados
- DADO una URL con filtros o página no reconocidos
- CUANDO se restablece la consulta
- ENTONCES se eliminan esos valores sin revelar documentos restringidos ni errores técnicos.

### Requirement: Composición editorial verificable del encabezado
El encabezado de Documentos MUST conservar jerarquía, alineación y proporciones del diseño vigente reconciliado, con contenido legible a 320, 768, 1024, 1200 y 1440 px. La aceptación MUST identificar la referencia y medidas observables aprobadas antes de corregir expectativas dimensionales.

#### Scenario: Contenido editorial representativo
- DADO encabezado publicado y referencia de diseño reconciliada
- CUANDO se compara su presentación en los anchos objetivo
- ENTONCES respeta la composición y medidas aprobadas y mantiene sus acciones visibles.

#### Scenario: Texto largo
- DADO título o resumen más extensos que la demostración
- CUANDO se presenta el encabezado
- ENTONCES el contenido se adapta sin recorte, solapamiento ni desplazamiento horizontal global.

#### Scenario: Discrepancia sin referencia resuelta
- DADO una diferencia dimensional cuya referencia vigente no está reconciliada
- CUANDO se evalúa la corrección
- ENTONCES la aceptación queda pendiente con la discrepancia identificada y no se declara aprobada.
