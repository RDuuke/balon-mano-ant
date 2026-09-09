# Verificación: Sección Misión y Visión

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Experiencia pública | Ambos artículos disponibles | ✅ COMPLIANT | PHPUnit render + Playwright Misión/Visión | — |
| Experiencia pública | Un artículo no disponible | ✅ COMPLIANT | `test_about_purpose_omits_each_unpublishable_article_independently` | — |
| Experiencia pública | Ningún artículo publicable | ✅ COMPLIANT | `test_about_purpose_omits_each_unpublishable_article_independently` | — |
| Experiencia pública | Presentación de escritorio | ✅ COMPLIANT | Playwright a 768, 1024, 1200 y 1440 px | — |
| Experiencia pública | Presentación móvil | ✅ COMPLIANT | Playwright a 320 px | — |
| Experiencia pública | Contenido editorial largo | ✅ COMPLIANT | CSS adaptable y contratos de desborde global | — |
| Fixtures | Carga inicial | ✅ COMPLIANT | `test_about_purpose_fixtures_are_independent_and_idempotent` | — |
| Fixtures | Carga repetida | ✅ COMPLIANT | `test_about_purpose_fixtures_are_independent_and_idempotent` | — |
| Fixtures | Conflicto independiente | ✅ COMPLIANT | `test_about_purpose_fixture_preserves_an_independent_conflict` | — |

## Evidencia TDD

Las once tareas tienen secciones RED y GREEN en `apply-progress.md`.

## Coherencia de Diseño

- ✅ Dos entradas estándar con slugs independientes.
- ✅ Renderizado SSR sin JavaScript.
- ✅ Apariencia clara/oscura determinada por clases del tema.

## Ejecución Real

- PHPUnit focal: ✅ 4 pruebas, 26 aserciones.
- Tests Composer e integración WordPress: ✅.
- Cobertura PHP: ✅ umbral mínimo del 80 %.
- PHPCS: ✅ tras corregir dos arrays compactos.
- PHPStan: ✅ sin errores.
- Playwright focal: ✅ 4 de 4 en 320, 768, 1024 y 1440 px.
- Regresión de experiencia pública: ✅ 32 de 32 pruebas.
- Axe focal: ✅ sin violaciones tras corregir el contraste del número 01.

## Evidencia visual

- Captura: `artifacts/visual/mision-vision-1440.png`.
- Viewport: 1440 × 1000 px.
- Sección: 1200 × 393 px.
- Tarjetas: igual altura, entre 280 y 420 px.
- Separación: 20 px, dentro del rango compacto de 12 a 32 px.
- Numerales: al menos 52 px, verde lima y con contraste Axe válido.
- Títulos: MISIÓN y VISIÓN completos en una línea.
- Responsive: apilado a 320 px; horizontal desde 768 px; sin overflow.

## Resultado

Los nueve escenarios son COMPLIANT. La sección queda lista para ARCHIVE, pero no se archiva en esta fase.
