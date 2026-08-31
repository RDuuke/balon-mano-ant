# Delta para Experiencia pública

## MODIFIED Requirements

### Requirement: Identidad visual responsive
Las vistas públicas MUST conservar la identidad y las alineaciones aprobadas en Pen. Su contenido principal MUST tener un ancho máximo de 1200 px y permanecer centrado cuando el viewport sea mayor; fondos decorativos MAY extenderse al ancho completo. Los gutters y alineaciones MUST ser consistentes a 320, 768, 1024, 1200 y 1440 px.

#### Scenario: Pantalla mayor que el ancho máximo
- DADO un viewport de 1440 px y contenido representativo
- CUANDO se presenta una vista pública
- ENTONCES el contenido principal no supera 1200 px y sus márgenes laterales son iguales.

#### Scenario: Cambio entre anchos objetivo
- DADO la misma vista a 320, 768, 1024 y 1200 px
- CUANDO se mide la posición de sus secciones principales
- ENTONCES respetan gutters coherentes, alineación común y controles visibles.

#### Scenario: Contenido que intenta desbordar
- DADO texto largo, medios amplios o ampliación de texto
- CUANDO el contenido excedería el espacio disponible
- ENTONCES no causa desplazamiento horizontal global, solapamientos ni pérdida de controles.

### Requirement: Slider principal accesible y estable
El slider SHALL mantener una altura exterior estable mientras el usuario navega entre ítems dentro del mismo viewport, aunque varíen el texto o los medios. El contenido esencial y los controles MUST permanecer visibles y operables, y la preferencia de movimiento reducido MUST conservarse.

#### Scenario: Navegación entre ítems diferentes
- DADO ítems publicados con distintas cantidades de texto y proporciones de medios
- CUANDO el visitante usa anterior, siguiente e indicadores
- ENTONCES la altura exterior del slider no cambia y el ítem activo resulta perceptible.

#### Scenario: Slider en anchos objetivo
- DADO el slider a 320, 768, 1024, 1200 y 1440 px
- CUANDO se recorren todos sus ítems
- ENTONCES cada ancho mantiene una altura estable sin ocultar contenido esencial ni controles.

#### Scenario: Contenido extremo o medio no disponible
- DADO un ítem con contenido largo o un medio que no puede mostrarse
- CUANDO el ítem se activa
- ENTONCES el slider conserva su altura, ofrece una presentación legible y no bloquea la navegación.

### Requirement: Experiencia responsive verificable
Las vistas públicas MUST mantener centrado, gutters, ausencia de desborde y estabilidad del slider verificables mediante recorridos end-to-end en los anchos objetivo. Una desviación geométrica mayor a 1 px entre márgenes que deban ser iguales, o cualquier cambio en la altura exterior del slider durante un recorrido, MUST fallar la comprobación correspondiente.

#### Scenario: Comprobación geométrica satisfactoria
- DADO una vista pública cargada a 1440 px
- CUANDO se miden contenedor, márgenes y ancho del viewport
- ENTONCES el contenedor mide como máximo 1200 px y la diferencia entre márgenes no supera 1 px.

#### Scenario: Recorrido responsive completo
- DADO contenido representativo en cada ancho objetivo
- CUANDO la comprobación recorre secciones y todos los ítems del slider
- ENTONCES no detecta desborde global, desalineación ni variación de altura del slider.

#### Scenario: Regresión geométrica
- DADO una vista cuyo contenido excede 1200 px, queda descentrado o cuyo slider cambia de altura
- CUANDO se ejecuta la comprobación end-to-end aplicable
- ENTONCES el resultado falla e identifica el ancho y la condición incumplida.

## Exclusiones

Este cambio MUST NOT rediseñar contenido, identidad o navegación; MUST NOT cambiar tipos de contenido, datos ni panel administrativo; y MUST NOT sustituir el slider por una librería externa.
