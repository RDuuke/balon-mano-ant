# Informe de verificación: Slider y superficies del inicio

## Resumen ejecutivo

La excepción mecánica autorizada quedó verificada. `quick.md` usa LF, contiene cero bytes CR y su blob de Git coincide exactamente con el blob del índice (`fb6e553c700abfe54adf35ad2d1153ac91058138`), lo que demuestra que la normalización no alteró el contenido. La auditoría binaria de los 16 archivos modificados dentro del alcance y de la política LF encontró cero bytes CR, y `git diff --check` terminó con código 0.

Se reutiliza la evidencia del VERIFY inmediatamente anterior: el gate canónico `scripts/gate.ps1 -IncludeBrowser` terminó con código 0, incluyendo PHPUnit, integración WordPress, cobertura PHP de 87,72 %, PHPCS, PHPStan y 64/64 pruebas Playwright. No se repitió el suite completo porque el único cambio posterior fue la normalización EOL de `quick.md`, con equivalencia binaria de contenido demostrada.

Lighthouse no se declara aprobado ni forma parte de este gate: permanece diferido como observabilidad consultiva de `nightly_release` conforme a `openspec/config.yaml`.

Veredicto: **COMPLIANT WITH WARNINGS**. No quedan fallos críticos y ARCHIVE está permitido.

## Matriz de Validación

| Dominio | Escenario | Estado | Test o evidencia asociada | Severidad |
|---------|-----------|--------|----------------------------|-----------|
| Slider | Sin desborde vertical, dots de 10 × 10 px, colores, área táctil, alineación y separación en 320, 390, 768, 1200 y 1440 px | ✅ COMPLIANT | `tests/e2e/home.spec.ts`; Playwright PASS | — |
| Slider | Comprobaciones geométricas detalladas a 1024 px | ⚠️ PARTIAL | La suite incluye proyecto 1024 px, pero el bucle geométrico detallado no incluye ese ancho | WARNING |
| Slider | Altura estable con contenido extremo en 320, 768, 1024, 1200 y 1440 px | ✅ COMPLIANT | `slider mantiene altura al usar anterior, siguiente e indicadores`; Playwright PASS | — |
| Slider | Altura estable con contenido extremo a 390 px | ⚠️ PARTIAL | El test de estabilidad no incluye 390 px | WARNING |
| Slider | `aria-current` cambia al usar siguiente y el indicador activo queda lima | ✅ COMPLIANT | `slider muestra dots circulares...`; Playwright PASS | — |
| Slider | `aria-current` cambia explícitamente al pulsar un indicador | ⚠️ PARTIAL | El test pulsa el indicador para comprobar altura, pero no vuelve a afirmar `aria-current` | WARNING |
| Portada | Sin desborde horizontal global en los proyectos responsive de la suite | ✅ COMPLIANT | Suite de experiencia pública; Playwright PASS | — |
| Portada | Sin desborde horizontal global a 390 px | ⚠️ PARTIAL | La regresión global no tiene proyecto dedicado de 390 px | WARNING |
| Superficies | Fondos exactos de presentación, clubes, evento, actualidad, vinculación y aliados | ✅ COMPLIANT | `secciones del inicio usan las superficies exactas del diseño`; Playwright PASS | — |
| Accesibilidad | Controles accesibles, pausa, movimiento reducido y axe WCAG 2.2 AA automático | ✅ COMPLIANT | Pruebas de portada; Playwright PASS | — |
| Marcado PHP | El marcado funcional permanece válido | ✅ COMPLIANT | PHPUnit unitario e integración WordPress PASS | — |
| Evidencia visual | Capturas de Inicio en 320, 768, 1024 y 1440 px | ✅ COMPLIANT | `artifacts/apply-home-*.png`; revisión realizada en APPLY | — |
| Calidad PHP | WordPress Coding Standards y PHPStan nivel 5 | ✅ COMPLIANT | `composer-lint` y `composer-analyse` PASS | — |
| Finales de línea | Todos los archivos modificados dentro del alcance usan LF | ✅ COMPLIANT | Auditoría binaria focalizada: 0 bytes CR en 16 archivos; `git diff --check` PASS | — |
| Integridad documental | La normalización de `quick.md` no cambió contenido | ✅ COMPLIANT | Blob raw = blob filtrado = blob del índice: `fb6e553c700abfe54adf35ad2d1153ac91058138` | — |

## Resultados de ejecución reutilizados

Evidencia canónica inmediatamente anterior:

```powershell
& 'scripts/gate.ps1' -IncludeBrowser
```

Resultado global: **PASS** (`exit 0`).

| Comprobación | Resultado | Evidencia |
|-------------|-----------|-----------|
| Configuración Docker Compose | PASS | `artifacts/gate/compose-config.log` |
| PHPUnit unitario | PASS | 1 prueba, 2 aserciones |
| Integración WordPress | PASS con 2 warnings | 55 pruebas, 318 aserciones |
| Cobertura PHP | PASS | 87,72 % (707/806 líneas), umbral 80 % |
| PHPCS / lint | PASS | Sin errores |
| PHPStan nivel 5 | PASS | Sin errores |
| Playwright completo | PASS | 64/64 pruebas |

La reutilización es válida porque después de ese gate solo se normalizó el final de línea de `quick.md`, un artefacto documental. La equivalencia exacta contra el blob del índice demuestra ausencia de cambios de contenido.

## Cobertura de código

- Cobertura PHP reutilizada: **87,72 %** (707/806 líneas).
- Umbral configurado: **80 %**.
- Estado: **PASS**.

## Evidencia TDD

El modo TDD estricto está activo. En modo quick no existe `tasks.md`. `apply-progress.md` conserva RED, GREEN y REFACTOR para las tareas funcionales y correctivas. No hay tareas funcionales pendientes.

## Auditoría de finales de línea

Resultado focalizado:

- `quick.md`: 0 bytes CR.
- Archivos auditados dentro del alcance y la política: 16.
- Archivos con CRLF o CR aislado: 0.
- `git diff --check`: PASS (`exit 0`).
- Hash SHA-256 actual de `quick.md`: `4d72a9c58d540e172ba7693a11b6cc6d92ae60200322ccdef1a1210d416c94da`.
- Blob Git raw, filtrado e índice: `fb6e553c700abfe54adf35ad2d1153ac91058138` en los tres casos.

Archivos auditados: `AGENTS.md`, `.editorconfig`, `.gitattributes`, `openspec/config.yaml`, `.status.yaml`, `quick.md`, `apply-progress.md`, `verify-report.md`, `.execution-log.md`, `tests/e2e/home.spec.ts`, `wp-content/themes/labm/style.css`, `wp-content/themes/labm/functions.php` y los cuatro archivos PHP del plugin normalizados durante el cambio.

## Lighthouse

Lighthouse permanece como `advisory_observability` con alcance `nightly_release`. No se ejecutó en esta auditoría focalizada y no se declara aprobado. El `PROTOCOL_TIMEOUT` anterior se conserva como antecedente de infraestructura no bloqueante.

## Riesgos aceptados

- **WARNING:** cobertura parcial de los subescenarios detallados a 390/1024 px y de `aria-current` tras pulsar directamente un indicador.
- **WARNING:** recorte preexistente de “Aliados” en 320 y 768 px, fuera del alcance aprobado.
- **WARNING:** la habilidad opcional `testing` no está instalada; se aplicaron la skill VERIFY y las reglas del proyecto.
- **ADVISORY:** Lighthouse diferido a `nightly_release`; no se declara aprobado.

## Veredicto de requisitos

- COMPLIANT: 11 verificaciones.
- PARTIAL: 4 verificaciones, todas con advertencias previamente aceptadas y no bloqueantes.
- FAILING: 0.
- UNTESTED: 0.
- Estado global: **WARNING**.
- `archive_allowed: true`.

Siguiente paso recomendado: **ARCHIVE**.
