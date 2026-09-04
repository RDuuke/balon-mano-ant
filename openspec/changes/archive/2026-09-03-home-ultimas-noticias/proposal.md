# Proposal: Últimas noticias en la portada

## Intent

Probar “Últimas noticias” con contenido ficticio reproducible y presentarla como la referencia: una pieza destacada, tres laterales y acceso al archivo, con imágenes, categoría, fecha y adaptación responsive.

## Scope

### In Scope

- Crear idempotentemente una categoría y seis noticias demo con fechas e imágenes.
- Mostrar cuatro noticias publicadas en orden predecible.
- Implementar layout destacado/lateral, CTA, metadatos, fallbacks y responsive accesible.
- Probar fixtures, consulta y marcado.

### Out of Scope

- Cambiar CPT, taxonomía o archivo completo de actualidad.
- Incorporar noticias reales, editores o servicios externos.

## Approach

Ampliar los fixtures con seis entradas de slugs estables, categoría común y medios locales. Crear un render específico que consulte cuatro publicaciones, destaque la primera y componga tres tarjetas semánticas. Añadir estilos encapsulados y pruebas TDD de idempotencia, salida y fallbacks.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modified | Categoría y seis noticias demo idempotentes. |
| `wp-content/themes/labm/functions.php` | Modified | Consulta y marcado específico. |
| `wp-content/themes/labm/style.css` | Modified | Layout responsive. |
| `tests/php/FixturesDomainTest.php` | Modified | Cantidad, categoría, slugs y medios. |
| `tests/php/HomePresentationTest.php` | Modified | Jerarquía, límite, metadatos y CTA. |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Duplicación o mezcla con contenido ajeno | Med | Slugs estables y marcador ficticio. |
| Datos editoriales ausentes | Med | Fallback seguro. |
| Desborde móvil | Med | Breakpoints y Playwright. |

## Rollback Plan

Restaurar los cinco archivos. No hay migraciones ni flags. Retirar solo entradas con slugs demo y marcador `FICTICIO` si fueron cargadas.

## Dependencies

- WordPress, `labm-core`, tema y activos locales.

## Success Criteria

- [ ] El fixture crea seis noticias en una categoría sin duplicarlas.
- [ ] La portada muestra una destacada y tres laterales con imagen, metadatos, enlaces y CTA.
- [ ] Datos incompletos producen HTML válido y usable.
- [ ] Desktop y móvil no presentan scroll horizontal.
- [ ] Pruebas, formato, accesibilidad básica y LF pasan.
