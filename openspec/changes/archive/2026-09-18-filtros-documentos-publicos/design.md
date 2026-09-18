# Diseño: filtros y metadata del catálogo público de documentos

## Enfoque técnico

El catálogo seguirá siendo renderizado en servidor mediante una consulta pública con parámetros GET normalizados. El formulario, los resultados, el estado vacío y la paginación compartirán un único conjunto de filtros. No se añadirá AJAX/REST ni se modificará el editor administrativo.

## Decisiones de arquitectura

| Decisión | Alternativa | Elección y justificación |
|---|---|---|
| Parámetros públicos | Estado JavaScript o sesión | GET con `texto`, `categoria`, `anio`, `orden` y `pagina`; produce URLs compartibles y funciona sin JavaScript. |
| Filtrado | Filtrar después de consultar | La consulta aplica texto, taxonomía y fecha antes de paginar para que el total y las páginas sean correctos. |
| Año | Fecha de publicación como respaldo | Usar únicamente `labm_documento_fecha`; una fecha ausente no coincide con un año, pero el documento sigue visible sin filtro. |
| Orden | Orden fijo o múltiples consultas | Valores permitidos: recientes y antiguos por fecha editorial, con fecha de publicación como desempate estable. |
| Código | ID del documento o valor inventado | No mostrar código hasta disponer de metadata editorial pública; un ID interno no es un código presentable. |
| Presentación | Cambiar el hero o crear una app | Conservar hero y ampliar HTML/CSS del catálogo; reduce superficie y mantiene el patrón visual existente. |

## Flujo de datos

```text
GET /documentos/?texto&categoria&anio&orden&pagina
          ↓ normalización y valores permitidos
   consulta de documentos publicados + PDF válido
          ↓ resultados y total de páginas
 formulario (valores seleccionados) + tarjetas + paginación
          ↓ enlaces con el mismo conjunto de parámetros
```

La normalización descartará texto vacío, categorías inexistentes, años no numéricos/no disponibles, órdenes desconocidos y páginas menores que uno. Las URLs solo conservarán parámetros activos. El enlace de limpiar omitirá todos los filtros y volverá a la primera página.

## Contratos de presentación

- El formulario tendrá etiquetas visibles, nombres estables, botón de aplicar y enlace de limpiar cuando haya filtros.
- Cada tarjeta expondrá categoría, título, fecha editorial y tamaño si existen; omitirá campos ausentes sin marcadores engañosos.
- Las acciones serán “Ver PDF” en nueva pestaña segura y “Descargar” mediante el enlace público existente.
- El estado vacío diferenciará catálogo sin documentos de consulta sin coincidencias y ofrecerá limpiar filtros en el segundo caso.
- El HTML usará estructura semántica, foco visible y controles apilables en pantallas estrechas; las acciones no dependerán de hover.

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificar | Normalización, consulta, opciones, tarjetas, estado vacío y URLs paginadas. |
| `wp-content/themes/labm/style.css` | Modificar | Formulario, metadata, tarjetas, paginación y responsive. |
| `tests/php/DocumentContactTest.php` | Modificar | Contratos de filtros, fechas, URLs y salida segura. |
| `tests/e2e/public-experience.spec.ts` | Modificar | Flujo público de filtros, teclado y viewport estrecho. |

## Interfaces y contratos

Los filtros aceptados serán `texto` (cadena), `categoria` (ID de término), `anio` (año de cuatro dígitos), `orden` (valor permitido) y `pagina` (entero positivo). La salida pública no añadirá endpoints: solo HTML y enlaces existentes. El tamaño se leerá únicamente cuando el adjunto sea válido y legible.

## Estrategia de pruebas

| Capa | Validar | Enfoque |
|---|---|---|
| PHP | Combinaciones, sanitización, ausencia de fecha, orden y paginación | Pruebas focales del catálogo y salida escapada. |
| E2E | Formulario, resultados, limpiar, teclado y responsive | Casos públicos con un viewport desktop y uno estrecho. |
| Estático | LF, formato y estándares | Validaciones focales antes de la verificación completa. |

## Migración y despliegue

No requiere migración: consume metadata y taxonomía ya existentes. El despliegue es reversible restaurando los tres archivos funcionales y sus pruebas.

## Preguntas abiertas

- [ ] Confirmar el texto final de etiquetas y orden con producto/diseño.
- [ ] Definir si en una fase futura se añadirá metadata editorial para código documental.
