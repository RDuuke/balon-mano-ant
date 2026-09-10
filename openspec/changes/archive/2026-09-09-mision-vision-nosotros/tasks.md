# Tasks: Sección Misión y Visión

## Phase 1: Contratos RED

- [x] 1.1 Añadir a `tests/php/FixturesDomainTest.php` pruebas de dos entradas completas, independientes, idempotentes y con conflicto aislado.
- [x] 1.2 Añadir a `tests/php/PublicExperienceTest.php` pruebas de orden, HTML seguro y omisión de estados no públicos o vacíos.
- [x] 1.3 Añadir a `tests/e2e/public-experience.spec.ts` pruebas de 01/02, claro/oscuro, geometría responsive, desborde y Axe.

## Phase 2: Contenido GREEN

- [x] 2.1 Extender `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` con Misión y Visión idempotentes.

## Phase 3: Presentación GREEN

- [x] 3.1 Añadir `labm_theme_render_about_purpose()` a `wp-content/themes/labm/functions.php` con consulta pública independiente.
- [x] 3.2 Insertar el helper tras el banner en `wp-content/themes/labm/patterns/nosotros.php`.
- [x] 3.3 Añadir la composición clara/oscura y responsive en `wp-content/themes/labm/style.css`.

## Phase 4: Verificación

- [x] 4.1 Ejecutar PHPUnit focal y refactorizar manteniendo GREEN.
- [x] 4.2 Ejecutar PHPCS y PHPStan aplicables.
- [x] 4.3 Ejecutar Playwright focal y completo en los anchos objetivo.

## Phase 5: Cierre

- [x] 5.1 Verificar `git diff --check`, finales LF y registrar evidencia RED/GREEN.
- [x] 5.2 Ajustar y verificar fidelidad visual: tarjetas compactas iguales, gap estrecho, numerales grandes y títulos sin cortes.
- [x] 5.3 Corregir la regresión visual reportada: compartir el fondo del footer y ajustar la escala de numerales y títulos al diseño.
- [x] 5.4 Cubrir contenido editorial largo real en Misión/Visión y asegurar títulos adaptables sin desborde.
