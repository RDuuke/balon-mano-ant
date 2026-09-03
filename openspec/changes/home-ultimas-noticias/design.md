# Design: Últimas noticias en la portada

## Technical Approach

Se conservarán `labm_actualidad` y `labm_categoria`. El fixture idempotente añadirá seis publicaciones ficticias con fechas explícitas, categoría `Noticias demo` y una ruta de imagen local controlada. La portada tendrá consultas separadas para evento y noticias, y un render específico con HTML semántico y clases BEM encapsuladas.

## Architecture Decisions

### Decision: Orden editorial estable

| Opción | Tradeoff | Decisión |
|---|---|---|
| Fecha descendente + ID descendente | Predecible y natural para noticias | Elegida |
| `menu_order` + título | Orden manual, pero mezcla intención editorial con orden alfabético | Descartada |

Rationale: `post_date DESC, ID DESC` evita empates inestables. Los seis fixtures tendrán fechas distintas y deterministas.

### Decision: Aislar noticias del evento

| Opción | Tradeoff | Decisión |
|---|---|---|
| Consultas específicas y mutuamente excluyentes | Añade dos contratos pequeños | Elegida |
| Reutilizar la consulta genérica | Puede repetir la misma entrada | Descartada |

Rationale: el evento requerirá `labm_fecha_evento`; noticias excluirá entradas con ese metadato. Así los fixtures informativos no desplazan ni duplican el evento destacado.

### Decision: CTA degradable

| Opción | Tradeoff | Decisión |
|---|---|---|
| Emitir CTA solo con URL de archivo segura | Evita enlaces vacíos | Elegida |
| Usar `/actualidad/` fijo | Puede romper instalaciones con otra estructura | Descartada |

Rationale: la URL se obtiene del archivo del CPT y se valida; si es falsa o vacía, se omite todo el ancla sin afectar el encabezado.

### Decision: Imágenes demo y fallbacks

| Opción | Tradeoff | Decisión |
|---|---|---|
| Imagen destacada, luego meta local permitida, luego fallback del tema | Reutiliza activos sin red ni duplicar adjuntos | Elegida |
| Descargar o crear adjuntos | Más realista, pero añade I/O y residuos | Descartada |

Rationale: `labm_demo_image` guardará únicamente rutas relativas de una lista permitida bajo `assets/images/`. Los fixtures alternarán los dos héroes existentes. El render prioriza miniatura editorial; valida la ruta demo y finalmente usa un fallback decorativo con dimensiones, `object-fit` y alt vacío cuando no aporta información adicional.

### Decision: Composición responsive

| Opción | Tradeoff | Decisión |
|---|---|---|
| Grid de una columna y mejora progresiva a 3/2 | Orden DOM estable y móvil seguro | Elegida |
| Reordenar con CSS | Se aproxima visualmente, pero altera lectura | Descartada |

Rationale: el DOM será encabezado, destacada y lista lateral. Desde 768 px, la lista usa tarjetas horizontales; desde 1024 px, el cuerpo usa columnas `minmax(0,3fr) minmax(0,2fr)`. Imágenes usarán proporción fija, textos podrán envolver y todo hijo tendrá `min-width: 0`.

## Data Flow

```text
fixture -> labm_actualidad + categoría + meta de imagen
evento  -> consulta con fecha de evento -> bloque evento
home    -> consulta sin fecha de evento -> destacada + laterales -> CSS responsive
```

## File Changes

| File | Action | Description |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modify | Seis noticias, fechas, categoría y rutas de imagen demo. |
| `wp-content/themes/labm/functions.php` | Modify | Consultas aisladas, medios, CTA y render semántico. |
| `wp-content/themes/labm/style.css` | Modify | Grid, tarjetas, foco y breakpoints. |
| `tests/php/FixturesDomainTest.php` | Modify | Idempotencia y datos demo. |
| `tests/php/HomePresentationTest.php` | Modify | Orden, exclusión, fallbacks, CTA y marcado. |
| `tests/e2e/home.spec.ts` | Modify | Geometría responsive, desborde, teclado y axe. |

## Interfaces / Contracts

- `labm_demo_image`: meta privada con ruta relativa permitida; nunca acepta URL externa ni traversal.
- Consulta de noticias: máximo cuatro, publicadas, sin `labm_fecha_evento`, ordenadas por fecha e ID descendentes.
- Consulta de evento: solo publicaciones con `labm_fecha_evento` válida; no comparte resultados con noticias.
- HTML: `section[data-labm-section="actualidad"]`, encabezado con CTA opcional, una destacada y una lista de cero a tres laterales.

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| PHPUnit fixtures | Seis entradas, categoría, fechas, rutas e idempotencia | Carga repetida y consultas por slug. |
| PHPUnit tema | Orden, límite, aislamiento, CTA, escape y fallbacks | Datos completos, parciales y ausentes. |
| Playwright | 320/768/1024/1440 px, foco, enlaces, sin overflow y axe | Navegación real y medición de geometría. |
| Gates | PHP, WPCS, PHPStan y LF | Gate focal y verificación explícita de EOL. |

## Migration / Rollout

No requiere migración de esquema. Se despliega código y luego se ejecuta el fixture solo en desarrollo/demo. La carga actualiza exclusivamente slugs marcados `FICTICIO`; rollback restaura archivos y elimina solo esas seis entradas si fueron cargadas.

## Open Questions

- Ninguna bloqueante.
