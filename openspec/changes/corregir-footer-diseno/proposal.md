# Proposal: Footer completamente administrable

## Intent

Convertir todo texto y enlace visible del footer en contenido administrable sin alterar su composición visual, evitando que el archivo del tema o el Editor del sitio actúen como fuentes editoriales paralelas.

## Scope

### In Scope

- Panel administrativo para identidad, descripción, títulos, enlaces, contacto, copyright y política.
- Defaults equivalentes al footer actual, sanitización, permisos y escape de salida.
- Renderizado dinámico con omisión segura de valores vacíos.

### Out of Scope

- Carga de imágenes, traducciones multidioma y rediseño visual.
- Migración automática de overrides históricos del Editor del sitio.

## Approach

Persistir una única configuración en `labm-core`, exponerla mediante una página administrativa restringida y renderizar el footer con un shortcode estable consumido por el template part del tema.

## Affected Areas

| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-footer-settings.php` | Nuevo | Configuración, panel, saneado y renderizado. |
| `wp-content/plugins/labm-core/labm-core.php` | Modificado | Carga del módulo. |
| `wp-content/themes/labm/parts/footer.html` | Modificado | Invocador único del footer dinámico. |
| `tests/php/FooterSettingsTest.php` | Nuevo | Contratos funcionales y de seguridad. |

## Risks

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Plugin inactivo | Baja | Fallback seguro del shortcode sin datos inventados. |
| Override de template part | Media | Documentar restablecimiento; contenido real reside solo en la opción. |

## Rollback Plan

Restaurar `footer.html` estático, retirar la carga del módulo y conservar la opción inerte. No hay migración destructiva ni feature flag.

## Dependencies

- WordPress Settings API y `labm-core` activo.

## Success Criteria

- [ ] Todo literal visible puede editarse desde LABM → Footer.
- [ ] Solo usuarios autorizados guardan datos saneados.
- [ ] Los defaults reproducen el footer actual y los vacíos se omiten con seguridad.
- [ ] Pruebas, lint, análisis aplicable y LF pasan.
