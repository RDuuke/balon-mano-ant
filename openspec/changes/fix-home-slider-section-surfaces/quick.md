# Quick Fix: Slider y superficies del inicio

## Objetivo

Corregir la presentación visual del slider principal para que no produzca desbordamiento vertical visible, sus indicadores se representen y comuniquen el estado activo conforme al diseño Pen, y el conjunto de indicadores y controles quede alineado en una única franja inferior. Aplicar además a cada sección de la portada el color de fondo exacto definido en `design/labm-wordpress-mockup.pen`.

## Archivos afectados

- `wp-content/themes/labm/style.css`
- `wp-content/themes/labm/functions.php` solo si el agrupamiento semántico actual impide alinear indicadores y controles sin posicionamiento frágil.
- `tests/e2e/home.spec.ts`
- `tests/php/HomePresentationTest.php` solo si cambia el marcado del grupo de controles.

## Blueprint

1. Ajustar el contenedor y los slides para conservar la altura responsive vigente sin barra ni contenido que sobresalga verticalmente del hero. Mantener visible la información editorial normal y la estabilidad entre slides; si el contenido administrado excede la capacidad segura, la implementación debe activar una salvaguarda interna sin alterar la altura exterior.
2. Estilizar exclusivamente los botones `data-labm-slide-to` como indicadores circulares perfectos de `10 × 10 px`: inactivos con blanco al 60 % y activo lima `#AECD25`, tomando `aria-current="true"` como fuente de estado. Conservar un área interactiva suficiente mediante una caja transparente o pseudo-elemento sin convertir el indicador visible en un cuadrado.
3. Ubicar indicadores y botones anterior/pausa/siguiente en la misma franja inferior del slider, centrados verticalmente y respetando el ancho de contenido máximo de `1200px`. En escritorio, los indicadores quedan a la izquierda y los controles a la derecha, conforme a Pen; en móvil se conserva una composición compacta sin solapamientos ni desborde.
4. Aplicar las superficies exactas del frame `Inicio — Desktop`: presentación `#FFFFFF`, clubes asociados `#F3F6E8`, evento destacado `#000000`, últimas noticias `#FFFFFF`, CTA de vinculación `#000000` y aliados oficiales `#F3F6E8`. El slider conserva su imagen y overlay; encabezado y pie mantienen sus superficies existentes.
5. Evitar selectores posicionales como `:nth-child` para estas superficies. Usar las clases o atributos semánticos de cada sección para que la ausencia de contenido editorial no cambie los colores de las secciones restantes.

## Riesgos

- El contenido editorial extraordinariamente largo puede requerir una salvaguarda interna; se debe comprobar que esta no vuelva a exponer una barra vertical en el estado normal mostrado en Pen.
- Los indicadores deben seguir siendo botones accesibles y conservar `aria-current`; el cambio visual no debe reducir el área interactiva ni romper la navegación existente.
- Si para conseguir la alineación exacta resulta necesario reestructurar más de `style.css` y el bloque local de controles en `functions.php`, se detendrá APPLY y se reevaluará la elegibilidad QUICK.

## Verificacion

- Añadir aserciones Playwright en 320, 390, 768, 1024, 1200 y 1440 px que comprueben: ausencia de desborde horizontal global y de desborde vertical visible en el slider; altura exterior estable al navegar; todos los indicadores visibles como círculos de `10 × 10 px`; indicador activo lima `#AECD25` e inactivos blancos al 60 %; cambio de `aria-current` al usar siguiente y al pulsar un indicador; alineación vertical de indicadores con anterior/pausa/siguiente; ausencia de solapamientos.
- Comprobar por estilos computados los fondos: presentación `rgb(255, 255, 255)`, clubes `rgb(243, 246, 232)`, evento `rgb(0, 0, 0)`, actualidad `rgb(255, 255, 255)`, vinculación `rgb(0, 0, 0)` y aliados `rgb(243, 246, 232)`.
- Ejecutar la prueba focalizada de portada y después la suite Playwright de experiencia pública.
- Ejecutar PHPUnit focalizado de presentación si cambia `functions.php`; en caso contrario, confirmar que el marcado no cambió.
- Realizar una captura visual de la portada a 1440 px y compararla con `design/labm-wordpress-mockup.pen`, verificando especialmente la franja inferior del slider y la secuencia de fondos.

## Gate unico de aprobacion

La aprobación de este blueprint autoriza pasar directamente a APPLY, implementar únicamente este alcance y ejecutar la verificación indicada. No autoriza rediseños adicionales ni cambios de contenido editorial.
