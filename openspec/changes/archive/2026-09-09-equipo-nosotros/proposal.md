# Proposal: Quiénes hacen posible la liga

## Intent
Incorporar después de Misión/Visión una sección editorial fiel al diseño. El equipo podrá administrar nombres, roles, imágenes, orden y pertenencia a Comité, Entrenadores o Representantes desde WordPress.

## Scope
### In Scope
- CPT administrable de integrantes y taxonomía de perfiles.
- Título, filtros y cuatro tarjetas iniciales con imagen, nombre y rol.
- Fixtures idempotentes con personas, roles e imágenes demo.
- Grilla accesible y responsive con cobertura automatizada.

### Out of Scope
- Perfiles individuales, buscador, paginación, biografías e integraciones externas.

## Approach
`labm-core` registrará el CPT y la taxonomía. El tema consultará publicaciones públicas, ordenadas editorialmente, y compondrá una sección semántica. Los filtros serán enlaces progresivos por taxonomía. Los fixtures cargarán términos, artículos y adjuntos repetibles.

## Affected Areas
| Area | Impact | Description |
|------|--------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modified | CPT y taxonomía. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modified | Fixtures demo idempotentes. |
| `wp-content/themes/labm/functions.php` | Modified | Consulta y renderizado. |
| `wp-content/themes/labm/patterns/nosotros.php` | Modified | Inserción de sección. |
| `wp-content/themes/labm/style.css` | Modified | Diseño y responsive. |
| `wp-content/themes/labm/assets/images/equipo-demo/` | New | Retratos demo. |
| `tests/php/DomainModelTest.php` | Modified | Contrato editorial. |
| `tests/php/FixturesDomainTest.php` | Modified | Fixtures. |
| `tests/php/PublicExperienceTest.php` | Modified | Salida y filtrado. |
| `tests/e2e/public-experience.spec.ts` | Modified | Visual y accesibilidad. |

## Risks
| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Retratos pesados | Med | Optimización, proporción uniforme y carga diferida. |
| Contenido incompleto | Low | Omitir tarjetas inválidas y mostrar estado accesible. |
| Cambios locales | Med | Ediciones acotadas y preservación del trabajo existente. |

## Rollback Plan
Restaurar archivos, retirar `equipo-demo/` y regenerar enlaces permanentes. Los registros quedarían inertes; no hay migraciones destructivas.

## Dependencies
- WordPress 6.8+, PHP 8.3+, tema LABM, WP-CLI y retratos demo.

## Success Criteria
- [ ] El administrador puede crear, editar, publicar, ordenar y categorizar integrantes.
- [ ] Nosotros muestra la sección inmediatamente después de Misión/Visión con cuatro tarjetas demo.
- [ ] Comité, Entrenadores y Representantes filtran solo contenido publicado de su categoría.
- [ ] Nombre, rol e imagen provienen de artículos editables y el contenido incompleto falla de forma segura.
- [ ] La sección no desborda entre 320 y 1440 px y no presenta violaciones Axe.
- [ ] Fixtures repetidos no duplican personas, términos ni adjuntos.
