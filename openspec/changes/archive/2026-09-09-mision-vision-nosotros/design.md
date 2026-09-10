# Design: Sección Misión y Visión

## Technical Approach

Extender el patrón ya usado por `banner-nosotros`: fixtures de entradas estándar con slugs reservados, consulta pública por slug y render PHP seguro. La sección será un grid de dos artículos cuyo modificador visual depende de la posición declarada, no del contenido editorial.

## Architecture Decisions

| Decisión | Elección | Alternativas | Justificación |
|---|---|---|---|
| Modelo editorial | Dos entradas `post` con slugs `mision-nosotros` y `vision-nosotros` | CPT o campos de página | Mantiene edición independiente y reutiliza infraestructura probada |
| Renderizado | Helper SSR `labm_theme_render_about_purpose()` | Bloque dinámico o JS | Evita controles, dependencias cliente y filtraciones de borradores |
| Apariencia | Clases posicionales `--light` y `--dark` | Estilos guardados en contenido | Conserva fidelidad y limita decisiones visuales del editor |

## Data Flow

`fixtures → entradas WordPress → helper del tema → patrón Nosotros → HTML/CSS`

## File Changes

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificar | Definiciones demo |
| `wp-content/themes/labm/functions.php` | Modificar | Consulta y render |
| `wp-content/themes/labm/patterns/nosotros.php` | Modificar | Composición |
| `wp-content/themes/labm/style.css` | Modificar | Diseño responsive |
| `tests/php/FixturesDomainTest.php` | Modificar | Persistencia e idempotencia |
| `tests/php/PublicExperienceTest.php` | Modificar | HTML y estados |
| `tests/e2e/public-experience.spec.ts` | Modificar | Orden, geometría y Axe |

## Interfaces / Contracts

`labm_theme_render_about_purpose(): string` devuelve HTML seguro o cadena vacía si no existe ningún artículo publicable.

## Testing Strategy

| Capa | Qué probar | Enfoque |
|---|---|---|
| Fixtures | Dos entradas, independencia e idempotencia | PHPUnit con WordPress |
| Tema | Orden, clases, limpieza y omisión | PHPUnit |
| Navegador | 01/02, claro/oscuro, apilado, desborde y Axe | Playwright |

## Migration / Rollout

Sin migración; la carga idempotente incorpora las entradas demo.

## Open Questions

- Ninguna.
