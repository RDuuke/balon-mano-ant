# Propuesta: Carrusel de logos de Aliados Oficiales

## Intención
Convertir «Aliados Oficiales» en una franja marquee de logos, sin artículos, textos, enlaces, botón de pausa ni otro control visible. La referencia aporta solo la composición general; las marcas demo serán ficticias.

## Alcance
### Incluido
- Mostrar solo imágenes publicadas con nombre accesible.
- Movimiento continuo, automático y de velocidad fija, sin controles.
- Estado estático automático con `prefers-reduced-motion: reduce`.
- Administración en WordPress, responsive, logos demo y pruebas.

### Fuera de alcance
- Artículos, páginas o enlaces para aliados.
- Marcas reales y otros rediseños.

## Enfoque
El CPT privado `labm_aliado` seguirá en **Aliados Oficiales** de wp-admin para administradores y editores. Usará solo **Título** (nombre/alt), **Imagen destacada** y **Orden** (`menu_order` mediante atributos de página); se retirarán editor, extracto, campos personalizados y URL. Alta, edición, papelera, restauración y borrado usarán acciones nativas y capacidades existentes. Publicar exigirá título y logo; los borradores no saldrán. El frontend consultará hasta 12 publicados por orden y título, omitirá logos inválidos y renderizará solo `<img>`.

La lista accesible será única; la réplica marquee será inerte. CSS usará duración constante; con movimiento reducido mostrará logos estáticos.

## Áreas afectadas
| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Modificado | CPT y validación. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificado | Logos demo ordenados, sin URL. |
| `wp-content/themes/labm/functions.php` | Modificado | Render exclusivo de imágenes. |
| `wp-content/themes/labm/style.css` | Modificado | Marquee fijo y responsive. |
| `wp-content/themes/labm/assets/home.js` | Modificado | Retiro de controles de aliados. |
| `wp-content/themes/labm/assets/images/aliados-demo/*.png` | Nuevo | Marcas ficticias. |
| `tests/php/HomeContentTest.php`, `tests/php/HomePresentationTest.php`, `tests/e2e/home.spec.ts` | Modificado | Pruebas. |

## Riesgos
| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Movimiento inaccesible | Media | Estado estático con movimiento reducido. |
| Logos deformados | Media | Caja uniforme y `object-fit: contain`. |
| Datos antiguos con URL/contenido | Baja | Ignorarlos sin migración destructiva. |

## Plan de reversión
Restaurar archivos, retirar PNG demo y reejecutar fixtures; sin migraciones ni flags.

## Criterios de éxito
- [ ] Solo aparecen logos, sin texto, enlaces o controles.
- [ ] Marquee continuo con duración fija; movimiento reducido lo deja estático.
- [ ] CRUD, orden, validación y publicación funcionan desde wp-admin.
- [ ] Logos demo son originales y no se deforman entre 320–1440 px.
- [ ] PHP, Playwright, formato y LF superan el gate.
