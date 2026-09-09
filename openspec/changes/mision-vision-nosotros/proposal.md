# Proposal: Sección Misión y Visión de Nosotros

## Intent

Completar la página “Nosotros” con una sección editorial de Misión y Visión fiel a la referencia, cuyos dos contenidos puedan administrarse de forma independiente desde WordPress.

## Scope

### In Scope

- Crear dos artículos demo independientes, publicados e idempotentes para Misión y Visión.
- Renderizar después del banner dos paneles numerados 01/02 con tratamiento claro/oscuro.
- Omitir de forma segura artículos no públicos o incompletos.
- Validar semántica, independencia editorial y respuesta en anchos objetivo.

### Out of Scope

- Nuevos tipos de contenido, campos personalizados o migraciones.
- Cambios al banner existente, navegación u otras páginas.

## Approach

Reutilizar entradas estándar con slugs reservados y marcador demo, igual que el banner. Un helper del tema consultará ambas entradas publicadas y generará una sección semántica; el patrón Nosotros la ubicará después del banner y CSS local resolverá la composición responsive.

## Affected Areas

| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificado | Dos entradas demo idempotentes |
| `wp-content/themes/labm/functions.php` | Modificado | Consulta y render seguro |
| `wp-content/themes/labm/patterns/nosotros.php` | Modificado | Inserción de la sección |
| `wp-content/themes/labm/style.css` | Modificado | Paneles claro/oscuro y responsive |
| `tests/php/FixturesDomainTest.php` | Modificado | Contratos de fixtures |
| `tests/php/PublicExperienceTest.php` | Modificado | Contratos de render público |
| `tests/e2e/public-experience.spec.ts` | Modificado | Geometría y accesibilidad |

## Risks

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Colisión de slug | Baja | Preservar contenido sin marcador demo |
| Contenido parcial | Media | Renderizar solo artículos publicados y completos |

## Rollback Plan

Restaurar los siete archivos afectados y retirar las dos entradas demo por sus slugs; no existen migraciones ni flags.

## Dependencies

- WordPress, tema LABM y cargador de fixtures existentes.

## Success Criteria

- [ ] Misión y Visión son entradas independientes y editables.
- [ ] La sección presenta 01/02, panel claro/oscuro y orden Misión→Visión.
- [ ] No hay desborde a 320, 768, 1024, 1200 ni 1440 px.
- [ ] Pruebas PHP, análisis y Playwright pasan.
