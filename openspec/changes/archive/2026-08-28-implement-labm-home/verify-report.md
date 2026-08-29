# Informe de verificación: Implementar el Home LABM

## Resumen ejecutivo

La implementación cumple los 24 escenarios declarados. Todas las tareas están completas, los gates PHP, WordPress, WPCS y PHPStan pasan, la cobertura PHP alcanza 87,56 % frente al umbral de 80 %, y Playwright/axe aprueba 48 de 48 pruebas en 320, 768, 1024 y 1440 px. No se detectaron problemas críticos; existe una advertencia no bloqueante porque la skill opcional `testing` no está instalada.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Arquitectura CMS | Publicación autorizada | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:75` | — |
| Arquitectura CMS | Borrador o lista vacía | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:162`, `HomePresentationTest.php:30` | — |
| Arquitectura CMS | Operación no autorizada | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:111` | — |
| Arquitectura CMS | Cambio de tema | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:133` | — |
| Arquitectura CMS | Componente de dominio inactivo | ✅ COMPLIANT | `HomePresentationTest.php:40` | — |
| Arquitectura CMS | Dato inválido | ✅ COMPLIANT | `HomeContentTest.php:60`, `HomeEditorialFlowsTest.php:162` | — |
| Calidad y seguridad | Flujos críticos válidos | ✅ COMPLIANT | `HomePresentationTest.php:59`, `home.spec.ts:12`, `home.spec.ts:24` | — |
| Calidad y seguridad | Estados vacíos y movimiento reducido | ✅ COMPLIANT | `HomePresentationTest.php:30`, `home.spec.ts:56`, `public-experience.spec.ts:51` | — |
| Calidad y seguridad | Regresión funcional o accesible | ✅ COMPLIANT | `home.spec.ts:4`, `home.spec.ts:12`, `public-experience.spec.ts:41` | — |
| Calidad y seguridad | Contenido autorizado | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:75`, `HomeContentTest.php:37` | — |
| Calidad y seguridad | Valores límite | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:162` | — |
| Calidad y seguridad | Solicitud manipulada | ✅ COMPLIANT | `HomeEditorialFlowsTest.php:111`, `HomeEditorialFlowsTest.php:162` | — |
| Experiencia pública | Portada con contenido publicado | ✅ COMPLIANT | `HomePresentationTest.php:59`, `home.spec.ts:12` | — |
| Experiencia pública | Sección sin contenido | ✅ COMPLIANT | `HomePresentationTest.php:30` | — |
| Experiencia pública | Contenido no público | ✅ COMPLIANT | `HomePresentationTest.php:13`, `HomeEditorialFlowsTest.php:162` | — |
| Experiencia pública | Slider: recorrido normal | ✅ COMPLIANT | `home.spec.ts:24` | — |
| Experiencia pública | Slider: movimiento reducido | ✅ COMPLIANT | `home.spec.ts:56` | — |
| Experiencia pública | Slide inválido o ausente | ✅ COMPLIANT | `HomeContentTest.php:60`, `HomePresentationTest.php:30` | — |
| Experiencia pública | Aliados: secuencia disponible | ✅ COMPLIANT | `home.spec.ts:24` | — |
| Experiencia pública | Aliados: pausa o movimiento reducido | ✅ COMPLIANT | `home.spec.ts:24`, `home.spec.ts:56` | — |
| Experiencia pública | Aliados: datos insuficientes | ✅ COMPLIANT | `HomeContentTest.php:60`, `HomeEditorialFlowsTest.php:162` | — |
| Experiencia pública | Anchos objetivo | ✅ COMPLIANT | `public-experience.spec.ts:64` | — |
| Experiencia pública | Texto sobre color de marca | ✅ COMPLIANT | `FrontendTokensTest.php:13`, `home.spec.ts:4`, `public-experience.spec.ts:51` | — |
| Experiencia pública | Desborde o solapamiento | ✅ COMPLIANT | `public-experience.spec.ts:51`, `public-experience.spec.ts:64` | — |

## Resumen de cumplimiento

| Dominio | COMPLIANT | FAILING | UNTESTED | PARTIAL |
|---|---:|---:|---:|---:|
| Arquitectura CMS | 6 | 0 | 0 | 0 |
| Calidad y seguridad | 6 | 0 | 0 | 0 |
| Experiencia pública | 12 | 0 | 0 | 0 |
| **Total** | **24** | **0** | **0** | **0** |

## Ejecución real

| Comprobación | Comando | Resultado |
|---|---|---|
| Gate integral | `scripts/gate.ps1` | PASS: configuración Compose, PHPUnit, integración WordPress, cobertura, WPCS y PHPStan |
| Cobertura PHP | `scripts/coverage.ps1` | PASS: 54 pruebas, 308 aserciones; 87,56 % (690/788 líneas) |
| Navegador y accesibilidad | `scripts/browser-gate.ps1 -Task playwright` | PASS: 48/48 pruebas en 4 proyectos; axe, teclado, pausa, movimiento reducido y responsive |

## Cobertura de Código

- Cobertura PHP: **87,56 %** (690 de 788 líneas).
- Umbral configurado: **80 %**.
- Estado: **PASS**, 7,56 puntos porcentuales sobre el umbral.

## Evidencia TDD

El gate `gates.apply.tdd` está configurado como `strict`. Se auditaron las 26 tareas completadas de `tasks.md`: cada una posee una sección correspondiente en `apply-progress.md` con evidencia RED y GREEN. No hay brechas de evidencia TDD.

## Coherencia con el diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Contenido de slider y aliados en `labm-core` | ✅ Implementada | Registro, REST, permisos, persistencia y pruebas de cambio de tema en PHP |
| Renderizado dinámico desde el tema | ✅ Implementada | Consultas `publish`, límites, orden, fallbacks y patrón renderizable cubiertos por `HomePresentationTest` |
| Mejora progresiva para movimiento | ✅ Implementada | Controles, pausa, estado activo, copia inerte y movimiento reducido ejercidos en Playwright/axe |
| Compatibilidad explícita con Selecciones | ✅ Implementada | Inicio excluye Piso/Playa y las rutas se conservan en `public-experience.spec.ts` |

## Completitud

- Tareas completadas: 26 de 26.
- Tareas incompletas: ninguna.
- Escenarios con prueba asociada y ejecución aprobada: 24 de 24.

## Riesgos y observaciones

- **WARNING:** la skill opcional `testing` no está disponible; la verificación se ejecutó con la infraestructura nativa del repositorio. Esto no afecta la evidencia ni bloquea ARCHIVE.
- El presupuesto de revisión está desactivado (`max_diff_lines: 0`, sin rutas sensibles), por lo que no aplica auditoría de carga de revisión.
- La integración experimental NeaBrain está desactivada.

## Resultado

**Estado general: WARNING.** Los 24 escenarios son COMPLIANT y no hay fallos; la única advertencia corresponde a una skill auxiliar opcional ausente. La fase VERIFY queda completada y el siguiente paso recomendado es ARCHIVE. Este informe no archiva el cambio.
