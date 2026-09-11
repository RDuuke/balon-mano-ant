## Tarea QUICK-1 — Alinear el CTA compartido con Pencil

- **RED:** los tests `tests/php/PublicExperienceTest.php::test_about_reuses_home_join_cta_after_team` y `tests/php/PublicExperienceTest.php::test_join_cta_uses_local_barlow_condensed_font` fallan con: `no contiene el nuevo texto` y `la familia Barlow Condensed es null`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`, `wp-content/themes/labm/theme.json`, `wp-content/themes/labm/style.css` y `wp-content/themes/labm/assets/fonts/`; los 2 tests pasan con 21 aserciones.
- **TRIANGULATE:** los tests `tests/e2e/home.spec.ts::vinculacion reproduce la composicion editorial y se adapta sin desborde` pasan en 4 perfiles y `tests/e2e/public-experience.spec.ts::Nosotros reutiliza el CTA de vinculación después del equipo` pasa junto con los escenarios de Nosotros en 16 ejecuciones; cubren Inicio, Nosotros, geometría desktop, fuente local, responsive y accesibilidad.
- **REFACTOR:** el CTA se declaró a ancho completo para neutralizar el límite `constrained` de WordPress que lo reducía a 720 px y colapsaba su retícula interna; no se duplicó markup ni se creó una variante exclusiva para Nosotros.
