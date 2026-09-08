# Quick Fix: Ajustar la sección de vinculación del inicio

## Objetivo

Hacer que el artículo de vinculación ubicado después de «Últimas noticias» reproduzca la composición visual aprobada: fondo negro de ancho completo, acento vertical verde, contenido textual alineado a la izquierda y llamada a la acción compacta alineada a la derecha en escritorio, manteniendo una adaptación legible y operable en pantallas pequeñas.

## Archivos afectados

- `wp-content/themes/labm/style.css`: estilos específicos y responsivos de `.labm-home-join` y sus bloques internos.
- `tests/e2e/home.spec.ts`: únicamente si la cobertura existente no permite comprobar la estructura o el comportamiento responsivo de la sección.

## Blueprint

1. Añadir estilos acotados a `.labm-home-join` para distribuir el encabezado, el texto y el bloque de botones con una cuadrícula de escritorio equivalente al diseño de referencia.
2. Incorporar el acento vertical verde mediante borde o pseudoelemento, conservando el fondo negro y el contraste de texto existente.
3. Ajustar tipografía, espaciado y botón para aproximar el título en mayúsculas, el texto secundario y la llamada «Quiero vincularme» al tamaño y la geometría mostrados, sin cambiar el contenido ni el destino `/contacto/`.
4. Definir una variante responsiva que apile contenido y acción sin desbordamientos ni pérdida del indicador verde.
5. Mantener los selectores limitados a la sección de vinculación para no alterar botones, encabezados u otras secciones del tema.

## Riesgos

- La tipografía exacta depende de las fuentes globales ya configuradas en el tema; el ajuste no incorporará una fuente nueva.
- El ancho disponible y el salto del título pueden variar respecto a la captura según el viewport y el contenido administrado.
- Si fuese necesario cambiar el marcado PHP o el contenido editorial para conseguir la composición, se detendrá el alcance quick y se reevaluará antes de ampliar archivos.

## Verificacion

- Ejecutar las pruebas focales de presentación del inicio y la prueba E2E del Home que resulten aplicables.
- Verificar en escritorio que el acento verde quede a la izquierda, el título y el texto formen un bloque, y el botón se alinee a la derecha sobre fondo negro.
- Verificar en viewport móvil que la sección se apile, conserve contraste y no produzca desbordamiento horizontal.
- Comprobar foco visible, área interactiva y contraste del enlace a `/contacto/`.
- Ejecutar los controles de formato o análisis estático aplicables al CSS y confirmar finales de línea LF en cada archivo modificado.
