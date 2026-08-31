# Propuesta: Implementar el Home LABM

## Intent
Implementar la portada aprobada en el mockup Pencil: experiencia deportiva, responsive y accesible, con paleta verde/negro/blanco y contenido administrable. Se conservan páginas y modelos existentes.

## Scope
### In Scope
- Slider administrable bajo el menú, con controles, pausa, foco y `prefers-reduced-motion`.
- Home con presentación, clubes, evento destacado, actualidad, CTA y footer.
- “Aliados Oficiales” antes del footer: marquee infinito derecha-izquierda, pausa accesible y fallback estático.
- Retirar solo del Home Piso/Playa, horarios y escenarios; conservar CPT, rutas y páginas de Selecciones.
- Aplicar tokens aprobados en `theme.json` y CSS; persistir slides y aliados en `labm-core`.
- Actualizar pruebas PHP/E2E responsive y de accesibilidad.

### Out of Scope
- Rediseñar Nosotros, Actualidad, Selecciones, Documentos o Contacto.
- Eliminar CPT, taxonomías o datos de horarios, clubes o modalidades.
- Plugins externos de carrusel o cambios a WordPress core.

## Approach
Ampliar el patrón de Inicio y partes globales del tema; encapsular el modelo editorial en `labm-core`; añadir JavaScript ligero y CSS compatible con Gutenberg, traducción, escape y WPCS. Reutilizar consultas de actualidad/clubes y validar ausencia de secciones retiradas sin huecos.

## Affected Areas
| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/themes/labm/patterns/inicio.php` | Modificado | Composición del Home. |
| `wp-content/themes/labm/parts/{header,footer}.html` | Modificado | Navegación y pie. |
| `wp-content/themes/labm/{theme.json,style.css,functions.php}` | Modificado | Tokens y frontend. |
| `wp-content/plugins/labm-core/` | Modificado | Slides, aliados, permisos y fixtures. |
| `tests/e2e/`, `tests/php/` | Modificado | Contratos de portada. |

## Risks
| Riesgo | Prob. | Mitigación |
|---|---|---|
| Clones del marquee enfocables/desborde | Media | Clones no enfocables, overflow controlado y modo reducido estático. |
| Contenido vacío | Media | Estado vacío y demo marcada. |
| Tokens rompen aserciones visuales | Media | Actualizar contratos y ejecutar pruebas. |

## Rollback Plan
Restaurar tema, plugin y pruebas al commit anterior; retirar registros de slides/aliados con la migración o comando documentado. No se modifican core ni feature flags.

## Dependencies
- `design/labm-wordpress-mockup.pen`, inspeccionado mediante MCP Pencil.
- WordPress 6.8+, PHP 8.3+, `labm-core`, PHPUnit y Playwright.

## Success Criteria
- [ ] Slider bajo el menú, operable por teclado.
- [ ] Home sin Piso/Playa, horarios ni escenarios.
- [ ] Aliados se mueve infinitamente y ofrece pausa/modo reducido.
- [ ] Selecciones Piso/Playa siguen accesibles fuera del Home.
- [ ] Tokens aprobados y pruebas críticas pasan.
