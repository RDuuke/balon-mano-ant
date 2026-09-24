# Design: Actualidad fiel a Pencil

## Enfoque técnico

Se conservará el archivo de CPT, el shortcode `[labm_actualidad_listado]` y la separación actual: `labm-core` posee el dominio y el tema LABM la consulta y presentación. El renderer de actualidad en `functions.php` pasará de una cuadrícula genérica de tres piezas a la composición del frame Pencil `Nrclx`: hero, formulario GET, primera noticia destacada y las tres restantes como tarjetas. La plantilla de archivo solo alojará el hero y shortcode; header, barra institucional, navegación y footer siguen siendo los template-parts existentes.

La consulta de actualidad pedirá cuatro publicaciones por página: una destacada más tres tarjetas. Selecciones conservará su renderer y límite actual. Todo contenido procede de una única `WP_Query`, pública y ordenada por fecha descendente, para impedir duplicados y desalineación con la paginación.

## Decisiones de arquitectura

### Decisión: Filtros en servidor y compatibilidad de categoría

| Opción | Tradeoff | Decisión |
|---|---|---|
| GET + `WP_Query` | Recarga progresiva, indexable y accesible | Elegida |
| Filtro JavaScript | Duplica consulta y estados | Descartada |

`texto` se saneará como texto público y se aplicará mediante `s`; `categoria` seguirá consultándose por nombre, como hace hoy, para conservar las URLs ya publicadas. El formulario preservará ambos valores al paginar con `add_query_arg`; una página inexistente renderizará el estado vacío, sin enlaces inválidos.

### Decisión: Componentes visuales aislados del archivo

| Opción | Tradeoff | Decisión |
|---|---|---|
| Clases `labm-actualidad-*` | CSS específico y sin afectar Selecciones | Elegida |
| Reutilizar `.labm-card` genérica | No expresa la composición Pencil | Descartada |

`style.css` incorporará tokens locales equivalentes a Pencil y bloques para hero negro, banda `surface-soft`, controles, destacada, tarjetas, paginación y vacío. Reutilizará variables LABM y Barlow Condensed; el cuerpo mantendrá `system-ui`, ya aprobado y configurado en `theme.json`. En escritorio, la destacada será una retícula imagen/contenido y las tarjetas tres columnas; a 768 px se reducen columnas y a 320 px formulario, destacada y tarjetas se apilan. `min-width: 0`, anchos fluidos y `object-fit: cover` previenen overflow. Los estados hover y `:focus-visible` existentes se aplicarán a enlaces y controles; se respetará `prefers-reduced-motion`.

### Decisión: Medio de noticia resiliente

| Opción | Tradeoff | Decisión |
|---|---|---|
| Helper de medio específico reutilizando el fallback local aprobado | No exige datos nuevos | Elegida |
| Imagen remota obligatoria | Rompe tarjetas sin miniatura | Descartada |

El renderer reutilizará la prioridad actual: miniatura destacada, `labm_demo_image` permitida y activo local. Si ningún recurso está disponible, emitirá una superficie visual no rota con texto alternativo útil. La metadata mostrará primera categoría y fecha mediante `time`; título, extracto y URL se escaparán con helpers de WordPress.

## Flujo de datos

```text
GET texto/categoria/pagina
  -> shortcode -> consulta publish (fecha DESC, 4)
  -> [destacada | 3 tarjetas] -> HTML semántico/CSS Pencil
  -> paginate_links con texto y categoria
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/themes/labm/functions.php` | Modificar | Filtros `texto`, consulta de cuatro, renderer semántico, medio, metadata y paginación de actualidad. |
| `wp-content/themes/labm/templates/archive-labm_actualidad.html` | Modificar | Sustituir copy demo por hero de Actualidad y conservar shortcode. |
| `wp-content/themes/labm/style.css` | Modificar | Añadir estilos aislados `labm-actualidad-*` y breakpoints. |
| `tests/php/PublicExperienceTest.php` | Modificar | Cubrir consulta, filtros, partición destacada/tarjetas, fallback y parámetros de paginación. |
| `tests/e2e/public-experience.spec.ts` | Modificar | Cubrir composición, interacción, vacío, foco y viewports. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificar si es necesario | Solo asegurar cuatro publicaciones paginables, categorías e imágenes locales. |

## Interfaces y contratos

- Ruta: `/actualidad/?texto={texto}&categoria={nombre}&pagina={n}`; `texto` y `categoria` son opcionales.
- `labm_theme_public_query()` conserva su contrato y admite `texto` únicamente para `labm_actualidad`.
- El contenedor continúa exponiendo `data-labm-listado="actualidad"`; la destacada y tarjetas recibirán selectores `labm-actualidad-*` para pruebas y CSS.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| PHPUnit | publish, orden, texto/categoría, 1+3 sin repetición, fallback, URL | Extender `PublicExperienceTest.php`. |
| Playwright | regiones Pencil, filtro, detalle, vacío, paginación, foco, 320/768/1440 | Extender el caso de actualidad existente. |
| Gate | WPCS, PHPStan, PHPUnit, accesibilidad y LF | Ejecutar en VERIFY según `scripts/gate.ps1 -IncludeBrowser`. |

## Migración y despliegue

No requiere migración ni cambio de CPT, taxonomía, REST o datos. El rollback es revertir tema, pruebas y fixtures de la rama.

## Preguntas abiertas

- [ ] Ninguna: el frame `Nrclx`, `?texto=`, categoría por nombre, fallback y `system-ui` fueron aprobados.
