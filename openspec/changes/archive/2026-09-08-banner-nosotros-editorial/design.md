# Design: Banner editorial de Nosotros

## Technical Approach

Se incorporará una entrada estándar (`post`) reservada por slug para representar el banner. El comando de fixtures la creará o actualizará de forma idempotente con el marcador demo y una imagen destacada ya incluida en el tema. Un renderizador del tema consultará exclusivamente esa entrada publicada y devolverá HTML semántico; el patrón “Nosotros” invocará el renderizador. CSS aislado implementará la composición partida y su variante móvil.

## Architecture Decisions

### Decision: Usar una entrada estándar con slug reservado

| Opción | Tradeoff | Decisión |
|---|---|---|
| Entrada estándar | Editable sin añadir infraestructura; requiere excluirla de listados genéricos si aparecen | Elegida |
| Tipo de contenido nuevo | Mejor aislamiento, pero amplía dominio, registro y panel para un solo elemento | Rechazada |
| Bloques estáticos en patrón | Simple, pero incumple administración editorial | Rechazada |

Rationale: la solicitud pide un artículo singular y el contrato de slug ofrece resolución determinista con el menor cambio de arquitectura.

### Decision: Renderizado del lado del servidor

Choice: helper PHP que solo consulta estado `publish`, extrae título limpio, resumen e imagen destacada y escapa la salida.

Alternatives: Query Loop de bloques o JavaScript cliente.

Rationale: mantiene control exacto del fallback, no requiere scripts y sigue los helpers dinámicos existentes del tema.

### Decision: Reutilizar un activo demo existente

Choice: importar `assets/images/hero-balonmano-seleccion-v1.png` como adjunto destacado mediante el mecanismo idempotente de fixtures.

Alternatives: referenciar directamente el archivo del tema o incorporar una imagen nueva.

Rationale: produce una imagen verdaderamente administrable desde WordPress, evita contenido externo y conserva el alcance.

## Data Flow

```text
wp labm fixtures load
  -> entrada post: banner-nosotros + adjunto destacado
  -> patrón page-nosotros
  -> consulta publicada por slug
  -> <section data-labm-section="nosotros-banner">
  -> CSS responsive, sin JavaScript
```

## File Changes

| File | Action | Description |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modify | Añadir definición del artículo y generalizar metadatos seguros del adjunto. |
| `wp-content/themes/labm/functions.php` | Modify | Resolver y renderizar el banner. |
| `wp-content/themes/labm/patterns/nosotros.php` | Modify | Sustituir el contenido provisional por el renderizador. |
| `wp-content/themes/labm/style.css` | Modify | Estilos del banner en móvil y escritorio. |
| `tests/php/FixturesDomainTest.php` | Modify | Probar existencia, campos, imagen e idempotencia. |
| `tests/php/PublicExperienceTest.php` | Modify | Probar salida pública y estados ausentes. |
| `tests/e2e/public-experience.spec.ts` | Modify | Probar geometría, ausencia de slider y accesibilidad. |

## Interfaces / Contracts

- Slug reservado: `banner-nosotros`.
- Tipo: `post`; estado público requerido: `publish`.
- Selector estable: `[data-labm-section="nosotros-banner"]`.
- La ausencia de artículo produce cadena vacía y no un fallback inventado.

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| PHP integración | Fixture idempotente, datos completos, conflicto seguro | PHPUnit con WordPress real |
| PHP presentación | HTML, escape, estado no público y medio ausente | PHPUnit focal |
| Navegador | Orden inicial, dos paneles, responsive, sin controles ni overflow | Playwright en 320 y 1440 px; Axe |
| Calidad | Sintaxis, estándares y tipos | Gate focal PHPCS/PHPStan |

## Migration / Rollout

No requiere migración. Ejecutar fixtures crea el artículo demo; en un entorno editorial real un administrador puede reemplazar sus datos desde Entradas y Medios.

## Open Questions

- Ninguna pregunta bloqueante.
