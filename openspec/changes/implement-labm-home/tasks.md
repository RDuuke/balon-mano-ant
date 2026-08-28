# Tareas: Implementar el Home LABM

## Fase 1: Fundamentos editoriales

- [x] 1.1 RED — Crear `tests/php/HomeContentTest.php` para exigir registro no público, metadatos saneados, permisos y rechazo de slides/aliados inválidos.
- [x] 1.2 GREEN — Crear `wp-content/plugins/labm-core/includes/class-labm-home-content.php` con CPT, metadatos REST, autorización y validación editorial.
- [x] 1.3 GREEN — Cargar el módulo desde `wp-content/plugins/labm-core/labm-core.php` e integrar capacidades/versionado en `includes/class-labm-domain.php`.
- [x] 1.4 REFACTOR — Ampliar `includes/class-labm-fixtures-command.php` con slides y aliados ficticios idempotentes sin sobrescribir contenido ajeno.

## Fase 2: Composición pública

- [x] 2.1 RED — Crear `tests/php/HomePresentationTest.php` para exigir consultas `publish`, orden editorial, límites, fallbacks y ausencia de secciones retiradas.
- [x] 2.2 GREEN — Añadir en `wp-content/themes/labm/functions.php` consultas y render seguro para slider, clubes, evento, actualidad y aliados.
- [x] 2.3 GREEN — Rehacer `wp-content/themes/labm/patterns/inicio.php` con el orden aprobado y actualizar `templates/front-page.html` para secciones de ancho completo.
- [x] 2.4 REFACTOR — Sustituir el contrato legado de `labm_theme_home_sections` en `functions.php` y `tests/php/VerifyCorrectivesTest.php`, preservando Selecciones.

## Fase 3: Interfaz e interacción

- [x] 3.1 RED — Ampliar `tests/e2e/home.spec.ts` con orden, controles por teclado, pausa, estado activo, aliados y exclusión de Piso/Playa/horarios/escenarios.
- [x] 3.2 RED — Actualizar `tests/php/FrontendTokensTest.php` para exigir paleta aprobada, foco, contraste estructural y movimiento reducido.
- [x] 3.3 GREEN — Actualizar `parts/header.html` y `parts/footer.html` con barra institucional, navegación, CTA y pie del mockup.
- [x] 3.4 GREEN — Crear `assets/home.js` y encolarlo desde `functions.php` para slider, pausa y marquee con copia visual `aria-hidden` e inerte.
- [x] 3.5 GREEN — Actualizar `theme.json` y `style.css` con tokens, layout responsive y estados estáticos en foco, pausa y movimiento reducido.
- [x] 3.6 REFACTOR — Revisar markup de `inicio.php` y `assets/home.js` para eliminar controles vacíos, foco duplicado y anuncios redundantes.

## Fase 4: Verificación

- [x] 4.1 Ejecutar PHPUnit sobre `tests/php/HomeContentTest.php` y `tests/php/HomePresentationTest.php`; corregir únicamente regresiones relacionadas hasta GREEN.
- [x] 4.2 Ampliar `tests/e2e/public-experience.spec.ts` para conservar rutas Piso/Playa y validar 320, 768, 1024 y 1440 px sin desborde.
- [x] 4.3 Ejecutar `tests/e2e/home.spec.ts` y `tests/e2e/public-experience.spec.ts` con Playwright/axe, teclado, movimiento reducido y cada ancho objetivo.
- [x] 4.4 Ejecutar contratos de `composer.json`, `phpcs.xml.dist` y `phpstan.neon.dist`; resolver hallazgos introducidos por el cambio.

## Fase 5: Cierre

- [x] 5.1 Ejecutar `scripts/gate.ps1` y registrar comandos, versiones, resultados y cualquier auditoría explícitamente diferida.
- [x] 5.2 Revisar el diff final contra specs y `design.md`, confirmando que no se borraron CPT, taxonomías, datos ni rutas de Selecciones.

## Fase 6: Correctivos de VERIFY

- [ ] 6.1 RED/GREEN — Añadir en `tests/php/HomeContentTest.php` una prueba que cambie de tema y verifique persistencia de slides, aliados, metadatos y permisos.
- [ ] 6.2 RED/GREEN — Añadir una prueba aislada en `tests/php/HomePresentationTest.php` que cargue el tema sin `labm-core` y valide fallbacks sin fatal.
- [ ] 6.3 RED/GREEN — Cubrir en `tests/php/HomeContentTest.php` publicación autorizada y rechazo de crear, publicar, editar y eliminar sin capacidades.
- [ ] 6.4 RED/GREEN — Cubrir texto largo, medios ausentes, estados no públicos y entradas manipuladas en `tests/php/HomeContentTest.php` y `HomePresentationTest.php`.
- [ ] 6.5 RED/GREEN — Rehacer `tests/e2e/home.spec.ts` con fixtures publicados para ejercer slider, indicadores, pausa, movimiento reducido y aliados sin salidas tempranas.
- [ ] 6.6 Ejecutar `scripts/gate.ps1` y `scripts/browser-gate.ps1`, y remapear los 24 escenarios de `verify-report.md`.
