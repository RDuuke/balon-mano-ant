# Progreso de APPLY: Marquee de logos de Aliados Oficiales

## Tarea 1.1 — Contrato editorial del CPT

- **RED:** test `tests/php/HomeContentTest.php::test_home_metadata_is_registered_sanitized_and_authorized_by_post` falla con: `labm_destino_url seguía expuesto para labm_aliado`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; el contrato focal pasa.
- **REFACTOR:** se dejó el registro REST de URL limitado a `labm_slide`.

## Tarea 1.2 — Flujos editoriales válidos e inválidos

- **RED:** test `tests/php/HomeEditorialFlowsTest.php::test_admin_rechaza_adjunto_que_no_es_imagen` falla con: `un PDF conservaba estado publish`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; publicación válida, borrador, nonce, capacidades, aviso y datos legados pasan.
- **TRIANGULATE:** test `tests/php/HomeEditorialFlowsTest.php::test_admin_publica_aliado_valido_y_no_interfiere_con_borradores` cubre publicación válida y bypass de borradores.

## Tarea 1.3 — Validación de título y logo

- **RED:** test `tests/php/HomeContentTest.php::test_invalid_home_items_are_rejected_before_publish` falla con: `un adjunto no-imagen era aceptado como logo`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; solo un adjunto con MIME de imagen satisface el logo.
- **TRIANGULATE:** el mismo test comprueba que una URL legada inválida del aliado se ignora sin impedir publicar.
- **REFACTOR:** la validación permanece pura y no llama `wp_update_post`, evitando recursión.

## Tarea 1.4 — Seis logos ficticios originales

- **RED:** comprobación `ImageGen::destino_proyecto` falla con: `las seis salidas nuevas solo existen en el directorio generado y no en los paths estables del tema`.
- **GREEN:** implementación en `wp-content/themes/labm/assets/images/aliados-demo/*.png`; seis prompts independientes produjeron marcas transparentes sin texto.
- **TRIANGULATE:** revisión visual individual cubre diferenciación, ausencia de texto y falta de similitud evidente con marcas reales.

## Tarea 1.5 — Normalización PNG

- **RED:** comprobación `PowerShell::raw_image_dimensions` falla con: `las seis salidas ImageGen miden 1774x887, no 800x400`.
- **GREEN:** implementación en `wp-content/themes/labm/assets/images/aliados-demo/*.png`; los seis lienzos PNG miden 800x400 y preservan alfa.
- **TRIANGULATE:** test `tests/php/HomeContentTest.php::test_demo_allies_assets_are_stable_transparent_png_files` cubre nombres exactos, PNG, dimensiones y esquinas transparentes.

## Tarea 2.1 — Contrato de fixtures de logos

- **RED:** test `tests/php/FixturesDomainTest.php::test_home_allies_fixtures_are_image_only_ordered_and_idempotent` falla con: `arco-comun no tiene thumbnail; 0 no es mayor que 0`.
- **GREEN:** implementación en `tests/php/FixturesDomainTest.php` y `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`; seis PNG, órdenes 0–5, URL legada vacía e IDs estables pasan tras dos cargas.
- **TRIANGULATE:** la misma prueba exige seis IDs de adjunto distintos y reutilizados.

## Tarea 2.2 — Importación idempotente y fallos visibles

- **RED:** test `tests/php/FixturesDomainTest.php::test_home_allies_fixture_reports_upload_failures` falla con: `no se lanzó RuntimeException ante wp_upload_bits() fallido`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`; WP-CLI informa el archivo fallido, descarta adjuntos huérfanos y reutiliza solo PNG con archivo existente.
- **REFACTOR:** la corrección durable de `scripts/content-sync.ps1` restaura recursivamente `33:33` tras importar uploads; el contrato `tests/contract/Test-ContentSyncUploadsOwnership.ps1` pasó de RED a GREEN.

## Tarea 2.3 — Selección válida antes del límite

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_collection_filters_orders_and_limits_before_rendering` falla con: `labm_theme_home_allies_posts no existe`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; borradores e imágenes inválidas se omiten antes de reunir hasta 12 aliados.
- **TRIANGULATE:** la prueba cubre trece válidos, un publicado sin imagen y un borrador con imagen.

## Tarea 2.4 — Orden estable de la colección

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_collection_filters_orders_and_limits_before_rendering` falla sin una colección independiente verificable.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; orden `menu_order ASC`, `title ASC`, `ID ASC` y límite posterior al filtro pasan.
- **REFACTOR:** el render existente consume la colección sin modificar todavía el contrato visual de las tareas 3.x.

## Tarea 3.1 — Contrato HTML de la marquee

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_render_image_only_marquee_with_stable_valid_selection` falla con: `la lista primaria no tiene una identidad inequívoca para distinguirla de la réplica`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; dos grupos idénticos, `alt` saneado, réplica inerte y ausencia de enlaces, texto auxiliar y controles pasan.
- **TRIANGULATE:** la misma prueba cubre título demo con ampersand y comillas tipográficas, además de doce logos válidos tras filtrar uno inválido.

## Tarea 3.2 — Lista primaria y réplica no accesible

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_render_image_only_marquee_with_stable_valid_selection` falla con: `se esperaba una lista labm-allies__primary y no existía`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; la lista primaria queda identificada y su contenido coincide exactamente con la réplica `aria-hidden` e `inert`.
- **TRIANGULATE:** `tests/php/HomePresentationTest.php::test_renderizadores_de_portada_tienen_fallback_y_salida_segura` conserva la salida vacía cuando el tipo no existe.

## Tarea 3.3 — Ausencia de lógica JavaScript de aliados

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_assets_define_css_only_marquee_and_reduced_motion_fallback` falla con: `persistía un selector legado de controles de aliados en los recursos del tema`.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; se retiró el selector legado y el contrato confirma que `assets/home.js` no contiene lógica de aliados, sin alterar el slider.
- **REFACTOR:** `wp-content/themes/labm/assets/home.js` no requirió cambios porque ya estaba limitado al slider.

## Tarea 3.4 — Movimiento CSS continuo

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_assets_define_css_only_marquee_and_reduced_motion_fallback` falla con: `la pista no declaraba separación nula entre grupos y conservaba estilos de controles inexistentes`.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; pista flex sin separación, animación lineal de 24 s, recorrido de -50 % y logos con `object-fit: contain` pasan.
- **TRIANGULATE:** el contrato de HTML exige que ambas listas contengan exactamente la misma secuencia de imágenes.

## Tarea 3.5 — Alternativa de movimiento reducido

- **RED:** test `tests/php/HomePresentationTest.php::test_allies_assets_define_css_only_marquee_and_reduced_motion_fallback` falla con: `el viewport seguía recortando la lista estática en movimiento reducido`.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; se desactiva animación y transformación, se oculta la réplica, la primaria envuelve y el viewport deja de recortar.
- **TRIANGULATE:** el contrato fuente verifica conjuntamente anchura completa, envoltura y ocultación de la réplica.

## Tarea 3.6 — Simplificación preservando contratos

- **RED:** los contratos focales 3.1 y 3.3–3.5 fallan antes del ajuste con dos fallos reproducibles.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php` y `wp-content/themes/labm/style.css`; la clase focal completa pasa con 17 pruebas y 109 aserciones.
- **REFACTOR:** el render itera directamente las publicaciones seleccionadas y elimina la colección intermedia sin cambiar datos, consulta ni salida.

## Tarea 4.1 — Contrato E2E del marquee tras recarga

- **RED:** test `tests/e2e/home.spec.ts::slider conserva controles y aliados funciona como marquee solo de logos` falla con: `style.css conserva ?ver=0.2.6 tras recargar y permite reutilizar estilos obsoletos`.
- **GREEN:** implementación en `tests/e2e/home.spec.ts` y `wp-content/themes/labm/functions.php`; la prueba fuerza `no-preference`, recarga, confirma dos grupos en una fila y observa desplazamiento entre dos instantes.
- **TRIANGULATE:** la misma prueba adjunta estilos computados, estado de `matchMedia`, transformaciones y posiciones de ambos grupos a 1840 px.

## Tarea 4.2 — Movimiento normal y versión vigente del CSS

- **RED:** test `tests/e2e/home.spec.ts::slider conserva controles y aliados funciona como marquee solo de logos` falla con: `la hoja del tema usa una versión fija y la recarga no invalida caché`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; `style.css` se publica con su `filemtime`, la pista queda lineal a 24 s y su posición disminuye tras 400 ms.
- **REFACTOR:** el HTML de la lista se genera una sola vez y se reutiliza en la réplica, evitando diferencias contextuales como `fetchpriority`.

## Tarea 4.3 — Movimiento reducido y geometría responsive

- **RED:** test `tests/e2e/home.spec.ts::slider y aliados quedan estaticos con movimiento reducido` falla con: `la réplica se computa como block cuando no se carga la hoja vigente`.
- **GREEN:** implementación en `tests/e2e/home.spec.ts` y `wp-content/themes/labm/functions.php`; Playwright confirma `matchMedia=true`, animación `none`, réplica oculta, lista envolvente, Axe limpio, proporción 2:1 y ausencia de desborde.
- **TRIANGULATE:** la suite pasa en 320, 768, 1024 y 1440 px, y el modo normal se observa además a 1840 px tras recarga.

## Tarea 4.4 — Verificación focal integrada

- **RED:** test `tests/smoke/Test-Fixtures.ps1` falla con: `Fixtures publicaron adjuntos PDF`, aunque los IDs 1118 y 1134 ya existían antes de las dos cargas.
- **GREEN:** implementación en `tests/smoke/Test-Fixtures.ps1`; el smoke compara el conteo de PDFs antes y después, preserva ambos adjuntos ajenos y pasa tras dos cargas de fixtures.
- **TRIANGULATE:** PHPUnit focal pasa 25 pruebas con 226 aserciones y Playwright focal de aliados pasa 12 pruebas en 320, 768, 1024 y 1440 px.
- **REFACTOR:** la verificación expresa el contrato real —fixtures no crean PDFs— sin exigir que una base compartida carezca de contenido previo.

## Tarea 5.1 — Análisis estático y formato

- **RED:** PHPCS focal sobre `tests/php/HomePresentationTest.php` informa 8 errores y 3 advertencias por estructuras compactas y documentación incompleta.
- **GREEN:** PHPCBF y ajustes focales de documentación dejan ese archivo sin hallazgos; `composer lint` y `composer analyse -- --no-progress` finalizan con código 0.
- **REFACTOR:** solo se expandieron estructuras de prueba y PHPDoc requeridos por WPCS, sin modificar el contrato funcional.

## Tarea 5.2 — Gate integral reproducible

- **RED:** la integración aislada no podía escribir uploads porque `scripts/gate.ps1` omitía el volumen y UID 33; el primer intento de cobertura con UID 33 no escribió Clover y expuso un falso PASS basado en el reporte anterior.
- **GREEN:** `scripts/gate.ps1` monta uploads y ejecuta integración con `33:33`; `scripts/coverage.ps1` monta el mismo volumen, genera Clover fresco como root y la ruta de fallo de fixtures usa `/proc`, realmente no escribible incluso para root.
- **TRIANGULATE:** `scripts/gate.ps1 -IncludeBrowser` pasa PHPUnit unitario, 77 pruebas de integración con 540 aserciones, cobertura 89.64 % (995/1110), PHPCS, PHPStan y Playwright 84/84.

## Tarea 5.3 — Inspección visual responsive y accesible

- **RED:** la captura aportada por el usuario reproduce la variante estática en dos filas cuando el sistema informa `prefers-reduced-motion: reduce`.
- **GREEN:** inspección controlada tras recarga en 320, 768, 1024 y 1440 px confirma dos grupos equivalentes, `nowrap`, animación lineal de 24 s y desplazamiento observable en modo normal; en reduced-motion confirma animación `none`, réplica oculta, lista envolvente y posiciones estables.
- **TRIANGULATE:** el cruce 23.9 s → 0.1 s a 1440 px conserva ancho de grupo 1689.5625 px y solo 14.08 px de avance equivalente, sin salto geométrico perceptible.

## Tarea 5.4 — Finales de línea y limpieza del diff

- **RED:** PHPCBF fue una fuente potencial de CRLF en Windows, por lo que se auditó individualmente cada archivo de texto modificado y cada evidencia textual generada.
- **GREEN:** los 21 archivos auditados contienen cero secuencias CRLF; `git diff --check` finaliza con código 0.
- **REFACTOR:** no se normalizó ningún archivo ajeno ni se modificaron binarios durante la comprobación.

## Tarea FIX-1.1 — Ciclo editorial de papelera y restauración

- **RED:** test `tests/php/HomeEditorialFlowsTest.php::test_editor_envia_aliado_a_papelera_y_restaura_su_disponibilidad` falla con: `se esperaba publish tras restaurar y WordPress devolvió draft`.
- **GREEN:** implementación en `tests/php/HomeEditorialFlowsTest.php`; la prueba conserva el contrato nativo `publish → trash → draft`, confirma la ausencia pública durante papelera y borrador, y republica por REST con título y logo para recuperar la disponibilidad.
- **TRIANGULATE:** la prueba verifica capacidades de edición y borrado, existencia administrativa tras restaurar y pertenencia real a `labm_theme_home_allies_posts()` antes de retirar y después de republicar.
- **REFACTOR:** no se alteraron hooks, capacidades ni código productivo de WordPress.

## Tarea FIX-1.2 — Atestación reproducible de originalidad

- **RED:** test `tests/php/HomeContentTest.php::test_demo_allies_assets_are_stable_transparent_png_files` falla con: `originality-manifest.json no existe`.
- **GREEN:** implementación en `wp-content/themes/labm/assets/images/aliados-demo/originality-manifest.json` y `tests/php/HomeContentTest.php`; el gate exige decisión manual aprobada, criterios declarados y los SHA-256 exactos de los seis PNG revisados.
- **TRIANGULATE:** la misma prueba conserva las comprobaciones de nombres, formato, 800×400 y transparencia; cualquier sustitución no revisada cambia el hash y es rechazada por CI.
- **REFACTOR:** la atestación declara que el juicio visual no equivale a una búsqueda mundial exhaustiva y evita formular una garantía algorítmica imposible.
