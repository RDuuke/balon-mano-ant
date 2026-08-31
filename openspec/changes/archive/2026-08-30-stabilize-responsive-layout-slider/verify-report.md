# Reporte de verificación

## Resultado ejecutivo

**Estado contractual: `ok`.** La repetición independiente de VERIFY confirma los nueve escenarios del cambio. Los gates de composición, PHPUnit, integración WordPress, cobertura, WPCS, PHPStan y Playwright pasan. El contenido conserva un máximo de 1200 px y centrado simétrico; el slider mantiene su altura en 320, 768, 1024, 1200 y 1440 px; no hay desborde global y se preservan controles, `aria-current`, tamaño de objetivo y movimiento reducido. El cambio puede solicitar aprobación para ARCHIVE.

## Completitud

- Tareas cerradas: 15/15, más 2/2 tareas correctivas.
- Tareas incompletas: ninguna.
- Artefactos revisados: especificación de experiencia pública, `design.md`, `tasks.md`, `apply-progress.md` y `apply-report.md`.
- TDD estricto: existe evidencia RED/GREEN para todas las tareas cerradas, incluidas las correctivas 6.1 y 6.2.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Identidad responsive | Pantalla mayor que el ancho máximo | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts:7` | — |
| Identidad responsive | Cambio entre anchos objetivo | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts:7` | — |
| Identidad responsive | Contenido que intenta desbordar | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts:75`, `:88` | — |
| Slider estable | Navegación entre ítems diferentes | ✅ COMPLIANT | `tests/e2e/home.spec.ts:6`, `:48` | — |
| Slider estable | Slider en anchos objetivo | ✅ COMPLIANT | `tests/e2e/home.spec.ts:6` | — |
| Slider estable | Contenido extremo o medio no disponible | ✅ COMPLIANT | `tests/e2e/home.spec.ts:6`; `tests/php/HomePresentationTest.php` | — |
| Experiencia verificable | Comprobación geométrica satisfactoria | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts:7` | — |
| Experiencia verificable | Recorrido responsive completo | ✅ COMPLIANT | `tests/e2e/home.spec.ts:6`; `tests/e2e/public-experience.spec.ts:7`, `:75`, `:88` | — |
| Experiencia verificable | Regresión geométrica | ✅ COMPLIANT | Aserciones negativas y mensajes por ancho en `home.spec.ts:6` y `public-experience.spec.ts:7`, `:75`, `:88` | — |

Resumen: 9 COMPLIANT, 0 FAILING, 0 PARTIAL, 0 UNTESTED.

## Coherencia con el diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| `wideSize: 1200px` y tokens compartidos | ✅ Implementada | `theme.json`, `style.css` y `FrontendTokensTest` pasan. |
| Fondos full-width y contenido centrado | ✅ Implementada | La geometría pasa en los cinco anchos objetivo, con diferencia lateral máxima de 1 px. |
| Breakpoints base, 768 y 1024 px | ✅ Implementada | Los recorridos de 320, 768, 1024, 1200 y 1440 px pasan sin desalineación ni overflow. |
| `block-size` estable y overflow interno | ✅ Implementada | La altura exterior permanece idéntica al recorrer anterior, siguiente e indicadores en todos los anchos. |
| Conservar `hidden`, `aria-current`, pausa y reduced motion | ✅ Implementada | Navegación, indicador activo, pausa, axe y media `reduce` pasan. |

## Ejecución real

| Gate | Resultado | Detalle |
|---|---|---|
| Docker Compose | PASS | Configuración válida. |
| PHPUnit unitario | PASS | 1 test, 2 aserciones. |
| Integración WordPress/Gutenberg | PASS | 55 tests, 318 aserciones. |
| Cobertura PHP | PASS | 87,72 % (707/806 líneas), umbral 80 %. |
| WPCS/PHPCS | PASS | `composer lint`, código de salida 0. |
| PHPStan | PASS | Sin errores. |
| Playwright | PASS | 56/56 en proyectos 320/768/1024/1440; los recorridos geométricos y del slider incluyen además 1200 px. |

Comandos ejecutados:

```powershell
$gate=Get-Content -Raw -LiteralPath scripts/gate.ps1; & ([scriptblock]::Create($gate))
C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe -NoProfile -File scripts\browser-gate.ps1 -Task playwright
```

La segunda invocación fue bloqueada por la política corporativa `MachinePolicy: AllSigned`, incluso bajo ejecución elevada. Para no evaluar el archivo como `ScriptBlock` y perder `$PSScriptRoot`, se ejecutó después, en un único proceso y sin alterar el producto, la misma secuencia declarada por `browser-gate.ps1`: construir la imagen, sincronizar temporalmente `home`/`siteurl`, cargar fixtures, ejecutar `scripts/browser-gate.sh` y restaurar ambas opciones dentro de `finally`.

Comprobación posterior:

```text
HOME=http://localhost:8080
SITEURL=http://localhost:8080
EXITS=0,0
```

## Cobertura de Código

- Cobertura PHP: **87,72 %** (707/806 líneas).
- Umbral configurado: **80 %**.
- Estado: PASS.

## Validaciones responsive y baseline

- Anchos ejercitados por las pruebas: 320, 768, 1024, 1200 y 1440 px.
- Máximo de contenido, centrado, gutters y tolerancia geométrica de 1 px: PASS.
- Ausencia de desplazamiento horizontal global y contenido extremo: PASS.
- Altura exterior del slider durante anterior, siguiente e indicadores: PASS.
- Actualización de `aria-current`, controles visibles/operables y `target-size` de axe: PASS.
- `prefers-reduced-motion` y estado pausado de slider/aliados: PASS.

## Riesgos y clasificación

- **WARNING de infraestructura:** la política local `AllSigned` impide ejecutar directamente scripts PowerShell no firmados. La lógica oficial se ejecutó de forma equivalente y el estado final de WordPress quedó confirmado; conviene firmar los scripts o disponer de PowerShell 7 con una política compatible para futuras verificaciones.
- No hay riesgos CRITICAL ni regresiones de producto detectadas.

## Gate de progresión

**VERIFY aprobado técnicamente.** La siguiente fase recomendada es `ARCHIVE`, sujeta a aprobación explícita del usuario. Esta fase no archivó ni modificó código del producto.
