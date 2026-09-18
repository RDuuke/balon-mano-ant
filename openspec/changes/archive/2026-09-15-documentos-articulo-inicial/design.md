# Design: Artículo inicial de Documentos

## Technical Approach

Se añadirá al tema un renderizador equivalente al banner editorial de Nosotros. Consultará el artículo de tipo `post` con slug `banner-documentos`, aceptará el extracto como resumen prioritario y usará el contenido como alternativa. El patrón Documentos lo invocará, y la plantilla FSE asociará dicho patrón con la página.

## Architecture Decisions

### Decision: Artículo editorial ordinario como fuente

| Opción | Trade-off | Decisión |
|---|---|---|
| Post publicado con slug reservado | Editable con el editor nativo y consistente con Nosotros | Elegida |
| Texto fijo en patrón | Menos código, pero no editable | Descartada |
| Campo de opciones | Añade UI y persistencia adicionales | Descartada |

Rationale: el tema ya obtiene el banner de Nosotros desde un post publicado; reutilizar esa convención reduce complejidad editorial.

### Decision: Plantilla específica y patrón no insertable

| Opción | Trade-off | Decisión |
|---|---|---|
| `page-documentos.html` y patrón `labm/documentos` | Aísla la ruta y evita duplicación | Elegida |
| Alterar plantilla genérica | Puede afectar páginas ajenas | Descartada |

Rationale: conserva navegación y pie comunes sin cambiar las composiciones existentes.

### Decision: Componente visual propio y sin imagen

| Opción | Trade-off | Decisión |
|---|---|---|
| Clases `labm-documents-banner` | Expresa el diseño negro de ancho completo | Elegida |
| Reutilizar clases de Nosotros | Acopla una variante con imagen | Descartada |

Rationale: el diseño de referencia es un bloque textual sin medio y requiere independencia semántica.

## Data Flow

```text
Post publicado banner-documentos
  título + extracto/contenido
             ↓
labm_theme_render_documents_banner()
             ↓
patrón labm/documentos → plantilla page-documentos → visitante
```

## File Changes

| File | Action | Description |
|---|---|---|
| wp-content/themes/labm/functions.php | Modify | Consultar, sanear y renderizar el artículo. |
| wp-content/themes/labm/patterns/documentos.php | Create | Componer el encabezado. |
| wp-content/themes/labm/templates/page-documentos.html | Create | Cargar cabecera, patrón y pie. |
| wp-content/themes/labm/style.css | Modify | Aplicar disposición, escala y contraste responsivos. |
| tests/php/PublicExperienceTest.php | Modify | Probar contrato, seguridad, plantilla y patrón. |

## Interfaces / Contracts

- `labm_theme_render_documents_banner(): string` devuelve HTML seguro o cadena vacía si el artículo no puede publicarse.
- El slug editorial reservado es `banner-documentos`.
- La ceja visible es «Transparencia y consulta» y no es contenido de usuario.

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| PHPUnit | Renderizado, escape, ausencia segura y archivos de composición | Añadir pruebas a `PublicExperienceTest.php`. |
| Calidad PHP | Estilo y análisis estático del tema | Ejecutar scripts Composer focales/aplicables. |
| Higiene | Finales de línea de archivos modificados | Detectar CRLF en el conjunto de cambios. |

## Migration / Rollout

No migration required. Crear o publicar el post `banner-documentos` provee el contenido inicial; sin él, la página degrada sin encabezado.

## Open Questions

- [ ] Ninguna.
