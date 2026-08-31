# Progreso de aplicación

## Tarea 6.1 — Reproducción de bloqueos del VERIFY

- **RED:** el subset `home.spec.ts/public-experience.spec.ts` reproduce 5/5 bloqueos: gutter 0, overflow, target-size, aria-current y reduced motion.
- **GREEN:** el diagnóstico aísla la causa común: WordPress emitía activos absolutos a `localhost` mientras Playwright navegaba por `host.docker.internal`.

## Tarea 6.2 — Ejecución con URL sincronizada

- **RED:** el mismo subset falla 5/5 antes de sincronizar `home`/`siteurl` con `WP_URL`.
- **GREEN:** al sincronizar las URL, el subset pasa 5/5 y Playwright completo pasa 44/44 sin cambios de producto.
- **TRIANGULATE:** PHPUnit pasa 1/1 con 2 aserciones y la altura del slider permanece estable.
- **REFACTOR:** ejecutar `scripts/browser-gate.ps1` como archivo conserva `$PSScriptRoot`, sincroniza las URL y las restaura al finalizar.

## Tarea 1.1 — Contrato de tokens
- **RED:** `FrontendTokensTest` falla por `contentMax` y gutter ausentes.
- **GREEN:** `theme.json` y `style.css` pasan.

## Tarea 1.2 — Tokens compartidos
- **RED:** faltan variables CSS.
- **GREEN:** `--labm-content-max` y `--labm-gutter` declaradas.

## Tarea 1.3 — Consolidación
- **RED:** valores sin contrato compartido.
- **GREEN:** duplicados sustituidos por variables.
- **REFACTOR:** foco y movimiento reducido intactos.

## Tarea 2.1 — Geometría
- **RED:** medición falla con bloques persistidos.
- **GREEN:** selector `data-labm-section` compatible.

## Tarea 2.2 — Contenedores
- **RED:** contenido guardado no recibía el límite.
- **GREEN:** padding simétrico y máximo 1200 px.

## Tarea 2.3 — Anchos objetivo
- **RED:** faltaba cobertura de 1200 px.
- **GREEN:** 320/768/1024/1200/1440 sin overflow.

## Tarea 2.4 — Breakpoints
- **RED:** CSS dependía solo de 480 px.
- **GREEN:** base, 768 y 1024 pasan.
- **REFACTOR:** 1200 es límite, no breakpoint.

## Tarea 3.1 — Recorrido slider
- **RED:** texto extremo cambia la altura.
- **GREEN:** controles e indicadores conservan altura.

## Tarea 3.2 — Caja estable
- **RED:** `min-height` permite crecimiento.
- **GREEN:** `block-size`, reserva y overflow interno.

## Tarea 3.3 — Contenido extremo
- **RED:** falta clase estable.
- **GREEN:** clase, fallback y límite validados (1 test, 4 aserciones).

## Tarea 3.4 — Controlador
- **RED:** recorridos verifican estados existentes.
- **GREEN:** 56 pruebas pasan sin cambiar `home.js`.
- **REFACTOR:** sin sincronización innecesaria.

## Tarea 4.1 — Gate PHP
- **RED:** nuevas aserciones fallan inicialmente.
- **GREEN:** PHPUnit, integración, cobertura, WPCS y PHPStan pasan.

## Tarea 4.2 — Playwright
- **RED:** primer gate registra salto y selector incompatible.
- **GREEN:** 56/56 pasan.

## Tarea 4.3 — Axe
- **RED:** contraste 3.4:1 en eyebrow.
- **GREEN:** contraste local corregido; axe pasa.

## Tarea 5.1 — Cierre
- **RED:** dependencia de clase no garantizada.
- **GREEN:** compatibilidad, gates y rollback documentados.
- **REFACTOR:** sin cambios de datos o administración.
