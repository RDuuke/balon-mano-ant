# Diseño: Marquee de logos de Aliados Oficiales

## Enfoque técnico
Se especializará `labm_aliado` como catálogo privado de logos. El tema obtendrá publicaciones ordenadas, descartará las no representables y construirá una pista CSS con una lista semántica y una réplica inerte. No habrá comportamiento JavaScript ni controles para aliados. Los fixtures importarán PNG ficticios como adjuntos y los asignarán como imagen destacada.

## Decisiones de arquitectura

| Decisión | Elección | Alternativas | Justificación |
|---|---|---|---|
| Modelo editorial | Conservar `labm_aliado`, privado, con soportes `title`, `thumbnail` y `page-attributes`; no borrar metadatos legados. | Nuevo CPT o bloque repetidor. | Mantiene capacidades, CRUD y datos existentes sin migración destructiva. |
| Publicación válida | Extender la validación común a REST y guardado administrativo; ante título/logo faltante conservar como borrador y mostrar aviso. | Validar solo al renderizar. | Evita estados publicados inválidos sin perder la edición. Los hooks deben comprobar capacidad y nonce, y evitar recursión. |
| Selección | Consultar publicados por `menu_order ASC`, `title ASC`, `ID ASC`; filtrar con `get_post_thumbnail_id()` y `wp_get_attachment_image_src()`, detenerse al reunir 12. | Limitar a 12 antes del filtro. | Garantiza hasta 12 logos válidos y desempate determinista. |
| Marcado marquee | Un `div` recortado contiene una pista con dos `ul`: la primera accesible y la segunda `aria-hidden="true"` e `inert`. Ambas tienen el mismo ancho y separación. | Duplicar nodos con JS. | El desplazamiento de una longitud exacta es continuo y funciona sin JS. No existen enlaces, texto visible ni elementos enfocables. |
| Movimiento | `translate3d` lineal de `0` a `-50%`, duración fija de 24 s, sin pausa por hover/foco ni API de velocidad. | Carrusel JavaScript. | Cumple el comportamiento marquee y reduce código/estado. |
| Movimiento reducido | Desactivar animación, ocultar la réplica y permitir que la lista primaria envuelva todas las imágenes. | Ocultar excedentes. | Presenta todos los logos sin movimiento ni duplicación perceptible. |
| Logos | PNG transparentes originales, 800×400 px, caja visual uniforme con `object-fit: contain`; `alt` derivado del título saneado. | Logos reales o SVG generado. | Evita suplantación, deformación y dependencias externas. |

## Flujo de datos

```text
wp-admin -> labm_aliado (título + destacada + orden)
         -> validación de publicación
Home -> consulta ordenada -> filtro imagen válida -> 12 -> lista + réplica -> CSS marquee
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Modificar | Soportes específicos y validación REST/admin con aviso. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificar | Fixtures ordenados e importación idempotente de adjuntos. |
| `wp-content/themes/labm/functions.php` | Modificar | Selección válida y marcado solo de imágenes. |
| `wp-content/themes/labm/style.css` | Modificar | Pista continua, cajas responsive y modo reducido estático. |
| `wp-content/themes/labm/assets/home.js` | Modificar | Retirar exclusivamente la lógica de aliados. |
| `wp-content/themes/labm/assets/images/aliados-demo/*.png` | Crear | Seis logos demo ficticios. |
| `tests/php/HomeContentTest.php`, `tests/php/HomePresentationTest.php`, `tests/php/HomeEditorialFlowsTest.php` | Modificar | Contratos admin, consulta, salida y legado. |
| `tests/e2e/home.spec.ts` | Modificar | Marquee, ausencia de controles/enlaces/texto, responsive y reduced-motion. |

## Interfaces / contratos
`labm_theme_render_home_allies()` seguirá devolviendo HTML o cadena vacía. El contrato editorial de `labm_aliado` será: título no vacío, adjunto de imagen destacado y entero `menu_order`. Cuerpo, extracto y `labm_destino_url` podrán persistir, pero no se editarán ni consumirán en esta sección.

## Estrategia de pruebas

| Capa | Qué validar | Enfoque |
|---|---|---|
| PHPUnit | Soportes, capacidades, rechazo REST/admin, orden/desempate/límite, omisión y HTML seguro. | Pruebas focales con adjuntos reales y datos legados. |
| Fixtures | Idempotencia, adjuntos PNG, miniaturas y `menu_order`. | Ejecutar carga dos veces y verificar IDs/conteos. |
| Playwright | Dos grupos equivalentes, animación 24 s, cero controles/enlaces/texto, `alt`, sin overflow 320–1440 px. | Estilos computados, geometría y Axe. |
| Accesibilidad | Reduced-motion sin animación y todos los logos en la lista primaria. | Emulación de medios y comprobación de réplica oculta. |

## Generación de recursos
En APPLY se usará ImageGen para producir seis identidades ficticias claramente diferenciadas, sin nombres ni formas de marcas reales, sobre fondo transparente. Se revisarán visualmente, se normalizarán a PNG 800×400 y se guardarán en `assets/images/aliados-demo/` con nombres estables y LF no aplicable a binarios.

## Migración / despliegue
No se borrarán campos ni registros. Al desplegar, los aliados publicados sin logo quedarán almacenados pero no visibles; al volver a guardarlos deberán completarse. Reversión: restaurar código/assets y recargar fixtures.

## Preguntas abiertas
Ninguna.
