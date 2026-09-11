# Tasks: Banner editorial de Nosotros

## Phase 1: Contratos RED

- [x] 1.1 Añadir en `tests/php/FixturesDomainTest.php` pruebas RED para artículo `banner-nosotros`, campos editoriales, imagen destacada, idempotencia y conflicto seguro.
- [x] 1.2 Añadir en `tests/php/PublicExperienceTest.php` pruebas RED del helper, HTML semántico, limpieza del marcador y omisión de estados no públicos o medios ausentes.
- [x] 1.3 Añadir en `tests/e2e/public-experience.spec.ts` prueba RED de orden, ausencia de controles, geometría responsive, desborde y Axe.

## Phase 2: Contenido GREEN

- [x] 2.1 Extender `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` para crear o actualizar el artículo y su adjunto destacado idempotente.
- [x] 2.2 Ejecutar fixtures y comprobar desde WordPress que la entrada publicada es única, editable y conserva su imagen.

## Phase 3: Presentación GREEN

- [x] 3.1 Añadir en `wp-content/themes/labm/functions.php` la consulta pública por slug y el renderizado seguro del banner estático.
- [x] 3.2 Sustituir el contenido provisional de `wp-content/themes/labm/patterns/nosotros.php` por el banner dinámico.
- [x] 3.3 Añadir en `wp-content/themes/labm/style.css` la composición partida de escritorio y el apilado móvil sin desbordes.

## Phase 4: Verificación y refactor

- [x] 4.1 Ejecutar PHPUnit focal para confirmar GREEN y refactorizar duplicación sin alterar contratos.
- [x] 4.2 Ejecutar PHPCS y PHPStan aplicables al área modificada.
- [x] 4.3 Ejecutar Playwright focal en 320 y 1440 px, guardar capturas y revisar fidelidad visual.

## Phase 5: Cierre

- [x] 5.1 Verificar `git diff --check` y finales LF únicamente en archivos modificados del cambio.
- [x] 5.2 Registrar evidencia RED/GREEN, comandos, resultados y riesgos residuales en `apply-progress.md`.
