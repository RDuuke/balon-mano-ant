# Quick Fix: Alinear el CTA de Nosotros Desktop con Pencil

## Objetivo

Ajustar el bloque compartido «Haz parte del balonmano antioqueño» que aparece después del equipo en «Nosotros — Desktop» para que reproduzca el diseño de Pencil: sección negra de 1440 × 300 px, contenido centrado de 1200 × 180 px, acento verde izquierdo de 8 px, bloque de texto con la jerarquía tipográfica y espaciado previstos, y botón alineado a la derecha. También se deben igualar los textos visibles del diseño: descripción «Conecta con la Liga, sus clubes y procesos deportivos.» y llamada a la acción «CONTÁCTANOS», conservando el destino `/contacto/` y la accesibilidad.

La referencia inspeccionada es el frame `BU8oD` («Nosotros — Desktop») de `design/labm-wordpress-mockup.pen`, específicamente `myCtb` («CTA Nosotros») y `cxREz` («Contenido CTA Vinculación»). Pencil confirma que no hay elementos colapsados, recortados ni desbordados en este bloque. El mismo patrón aparece en Inicio (`wnmV0`/`RRzIn`), por lo que el ajuste debe mantenerse en el único render compartido y no introducir una variante exclusiva para Nosotros. La fuente Barlow Condensed se incorporará como activo local del tema y se declarará mediante `theme.json`, evitando una dependencia externa en tiempo de ejecución.

## Archivos afectados

- `wp-content/themes/labm/functions.php`: actualizar el contenido del helper compartido `labm_theme_render_join_cta()` para igualar la descripción y la etiqueta del botón de Pencil, sin cambiar el enlace `/contacto/`.
- `wp-content/themes/labm/theme.json`: registrar Barlow Condensed como familia tipográfica local con `fontFace`, peso variable adecuado y `fontDisplay: swap`, siguiendo la configuración tipográfica nativa de WordPress.
- `wp-content/themes/labm/assets/fonts/barlow-condensed-latin-wght-normal.woff2`: incorporar el archivo variable local de Barlow Condensed, obtenido de la distribución oficial y conservando su licencia OFL en el tema.
- `wp-content/themes/labm/assets/fonts/OFL.txt`: conservar junto al binario el texto de licencia incluido en la distribución oficial de Barlow Condensed.
- `wp-content/themes/labm/style.css`: afinar la geometría, tipografía y distribución de `.labm-home-join` en escritorio, preservando su comportamiento responsive actual.
- `tests/e2e/public-experience.spec.ts`: revisar y ajustar únicamente las expectativas literales necesarias para cubrir el contenido compartido y la paridad visual básica en viewport desktop, si las aserciones existentes no alcanzan.

## Blueprint

1. Mantener `labm_theme_render_join_cta()` como única fuente para Inicio y Nosotros; sustituir el párrafo por «Conecta con la Liga, sus clubes y procesos deportivos.» y el texto del botón por «Contáctanos», conservando la semántica de `section`, el `h2`, el enlace y `data-labm-section="vinculacion"`.
2. En escritorio, llevar `.labm-home-join` a una altura visual de 300 px con 60 px de espacio vertical y ancho útil de 1200 px centrado; modelar el borde/acento izquierdo con 8 px de ancho y 180 px de alto.
3. Hacer coincidir el desplazamiento interior del contenido desde el acento (52 px), el ancho disponible del bloque textual (760 px), la separación título-descripción (8 px) y la alineación vertical del botón de 48 px a la derecha.
4. Añadir al tema el archivo variable WOFF2 de Barlow Condensed desde su distribución oficial, junto con la licencia OFL correspondiente. Registrar la familia en `theme.json` mediante `settings.typography.fontFamilies[].fontFace`, con origen `file:./assets/fonts/barlow-condensed-latin-wght-normal.woff2`, estilo normal, rango de pesos compatible y `fontDisplay: swap`; no cargar Google Fonts ni otro proveedor remoto en el navegador.
5. Aplicar la variable generada por WordPress `var(--wp--preset--font-family--barlow-condensed)` al `h2` del CTA, con fallback `sans-serif`, 46 px, peso 700, línea compacta y mayúsculas. Mantener la descripción a 17 px con blanco al 80 %, y el botón verde `#AECD25`, texto negro, esquina mínima y etiqueta en mayúsculas por presentación.
6. Encapsular las medidas rígidas bajo el breakpoint desktop existente; conservar en viewports menores el flujo apilado, gutters adaptativos, wrapping del título y objetivos táctiles accesibles.
7. No crear markup ni estilos exclusivos para Nosotros: validar que el CTA compartido continúa siendo idéntico en Inicio, tal como lo muestran los frames de Pencil.

## Riesgos

- El CTA se comparte entre Inicio y Nosotros; cualquier cambio en el helper o en `.labm-home-join` afecta ambas páginas. Esto es coherente con Pencil, que muestra la misma composición en ambos frames, pero debe verificarse en las dos rutas.
- El proyecto no contiene actualmente activos tipográficos. APPLY deberá obtener el WOFF2 de la distribución oficial de Barlow Condensed, conservar la licencia OFL y limitar su uso al encabezado del CTA; si el activo no puede incorporarse o validarse, deberá detenerse y reportar el bloqueo en vez de sustituirlo silenciosamente por una fuente remota.
- Registrar una familia en `theme.json` expone un preset tipográfico global, aunque su aplicación en este cambio queda limitada al `h2` del CTA mediante CSS.
- Las medidas exactas de escritorio no deben degradar el responsive móvil ni provocar desbordamiento con traducciones o ajustes del tamaño de fuente del navegador.

## Verificacion

- En un viewport de 1440 px, comparar `/nosotros/` con `myCtb`/`cxREz` de Pencil: sección de 300 px, caja útil de 1200 × 180 px centrada, franja `#AECD25` de 8 px, contenido a 52 px, título de 46 px, descripción de 17 px y botón de 163 × 48 px alineado a la derecha.
- Confirmar los textos «HAZ PARTE DEL BALONMANO ANTIOQUEÑO», «Conecta con la Liga, sus clubes y procesos deportivos.» y «CONTÁCTANOS», con destino `/contacto/`.
- Confirmar en estilos computados que el `h2` usa `Barlow Condensed` con peso 700, que el WOFF2 se sirve desde el propio tema sin error y que no se realizan solicitudes a proveedores externos de fuentes; comprobar también que el fallback evita texto invisible durante la carga.
- Repetir la inspección en Inicio para comprobar que el render compartido conserva la misma composición y no se duplicó código.
- Ejecutar las pruebas PHP del helper compartido y las pruebas E2E de experiencia pública relacionadas con `[data-labm-section="vinculacion"]`.
- Revisar al menos un viewport móvil para confirmar apilado, wrapping, ausencia de overflow horizontal, foco visible y objetivo táctil del enlace.
