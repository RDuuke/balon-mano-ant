# Tareas: Estabilizar layout responsive y slider

Referencias: R1 identidad responsive (escenarios R1.1â€“R1.3), R2 slider estable (R2.1â€“R2.3) y R3 verificaciÃ³n (R3.1â€“R3.3). La accesibilidad se limita a conservar el baseline y evitar regresiones indispensables.

## Fase 1: FundaciÃ³n (lote A)

- [x] 1.1 RED â€” Ampliar `tests/php/FrontendTokensTest.php` para exigir `wideSize: 1200px`, `--labm-content-max` y gutter compartido (R1.1, R3.1); depende de: ninguna.
- [x] 1.2 GREEN â€” Ajustar `wp-content/themes/labm/theme.json` y tokens base de `wp-content/themes/labm/style.css` para satisfacer 1.1 (R1.1); depende de: 1.1.
- [x] 1.3 REFACTOR â€” Consolidar variables y eliminar valores de ancho/gutter duplicados en `style.css`, sin cambiar el baseline accesible (R1.1â€“R1.3); depende de: 1.2.

## Fase 2: Layout responsive (lote B)

- [x] 2.1 RED â€” AÃ±adir en `tests/e2e/public-experience.spec.ts` mediciones fallidas de centrado, ancho mÃ¡ximo y simetrÃ­a â‰¤1 px a 1440 px (R1.1, R3.1, R3.3); depende de: 1.3.
- [x] 2.2 GREEN â€” Aplicar en `style.css` contenedores centrados de mÃ¡ximo 1200 px, manteniendo fondos `alignfull` (R1.1); depende de: 2.1.
- [x] 2.3 RED â€” Cubrir en `public-experience.spec.ts` gutters, eje comÃºn y overflow global a 320/768/1024/1200/1440 px (R1.2, R1.3, R3.2); depende de: 2.2.
- [x] 2.4 GREEN â€” Reordenar en `style.css` reglas base, `768px` y `1024px`, y corregir gutters/alineaciones hasta aprobar 2.3 (R1.2, R1.3); depende de: 2.3.

## Fase 3: Slider estable (lote C)

- [x] 3.1 RED â€” Ampliar `tests/e2e/home.spec.ts` para recorrer anterior, siguiente e indicadores y exigir altura exterior idÃ©ntica por viewport (R2.1, R2.2, R3.2, R3.3); depende de: 2.4.
- [x] 3.2 GREEN â€” Definir en `style.css` `block-size` responsive compartido, reserva para controles y scroll interno del contenido extremo (R2.1â€“R2.3); depende de: 3.1.
- [x] 3.3 RED/GREEN â€” Cubrir fixture con texto extremo y medio ausente en `tests/php/HomePresentationTest.php` y `home.spec.ts`; ajustar solo marcado PHP existente si falla (R2.3); depende de: 3.2.
- [x] 3.4 REFACTOR â€” Confirmar que `assets/home.js` no requiere sincronizaciÃ³n; modificarlo Ãºnicamente si los recorridos prueban una necesidad, preservando `hidden`, `aria-current` y reduced motion (R2.1â€“R2.3); depende de: 3.3.

## Fase 4: VerificaciÃ³n (lote D)

- [x] 4.1 Ejecutar PHPUnit, lint, anÃ¡lisis estÃ¡tico y pruebas JS aplicables; corregir solo regresiones del cambio (R1â€“R3); depende de: 3.4.
- [x] 4.2 Ejecutar Playwright responsive completo y registrar ancho/condiciÃ³n en cada fallo geomÃ©trico (R3.1â€“R3.3); depende de: 4.1.
- [x] 4.3 Ejecutar el gate axe existente como baseline/no regresiÃ³n, sin incorporar mejoras accesibles fuera de alcance (R1.2, R2.1â€“R2.3); depende de: 4.2.

## Fase 6: Recuperación tras VERIFY fallido (lote F)

- [x] 6.1 RED — Reproducir los cinco bloqueos del `verify-report.md` con el subset Playwright y aislar la causa común de carga de activos; depende de: 5.1.
- [x] 6.2 GREEN — Sincronizar `home`/`siteurl` con `WP_URL` durante la ejecución del navegador y confirmar subset, gate Playwright completo y PHPUnit sin modificar el producto; depende de: 6.1.

## Fase 5: Cierre (lote E)

- [x] 5.1 Revisar diff contra Pen y alcance, documentar resultados y rollback de `theme.json`, `style.css` y pruebas en el reporte de aplicaciÃ³n; depende de: 4.3.

