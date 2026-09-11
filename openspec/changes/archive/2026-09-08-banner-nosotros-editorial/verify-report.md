# Verificación: Banner editorial de Nosotros

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Experiencia pública | Artículo publicado completo | ✅ COMPLIANT | `PublicExperienceTest::test_about_page_renders_published_editorial_banner`; Playwright `Nosotros inicia...` | — |
| Experiencia pública | Adaptación a pantalla estrecha | ✅ COMPLIANT | Playwright `Nosotros inicia...` a 320 px | — |
| Experiencia pública | Artículo ausente o no público | ✅ COMPLIANT | `test_about_banner_omits_unpublishable_or_incomplete_article` | — |
| Experiencia pública | Presentación de escritorio | ✅ COMPLIANT | Playwright `Nosotros inicia...` a 1440 px; captura inspeccionada | — |
| Experiencia pública | Contenido editorial largo | ✅ COMPLIANT | Playwright inyecta título/resumen largo y mide separación y desborde | — |
| Experiencia pública | Imagen destacada no disponible | ✅ COMPLIANT | `test_about_banner_omits_unpublishable_or_incomplete_article` | — |
| Fixtures | Carga inicial | ✅ COMPLIANT | `test_about_banner_fixture_is_complete_and_idempotent` | — |
| Fixtures | Carga repetida | ✅ COMPLIANT | `test_about_banner_fixture_is_complete_and_idempotent` | — |
| Fixtures | Conflicto con contenido ajeno | ✅ COMPLIANT | `test_about_banner_fixture_preserves_foreign_content_conflict` | — |

## Evidencia TDD

- Las 13 tareas marcadas como completas tienen secciones RED y GREEN en `apply-progress.md`.
- Ejecución RED inicial: 2 errores y 2 fallos.
- Ejecución GREEN PHP: 16 pruebas y 169 aserciones correctas.

## Coherencia de Diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Entrada estándar con slug reservado | ✅ Implementada | `banner-nosotros`, tipo `post`, publicada y única |
| Renderizado del lado del servidor | ✅ Implementada | `labm_theme_render_about_banner()` sin JavaScript |
| Activo demo existente como adjunto | ✅ Implementada | imagen destacada ID 2594 importada desde el tema |

## Ejecución Real

- PHPUnit focal: ✅ 16 pruebas, 169 aserciones.
- PHPCS: ✅ sin hallazgos.
- PHPStan: ✅ sin errores.
- Playwright focal: ✅ 8 pruebas en 320, 768, 1024 y 1440 px.
- Suite Playwright completa: ✅ 88 pruebas correctas en 320, 768, 1024 y 1440 px.
- Gate PHP: ✅ pruebas Composer e integración WordPress correctas.
- Cobertura PHP: ✅ por encima del umbral mínimo del 80 %.
- Calidad estática: ✅ Composer lint y PHPStan correctos.
- Evidencia visual: `artifacts/visual/banner-nosotros-1440.png` y `artifacts/visual/banner-nosotros-320.png`.
- Navegador integrado: no disponible; se utilizó el Playwright oficial del repositorio.

## Resultado

Los nueve escenarios del cambio son COMPLIANT y el gate global queda **COMPLIANT**. El desborde de la portada a 768 px procedía de activar prematuramente la cuadrícula de cuatro columnas del pie de página; su breakpoint se alineó con el ancho mínimo real de la composición. El cambio queda listo para ARCHIVE.
