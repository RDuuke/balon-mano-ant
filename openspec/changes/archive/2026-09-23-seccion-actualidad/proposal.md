# Proposal: Actualidad fiel a Pencil

## Intent

Convertir `/actualidad/` en una implementación fiel del canvas `design/labm-wordpress-mockup.pen`, frame `Actualidad — Desktop` (`Nrclx`, 1440 × 2168). La composición visual dirige el cambio.

## Scope

### In Scope

- Reproducir barra institucional, navegación, hero negro, filtros, destacada, tres tarjetas, paginación y footer con la jerarquía y contraste del frame.
- Aplicar los tokens Pencil `primary` #AECD25, `primary-dark` #789614, `ink` #000000, `surface` #FFFFFF, `surface-soft` #F3F6E8, `text` #202020, `muted` #686868 y `line` #DDE3CC; encabezados Barlow Condensed y cuerpo Inter o el equivalente local aprobado (`system-ui`).
- Reutilizar Botón Primario, Tarjeta Noticia y Control Filtro de Pencil como componentes HTML/CSS, incluidos foco y contraste.
- Interpretar desktop en tableta y móvil: controles y hero fluidos, destacada apilada, tarjetas a una columna y paginación utilizable; sin desbordamiento a 320 px.
- Mantener como soporte la consulta PHP publicada, `?texto=`, categoría, imágenes/fallback, fecha y filtros persistentes.

### Out of Scope

- Alterar CPT, taxonomía, REST, detalle, contenido editorial, fuentes remotas o el diseño fuente de Pencil.

## Approach

Extender shortcode y plantilla con marcado semántico que mapea `Nrclx`; CSS aislado materializa tokens y componentes. El servidor alimenta los estados del diseño.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| `wp-content/themes/labm/functions.php` | Modified | Datos y marcado de regiones Pencil. |
| `wp-content/themes/labm/templates/archive-labm_actualidad.html` | Modified | Entrada semántica del archivo. |
| `wp-content/themes/labm/style.css` | Modified | Tokens, componentes y respuesta. |
| `tests/php/PublicExperienceTest.php` | Modified | Filtros y fallback. |
| `tests/e2e/public-experience.spec.ts` | Modified | Estados, foco y viewports. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modified if needed | Fixtures visuales. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Imágenes insuficientes | Med | Fallback accesible y fixtures mínimos. |
| Diferencia tipográfica | Med | Activo local equivalente; sin remoto. |

## Rollback Plan

Revertir los archivos de tema, pruebas y fixtures de esta rama. Sin migraciones ni datos persistentes.

## Dependencies

- Frame `Nrclx`, tokens y componentes del archivo Pencil canónico.
- CPT, taxonomía y shortcode existentes.

## Success Criteria

- [ ] Desktop reproduce regiones, orden, colores, tipografías, botones, tarjetas y paginación de `Nrclx`.
- [ ] Tableta y móvil preservan esa jerarquía mediante el apilamiento definido, sin overflow a 320 px.
- [ ] Componentes, foco, contraste, búsqueda, categoría, paginación e imágenes ausentes funcionan y son accesibles.
- [ ] Pruebas focales pasan y los archivos modificados usan LF.
