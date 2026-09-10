## Tarea 1 — Reutilizar el CTA de vinculación en Nosotros

- **RED:** test `tests/php/PublicExperienceTest.php::test_about_reuses_home_join_cta_after_team` falla con: `Call to undefined function labm_theme_render_join_cta()`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`, `wp-content/themes/labm/patterns/inicio.php` y `wp-content/themes/labm/patterns/nosotros.php`; test pasa con 8 aserciones.
- **TRIANGULATE:** test `tests/e2e/public-experience.spec.ts::Nosotros reutiliza el CTA de vinculación después del equipo` cubre equivalencia con portada, orden, destino, cuatro anchos responsive y accesibilidad; 4 proyectos pasan.
- **REFACTOR:** el marcado inline de portada se movió sin alterar clases, textos ni enlace a un único helper compartido; no se añadieron estilos.
