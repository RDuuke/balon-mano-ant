# Tasks: Sitio WordPress de LABM

## DecisiÃ³n de alcance posterior al VERIFY fallido (2026-08-27)

- Lighthouse, SEO y la garantÃ­a WCAG 2.2 AA quedan diferidos por decisiÃ³n expresa del usuario a un cambio futuro y no bloquean este APPLY correctivo.
- Estos puntos no se declaran COMPLIANT: las especificaciones vigentes contienen requisitos MUST/SHALL que los hacen obligatorios. Para avanzar coherentemente a VERIFY se requiere una regresiÃ³n formal a SPEC que ajuste el alcance, sin reabrir 5.4 ni incorporar hosting, SMTP, PDF o datos reales.
- El correctivo actual cubre solamente las brechas funcionales y la portabilidad de Playwright; el gate agregado ya no ejecuta Lighthouse.

## Fase 1: APPLY 1 — base

- [x] 1.1 Crear `.gitignore` para `.env`, secretos, volúmenes, dumps, uploads, dependencias, core y `artifacts/`; probar archivos prohibidos.
- [x] 1.2 Crear `.env.example` ficticio y `scripts/validate-env.ps1`; probar variables ausentes y personalización sin filtrar valores.
- [x] 1.3 Crear `compose.yaml` con imágenes por tag/digest, `db`, `wordpress`, `wp-cli`, healthchecks y volúmenes; validar `docker compose config`.
- [x] 1.4 Añadir `tests/smoke/` para salud HTTP/DB, fallo visible y persistencia sin borrar datos.
- [x] 1.5 Crear tema de bloques mínimo `wp-content/themes/labm/`; probar activación aislada y ruta pública.
- [x] 1.6 Crear plugin mínimo `wp-content/plugins/labm-core/`; probar activación aislada, cambio de tema y errores fatales.
- [x] 1.7 Implementar fallback seguro sin `labm-core`; cubrir RED→GREEN→REFACTOR.
- [x] 1.8 Crear `wp labm fixtures load` con upsert y `[DEMO LABM — FICTICIO]`; probar repetición, contenido ajeno y exclusión de PDF.
- [x] 1.9 Crear `scripts/bootstrap.ps1` para instalar, activar y cargar fixtures; probarlo dos veces.
- [x] 1.10 Configurar Composer/lock, WPCS, PHPStan y PHPUnit/WordPress Test Suite; ejecutar cada gate.
- [x] 1.11 Configurar Playwright/axe para ruta mínima y 320/768/1024/1440; registrar baseline automático.
- [x] 1.12 Configurar Lighthouse CI reproducible como baseline; reservar umbrales finales.
- [x] 1.13 Crear `scripts/gate.ps1` con versiones, comandos, salidas y retornos ignorados; marcar herramientas ausentes “no ejecutadas”.
- [x] 1.14 Documentar en `README.md` y `docs/` ciclo de vida, diagnóstico, pruebas, reset confirmado y validación limpia.

## Fase 2: APPLY 2 — dominio

- [x] 2.1 Escribir RED en `tests/php/` para tipos, taxonomías, metadatos, capacidades, borradores, validación e i18n.
- [x] 2.2 Implementar en `labm-core` actualidad, selecciones, clubes, integrantes y horarios hasta GREEN; probar persistencia entre temas.
- [x] 2.3 Ampliar fixtures con bordes, privados y categorías; probar idempotencia y preservar contenido editorial.

## Fase 3: APPLY 3 — frontend

- [x] 3.1 Crear patrones/plantillas Gutenberg para navegación, Inicio y Nosotros; probar teclado, foco, opcionales y rutas ausentes.
- [x] 3.2 Crear actualidad y selecciones con filtros, paginación, detalle, vacíos, fallback y privacidad; cubrir integración/E2E.
- [x] 3.3 Ajustar tokens/layouts responsive; probar WCAG automática y movimiento reducido en cuatro anchos.

## Fase 4: APPLY 4 — documentos/contacto

> Reintento del 2026-08-24 22:07 bloqueado antes de RED: Docker no está disponible en el entorno. No se modificó código productivo.

- [x] 4.1 Escribir RED de permisos, PDF real/tamaño, catálogo combinado, enlaces seguros y ciclo de adjuntos.
- [x] 4.2 Implementar documentos/catálogo hasta GREEN; mantener `docs/*.pdf` fuera de automatización.
- [x] 4.3 Escribir RED e implementar contacto con SMTP simulado, nonce, antispam accesible, validación, no duplicación y retención mínima.

## Fase 5: APPLY 5 — cierre

- [x] 5.1 Completar unitarias, integración, E2E y seguridad para 60 escenarios; exigir RED→GREEN y cobertura ≥80%.
- [x] 5.2 Auditar WCAG 2.2 AA y Lighthouse ≥85/90 por vista/ancho; fallar con evidencia.
- [x] 5.3 Crear CI limpio que ejecute gates, conserve reportes y bloquee fallos o pasos no ejecutados.
- [x] 5.4 Cerrada administrativamente como fuera de alcance: no se validaron hosting/SMTP ni matriz productiva, no se desplegó y no se usaron PDF ni datos reales. La exclusión es coherente con `proposal.md` y `design.md`.
- [x] 5.5 Actualizar documentación, rollback y trazabilidad spec-prueba; validar higiene sin commit ni push.
