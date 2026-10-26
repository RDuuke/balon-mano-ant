# Delta para Experiencia Pública

## ADDED Requirements

### Requirement: Presentación administrable de Nuestra Liga

La portada MUST presentar el bloque Nuestra Liga en su ubicación, orden y jerarquía visual actuales. El bloque SHALL mostrar el rótulo de sección, título principal, descripción, indicador numérico, etiqueta del indicador y botón usando la configuración efectiva disponible. El botón MUST conservar su nombre accesible y MAY omitirse cuando no exista un destino válido; los demás contenidos MUST seguir siendo utilizables.

#### Scenario: Valores administrados visibles

- GIVEN (DADO) una configuración válida con valores personalizados para los siete campos.
- WHEN (CUANDO) un visitante abre Inicio.
- THEN (ENTONCES) ve los valores personalizados en Nuestra Liga, conserva la jerarquía existente y el botón apunta al destino configurado.

#### Scenario: Fallback de configuración

- GIVEN (DADO) que la configuración no existe o que uno o más campos están vacíos.
- WHEN (CUANDO) un visitante abre Inicio.
- THEN (ENTONCES) cada campo afectado muestra su valor actual predeterminado, el destino predeterminado permanece disponible cuando es válido y el bloque no queda roto ni presenta marcadores técnicos.

#### Scenario: URL no válida

- GIVEN (DADO) una configuración cuyo destino del botón está vacío, usa un protocolo no permitido o no representa una ruta válida.
- WHEN (CUANDO) se renderiza Nuestra Liga.
- THEN (ENTONCES) el destino inseguro no se imprime como enlace y el botón se oculta cuando no existe un destino válido, sin afectar el resto del bloque.

#### Scenario: Texto administrado con marcado

- GIVEN (DADO) valores de texto que contienen etiquetas, atributos o contenido no permitido.
- WHEN (CUANDO) una persona visita Inicio.
- THEN (ENTONCES) solo se muestra texto seguro y visible, sin ejecutar ni interpretar marcado introducido desde la configuración.

### Requirement: Accesibilidad y composición conservadas

La sección Nuestra Liga MUST conservar una jerarquía semántica coherente, nombres accesibles, foco visible y la relación comprensible entre el contenido y el botón. MUST mantener la lectura y el uso del bloque a 320, 768, 1024 y 1440 píxeles, sin desborde horizontal, superposición ni pérdida de información por textos administrados más extensos.

#### Scenario: Recorrido con teclado

- GIVEN (DADO) Nuestra Liga con valores predeterminados o personalizados.
- WHEN (CUANDO) una persona recorre Inicio mediante teclado.
- THEN (ENTONCES) alcanza el botón cuando existe, percibe su foco y entiende su nombre y destino sin depender únicamente del color o de un efecto visual.

#### Scenario: Texto extenso en vista responsive

- GIVEN (DADO) textos administrados más largos que los valores predeterminados.
- WHEN (CUANDO) se observa Inicio en los anchos objetivo y con ampliación de texto.
- THEN (ENTONCES) el contenido permanece legible, conserva el orden del bloque y no produce recortes, solapamientos ni desborde horizontal.

#### Scenario: Componente administrativo no disponible

- GIVEN (DADO) que la configuración administrada no puede consultarse durante la carga pública.
- WHEN (CUANDO) un visitante abre Inicio.
- THEN (ENTONCES) Nuestra Liga utiliza valores predeterminados seguros o se presenta de forma coherente, sin error fatal ni exposición de detalles técnicos.
