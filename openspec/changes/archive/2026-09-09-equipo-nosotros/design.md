# Design: Quiénes hacen posible la liga

## Technical Approach

Se reutiliza `labm_integrante`, ya administrable por REST y con soporte de imagen, orden y metadato `labm_cargo`. `labm-core` añadirá la taxonomía jerárquica `labm_grupo_integrante`. El tema consultará solo integrantes publicados, completos y asignados a los grupos permitidos, y renderizará hasta cuatro tarjetas. `?grupo=` aplicará el filtro en servidor; un valor inválido vuelve a la colección general.

## Architecture Decisions

| Decisión | Opción elegida | Alternativas | Justificación |
|----------|----------------|--------------|---------------|
| Modelo editorial | Reutilizar `labm_integrante` + taxonomía propia | Entradas estándar o CPT nuevo | El dominio ya ofrece capacidades, REST, imagen, orden y cargo. |
| Filtrado | Enlaces GET con renderizado servidor | JavaScript obligatorio | Es indexable, accesible y funcional sin scripts. |
| Elegibilidad | Título, cargo, imagen y grupo obligatorios | Fallbacks visuales | Evita tarjetas ambiguas o medios rotos. |
| Medios demo | PNG locales importados como adjuntos | URLs externas | Fixtures deterministas y sin dependencias de red. |

## Data Flow

```text
Admin -> labm_integrante + grupo + cargo + imagen
      -> consulta pública filtrada -> validación -> tarjetas de Nosotros
Assets locales -> WP-CLI fixtures -> adjuntos destacados
```

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modify | Registrar taxonomía y asociarla al integrante. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modify | Añadir cuatro fixtures con términos, orden y retratos. |
| `wp-content/themes/labm/functions.php` | Modify | Consultar, validar y renderizar la sección. |
| `wp-content/themes/labm/patterns/nosotros.php` | Modify | Invocar la sección tras Misión/Visión. |
| `wp-content/themes/labm/style.css` | Modify | Replicar composición y estados responsive. |
| `wp-content/themes/labm/assets/images/quienes-demo/*.png` | Create | Retratos demo originales. |
| `tests/php/DomainModelTest.php` | Modify | Probar taxonomía administrable. |
| `tests/php/FixturesDomainTest.php` | Modify | Probar colección e idempotencia. |
| `tests/php/PublicExperienceTest.php` | Modify | Probar consulta, salida y privacidad. |
| `tests/e2e/public-experience.spec.ts` | Modify | Probar filtros, geometría y Axe. |

## Interfaces / Contracts

- Taxonomía: `labm_grupo_integrante`; slugs permitidos: `comite`, `entrenadores`, `representantes`.
- Filtro público: `grupo`; valor sanitizado y limitado a los slugs anteriores.
- Render: `labm_theme_render_about_team()` devuelve HTML seguro o sección vacía si no hay tarjetas.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|--------------|----------|
| PHP dominio | Registro REST y asociación | PHPUnit de integración WordPress. |
| PHP fixtures | Cuatro artículos, términos y adjuntos estables | Dos cargas consecutivas y conflicto. |
| PHP tema | Elegibilidad, filtro y privacidad | Consultas y aserciones de marcado. |
| Navegador | Orden, filtros, responsive, foco y Axe | Playwright en anchos objetivo. |

## Migration / Rollout

No requiere migración. Al activar o actualizar, la taxonomía queda disponible; los fixtures son solo de desarrollo.

## Open Questions

- Ninguna bloqueante.
