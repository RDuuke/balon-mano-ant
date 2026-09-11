# Design: Footer completamente administrable

## Technical Approach

Crear un módulo funcional en `labm-core` que gestione la opción `labm_footer_settings`, el panel LABM → Footer, defaults, saneado y un shortcode renderizado en servidor. `parts/footer.html` contendrá únicamente el bloque Shortcode; el HTML producido conservará exactamente las clases actuales.

## Architecture Decisions

| Decisión | Alternativas | Justificación |
|---|---|---|
| Opción única en `labm-core` | Literales del tema; Site Editor | El contenido persiste al cambiar de tema y tiene una fuente administrativa única. |
| Settings API con `edit_theme_options` | CPT; Customizer | El volumen es pequeño, estructurado y propio de configuración global. |
| Shortcode server-side | Bloque dinámico con JS; PHP en HTML | Reutiliza APIs WordPress sin añadir toolchain editorial y mantiene escape centralizado. |
| Arrays fijos de enlaces | HTML libre | Permite sanear etiquetas y destinos por campo, sin aceptar marcado arbitrario. |

## Data Flow

`LABM → Footer -> Settings API -> labm_footer_settings -> shortcode -> HTML escapado`

## File Changes

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-footer-settings.php` | Crear | Defaults, esquema, panel, saneado y render. |
| `wp-content/plugins/labm-core/labm-core.php` | Modificar | Cargar módulo. |
| `wp-content/themes/labm/parts/footer.html` | Modificar | Consumir `[labm_footer]`. |
| `tests/php/FooterSettingsTest.php` | Crear | Validar permisos, saneado, defaults y salida. |
| `tests/php/FrontendTokensTest.php` | Modificar | Ajustar contrato del template part. |

## Interfaces / Contracts

- Opción: `labm_footer_settings`, array no autoload.
- Capacidad: `edit_theme_options`.
- Shortcode: `[labm_footer]`.
- Campos vacíos: se omite el nodo o enlace correspondiente.

## Testing Strategy

| Capa | Qué probar | Enfoque |
|---|---|---|
| Unidad | Defaults, esquema, saneado y HTML | PHPUnit con stubs WordPress existentes. |
| Contrato | Template sin literales editoriales | Lectura de archivo. |
| Estática | Estilo y seguridad PHP | PHPCS/PHPStan aplicables. |

## Migration / Rollout

No se requiere migración: la ausencia de opción activa defaults equivalentes. Cualquier override previo del template part debe restablecerse para consumir el archivo versionado.

## Open Questions

- Ninguna.
