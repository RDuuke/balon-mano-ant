# Tareas: Detalle editorial de actualidad

## Fase 1: Contratos TDD

- [x] 1.1 RED — En `tests/php/PublicExperienceTest.php`, definir casos de detalle público, no público, metadatos, medio ausente, galería y compartir seguro.
- [x] 1.2 RED — En `tests/e2e/public-experience.spec.ts`, definir recorrido del detalle, acciones de compartir, copia sin JavaScript, teclado, axe y 320–1440 px.

## Fase 2: Renderizado editorial

- [x] 2.1 En `wp-content/themes/labm/templates/single-labm_actualidad.html`, componer hero, medio, `post-content`, compartir y retorno con jerarquía semántica.
- [x] 2.2 En `wp-content/themes/labm/functions.php`, registrar `[labm_actualidad_detalle]` con metadatos y URLs de Facebook/WhatsApp escapadas, solo para publicación canónica segura.
- [x] 2.3 En `wp-content/themes/labm/functions.php`, encolar el comportamiento de copia exclusivamente para `is_singular( 'labm_actualidad' )`.
- [x] 2.4 GREEN — Ejecutar los casos PHP de 1.1 y ajustar el renderer sin cambiar CPT, datos ni ubicación.

## Fase 3: Interacción y presentación

- [x] 3.1 En `wp-content/themes/labm/assets/actualidad-detail.js`, mejorar “Copiar enlace” con Clipboard API, mensaje accesible y conservación del campo `readonly` sin JavaScript.
- [x] 3.2 En `wp-content/themes/labm/style.css`, añadir `labm-actualidad-detail-*` para hero, lectura, panel compartir, galería `core/gallery`, foco y contraste.
- [x] 3.3 En `wp-content/themes/labm/style.css`, adaptar 320/768/1024/1200/1440 px sin desborde, recorte ni solapamiento.
- [x] 3.4 GREEN — Ejecutar los E2E de 1.2, incluidos modo sin JavaScript y acción de copia simulada.

## Fase 4: Verificación local

- [ ] 4.1 REFACTOR — Eliminar duplicación de helpers y selectores sin cambiar los contratos de SPEC; repetir las pruebas focales.
- [x] 4.2 Ejecutar formato y análisis aplicables, comprobar LF de los archivos del alcance y corregir solo CRLF introducidos.
- [x] 4.3 Registrar evidencia en `openspec/changes/detalle-actualidad/apply-progress.md` para cada ciclo RED/GREEN.
- [x] 4.4 Complementar los fixtures demo de Actualidad, aplicarlos localmente y sincronizar el contenido canonico.
