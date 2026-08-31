# Tasks: Sitio WordPress de LABM

## Alcance reconciliado (2026-08-27)

- Lighthouse, SEO y la garantía integral WCAG 2.2 AA quedan diferidos y nunca `COMPLIANT` en este cambio.
- La evidencia Lighthouse existente es histórica, no un gate de cierre.
- La tarea 5.4, hosting, SMTP real, PDF reales y datos reales permanecen fuera de alcance.

## Fase 1: Base

- [x] 1.1 Crear `.gitignore` para excluir secretos, datos locales, dependencias, core y `artifacts/`; probar exclusiones.
- [x] 1.2 Crear `.env.example` y `scripts/validate-env.ps1`; probar valores ausentes sin filtrar secretos.
- [x] 1.3 Crear `compose.yaml` con servicios, healthchecks y volúmenes; validar `docker compose config`.
- [x] 1.4 Añadir `tests/smoke/` para HTTP, DB, fallos y persistencia.
- [x] 1.5 Crear `wp-content/themes/labm/`; probar activación aislada y ruta pública.
- [x] 1.6 Crear `wp-content/plugins/labm-core/`; probar activación aislada y cambio de tema.
- [x] 1.7 Implementar fallback seguro del tema sin `labm-core`; cubrir RED→GREEN→REFACTOR.
- [x] 1.8 Crear fixtures idempotentes ficticios en `labm-core`; probar repetición, contenido ajeno y exclusión de PDF.
- [x] 1.9 Crear `scripts/bootstrap.ps1`; probar dos ejecuciones consecutivas.
- [x] 1.10 Configurar Composer, WPCS, PHPStan y PHPUnit; ejecutar cada gate.
- [x] 1.11 Configurar Playwright/axe en `tests/e2e/` para 320/768/1024/1440.
- [x] 1.12 Conservar Lighthouse CI como baseline histórico; diferir validación y umbrales.
- [x] 1.13 Crear `scripts/gate.ps1` con versiones, salidas y retornos; identificar gates no ejecutados.
- [x] 1.14 Documentar ciclo de vida, pruebas y reset confirmado en `README.md` y `docs/`.

## Fase 2: Dominio

- [x] 2.1 Escribir RED en `tests/php/` para dominio, permisos, validación e i18n.
- [x] 2.2 Implementar dominio en `wp-content/plugins/labm-core/` hasta GREEN; probar persistencia entre temas.
- [x] 2.3 Ampliar fixtures de `labm-core` con bordes y privados; preservar contenido editorial.

## Fase 3: Frontend

- [x] 3.1 Crear patrones Gutenberg en `wp-content/themes/labm/`; probar teclado, foco y rutas ausentes.
- [x] 3.2 Implementar listados y filtros en tema/plugin; cubrir paginación, vacíos y privacidad.
- [x] 3.3 Ajustar layouts responsive del tema; probar accesibilidad concreta y movimiento reducido en cuatro anchos.

## Fase 4: Documentos y contacto

- [x] 4.1 Escribir RED en `tests/php/` para permisos, PDF ficticio válido, catálogo y adjuntos.
- [x] 4.2 Implementar documentos en `labm-core` hasta GREEN; excluir `docs/*.pdf` de automatización.
- [x] 4.3 Implementar contacto en `labm-core` con SMTP simulado, nonce, antispam y validación mediante RED→GREEN.

## Fase 5: Cierre

- [x] 5.1 Completar `tests/php/` y `tests/e2e/` para 60 escenarios; exigir cobertura ≥80%.
- [x] 5.2 Conservar evidencia responsive/accesible; registrar Lighthouse, SEO y WCAG integral como diferidos.
- [x] 5.3 Crear CI que ejecute gates vigentes, conserve reportes y bloquee fallos o pasos omitidos.
- [x] 5.4 Registrar en `proposal.md` y `design.md` el alcance productivo excluido, sin despliegue ni datos reales.
- [x] 5.5 Actualizar `README.md` y `docs/` con rollback y trazabilidad; validar higiene sin commit ni push.
