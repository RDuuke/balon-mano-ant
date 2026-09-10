# Informe de verificación: CTA en Nosotros

## Resumen

El CTA «Haz parte del balonmano antioqueño» se renderiza desde un único helper compartido en portada y Nosotros. En Nosotros aparece inmediatamente después del equipo, conserva clases, textos y enlace, y supera las verificaciones funcionales, responsive y de accesibilidad.

## Matriz de Validacion

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---------|-----------|--------|----------------|-----------|
| CTA compartido | PHP focal comprueba contenido, enlace, reutilización única y orden del patrón | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php::test_about_reuses_home_join_cta_after_team` | — |
| Navegador | Portada y Nosotros producen la misma firma semántica del CTA | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::Nosotros reutiliza el CTA de vinculación después del equipo` | — |
| Responsive | El CTA cabe sin recorte, solapamiento ni desborde en 320, 768, 1024 y 1440 px | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::Nosotros reutiliza el CTA de vinculación después del equipo` | — |
| Accesibilidad | El CTA conserva heading, enlace accesible y cero violaciones axe automáticas | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::Nosotros reutiliza el CTA de vinculación después del equipo` | — |
| Calidad | PHP unitario/integración, cobertura, PHPCS y PHPStan pasan; diff y LF conformes | ✅ COMPLIANT | `scripts/gate.ps1 -IncludeBrowser` y auditorías directas | — |

## Evidencia TDD

- RED confirmado: el test PHP nuevo falló por `Call to undefined function labm_theme_render_join_cta()`.
- GREEN confirmado: el mismo test pasó con 1 test y 8 aserciones.
- TRIANGULATE confirmado: la prueba Playwright focal pasó en 4 proyectos y la suite completa pasó 104/104.
- Evidencia detallada registrada en `apply-progress.md`.

## Ejecución Real

- PHPUnit focal ampliado de experiencia pública y portada: sin fallos.
- Gate PHP: configuración Compose, pruebas unitarias, integración WordPress, cobertura, PHPCS y PHPStan en estado `PASS`.
- Cobertura PHP: gate superado por encima del umbral configurado de 80 %.
- Playwright focal: 4/4 proyectos superados.
- Playwright completo: 104/104 pruebas superadas en 2,5 minutos.
- `git diff --check`: correcto.
- Auditoría de los nueve archivos textuales del alcance: finales LF, sin CRLF.

## Incidencias de Infraestructura

El wrapper `gate.ps1` cargado como scriptblock no pudo resolver `$PSScriptRoot` al encadenar el gate de navegador. Esto no afectó el producto: la suite Playwright completa se ejecutó directamente con el mismo contenedor, variables y script del proyecto, y pasó 104/104. Las URLs `home` y `siteurl` fueron restauradas a `http://localhost:8080` al finalizar.

## Resultado

Cinco de cinco filas conformes, sin fallos, escenarios no probados ni coberturas parciales. El cambio está listo para ARCHIVE.
