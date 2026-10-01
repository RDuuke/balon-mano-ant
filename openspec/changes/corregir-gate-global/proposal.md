# Propuesta: corregir el gate global

## Intención

Restablecer el gate completo tras VERIFY de Selecciones: siete fallos PHPUnit, cobertura 73.47 %, 35 errores PHPCS, un error PHPStan y 28 fallos Playwright. La exploración distingue contratos acoplados de defectos reales; Selecciones permanece VERIFY failed, FIX2/2.

## Alcance

### Incluido

- Corregir contraste WCAG, WPCS y código sin uso demostrado; cubrir ramas reales hasta 80 %.
- Reconciliar pruebas de visibilidad, adjuntos, filtros, destacados y fallback con contratos vigentes, conservando privacidad, unicidad, recuperación y accesibilidad.
- Comprobar dimensiones contra diseño vigente antes de corregir implementación o expectativas.

### Excluido

- Nuevas funcionalidades, rediseño general, migraciones, tercer FIX de Selecciones y absorción de Detalle Actualidad.
- Reducir umbrales, excluir fuentes/suites, suprimir assertions o aceptar resultados ambiguos.

## Enfoque

SPEC/DESIGN documentarán evidencia y solución por fallo; APPLY trabajará en lotes con TDD strict RED/GREEN. Preservar hunks ajenos y scripts ya modificados. Antes de tocar plantilla/pruebas de Detalle, resolver renderer frente a `wp:post-content` en su flujo pausado mediante SPEC-FIX/DESIGN-FIX, dejando contrato y handoff explícitos.

## Rutas afectadas

| Ruta | Impacto |
|---|---|
| `wp-content/themes/labm/style.css` | Contraste/dimensiones justificadas |
| `wp-content/themes/labm/functions.php` | WPCS/fallback contratado |
| `wp-content/themes/labm/templates/single-labm_actualidad.html` | Condicionada a reconciliación de Detalle |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | WPCS |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | WPCS/método sin uso |
| `tests/php/FixturesDomainTest.php`, `tests/php/PublicExperienceTest.php`, `tests/php/HomePresentationTest.php` | Contratos/cobertura |
| `tests/e2e/public-experience.spec.ts`, `tests/e2e/document-admin.spec.ts`, `tests/e2e/home.spec.ts` | Aislamiento/contratos/accesibilidad |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Solapamiento con Detalle/trabajo previo | Alta | Reconciliar artefactos y delimitar hunks |
| Tests alteran persistencia | Media | Backup, limpieza y content-sync oficial sin demos/cuentas/credenciales |
| Cobertura requiere esfuerzo adicional | Alta | Ramas reales; 182 líneas es mínimo provisional |

## Reversión

Revertir únicamente hunks de las rutas anteriores, conservando snapshots y cambios previos. Sin migraciones ni flags. Si cambia estado persistido, restaurar backup previo y sincronizar canónico, verificando versión/hash.

## Dependencias

- Aprobación de esta propuesta; reconciliación de Detalle antes de cambios asociados.
- Runtime Docker limpio; snapshots Selecciones/Detalle intactos.

## Criterios verificables

- [ ] `scripts/gate.ps1 -IncludeBrowser` verde completo; cobertura fresca ≥80 %; PHPCS/PHPStan sin errores.
- [ ] Playwright sin omisiones causadas por fallos; contratos funcionales, privacidad y WCAG 2.2 AA íntegros.
- [ ] LF en archivos tocados; sincronización verificada si afecta persistencia.
- [ ] Handoff documentado para reanudar VERIFY de Selecciones y flujo propio de Detalle, sin archivo automático.
