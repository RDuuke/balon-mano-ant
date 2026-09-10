# Informe de verificación: Quiénes hacen posible la liga

## Resultado

**Estado: ok.** Las ocho tareas están completas, los doce escenarios tienen prueba automatizada asociada y todas las ejecuciones requeridas finalizaron correctamente.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---------|-----------|--------|---------------|-----------|
| experiencia-publica | Colección publicada | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_renders_editable_published_members_and_filters` | — |
| experiencia-publica | Integrante incompleto | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_omits_incomplete_and_restricted_members` | — |
| experiencia-publica | Contenido restringido | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_omits_incomplete_and_restricted_members` | — |
| experiencia-publica | Filtrado por grupo | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_renders_editable_published_members_and_filters`; Playwright de integrantes | — |
| experiencia-publica | Grupo sin resultados | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_renders_editable_published_members_and_filters` | — |
| experiencia-publica | Filtro inválido | ✅ COMPLIANT | `PublicExperienceTest::test_about_team_renders_editable_published_members_and_filters` | — |
| experiencia-publica | Anchos objetivo | ✅ COMPLIANT | Playwright `Nosotros presenta integrantes editables, filtrables y responsive` | — |
| experiencia-publica | Contenido largo | ✅ COMPLIANT | Playwright `Nosotros presenta integrantes editables, filtrables y responsive` | — |
| experiencia-publica | Navegación asistida | ✅ COMPLIANT | Playwright + Axe de integrantes | — |
| fixtures | Carga inicial completa | ✅ COMPLIANT | `FixturesDomainTest::test_about_team_fixtures_are_complete_categorized_and_idempotent` | — |
| fixtures | Carga repetida | ✅ COMPLIANT | `FixturesDomainTest::test_about_team_fixtures_are_complete_categorized_and_idempotent` | — |
| fixtures | Conflicto con contenido ajeno | ✅ COMPLIANT | `FixturesDomainTest::test_about_team_fixture_preserves_foreign_content_conflict` | — |

## Coherencia de Diseño

| Decisión | Estado | Evidencia |
|----------|--------|-----------|
| Reutilizar `labm_integrante` con taxonomía propia | ✅ implementada | Registro REST y pruebas de dominio. |
| Filtrado progresivo mediante GET | ✅ implementada | Enlaces `grupo` y prueba de navegación. |
| Exigir título, cargo, imagen y grupo | ✅ implementada | Consulta defensiva y prueba de omisión. |
| Medios demo locales | ✅ implementada | Cuatro PNG 1024×1536 e idempotencia de adjuntos. |

## Evidencia TDD

Las ocho tareas disponen de entradas RED y GREEN en `apply-progress.md`. La triangulación cubre privacidad, contenido largo, filtros, orden y geometría. Los fallos RED observados incluyeron taxonomía ausente, fixtures inexistentes, función de render no definida, formato PHPCS, contraste Axe y CRLF.

## Ejecución Real

- PHPUnit focal: 34 tests, 289 aserciones, sin fallos.
- Gate unitario Composer: PASS.
- Gate de integración WordPress: PASS.
- PHPCS: PASS.
- PHPStan: PASS.
- Playwright: 100 tests, 100 aprobados en 320, 768, 1024 y 1440 px.
- `git diff --check`: PASS.
- Finales de línea: cero CRLF en los archivos textuales del alcance.

## Cobertura de Código

- Sentencias: 1381/1524.
- Cobertura: **90.62%**.
- Umbral: 80%; superado.

## Resumen de Cumplimiento

| Dominio | COMPLIANT | FAILING | UNTESTED | PARTIAL |
|---------|-----------|---------|----------|---------|
| experiencia-publica | 9 | 0 | 0 | 0 |
| fixtures | 3 | 0 | 0 | 0 |
| **Total** | **12** | **0** | **0** | **0** |

No se detectaron problemas CRITICAL, WARNING ni SUGGESTION bloqueantes. El cambio puede avanzar a ARCHIVE.
