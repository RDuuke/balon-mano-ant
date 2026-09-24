# Informe de verificación: Actualidad fiel a Pencil

## Resultado

**Estado: WARNING.** Actualidad cumple sus requisitos con evidencia focal aprobada. El gate integral no queda verde por cuatro fallos ajenos de Documentos y avisos PHPCS históricos. El reintento local de Playwright no inició por faltar `node_modules/@playwright/test`; el intento portable excedió el límite del entorno.

## Completitud y evidencia TDD

- Tareas completas: 13 de 13; tareas incompletas: ninguna.
- TDD estricto: `apply-progress.md` incluye evidencia RED y GREEN para las tareas completas; no hay huecos.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Composición | Regiones y orden de escritorio Pencil | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts:433` (4/4 en APPLY) | — |
| Composición | Adaptación 320/768/1440 sin desborde | ✅ COMPLIANT | `public-experience.spec.ts:507` (4/4 en APPLY) | — |
| Composición | Recurso visual no disponible | ✅ COMPLIANT | `PublicExperienceTest::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` | — |
| Filtros | Búsqueda y categoría válidas | ✅ COMPLIANT | `PublicExperienceTest::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` | — |
| Filtros | Sin coincidencias conserva controles | ✅ COMPLIANT | `public-experience.spec.ts:450` (4/4 en APPLY) | — |
| Filtros | Parámetros no válidos o vacíos | ✅ COMPLIANT | `PublicExperienceTest::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` | — |
| Resultados | Publicadas DESC, destacada y tres tarjetas sin repetición | ✅ COMPLIANT | `PublicExperienceTest::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` | — |
| Resultados | Última página parcial | ✅ COMPLIANT | `PublicExperienceTest::test_actualidad_query_and_listing_preserve_the_public_editorial_contract` | — |
| Resultados | Página inexistente sin enlaces inválidos | ✅ COMPLIANT | PHPUnit focal y `public-experience.spec.ts:450` | — |

**Totales:** 9 COMPLIANT, 0 FAILING, 0 UNTESTED, 0 PARTIAL.

## Coherencia con el diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Filtros GET, texto y categoría preservados | ✅ Implementada | Renderer y prueba PHP de contrato editorial. |
| Consulta publicada DESC de cuatro resultados | ✅ Implementada | Prueba PHP de orden y partición 1+3. |
| Hero, controles y componentes aislados | ✅ Implementada | Plantilla, CSS `labm-actualidad-*` y E2E. |
| Geometría Pencil de escritorio | ✅ Implementada | E2E comprueba hero 340, filtros 420/260/52, medio 670×440 y paginación. |
| Retícula fluida y foco visible | ✅ Implementada | E2E comprueba foco y `scrollWidth` 320/768/1440. |
| Fallback local y contenido escapado | ✅ Implementada | Helper y prueba PHP focal. |

## Comandos y resultados

- `scripts/gate.ps1 -IncludeBrowser`: `compose-config`, `composer-test` y `composer-analyse` aprobaron. La ejecución completa encontró cuatro fallos de Documentos y no concluyó el tramo de navegador dentro del límite.
- Cobertura/integración: 143 pruebas, 1183 aserciones, 4 fallos, todos de Documentos (`PublicExperienceTest`, `DocumentContactTest` x2, `VerifyCorrectivesTest`); ninguno de Actualidad.
- `pnpm run test:e2e -- --timeout=20000 --grep "1.2 actualidad reproduce las regiones Pencil"`: no inició por ausencia de `node_modules/@playwright/test/cli.js`.
- Evidencia vigente de APPLY: PHPUnit focal de Actualidad aprobado; prueba Playwright focal aprobada 4/4 en `mobile-320`, `tablet-768`, `desktop-1024` y `wide-1440`.
- `git diff --check`: sin errores. La verificación de APPLY confirma LF en los archivos modificados del alcance.

## Cobertura de Código

El umbral configurado es 80%, pero no hay un porcentaje agregado fiable: el reporte se generó antes de que la suite fallara exclusivamente por Documentos.

## Fallos Detectados

### Tests fallidos
- `PublicExperienceTest::test_documents_pattern_composes_the_simple_pdf_catalog_without_filters`: llamada histórica de Documentos ausente.
- `DocumentContactTest::test_document_catalog_empty_filter_offers_clear_action`: texto vacío de Documentos distinto al esperado.
- `DocumentContactTest::test_document_catalog_is_simple_paginated_and_omits_invalid_attachments`: catálogo de Documentos devuelve 0 en vez de 10.
- `VerifyCorrectivesTest::test_documento_publicado_consulta_combinada_paginada_y_consulta_vacia`: texto vacío de Documentos distinto al esperado.

### Errores de build
- Ninguno en Actualidad; PHPStan aprobó. PHPCS conserva avisos históricos fuera del cambio.

### Tareas incompletas
- Ninguna.

## Riesgos

- **WARNING:** gate integral con cuatro fallos ajenos de Documentos; no se modificaron por estar fuera de alcance.
- **WARNING:** Playwright no puede reiniciarse localmente sin dependencias; se conserva evidencia focal aprobada de APPLY en los cuatro viewports.
- **SUGGESTION:** restaurar el entorno local de Playwright antes de una verificación integral posterior.

## Conclusión

Actualidad **aprueba VERIFY** respecto de sus requisitos. Siguiente fase recomendada: **ARCHIVE**, con excepciones documentadas.
