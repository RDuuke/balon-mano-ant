# Tareas: Actualidad fiel a Pencil

## Fase 1: Preparación TDD
- [x] 1.1 GREEN — En `tests/php/PublicExperienceTest.php`, los casos para `texto`, categoría, publicaciones, orden DESC, partición 1+3, fallback y paginación que conserva filtros pasan.
- [x] 1.2 RED/GREEN — En `tests/e2e/public-experience.spec.ts`, añadir y validar casos de regiones `Nrclx`, filtros, vacío, detalle, foco visible y viewports 320/768/1440.

## Fase 2: Consulta y marcado
- [x] 2.1 En `wp-content/themes/labm/functions.php`, ampliar `labm_theme_public_query()` para actualidad con `texto`, categoría por nombre, solo `publish`, fecha DESC y cuatro resultados.
- [x] 2.2 En `wp-content/themes/labm/functions.php`, renderizar formulario GET accesible, estado vacío, una destacada y tres tarjetas semánticas sin duplicados.
- [x] 2.3 En `wp-content/themes/labm/functions.php`, reutilizar fallback local de medio, metadata escapada y enlaces de paginación con `texto` y `categoria` preservados.
- [x] 2.4 GREEN — Ejecutar los casos PHP focales de `tests/php/PublicExperienceTest.php` y ajustar el renderer hasta satisfacer 1.1.

## Fase 3: Integración visual
- [x] 3.1 En `wp-content/themes/labm/templates/archive-labm_actualidad.html`, sustituir el copy demo por el hero de Actualidad y conservar `[labm_actualidad_listado]`.
- [x] 3.2 En `wp-content/themes/labm/style.css`, añadir tokens y estilos aislados `labm-actualidad-*` para hero, filtros, destacada, tarjetas, paginación, vacío, foco y movimiento reducido.
- [x] 3.3 En `wp-content/themes/labm/style.css`, incorporar breakpoints fluidos: retícula desktop, reducción a 768 px y apilado sin overflow a 320 px.
- [x] 3.4 GREEN — Ejecutar los casos Playwright focales y corregir marcado/CSS hasta satisfacer 1.2.

## Fase 4: Datos y verificación local
- [x] 4.1 En `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`, solo si faltan datos, crear cuatro fixtures publicadas, categorizadas e ilustradas para paginación.
- [x] 4.2 REFACTOR — Simplificar duplicación del renderer y selectores sin cambiar contratos; repetir PHPUnit y Playwright focales.
- [x] 4.3 Ejecutar formato/análisis aplicables y verificar LF en los archivos modificados; corregir únicamente CRLF introducidos.
