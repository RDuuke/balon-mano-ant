# Tareas: corregir el gate global

Cada lote sigue RED→GREEN→REFACTOR genuino, registrando comandos/resultados en `apply-progress.md`; fallos antiguos solo son diagnóstico. Dependencias: 1 antes de 2–5; 6 condiciona únicamente sus cambios; 7 requiere todos. Sin ciclos.

## Fase 1: evidencia y aislamiento
- [x] 1.1 Registrar baseline, ramas Clover y recursos/asociaciones propios en `apply-progress.md`; backup oficial `scripts/content-sync.ps1` antes de pruebas mutables.
- [x] 1.2 Mapear los 36 escenarios de `specs/` a tareas/comprobaciones en `apply-progress.md`, preservando snapshots Selecciones/Detalle.

## Fase 2: contratos autónomos
- [x] 2.1 RED: `tests/php/PublicExperienceTest.php` y `tests/e2e/public-experience.spec.ts`: grupos visibles/vacíos, privacidad, teclado, sin JS y limpiar filtros por región.
- [x] 2.2 GREEN: `wp-content/themes/labm/functions.php`: vacío seleccionado y restablecimiento correcto; corregir aserciones semánticas sin reducir garantías.
- [x] 2.3 REFACTOR: consolidar comprobaciones focales en `tests/e2e/public-experience.spec.ts` y demostrar GREEN conservado.
- [x] 2.4 RED: `tests/php/FixturesDomainTest.php`: recurso aislado fallido, recuperación idempotente y preservación ajena.
- [x] 2.5 GREEN/REFACTOR: precargar disponibles, retirar solo objetivo, restaurar filtro en `finally` en `tests/php/FixturesDomainTest.php`; verificar diagnóstico/recuperación.
- [ ] 2.6 RED: `tests/e2e/document-admin.spec.ts`: reemplazo, colisión, inválido/fallo conservando asociación; usar identidad/nombre/tamaño reales.
- [ ] 2.7 GREEN/REFACTOR: corregir selección/aserciones y limpieza idempotente en `tests/e2e/document-admin.spec.ts`; restaurar asociación previa.
- [ ] 2.8 RED: `tests/php/PublicExperienceTest.php`: destacada+tarjetas únicas, orden, privacidad, filtros, última página y fuera de rango.
- [ ] 2.9 GREEN/REFACTOR: ajustar roles editoriales en `tests/e2e/public-experience.spec.ts` y prueba PHP sin cambiar paginación vigente.
- [x] 2.10 RED: `tests/php/HomePresentationTest.php` y `tests/e2e/home.spec.ts`: prioridad de medios, metadatos, referencias inválidas/restringidas.
- [x] 2.11 GREEN/REFACTOR: verificar/corregir `labm_theme_news_fallback_path` en `wp-content/themes/labm/functions.php`; conservar respaldo institucional seguro.

## Fase 3: contraste autónomo
- [ ] 3.1 RED: `tests/e2e/public-experience.spec.ts`: registrar contraste insuficiente, estado/fondo/componente y foco en cinco viewports.
- [ ] 3.2 GREEN/REFACTOR: ajustar `--labm-green-dark` en `wp-content/themes/labm/style.css`; validar usos/estados, axe y umbrales 4.5:1/3:1.

## Fase 4: calidad autónoma
- [x] 4.1 Registrar fallos PHPCS/PHPStan actuales en `apply-progress.md`; RED conductual antes de cambios lógicos.
- [x] 4.2 Corregir WPCS en `wp-content/themes/labm/functions.php` y `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`, conservando escape/sanitización.
- [x] 4.3 Corregir `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`; retirar wrapper únicamente sin referencias; demostrar análisis/GREEN y refactor seguro.

## Fase 5: cobertura autónoma
- [ ] 5.1 RED→GREEN→REFACTOR: cubrir ramas reales documentadas en `tests/php/DocumentContactTest.php`, éxito/borde/error.
- [ ] 5.2 RED→GREEN→REFACTOR: cubrir ramas restantes en `tests/php/ClosingCoverageTest.php`; medir Clover fresco≥80% sin alterar fuentes/suites.

## Fase 6: condiciones visuales y handoff
- [ ] 6.1 Extraer referencias/medidas existentes Documentos y frame Actualidad `Nrclx` en `apply-progress.md`; registrar aprobación vigente a320/768/1024/1200/1440px.
- [ ] 6.2 Tras6.1, RED→GREEN→REFACTOR en `tests/e2e/public-experience.spec.ts`/`wp-content/themes/labm/style.css`: composición y texto largo sin desborde.
- [ ] 6.3 Registrar handoff reconciliado de Detalle en `apply-progress.md`; hasta recibirlo bloquear plantilla/aserciones asociadas; preservar pendientes.
- [ ] 6.4 Tras6.3, RED→GREEN→REFACTOR: ajustar exclusivamente contratos globales reconciliados de `wp-content/themes/labm/templates/single-labm_actualidad.html` y pruebas asociadas.

## Fase 7: cierre
- [ ] 7.1 Limpiar/restaurar recursos propios; si persistencia cambió, ejecutar `scripts/content-sync.ps1` oficial y verificar versión/hash/exclusiones; comprobar LF modificados.
- [ ] 7.2 Ejecutar `scripts/gate.ps1 -IncludeBrowser` íntegro; registrar cobertura fresca y todas las etapas en `verify-report.md`; emitir handoff sin archivar cambios ajenos.
