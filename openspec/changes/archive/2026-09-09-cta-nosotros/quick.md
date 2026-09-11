# Quick Fix: Reutilizar el CTA de vinculación en Nosotros

## Objetivo

Mostrar en la página Nosotros, inmediatamente después de «Quiénes hacen posible la Liga», el mismo CTA «Haz parte del balonmano antioqueño» disponible en la portada, conservando su contenido, enlace, presentación responsive y accesibilidad.

## Archivos afectados

- `wp-content/themes/labm/functions.php`: extraer o exponer un único render reutilizable para el CTA existente.
- `wp-content/themes/labm/patterns/inicio.php`: consumir el render compartido en portada sin cambiar su comportamiento.
- `wp-content/themes/labm/patterns/nosotros.php`: insertar el render compartido después del equipo.
- `tests/php/PublicExperienceTest.php` y/o pruebas focales equivalentes: comprobar unicidad, contenido, enlace y orden.
- `tests/e2e/public-experience.spec.ts`: comprobar presencia, posición, responsive y accesibilidad en Nosotros.

## Blueprint

1. Añadir primero pruebas que fallen porque el CTA todavía no aparece en Nosotros después del equipo.
2. Convertir el marcado vigente del CTA de portada en un helper de renderizado compartido, preservando exactamente clases, textos y destino `/contacto/`.
3. Sustituir el marcado inline de portada por el helper y llamarlo desde `patterns/nosotros.php` después de `labm_theme_render_about_team()`.
4. No duplicar estilos ni introducir contenido o configuración editorial nuevos.
5. Verificar que portada y Nosotros contienen una sola instancia del mismo CTA y que Nosotros mantiene el orden propósito → equipo → vinculación.

## Riesgos

- Una extracción incompleta podría alterar el HTML o el estilo vigente de portada.
- Las pruebas deben distinguir el CTA compartido de otras llamadas a la acción de la página.
- Si la implementación exige modificar el contrato editorial, estilos globales o más subsistemas, se detendrá la vía rápida y se recomendará `PROPOSE`.

## Verificacion

- Ejecutar las pruebas PHP focales del patrón público y las pruebas existentes del CTA de portada.
- Ejecutar Playwright focal para Nosotros y portada en los anchos responsive ya cubiertos por la suite.
- Ejecutar lint/análisis estático pertinente para los archivos PHP modificados.
- Confirmar ausencia de regresiones visuales, desbordamiento horizontal y violaciones de accesibilidad en el CTA.
- Ejecutar `git diff --check` y verificar finales LF únicamente en los archivos modificados por este cambio.
