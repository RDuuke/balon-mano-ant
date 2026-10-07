# Diseño: Selecciones y submenú

## Enfoque técnico

Entradas directas: `proposal.md`, `exploration.md` y `exploration-design.md` de este cambio. Preflight STATUS de solo lectura: PROPOSE, aprobado «Ok», sin bloqueo ni dependencias faltantes para DESIGN. SPEC es independiente; no se ha leído. Diseño limitado al archivo público y navegación global. Quedan excluidos plantillas de detalle, rutas individuales y CTA individuales; las filas no enlazan títulos ni medios.

## Decisiones de arquitectura

| Opción | Coste o alternativa | Decisión y motivo |
| --- | --- | --- |
| Consulta principal del archivo | Repetir WP_Query en shortcode duplica consulta y puede divergir del estado HTTP | `pre_get_posts`, exclusivamente frontend, consulta principal y archivo `labm_seleccion`: publish, tres filas, fecha DESC e ID DESC como desempate. El shortcode consume `$wp_query`; no altera Actualidad ni administración. |
| Modalidad por término | Conteos globales incluyen Clubes | Resolver nombre exacto a term_id de `labm_modalidad`; filtrar con `include_children=false`. Término ausente produce cero resultados, nunca listado sin filtro. `found_posts` cuenta solo Selecciones publicadas del filtro. |
| Navegación propia acotada | Core navigation conserva editor, pero su overlay modal contradice el panel en flujo; cambiar solo CSS deja semántica/foco inconsistentes | Sustituir únicamente la navegación del header por shortcode servidor y `assets/navigation.js`. Retirar overlay core de ese componente, sin modificar globalmente clases ni handlers core. Conservar seis destinos, logo, CTA, sticky y back-to-top. |
| Render específico | Ampliar renderer compartido arriesga Actualidad | Helpers Selecciones en functions.php y estilos `.labm-selecciones-*`; mantener APIs compartidas fuera de este archivo. Sin REST nuevo ni cambio de capacidades/esquema. |
| Contenido editorial existente | Copiar demos o añadir opciones/esquemas | Descripción del término editable para introducción, título/extracto/contenido, categoría y `labm_modalidad_detalle` existentes para filas. Encabezados funcionales traducibles; no inventar resultados, lugares o años. |

## Flujo y contratos

`/selecciones/` → parámetros escalares → modalidad → consulta principal → hero, introducción, conteo, filas, paginación.

Ausencia, vacío, array o valor inválido de `modalidad` normaliza a Piso; admitir Piso/Playa y nombres exactos de términos adicionales con Selecciones publicadas. Menú fijo Piso/Playa, ambos visibles aunque vacíos. Selector añade términos disponibles mediante consulta restringida a CPT/publish, no `get_terms(hide_empty)` global. `pagina` entero positivo, por defecto 1; traducirlo a `paged` antes de ejecutar consulta. Página fuera de rango muestra recuperación a página 1, sin inventar resultados. Cambiar filtro omite pagina; enlaces conservan modalidad y usan `get_post_type_archive_link`, `add_query_arg`, escape contextual. No búsquedas ni filtros de categoría nuevos.

`archive-labm_seleccion.html` conserva main/header/footer y delega composición al shortcode. Hero negro e introducción clara según neIe6/p1GT2; filas imagen izquierda/texto derecha, apiladas bajo 768px. Miniaturas WordPress con dimensiones/srcset, carga diferida; fallback neutro sin foto inventada. Extracto editable, o resumen saneado del contenido; metadatos ausentes se omiten. Vacío real ofrece Piso/Playa y reinicio de paginación.

Navegación: `<nav>` y listas/enlaces ordinarios. Padre Selecciones navegable y botón separado con nombre, aria-controls/expanded. Solo hijo correspondiente recibe aria-current=page; padre indica sección visualmente. Menú móvil en flujo bajo 768px, sin dialog, trap ni bloqueo de scroll. JS mejora HTML inicialmente expandido: activa botones y colapsa paneles; sin JS todos los enlaces siguen visibles. Enter/Espacio alternan; Tab natural; Escape cierra primero submenú y restaura su botón, después panel y disparador. Clic/touch exterior o salida de foco cierra sin robar foco exterior; nunca ocultar foco retenido. Hidden elimina descendientes del recorrido. Resize sincroniza estados, controles de 44px, foco visible y movimiento reducido.

## Archivos y límites

| Archivo relativo | Acción futura |
| --- | --- |
| wp-content/themes/labm/functions.php | Query, helpers, shortcode navegación, enqueue global pequeño; conservar filtro core para otros bloques |
| wp-content/themes/labm/parts/header.html | Sustituir solo navegación |
| wp-content/themes/labm/templates/archive-labm_seleccion.html | Composición del archivo |
| wp-content/themes/labm/style.css | Reglas acotadas responsive |
| wp-content/themes/labm/assets/navigation.js | Disclosures progresivos |
| tests/php/PublicExperienceTest.php | Query/conteos/HTML |
| tests/e2e/public-experience.spec.ts y verify-correctives.spec.ts | Casos focales y jerarquía |

## Pruebas, despliegue y reversión

APPLY: TDD strict RED/GREEN futuro para lógica; privados, Clubes, términos ausentes, arrays, conteos/páginas, ausencia de enlaces individuales. Browser focal Selecciones y header en Inicio/Actualidad: teclado, touch, sin JS, resize, sticky, responsive y comparación Pencil. Lighthouse focal Inicio/Selecciones desktop/móvil necesario por header global, layout y medios; sin suite monolítica. WPCS/PHPStan pertinentes; PHP ≥80% solo con instrumentación y evidencia vigente concordantes. Reusar PASS válido; repetir fallidas/pendientes/invalidadas.

Antes de aplicar, inspeccionar colisión de slug y overrides persistidos; preservar personalizaciones. Rollback: restaurar archivos afectados a ecab4ca y retirar asset nuevo. Si se modifica estado WP/uploads: respaldo y content-sync oficial, verificar versión/hash y excluir users/usermeta/credenciales; entregar canonical.zip/latest.json. Solo archivos no requiere sincronización. DESIGN no ejecuta runtime/pruebas. Verificar LF únicamente del artefacto.

## Preguntas abiertas

Ninguna bloqueante; overrides pendientes de inspección futura, gaps responsive aceptados. TASKS requiere también SPEC terminado.
