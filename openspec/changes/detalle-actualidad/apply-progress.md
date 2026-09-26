# Progreso de implementación

## Tarea 1.1 — Contratos PHP del detalle

- **RED:** test `PublicExperienceTest::test_actualidad_detail_renders_safe_public_metadata_and_share_controls` falla con: `Call to undefined function labm_theme_render_actualidad_detail()`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; los dos casos focalizados pasan.

## Tarea 2.1 — Plantilla editorial

- **GREEN:** implementación en `wp-content/themes/labm/templates/single-labm_actualidad.html`; el contrato PHP confirma `post-content`, el shortcode de detalle y la ausencia de ubicación.

## Tarea 2.2 — Compartir seguro

- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; los enlaces Facebook y WhatsApp codifican la URL canónica y el panel se omite sin URL segura.

## Tarea 2.3 — Carga acotada del script

- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; el activo solo se encola en `is_singular( 'labm_actualidad' )`.

## Tarea 2.4 — Renderer verificado

- **GREEN:** tests `PublicExperienceTest::test_actualidad_detail_*` pasan con 15 aserciones, sin cambios de CPT, datos persistidos ni ubicación.

## Tareas 1.2 y 3.1–3.4 — Interacción y validación E2E

- **RED:** el caso E2E detectó dependencias de URL de entorno y contraste insuficiente de los metadatos.
- **GREEN:** se corrigió la navegación relativa, la inicialización fiable de copia, el fallback accesible y el color de metadatos a `#526d00`.
- **TRIANGULATE:** Playwright focalizado pasa en `desktop-1024`, `mobile-320` y `tablet-768`, incluyendo teclado, Clipboard/fallback, modo sin JavaScript, axe y ausencia de desborde.

## Tareas 4.2 y 4.3 — Verificación local

- **GREEN:** sintaxis JavaScript, PHP focalizado, `git diff --check`, finales LF y paquete `content-sync` verificado con versión y SHA-256 coincidentes.

## Tarea 4.4 - Fixtures demo completos de Actualidad

- **RED:** `FixturesDomainTest::test_home_news_fixtures_include_rich_detail_content_and_gallery` fallo porque la noticia demo solo tenia un parrafo simple, sin bloques Gutenberg, galeria ni miniatura.
- **GREEN:** `class-labm-fixtures-command.php` incorpora contenido Gutenberg, miniaturas, alternativas y una galeria `core/gallery` idempotente para la noticia demo de resultado; el evento de fecha limite ahora tiene extracto y miniatura. Las cuatro pruebas focalizadas de fixtures pasan con 54 aserciones.
- **REFACTOR:** se reutiliza un importador generico de adjuntos demo para miniaturas y galerias, preservando el nombre estable de los logos existentes.
- **SINCRONIZACION:** se ejecuto `content-sync Push` tras aplicar `wp labm fixtures load` en la base local; el paquete canonico se publico con version `20260926T042134250Z-daesca`.

## Corrección visual â€” Hero del detalle de Actualidad

- **RED:** `PublicExperienceTest::test_actualidad_detail_renders_black_hero_before_featured_media` falló porque no existía un renderer de hero y la plantilla imprimía el título fuera de la composición editorial.
- **GREEN:** `functions.php` separa los shortcodes de hero, medio y compartir; `single-labm_actualidad.html` los ordena como hero, medio, contenido y compartir. `style.css` define el hero negro con metadatos visibles y título blanco.
- **TRIANGULATE:** los tres contratos PHP focalizados pasan con 23 aserciones, incluyendo el orden de los bloques de plantilla y la exclusión de publicaciones restringidas.
- **REFACTOR:** se centralizó la validación de publicación pública para evitar que hero, medio y compartir diverjan en su criterio de visibilidad.

## Corrección visual â€” Medio destacado dentro del flujo

- **RED:** el caso E2E focalizado verificó que el contenedor de medio tenía altura `0`; la imagen reutilizaba una clase de portada con `position: absolute` y aparecía antes del hero.
- **GREEN:** el renderer del detalle usa una clase propia de imagen estática; la plantilla amplía el artículo y el hero limita el título a una composición compacta. El caso aislado de escritorio pasa con hero, medio, contenido y compartir en ese orden.
- **TRIANGULATE:** el E2E comprueba fondo negro, altura real del medio, posición de la imagen dentro de su contenedor, ausencia de desborde y dimensiones del H1 dentro del hero.
- **SINCRONIZACION:** se restauraron `home` y `siteurl` locales tras el navegador Docker y se actualizó el paquete canónico `20260926T054244664Z-daesca` con hash verificado.

## Corrección visual â€” Hero y medio a ancho completo

- **RED:** el E2E focalizado falló con el hero a `x=152` px dentro del contenedor editorial, en vez de ocupar el viewport.
- **GREEN:** la plantilla usa un artículo `alignfull` para hero y medio, y un grupo interno exclusivo para lectura, galería, compartir y retorno. Los dos bloques visuales pasan a ocupar el ancho completo sin alterar el contenido editorial.
- **TRIANGULATE:** a 1024 px el E2E comprueba hero y medio desde `x=0`, con ancho mínimo de `1023` px, sin desborde horizontal y con el contenido posterior en orden.
- **SINCRONIZACION:** se restauraron las URLs locales tras el navegador Docker; `canonical.zip` y `latest.json` mantienen versión `20260926T054244664Z-daesca` y SHA-256 coincidente.
