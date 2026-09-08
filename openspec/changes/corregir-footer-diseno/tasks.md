# Tasks: Footer completamente administrable

## Fase 1: Contratos

- [x] 5.1 RED: crear `tests/php/FooterSettingsTest.php` para defaults, esquema completo, saneado, permisos y render seguro.
- [x] 5.2 RED: adaptar `tests/php/FrontendTokensTest.php` para exigir un template part sin contenido editorial literal.

## Fase 2: Administración

- [x] 6.1 GREEN: crear `class-labm-footer-settings.php` con opción, defaults y sanitización por tipo.
- [x] 6.2 GREEN: registrar página LABM → Footer con `edit_theme_options`, nonce de Settings API y todos los campos.

## Fase 3: Renderizado

- [x] 7.1 GREEN: renderizar shortcode escapado, omitiendo valores vacíos y conservando clases/estructura visual.
- [x] 7.2 GREEN: cargar el módulo desde `labm-core.php` y sustituir literales de `parts/footer.html` por el invocador.

## Fase 4: Verificación

- [x] 8.1 REFACTOR: ejecutar suites focales, lint, análisis estático aplicable y corregir hallazgos.
- [x] 8.2 Verificar visualmente si hay navegador disponible, ejecutar `git diff --check` y confirmar LF.
