# Verificación: Implementar el Home LABM

## Resumen

La ejecución técnica pasa, pero el cambio no alcanza cumplimiento integral por escenario. Resultado: **FAILED**. Se identificaron 11 escenarios COMPLIANT, 12 PARTIAL y 1 UNTESTED. El escenario sin prueba es crítico y bloquea ARCHIVE.

## Completitud

- Tareas: 20 completadas, 0 pendientes.
- Evidencia TDD estricta: 20 de 20 tareas con entradas RED y GREEN.
- Artefactos requeridos: propuesta, specs, diseño, tareas, progreso APPLY e implementación presentes.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Experiencia pública | Portada con contenido publicado | ✅ COMPLIANT | `HomePresentationTest::test_patron_compone...`, `home.spec.ts` | — |
| Experiencia pública | Sección sin contenido | ✅ COMPLIANT | `test_renderizadores...fallback`, `home.spec.ts` | — |
| Experiencia pública | Contenido no público | ✅ COMPLIANT | `test_consultas...`, `PublicExperienceTest::test_public_query...` | — |
| Experiencia pública | Slider: recorrido normal | ⚠️ PARTIAL | `home.spec.ts::slider y aliados...` sale temprano si no hay slider | WARNING |
| Experiencia pública | Slider: movimiento reducido | ⚠️ PARTIAL | `public-experience.spec.ts::3.3` no fuerza slider publicado | WARNING |
| Experiencia pública | Slide inválido o ausente | ✅ COMPLIANT | `HomeContentTest::test_invalid...`, fallback PHP/E2E | — |
| Experiencia pública | Aliados: secuencia disponible | ⚠️ PARTIAL | PHP valida markup; E2E no prueba continuidad ni dirección | WARNING |
| Experiencia pública | Aliados: pausa o movimiento reducido | ⚠️ PARTIAL | E2E condicional y contrato CSS | WARNING |
| Experiencia pública | Aliados: datos insuficientes | ✅ COMPLIANT | validación editorial y fallback sin controles | — |
| Experiencia pública | Anchos objetivo | ✅ COMPLIANT | `portada y Selecciones conservan...` | — |
| Experiencia pública | Texto sobre color de marca | ✅ COMPLIANT | axe en cuatro proyectos y `FrontendTokensTest` | — |
| Experiencia pública | Desborde o solapamiento | ✅ COMPLIANT | `public-experience.spec.ts` en 320/768/1024/1440 | — |
| Arquitectura CMS | Publicación autorizada | ⚠️ PARTIAL | capacidades y validación pasan; no prueba flujo editorial completo | WARNING |
| Arquitectura CMS | Borrador o lista vacía | ✅ COMPLIANT | consultas `publish`, fixtures draft y fallback | — |
| Arquitectura CMS | Operación no autorizada | ⚠️ PARTIAL | prueba metadatos/capacidad; no cubre crear, publicar y eliminar | WARNING |
| Arquitectura CMS | Cambio de tema | ❌ UNTESTED | — | CRITICAL |
| Arquitectura CMS | Componente de dominio inactivo | ⚠️ PARTIAL | fallback por tipo ausente; no desactiva realmente `labm-core` | WARNING |
| Arquitectura CMS | Dato inválido | ✅ COMPLIANT | `HomeContentTest::test_invalid...` | — |
| Calidad y seguridad | Flujos críticos válidos | ⚠️ PARTIAL | gates pasan; interacción slider/aliados puede omitirse | WARNING |
| Calidad y seguridad | Estados vacíos y movimiento reducido | ⚠️ PARTIAL | estados vacíos pasan; movimiento no se prueba con contenido publicado | WARNING |
| Calidad y seguridad | Regresión funcional o accesible | ✅ COMPLIANT | Playwright/axe, ausencia de secciones y rutas Selecciones | — |
| Calidad y seguridad | Contenido autorizado y saneado | ⚠️ PARTIAL | saneado y render pasan; falta flujo editorial autorizado completo | WARNING |
| Calidad y seguridad | Valores límite | ⚠️ PARTIAL | límites de consulta pasan; texto largo y medio ausente no están aislados | WARNING |
| Calidad y seguridad | Solicitud manipulada | ⚠️ PARTIAL | URL y metadatos rechazados; no cubre todas las operaciones mutables | WARNING |

## Resumen por Dominio

| Dominio | COMPLIANT | PARTIAL | UNTESTED | FAILING |
|---|---:|---:|---:|---:|
| Experiencia pública | 8 | 4 | 0 | 0 |
| Arquitectura CMS | 2 | 3 | 1 | 0 |
| Calidad y seguridad | 1 | 5 | 0 | 0 |
| **Total** | **11** | **12** | **1** | **0** |

## Evidencia TDD

`apply-progress.md` contiene 20 secciones `## Tarea {id}`. Todas las tareas marcadas `[x]` tienen al menos RED y GREEN; auditoría TDD: **20/20**.

## Coherencia de Diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Slides y aliados persisten en `labm-core` | ✅ Implementada | CPT, metadatos, capacidades y fixtures. |
| Renderizado dinámico desde el tema | ✅ Implementada | helpers, patrón y consultas `publish`. |
| Mejora progresiva para movimiento | ⚠️ Parcial | implementación presente; prueba E2E feliz no garantizada. |
| Compatibilidad con Selecciones | ✅ Implementada | CPT, taxonomía, slug y rutas Piso/Playa pasan. |

## Ejecución Real

| Comprobación | Resultado |
|---|---|
| Compose config | PASS |
| PHPUnit unitario | PASS |
| PHPUnit WordPress | PASS: 49 pruebas, 266 aserciones, 2 warnings |
| Cobertura PHP | PASS: 85.48% (636/744), umbral 80% |
| WPCS | PASS |
| PHPStan | PASS |
| Playwright + axe | PASS: 44/44 en 320, 768, 1024 y 1440 px |
| Build dedicado | No existe comando `build`; WARNING no bloqueante |

## Hallazgos

### CRITICAL

- No existe una prueba que cambie de tema y demuestre que slides, aliados, permisos y metadatos permanecen disponibles.

### WARNING

- El E2E interactivo retorna con éxito cuando slider o aliados no existen; debe preparar contenido publicado y ejercer siguiente, pausa, indicador y aliado.
- Falta desactivar realmente `labm-core` para verificar el fallback del Home.
- Faltan pruebas completas de operaciones editoriales autorizadas/no autorizadas y valores límite.
- No existe comando de build dedicado; lint y análisis estático sí pasan.
- La skill opcional `testing` no estaba disponible.

## Próximo Paso

Regresar a APPLY para añadir las pruebas faltantes. No avanzar a ARCHIVE hasta que los 24 escenarios sean COMPLIANT.
