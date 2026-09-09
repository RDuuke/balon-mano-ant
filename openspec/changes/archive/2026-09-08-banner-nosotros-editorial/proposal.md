# Proposal: Banner editorial de Nosotros

## Intent

Reemplazar el contenido provisional al inicio de “Nosotros” por un banner estático, administrable como un artículo de WordPress, con la composición dividida del diseño: bloque negro de texto institucional a la izquierda e imagen editorial a la derecha.

## Scope

### In Scope

- Crear un artículo publicado, idempotente y editable desde el panel, con título, extracto, contenido y una imagen destacada demo.
- Consultar exclusivamente ese artículo por slug estable para construir el banner de “Nosotros”.
- Aplicar estilos responsive y accesibles, sin controles ni comportamiento de slider.
- Añadir pruebas PHP y de navegador focales.

### Out of Scope

- El resto de secciones de “Nosotros”.
- Nuevos tipos de contenido o campos personalizados.
- Reutilizar el slider de portada.

## Approach

Usar una entrada estándar con slug `banner-nosotros`, creada por fixtures y administrable en WordPress. El tema resolverá la entrada publicada, renderizará su título, resumen e imagen destacada mediante un helper seguro y lo insertará desde el patrón de la página. Si falta la entrada, el banner se omitirá limpiamente. CSS específico reproducirá el diseño en escritorio y apilará contenido e imagen en pantallas estrechas.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modified | Crear artículo y adjunto demo idempotentes. |
| `wp-content/themes/labm/functions.php` | Modified | Resolver y renderizar el banner estático. |
| `wp-content/themes/labm/patterns/nosotros.php` | Modified | Usar el banner como inicio de página. |
| `wp-content/themes/labm/style.css` | Modified | Composición responsive fiel al diseño. |
| `tests/php/` y `tests/e2e/` | Modified | Cubrir contrato editorial, salida y presentación. |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Artículo eliminado o en borrador | Low | Omitir el banner sin errores ni contenido inventado. |
| Imagen sin recorte adecuado | Med | Usar `object-fit: cover` y focalización CSS verificable. |
| Contenido demo mezclado con noticias | Low | Slug reservado y marcador ficticio explícito. |

## Rollback Plan

Restaurar los cuatro archivos de producción y las pruebas, volver a ejecutar fixtures para retirar manualmente la entrada demo si se desea y conservar la página “Nosotros” previa; no hay migraciones ni banderas.

## Dependencies

- WordPress, LABM Core, tema LABM y fixtures locales existentes.

## Success Criteria

- [ ] Existe y es editable un artículo publicado con slug `banner-nosotros` e imagen destacada.
- [ ] `/nosotros/` inicia con un único banner estático sin controles de slider.
- [ ] La composición coincide con el diseño en escritorio y responde sin desbordes en móvil.
- [ ] El contenido se sanea/escapa, la imagen tiene alternativa adecuada y las pruebas focales pasan.
