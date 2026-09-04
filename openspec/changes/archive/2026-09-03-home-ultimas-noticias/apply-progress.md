# Progreso de APPLY: Últimas noticias

## Tarea 1.1 — Contrato de fixtures
- **RED:** test `FixturesDomainTest.php::test_home_news_fixtures_are_complete_categorized_and_deterministic` falla: `demo-labm-noticia-resultado no existe`.
- **GREEN:** prueba persistida; pasa con la implementación.
- **TRIANGULATE:** cubre seis slugs, categoría, fechas, rutas y conteo.

## Tarea 1.2 — Seis noticias demo
- **RED:** test `FixturesDomainTest.php::test_home_news_fixtures_are_complete_categorized_and_deterministic` falla por publicación ausente.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; 5 tests/61 aserciones pasan tras dos cargas.
- **TRIANGULATE:** la recarga conserva exactamente seis entradas.

## Tarea 1.3 — Datos centralizados
- **RED:** el mismo test falla antes del proveedor de colección.
- **GREEN:** proveedor privado en `class-labm-fixtures-command.php`; suite verde.
- **REFACTOR:** elimina repetición y preserva fixtures previos.

## Tarea 2.1 — Consultas aisladas
- **RED:** test `HomePresentationTest.php::test_home_news_query_orders_limits_and_excludes_events` falla con función inexistente.
- **GREEN:** consultas en `functions.php`; límite, orden y exclusión pasan.
- **TRIANGULATE:** cubre borrador, evento y orden estable.

## Tarea 2.2 — Contratos de consulta
- **RED:** el test anterior falla con `labm_theme_home_news_query` inexistente.
- **GREEN:** noticias y evento no comparten IDs.
- **REFACTOR:** cada dominio conserva consulta específica.

## Tarea 2.3 — Jerarquía 1+3
- **RED:** test `HomePresentationTest.php::test_home_news_renders_featured_sidebar_metadata_and_archive_cta` no encuentra destacada.
- **GREEN:** render en `functions.php`; una destacada y tres laterales pasan.
- **TRIANGULATE:** colección parcial/vacía conserva salida segura.

## Tarea 2.4 — CTA y medios
- **RED:** test `HomePresentationTest.php::test_home_news_helpers_omit_invalid_cta_and_use_safe_media_fallbacks` falla con helper inexistente.
- **GREEN:** CTA inválido se omite y traversal usa fallback.
- **TRIANGULATE:** cubre ruta local y alt decorativo.

## Tarea 2.5 — Render semántico
- **RED:** el test de render recibe tarjetas genéricas.
- **GREEN:** encabezado, enlaces, tiempos y categorías pasan.
- **TRIANGULATE:** títulos se muestran sin marcador técnico.

## Tarea 2.6 — Helpers seguros
- **RED:** el test de helpers falla antes de separar contratos.
- **GREEN:** helpers en `functions.php`; 10 tests/64 aserciones pasan.
- **REFACTOR:** medios, metadatos, título y archivo quedan separados.

## Tarea 3.1 — Integración editorial
- **RED:** el test PHP de render falla antes del marcado 1+3.
- **GREEN:** pruebas en `home.spec.ts`; estructura, CTA, enlaces y fechas pasan.
- **TRIANGULATE:** cuatro proyectos Playwright cubren el contrato.

## Tarea 3.2 — Responsive y axe
- **RED:** test `home.spec.ts::ultimas noticias conserva orden y geometria responsive sin desborde` recibe `scrollWidth 1552` a 320 px.
- **GREEN:** 320/768/1024/1440 pasan sin overflow.
- **TRIANGULATE:** axe WCAG 2.2 AA pasa en cuatro viewports.

## Tarea 3.3 — Estilos móviles
- **RED:** el test responsive falla por ancho intrínseco de imágenes.
- **GREEN:** estilos en `style.css`; medios fluidos, foco y columna móvil pasan.
- **REFACTOR:** selectores encapsulados en `.labm-home-news`.

## Tarea 3.4 — Breakpoints
- **RED:** el test responsive detecta una columna en desktop.
- **GREEN:** tarjetas horizontales y grid 3/2 pasan.
- **TRIANGULATE:** cuatro anchos objetivo verificados.

## Tarea 3.5 — Aislamiento CSS
- **RED:** suite `public-experience.spec.ts` registra overflow global antes del encapsulado.
- **GREEN:** suite completa Playwright pasa 72/72.
- **REFACTOR:** evento y grid genérico permanecen sin cambios.

## Tarea 4.1 — PHPUnit focal
- **RED:** `HomePresentationTest.php` falla con consultas/helpers inexistentes.
- **GREEN:** 15 tests y 126 aserciones focales pasan.

## Tarea 4.2 — Idempotencia real
- **RED:** el test de fixtures falla antes de cargar la colección.
- **GREEN:** dos ejecuciones `wp labm fixtures load`; seis noticias y una categoría.
- **TRIANGULATE:** contenido ajeno conflictivo se preserva con advertencia.

## Tarea 4.3 — Verificación visual
- **RED:** el test responsive falla en cuatro viewports antes del CSS.
- **GREEN:** captura `artifacts/home-news-1440.png` revisada; composición 1+3 conforme.
- **TRIANGULATE:** focal final Playwright pasa 12/12.

## Tarea 4.4 — Gates locales
- **RED:** `composer lint` falla con 44 errores WPCS.
- **GREEN:** PHPCS, PHPStan, unitarias, integración y cobertura pasan.
- **TRIANGULATE:** browser gate directo pasa 72/72.

## Tarea 5.1 — Limpieza
- **RED:** suite pública falla con overflow antes de encapsular.
- **GREEN:** suite completa pasa; archivo de actualidad sin cambios.
- **REFACTOR:** marcado, helpers y CSS quedan acotados a portada.

## Tarea 5.2 — LF
- **RED:** `ReadAllText.Contains("\r\n")` detecta CRLF en `FixturesDomainTest.php`.
- **GREEN:** normalización limitada; comprobación final sin CRLF.

## Tarea 5.3 — Registro
- **RED:** auditoría detecta evidencia APPLY ausente y tareas pendientes.
- **GREEN:** `apply-progress.md`, `tasks.md` y estado registran 21 tareas.
- **REFACTOR:** resultados y advertencias quedan centralizados.

## Tarea 6.1 — Colecciones parciales y vacías

- **RED:** no disponible: las pruebas nuevas de colección parcial, jerarquía y colección vacía pasaron con la implementación existente; no se inventa evidencia RED.
- **GREEN:** tests `HomePresentationTest.php::test_home_news_renders_partial_collection_with_stable_hierarchy` y `test_home_news_omits_section_when_collection_is_empty` pasan.
- **TRIANGULATE:** cubre tres noticias con jerarquía 1+2, ausencia de duplicados y cero noticias sin sección residual.

## Tarea 6.2 — CTA, miniatura, meta y fallback

- **RED:** el primer intento falló porque el fixture de adjunto no exponía una fuente de imagen; fue un defecto de preparación del test, no un fallo de producción. Tras corregir el fixture, la implementación existente pasó; no se atribuye un RED de producción.
- **GREEN:** tests `HomePresentationTest.php::test_home_news_render_omits_archive_cta_when_url_is_unavailable` y `test_home_news_media_priority_and_missing_category_are_safe` pasan sin modificar producción.
- **TRIANGULATE:** afirma miniatura antes que meta permitida, meta antes que fallback, categoría ausente sin texto inventado y CTA completamente omitido.

## Tarea 6.3 — Teclado y foco a 320 px

- **RED:** no disponible: el test nuevo pasó con los estilos existentes; no se inventa evidencia RED.
- **GREEN:** test `home.spec.ts::ultimas noticias conserva orden secuencial y foco visible a 320 pixeles` pasa en la suite Playwright.
- **TRIANGULATE:** recorre todos los enlaces en orden DOM, exige contorno de al menos 2 px y confirma ausencia de scroll horizontal.

## Tarea 6.4 — Saneamiento de uploads

- **RED:** inspección previa encontró 6 archivos en `labm-club-import` —incluido un PHP— y 1 archivo en `labm-event-import`.
- **GREEN:** se eliminaron exclusivamente ambos directorios temporales; `uploads/2026/09` conserva 29 medios.
- **REFACTOR:** no se borraron respaldos, volúmenes ni medios administrados por WordPress.

## Tarea 6.5 — Cadena canónica válida

- **RED:** `latest.json` contenía `baseVersion: unversioned`.
- **GREEN:** la opción local se fijó controladamente en `20260831T004739066Z-rduuqe-RDUUQE` y se generó la versión `20260903T002837185Z-USUARIO-DESKTOP-JP8MFJ8`.
- **REFACTOR:** se usó `ForceStale` únicamente para sustituir el puntero inválido generado durante la consolidación; el manifiesto final conserva la base correcta.

## Tarea 6.6 — Validación proporcional

- **RED:** el paquete anterior contenía 36 entradas de uploads, 2 directorios temporales y 1 PHP.
- **GREEN:** paquete final con 29 medios, 0 PHP, 0 entradas temporales, 0 errores de manifiesto y checksum `6a46e9d726a4ba05cf84997fd1134f56857a0521108a7b47bc8303d9b9dc5637` coincidente.
- **TRIANGULATE:** PHPUnit focal final 4/4 con 18 aserciones y suite de portada 14/14; PHPCS, PHPStan y Playwright 76/76 pasan.

## Tarea 6.7 — Registro de remediación

- **RED:** el informe VERIFY exigía volver a APPLY y no existían tareas explícitas de remediación.
- **GREEN:** tareas 6.1–6.7, progreso, estado y log reflejan el trabajo y sus advertencias reales.
- **REFACTOR:** el siguiente paso queda en VERIFY; no se ejecutó commit ni `git push`.

## Tarea 6.8 — Fixture sin medio disponible

- **RED:** test `FixturesDomainTest.php::test_home_news_fixture_without_available_media_remains_consultable_with_fallback` falla porque conserva `assets/images/hero-balonmano-seleccion-v1.png` cuando el activo simulado no existe.
- **GREEN:** `class-labm-fixtures-command.php` comprueba el archivo del tema, elimina solo la meta `labm_demo_image` no disponible y mantiene publicada la noticia; el test pasa con 5 aserciones.
- **TRIANGULATE:** la suite `FixturesDomainTest.php` pasa 6/6 con 66 aserciones y la integración completa pasa 64/64 con 393 aserciones.
- **REFACTOR:** la condición queda limitada a la meta de imagen demo; PHPCS y PHPStan pasan sin cambios adicionales.
