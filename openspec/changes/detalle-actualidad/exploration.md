# Exploración: Detalle de la actualidad

## Contexto y estado comprobado

La solicitud es una mejora de la vista individual pública de `labm_actualidad`. La ruta canónica ya la resuelve WordPress mediante el CPT `labm_actualidad` y `wp-content/themes/labm/templates/single-labm_actualidad.html`, pero la plantilla actual solo compone categoría, título, fecha, contenido y un enlace de retorno.

El export canónico `design/exports/detalle-actualidad-desktop.png` y el frame homónimo de `design/labm-wordpress-mockup.pen` requieren: hero negro con categoría, fecha y título; datos de ubicación/fecha; imagen destacada; cuerpo enriquecido; panel de compartir; y una galería sobre superficie clara.

## Capacidades reutilizables

- `labm_actualidad` ya es público, tiene editor, extracto, miniatura y categorías (`labm_categoria`).
- El editor de bloques permite cuerpo enriquecido y bloque Galería sin crear un modelo paralelo.
- `labm_fecha_evento` ya existe como metadato ISO administrable. No existe un campo de ubicación ni una galería estructurada específica.
- El tema ya tiene helpers seguros para título, categoría, fecha y medios de actualidad, además de tokens, foco visible y breakpoints para el archivo.
- Las pruebas PHP y Playwright ya cubren el archivo y el acceso al detalle, pero no el contrato visual o funcional completo del individual.

## Opciones evaluadas

1. **Plantilla individual de bloques + estilos del tema (recomendada):** conserva la edición nativa de contenido y galería; requiere resolver únicamente metadatos y acciones que no son bloques estándar.
2. **Renderer PHP/shortcode propio:** da control total, pero duplicaría el flujo de bloques y hace más frágil el contenido enriquecido.
3. **Campos o plugin de galería externo:** añade dependencia y migración sin evidencia de que el bloque Galería nativo sea insuficiente.

## Incógnitas que condicionan el alcance

- El diseño pide ubicación, pero no existe dato persistente equivalente. Debe aprobarse si se añade `labm_ubicacion` al CPT o si se omite cuando no exista.
- El mockup incluye Facebook, WhatsApp y “Copiar enlace”; falta decidir si las dos primeras serán enlaces de compartir y cómo degradará “Copiar enlace” sin JavaScript/Clipboard API.
- La galería puede derivarse solo de bloques `core/gallery` del cuerpo. Debe confirmarse si se exige una galería editorial separada y administrable fuera del contenido.

## Recomendación

Crear el cambio `detalle-actualidad` con la plantilla individual como punto de integración, reutilizar el contenido Gutenberg y su bloque Galería, y añadir solo los contratos de metadatos y compartición que se aprueben. No se necesita cambiar rutas ni migrar datos existentes.

## Riesgos

- Añadir ubicación o galería como metadatos cambia el modelo editorial y exige validación REST, permisos y compatibilidad con contenido existente.
- Enlaces de compartir dependen de proveedores externos; deben ser opcionales, URL-codificados y no filtrar contenido no público.
- El hero necesita un fallback semántico si falta miniatura para evitar imágenes rotas o encabezados vacíos.
