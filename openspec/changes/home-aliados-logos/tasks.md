# Tareas: Marquee de logos de Aliados Oficiales

## Fase 1: Fundamentos y recursos
- [ ] 1.1 RED: ampliar `tests/php/HomeContentTest.php` para exigir soportes `title`, `thumbnail`, `page-attributes` y excluir editor, extracto y campos personalizados.
- [ ] 1.2 RED: ampliar `tests/php/HomeEditorialFlowsTest.php` con publicación REST/admin válida e inválida, avisos, capacidades, nonce, borrador y conservación de datos legados.
- [ ] 1.3 GREEN: especializar CPT y validación sin recursión en `wp-content/plugins/labm-core/includes/class-labm-home-content.php` hasta superar 1.1–1.2.
- [ ] 1.4 En APPLY, usar ImageGen para crear seis logos ficticios originales, transparentes y diferenciados; revisar que no imiten marcas reales.
- [ ] 1.5 Normalizar los seis recursos a PNG 800×400 en `wp-content/themes/labm/assets/images/aliados-demo/` y comprobar dimensiones, transparencia y nombres estables.

## Fase 2: Fixtures y selección
- [ ] 2.1 RED: ampliar pruebas de fixtures en `tests/php/FixturesDomainTest.php` para seis adjuntos PNG, miniaturas, `menu_order`, ausencia de URL e idempotencia tras dos cargas.
- [ ] 2.2 GREEN: adaptar `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` para importar/reutilizar los seis logos y crear aliados ordenados sin contenido enlazable.
- [ ] 2.3 RED: ampliar `tests/php/HomePresentationTest.php` con orden `menu_order`/título/ID, filtro antes del límite de 12, borradores e imágenes inválidas.
- [ ] 2.4 GREEN: ajustar consulta y colección en `wp-content/themes/labm/functions.php` hasta satisfacer 2.3.

## Fase 3: Presentación marquee
- [ ] 3.1 RED: exigir en `tests/php/HomePresentationTest.php` HTML solo con imágenes, `alt` saneado, lista primaria y réplica `aria-hidden`/`inert`, sin enlaces, texto ni controles.
- [ ] 3.2 GREEN: implementar `labm_theme_render_home_allies()` en `wp-content/themes/labm/functions.php`, con salida vacía sin logos y réplica idéntica no accesible.
- [ ] 3.3 Retirar únicamente la lógica JavaScript de aliados en `wp-content/themes/labm/assets/home.js`, conservando intactas las demás secciones.
- [ ] 3.4 GREEN: implementar en `wp-content/themes/labm/style.css` la pista lineal de 24 s, cajas con `object-fit: contain` y continuidad por grupos equivalentes.
- [ ] 3.5 GREEN: añadir a `wp-content/themes/labm/style.css` el modo `prefers-reduced-motion`, sin animación, réplica oculta y lista primaria envolvente.
- [ ] 3.6 REFACTOR: simplificar PHP/CSS/JS preservando contratos, convenciones WordPress y datos legados.

## Fase 4: Verificación automatizada
- [ ] 4.1 RED: ampliar `tests/e2e/home.spec.ts` para verificar dos grupos equivalentes, animación lineal de 24 s y ausencia de pausa, velocidad, enlaces y texto visible.
- [ ] 4.2 GREEN: completar ajustes de presentación hasta superar Playwright en movimiento normal.
- [ ] 4.3 Ampliar `tests/e2e/home.spec.ts` para reduced-motion, `alt`, Axe, proporción y ausencia de overflow a 320, 768, 1024 y 1440 px.
- [ ] 4.4 Ejecutar pruebas PHP focales, fixtures dos veces y Playwright focal; corregir regresiones dentro del alcance.

## Fase 5: Gates y cierre
- [ ] 5.1 Ejecutar análisis estático y formato aplicables; corregir hallazgos en archivos afectados.
- [ ] 5.2 Ejecutar `scripts/gate.ps1 -IncludeBrowser` y confirmar PHPUnit, integración, cobertura ≥80 %, PHPCS, PHPStan y Playwright completos.
- [ ] 5.3 Inspeccionar visualmente logos y marquee en 320–1440 px, incluyendo reduced-motion y continuidad sin saltos perceptibles.
- [ ] 5.4 Verificar LF en cada archivo de texto modificado y `git diff --check`, sin normalizar archivos ajenos.
