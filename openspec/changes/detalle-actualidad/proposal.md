# Proposal: Detalle editorial de actualidad

## Intent

Convertir el detalle individual de Actualidad en la experiencia editorial canónica: hero, metadatos, medio, cuerpo, galería y compartir, sin duplicar contenido de WordPress.

## Scope

### In Scope

- Detalle público con título, categoría, fecha, imagen y contenido enriquecido.
- Galería nativa, compartir accesible, retorno, respuesta y pruebas.

### Out of Scope

- Cambiar la ruta, el archivo de Actualidad, contenido editorial existente, fuentes remotas o redes sociales institucionales.
- Definir un gestor externo de galerías.

## Approach

Extender la plantilla individual y CSS aislado del tema; usar Gutenberg como fuente del cuerpo y galería. Registrar o presentar metadatos únicamente tras aprobar los campos faltantes.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| `wp-content/themes/labm/templates/single-labm_actualidad.html` | Modified | Estructura pública individual. |
| `wp-content/themes/labm/functions.php` | Modified | Helpers seguros y compartir. |
| `wp-content/themes/labm/style.css` | Modified | Detalle y respuesta. |
| `tests/php/PublicExperienceTest.php` | Modified | Contrato de datos, escape y degradación. |
| `tests/e2e/public-experience.spec.ts` | Modified | Ruta, interacción, accesibilidad y viewports. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Compartir sin soporte Clipboard | Med | Enlace seguro y alternativa sin JavaScript. |
| Galería inexistente | Med | Omitir sección sin huecos ni marcado incompleto. |

## Rollback Plan

Revertir plantilla, helpers, CSS y pruebas del cambio; si se aprueba un metadato nuevo, dejarlo sin uso no afecta publicaciones existentes y revertir su registro en código. No hay migración destructiva ni cambio de ruta.

## Dependencies

- CPT, taxonomía y bloque Galería existentes.

## Decisiones aprobadas

- La ubicación se omite en esta entrega; no se crea metadato ni se muestra texto de relleno.
- La galería procede únicamente de bloques `core/gallery` dentro del contenido publicado.
- Facebook y WhatsApp usan enlaces de compartir; “Copiar enlace” ofrece una alternativa usable sin JavaScript.

## Success Criteria

- [ ] Una publicación completa presenta hero, metadatos, imagen, cuerpo y retorno de forma semántica y responsive.
- [ ] Los datos o medios ausentes se omiten o degradan sin enlaces/imágenes rotos ni contenido privado.
- [ ] La galería y las acciones aprobadas son operables por teclado y verificables en pruebas.
- [ ] Pruebas focales, análisis aplicables y LF pasan en los archivos modificados.
