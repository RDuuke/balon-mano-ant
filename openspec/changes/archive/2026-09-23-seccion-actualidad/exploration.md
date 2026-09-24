# Exploración: sección Actualidad

## Contexto y alcance

La solicitud es una mejora de experiencia pública para la ruta canónica `/actualidad/`. La rama activa es `feature/seccion-actualidad`. El diseño de Pencil `Actualidad — Desktop` (frame `Nrclx`, 1440 × 2168) define una página editorial compuesta por encabezado oscuro, búsqueda y categoría, una noticia destacada, tres tarjetas, paginación y el pie existente.

No se requiere un modelo de contenido nuevo: el CPT público `labm_actualidad`, su taxonomía `labm_categoria`, extracto, imagen destacada y fecha de publicación ya cubren los datos del diseño.

## Estado actual comprobado

- `wp-content/plugins/labm-core/includes/class-labm-domain.php` registra `labm_actualidad` con REST, archivo público, soporte de título, editor, extracto, miniatura y campos personalizados. Su slug público ya es `actualidad`; `labm_categoria` es la clasificación compartida disponible en REST.
- `wp-content/themes/labm/templates/archive-labm_actualidad.html` aporta actualmente un `h1`, texto demo y el shortcode `[labm_actualidad_listado]`. El template individual es deliberadamente mínimo.
- `wp-content/themes/labm/functions.php` registra ese shortcode y concentra la consulta y renderización pública. `labm_theme_public_query()` consulta exclusivamente publicaciones `publish`, ordenadas por fecha descendente, permite `categoria` y pagina tres elementos. `labm_theme_render_listing()` ya construye filtro de categoría, tarjetas, estado vacío y `paginate_links()`.
- La implementación actual no incluye búsqueda textual, jerarquía de destacada frente a tarjetas, miniaturas en el archivo, fecha visible, ni las superficies y proporciones del frame de Pencil.
- `theme.json` ya declara los colores principales del diseño (`#AECD25`, `#789614`, `#202020`, `#F3F6E8`, blanco), Barlow Condensed y ancho editorial de 1200 px. El CSS del tema mantiene las variables de contenido y el patrón visual de las noticias de portada, reutilizable como referencia de marcado y estados responsivos.
- El diseño usa también Inter, pero el tema solo autoalberga Barlow Condensed y define `system-ui` como tipografía de cuerpo. Se debe resolver explícitamente si Inter se incorpora como activo local o si `system-ui` es la aproximación aprobada; no se deben cargar fuentes de terceros en tiempo de ejecución.

## Mapeo diseño → integración

| Elemento de diseño | Fuente de datos / integración propuesta |
| --- | --- |
| Hero “Información y eventos / Actualidad” | Template de archivo y una función de renderizado del tema para conservar contenido traducible y semántica `h1`. |
| Campo “Buscar noticias o eventos” | Nuevo filtro GET `texto`, saneado y aplicado como búsqueda `s` en `labm_theme_public_query()`; debe preservar categoría y página. |
| Selector “Todas las categorías” | Reutilizar `labm_categoria`, preferiblemente con el valor slug para URLs estables y etiqueta visible del término. |
| Noticia destacada | Primera publicación de la consulta filtrada; imagen destacada, categoría, fecha, título, resumen y enlace individual. |
| Tres tarjetas | Restantes publicaciones de la misma página, con imagen, categoría, fecha, título y enlace; no duplicar la destacada. |
| Paginación | Conservar `paginate_links()`, propagando `texto` y `categoria`; el tamaño de página debe documentarse y probarse frente al diseño. |
| Footer | No cambia: el shortcode permanece dentro de la plantilla que ya recibe el template-part global. |

## Rutas de cambio probables

1. `wp-content/themes/labm/functions.php`: ampliar de forma compatible los filtros, la consulta y el marcado del shortcode; mantener exclusión de borradores y saneado de parámetros públicos.
2. `wp-content/themes/labm/templates/archive-labm_actualidad.html`: retirar el copy demo y sustituir el encabezado simple por la composición editorial, idealmente delegada en una función del tema para no incrustar lógica en el HTML de bloques.
3. `wp-content/themes/labm/style.css`: añadir el bloque aislado de estilos de archivo (`labm-actualidad-*`) y sus breakpoints. Debe respetar tokens existentes, `prefers-reduced-motion`, foco visible y evitar desbordamiento a 320 px.
4. `tests/php/PublicExperienceTest.php` y/o `tests/php/ClosingCoverageTest.php`: cubrir búsqueda, categoría, orden, partición destacada/tarjetas, imágenes alternativas y preservación de publicaciones no públicas.
5. `tests/e2e/public-experience.spec.ts`: actualizar la expectativa actual de tres artículos y comprobar búsqueda, select, noticia destacada, tarjetas, paginación, estado vacío, detalle, foco y viewport móvil. Puede añadirse un spec enfocado si mejora la legibilidad.
6. `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` solo si los fixtures actuales no aportan imágenes y categorías suficientes para el resultado visual y las páginas de paginación. No debe ser necesario cambiar el registro del dominio.

## Enfoques evaluados

### A. Extender el shortcode y la plantilla existentes (recomendado)

Mantiene el archivo de bloques, la URL, las pruebas y el CMS actuales. Una sola consulta pública filtrada compone la destacada y las tres tarjetas sin divergencias entre datos ni paginación. Es la intervención más pequeña y conserva la separación actual: dominio en el plugin, presentación en el tema.

### B. Crear un bloque dinámico nuevo

Ofrecería controles editoriales adicionales, pero añade registro, metadatos y superficie de mantenimiento sin que el diseño los requiera. No es necesario para el archivo de un CPT ya existente.

### C. Resolver filtros y paginación con JavaScript cliente

No se recomienda: rompería la accesibilidad y la indexabilidad de las URLs GET ya empleadas, duplicaría la consulta del servidor y aumentaría el alcance. El filtro debe permanecer progresivo y renderizado en PHP.

## Estrategia de validación

- PHPUnit: validar que `texto` y `categoria` se saneen, que la consulta solo devuelva `publish`, que la primera publicación aparezca una vez como destacada, que las siguientes formen las tarjetas y que los parámetros sobrevivan en la paginación.
- Playwright: visitar `/actualidad/`, aplicar búsqueda/categoría, abrir el detalle, validar vacío y paginación; comprobar foco visible y ausencia de desbordamiento horizontal a 320 px.
- Ejecutar el gate del repositorio cuando corresponda a VERIFY, incluyendo formato/análisis y la comprobación de LF en los archivos modificados.

## Riesgos y decisiones pendientes

- El diseño muestra una búsqueda que no existe hoy. Debe definirse el nombre público del parámetro (`texto` es consistente con Documentos) y cubrirlo con pruebas de URL.
- La categoría actual se filtra por nombre. Migrar el selector a slug mejora estabilidad, pero requiere compatibilidad con enlaces existentes `?categoria=Noticias` o una normalización explícita.
- Las miniaturas son opcionales en el dominio. El render debe usar un fallback visual accesible para evitar tarjetas rotas; si el resultado debe ser idéntico al mockup, los fixtures/editorial necesitarán imágenes destacadas reales o de demostración.
- El copy y los elementos de UI del frame contienen “contenido demo”. La propuesta debe conservar ese carácter hasta que el equipo suministre contenido editorial aprobado.
- Inter no está empaquetada en el tema. La decisión de tipografía del cuerpo condiciona la fidelidad visual y los activos permitidos.

## Recomendación

Continuar con PROPOSE usando el enfoque A: conservar CPT, taxonomía y plantilla de archivo, y evolucionar el renderer PHP/CSS del tema con filtros GET accesibles, composición destacada + tres tarjetas y pruebas focalizadas.
