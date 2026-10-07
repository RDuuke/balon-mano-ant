# Exploración: frontend de Selecciones y submenú

## Alcance y estado

Unidad EXPLORE de código e integración, completada sobre main `ecab4cae066f74eb4c9c382b38773e69154a286d`; rama creada `feat/selecciones-frontend-submenu`. No se ejecutaron pruebas, instalaciones ni cambios de producto o runtime. OpenSpec ya estaba inicializado. El cambio anterior `selecciones-backend` permanece archivado; no se heredan su fase ni sus aprobaciones.

Se incorporó como entrada formal `exploration-design.md`, producido por Locke mediante MCP Pencil sobre `design/labm-wordpress-mockup.pen`. Esta unidad no leyó ni modificó archivos `.pen`, ni invocó Pencil. La evidencia observada y las adaptaciones propuestas se distinguen a continuación; estas últimas quedan revisables en PROPOSE.

## Conciliación con el diseño MCP

Los frames observados son `neIe6` (Selecciones desktop: listado Piso), `p1GT2` (nombre Archivo de Selecciones, contenido listado Playa) y `coPGt` (móvil con menú abierto). Submenú desktop `yRBhE`: BALONMANO PISO `H3swWG` y BALONMANO PLAYA `YCm3F`; móvil: Balonmano Piso `rp20t` y Balonmano Playa `TaEj4`. Sus destinos no están definidos por href ni flows. Propuesta sobre backend real: enlazar a `/selecciones/?modalidad=Piso` y `/selecciones/?modalidad=Playa`, respectivamente, con permalinks reales de cada publicación para VER PUBLICACIÓN. El archivo `/selecciones/` mantiene la entrada general existente; no añadir «Ver todas» al submenú como elemento observado.

Pencil muestra hero negro, introducción clara y publicaciones en filas verticales (listados `ImSRL` Piso y `fo7wU` Playa), foto izquierda y texto/acción derecha. Esto reemplaza la sugerencia preliminar de grilla: recomendar filas editoriales desktop y adaptación a columnas apiladas en móvil. Contadores derivados de WP_Query, cantidad variable, contenido y fotos editables; no fijar tres participaciones ni trasladar resultados/lugares/años demo a datos oficiales. El modelo no tiene campos estructurados para esos metadatos: usar contenido editorial existente y metadatos disponibles, o proponer una ampliación separada si se exige estructura.

No hay detalle específico de Selecciones ni listado móvil completo. `LXU5I` es detalle de Actualidad, solo referencia editorial. Reutilizar convenciones del tema para detalle con Gutenberg, medios opcionales y retorno es una propuesta, no un frame aprobado de Selecciones. La adaptación móvil de filas, vacío, paginación y encabezado general también son propuestas revisables. No se requiere autorización adicional para investigar estos gaps.

El móvil `coPGt` muestra panel en flujo que desplaza el hero; el tema actual usa overlay core móvil. PROPOSE debe decidir explícitamente conservar/adaptar core o reproducir panel en flujo, valorando diferencias y accesibilidad; no fingir equivalencia visual entre ambos. Desktop usa chevrons/resaltados inconsistentes: unificar con header existente y resolver estado activo semántico. El enlace padre navegable es una propuesta de continuidad, no comportamiento observado. El diseño solo acredita etiquetas/jerarquía/estados gráficos.

Los controles móviles observados miden 30/34/38px; proponer áreas cómodas de 44px, foco visible, disclosures con estado y nombres accesibles, teclado/touch y ocultación sin foco. Estos requisitos completan el diseño, no provienen de interacciones probadas. No usar roles de menú de aplicación ni focus trap si se adopta panel no modal. Fotos Unsplash y logos placeholder son referencias; mantener logo actual y utilizar medios editoriales reales o fallback explícito.

## Evidencia del código

- `wp-content/plugins/labm-core/includes/class-labm-domain.php`: `labm_seleccion` es público, tiene archivo, REST y rewrite `selecciones`. Soporta título, editor, extracto, miniatura y custom fields. La colección REST existente es `/wp-json/wp/v2/labm_seleccion`; el detalle utiliza ID. No hace falta un endpoint nuevo para renderizar servidor.
- `labm_modalidad` es jerárquica y compartida por Selecciones y Clubes. `labm_categoria` es jerárquica y compartida por Selecciones y Actualidad; ambas son públicas y REST. No hay taxonomía de rama/género, relación de plantel con integrantes, estadísticas, calendario o convocatorias de Selecciones en este modelo. No presentar esas estructuras como datos existentes.
- `labm_modalidad_detalle` es string, single, REST, saneado con `sanitize_text_field`; escritura autorizada mediante `edit_post`. Administrador y Editor reciben capacidades del CPT, incluso antes del retorno por versión vigente. No se propone cambiar capacidades ni esquema.
- `templates/archive-labm_seleccion.html` compone header, H1, párrafo ficticio y `[labm_selecciones_listado]`, seguido de footer. No existe `page-selecciones.html`. `templates/single-labm_seleccion.html` solo muestra modalidad, título, contenido y enlace de retorno; aún no muestra miniatura, extracto, categoría o modalidad_detalle.
- `functions.php:labm_theme_public_query` limita a `publish`, ordena por fecha DESC y pagina. Para Selecciones filtra modalidad por nombre; categoría y búsqueda de texto solo están implementadas para Actualidad. `labm_theme_render_listing` es compartido: Selecciones recibe tres artículos por página, sin imágenes, con modalidad, título y resumen recortado desde contenido. Ya hay formulario GET, vacío con limpieza y paginación que conserva modalidad mediante `pagina`.
- `labm_theme_selecciones_shortcode` sanea todos los valores GET con `array_map`; no controla formas array. Conviene extraer exclusivamente parámetros escalares permitidos y acotar página/filtro en el nuevo alcance.
- `parts/header.html` contiene `core/navigation` con overlay móvil y seis enlaces directos, incluido `/selecciones/`. No usa `ref` de wp_navigation en el archivo. `labm_theme_mark_current_navigation_link` filtra únicamente `core/navigation-link`, compara paths e ignora query strings: varios hijos con el mismo path podrían terminar marcados como página activa; el nuevo padre `core/navigation-submenu` requiere tratamiento explícito.
- `style.css` tiene reglas globales para navigation y tarjetas. Header sticky desde 768px con z-index 40; la navegación core usa su propio overlay móvil. `assets/back-to-top.js` altera header compacto al hacer scroll. No hay script propio de submenú. `functions.php` carga JS de otras secciones condicionalmente.
- `theme.json` y CSS aportan ancho 1200px, gutter fluido, verde #AECD25, negro #202020, neutro #F3F6E8 y Barlow Condensed para encabezados existentes. Usar la base existente; no reescribir tokens globales durante este cambio.
- `parts/footer.html` delega a `[labm_footer]`; `class-labm-footer-settings.php` renderiza opciones editables `labm_footer_settings`. Su enlace inicial de Selecciones apunta al archivo. No necesita duplicar el submenú salvo que el diseño lo requiera.
- `class-labm-fixtures-command.php` declara dos Selecciones ficticias publicadas (Piso y Playa) y una privada; no declara imágenes, extractos, categorías ni modalidad_detalle para esas tres. La siembra preserva contenido ajeno que ocupa un slug, actualiza sus demos y puede crear términos/attachments. Esto prueba la definición de fixtures, no la presencia efectiva en la base local.

## Opciones y recomendación

1. **Archivo CPT + filtros GET + detalle CPT + submenú core (recomendado).** Mantener `/selecciones/`, `/selecciones/?modalidad=Piso`, `/selecciones/?modalidad=Playa`, `?pagina=N` y `/selecciones/{post_name}/`. El enlace padre conserva acceso a todas; hijos Piso/Playa usan contratos existentes. Construir URLs de archivo y detalle con API WordPress, evitando concatenar paths y asumiendo slugs de detalle no reservados. No crear páginas `piso`/`playa` bajo el archivo.
2. **Landings por modalidad o archivos de taxonomía.** Aumentan plantillas y definición editorial. La taxonomía compartida puede listar Clubes si se usa su archivo sin consulta específica. Solo justificable si el diseño exige una landing independiente; acotar siempre a `labm_seleccion`.
3. **Cliente REST como fuente del listado.** Reutiliza backend, pero agrega estados de carga/error y dependencia JS para una colección ya consultable en PHP. No recomendado para el alcance inicial.

Separar el renderer de Selecciones del renderer visual de Actualidad, manteniendo compatibilidad de shortcode/consulta pública existente. Usar WP_Query server-side, solo publicaciones, orden estable (fecha y desempate por ID), filtros permitidos y paginación acotada. Mantener modalidad por nombre para enlaces vigentes; si DESIGN migra a slug/ID, definir compatibilidad y normalización explícitas. No introducir filtro de categoría hasta que el diseño y la especificación lo soliciten.

Regiones conciliadas: encabezado por modalidad, introducción, filas editoriales con miniatura opcional, modalidad/categoría disponibles, título y extracto editorial (fallback a contenido recortado), acción al permalink, vacío y paginación. Los frames no muestran filtros ni paginación: conservar su funcionalidad de backend e integrarla visualmente es una adaptación propuesta. Detalle con un H1, imagen opcional, taxonomías y modalidad_detalle escapados, contenido Gutenberg una sola vez y retorno a Selecciones. No inventar personas, resultados o planteles estructurados; el editor puede aportar contenido rico.

Usar `core/navigation-submenu` con padre navegable y control de apertura diferenciado si el bloque instalado lo permite. Verificar posteriormente clic, Enter/Space, Escape, recorrido Tab, foco visible/restauración y touch en overlay móvil; evitar interacción exclusiva por hover. Mantener acceso al archivo y establecer estado de sección para el padre sin declarar todos los hijos como página actual. Evaluar estado por modalidad en archivo; en detalle no atribuir una modalidad activa si hay cero o varias.

Los términos del filtro deben provenir de Selecciones publicadas o comprobar disponibilidad dentro del CPT: `get_terms(hide_empty=true)` sobre una taxonomía compartida puede ofrecer términos poblados únicamente por Clubes. Acordar con SPEC si Piso/Playa permanecen visibles sin publicaciones; mostrar vacío real y nuevos términos editoriales sin ocultarlos por listas rígidas. La decisión de menú fijo Piso/Playa o dinámico queda pendiente del diseño.

## Archivos esperados

- Núcleo de presentación: `wp-content/themes/labm/functions.php`, `style.css`, `parts/header.html`, `templates/archive-labm_seleccion.html`, `templates/single-labm_seleccion.html`.
- Condicionales: `patterns/selecciones.php` si se adopta patrón de composición; helpers PHP separados si DESIGN decide extraer; JS específico de Selecciones solo para interacción requerida que core no cubra; nuevos assets solo tras entrada visual MCP.
- Persistencia condicional: `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` si se enriquecen demos; `content-sync/canonical.zip` y `latest.json` si se modifica contenido/medios/opciones/overrides del runtime. El modelo de dominio y footer no necesitan cambios para la recomendación base.
- Verificación futura: pruebas focales PHP de Selecciones/consulta/estado activo; `tests/e2e/public-experience.spec.ts` y `tests/e2e/verify-correctives.spec.ts`, o suite focal nueva. La prueba móvil actual exige exactamente seis enlaces y debe pasar a verificar jerarquía y destinos intencionales del submenú.

## Persistencia y límites de evidencia

No se inspeccionó la base local ni se inició Docker. Queda comprobar en la fase autorizada de implementación/verificación si existen páginas con slug selecciones, wp_template/wp_template_part personalizados o wp_navigation persistido que sobreescriban el tema; no afirmar su existencia. Una personalización podría impedir que el header o las plantillas de archivo actualizadas se vean. No eliminar overrides indiscriminadamente.

Cambios exclusivamente de archivos del tema no requieren content-sync. Si se enriquecen fixtures y se aplican, se editan banners/contenido/medios o se ajustan overrides persistidos, usar el procedimiento oficial content-sync, verificar versión/hash y exclusión de credenciales y tablas de usuarios. El exportador existente excluye users/usermeta y exporta las demás tablas y uploads; incluir el paquete actualizado en la entrega. No aplicar fixtures ni exportar durante EXPLORE.

## Riesgos y verificación a planificar

1. Header compartido: regresión potencial en navegación de todas las páginas, overlay, sticky y foco; cobertura focal debe incluir header en rutas representativas externas a Selecciones.
2. Estado activo ignora query y solo intercepta navigation-link: corregir semántica antes de introducir hijos con el mismo path.
3. Taxonomías compartidas y términos ausentes/nuevos: no equiparar conteos globales con Selecciones públicas ni asumir Piso/Playa como vocabulario exhaustivo.
4. Fixtures actuales son escasos y sin medios; definir fallbacks y evidencia de paginación con fixtures temporales, sin fabricar contenido de producción.
5. Override persistido y colisión de slug son incógnitas de runtime, no fallos confirmados.
6. Contrato de navegación en prueba existente (seis enlaces) deja de ser válido al ampliar jerarquía; actualización intencional necesaria.

VERIFY inicial por impacto; re-VERIFY solo failed, pending/interrupted e invalidated, reutilizando PASS trazable vigente. No ejecutar gate completo por defecto. `openspec/config.yaml` conserva verify_pr con PHPUnit/integración, cobertura >=80%, PHPCS, PHPStan y suite Playwright completa: documentar en PROPOSE/DESIGN el criterio focal explícito para Selecciones, submenú y header compartido, y cualquier excepción aprobada o evidencia acumulada necesaria. No declarar que un filtro acredita una suite completa ni reescribir configuración global silenciosamente. Lighthouse se evalúa por impacto real del header/plantillas/assets; no es necesario ejecutarlo durante esta exploración.

APPLY mantendrá TDD strict para lógica nueva. La instrucción de esta unidad declara review budget deshabilitado; no modifica el `max_diff_lines: 0` global. El orquestador debe transmitir esta excepción explícita a APPLY sin reinterpretar ese cero. Todos los artefactos nuevos/modificados deben usar LF.

## Siguiente paso

Exploración de código y diseño consolidada. Preparar PROPOSE con filas por modalidad, rutas GET compatibles, decisión explícita sobre panel móvil/core, detalle editorial adaptado, fallbacks y criterio de verificación. Los gaps visuales se convierten en decisiones revisables, no bloqueos de investigación. Esta unidad no ejecuta PROPOSE.
