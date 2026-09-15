# Tasks: Artículo inicial de Documentos

## Phase 1: Contrato RED

- [x] 1.1 Añadir en `tests/php/PublicExperienceTest.php` pruebas que exijan el encabezado editable, saneado y ausente cuando no sea publicable.
- [x] 1.2 Ejecutar la prueba focal y confirmar que falla antes de incorporar el renderizador.

## Phase 2: Implementación GREEN

- [x] 2.1 Añadir `labm_theme_render_documents_banner()` en `wp-content/themes/labm/functions.php` con el slug `banner-documentos`, extracto prioritario y escape de texto.
- [x] 2.2 Crear `wp-content/themes/labm/patterns/documentos.php` para componer exclusivamente el renderizador del encabezado.
- [x] 2.3 Crear `wp-content/themes/labm/templates/page-documentos.html` con cabecera, patrón Documentos y pie global.
- [x] 2.4 Añadir en `wp-content/themes/labm/style.css` el bloque negro, tipografía, espaciado, contraste y adaptación móvil de `.labm-documents-banner`.

## Phase 3: Refactor e integración

- [x] 3.1 Ajustar las pruebas de composición para comprobar patrón, plantilla y la independencia de otras rutas públicas.
- [x] 3.2 Ejecutar la prueba focal hasta obtener resultado GREEN y eliminar duplicación o formato innecesario dentro del alcance.

## Phase 4: Verificación

- [x] 4.1 Ejecutar PHPUnit focal, PHPCS y PHPStan aplicables al tema y pruebas modificadas.
- [x] 4.2 Verificar que todos los archivos modificados tienen LF y revisar el diff del cambio para descartar efectos ajenos.
