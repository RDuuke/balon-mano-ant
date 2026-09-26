# Diseño: Detalle editorial de actualidad

## Enfoque técnico

Se conserva `labm_actualidad` y su URL individual. La plantilla `single-labm_actualidad.html` compone hero, medio, contenido y retorno con bloques del tema. Un shortcode de detalle, registrado en `functions.php`, resolverá únicamente los metadatos que no aporta un bloque estándar y el panel de compartir seguro. El contenido permanece nativo: `post-content` procesa Gutenberg y la galería es exclusivamente `core/gallery` dentro del cuerpo.

## Decisiones de arquitectura

| Decisión | Alternativas | Justificación |
|---|---|---|
| No crear ubicación | Registrar `labm_ubicacion` | El producto aprobó omitirla; evita ampliar el modelo y mostrar texto ficticio. |
| Galería en `core/gallery` | Metadato o CPT de galería | Reutiliza editor, medios, orden y alternativas existentes, sin duplicación. |
| Panel PHP pequeño para compartir | URLs en plantilla fija o servicio externo | Calcula URL canónica y codifica enlaces sin dependencias externas. |
| Copia progresiva | Exigir Clipboard API | Un script del detalle mejora el botón; un campo `readonly` etiquetado permite seleccionar la URL sin JavaScript. |
| Fallback del hero | Exigir miniatura | Mantiene el detalle publicable sin medio roto y sigue los helpers seguros de Actualidad. |

## Flujo de datos

```text
entrada publicada
  -> template individual
  -> categoría + fecha + título + miniatura/fallback
  -> post-content (incluye core/gallery si existe)
  -> shortcode compartir (URL canónica)
  -> enlaces Facebook/WhatsApp + control de copia/fallback
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/themes/labm/templates/single-labm_actualidad.html` | Modificar | Orden semántico de hero, medio, cuerpo, compartir y retorno. |
| `wp-content/themes/labm/functions.php` | Modificar | Registrar shortcode, construir metadatos, URL y enlaces seguros; encolar script solo en el individual. |
| `wp-content/themes/labm/assets/actualidad-detail.js` | Crear | Copia progresiva, mensaje perceptible y preservación del fallback sin JS. |
| `wp-content/themes/labm/style.css` | Modificar | Bloques `labm-actualidad-detail-*`, galería, foco y breakpoints. |
| `tests/php/PublicExperienceTest.php` | Modificar | Contratos de publicación, metadatos, escape, enlaces, fallbacks y galería. |
| `tests/e2e/public-experience.spec.ts` | Modificar | Ruta individual, copiar/enlaces, teclado, axe y geometría. |

## Interfaces / contratos

- Shortcode: `[labm_actualidad_detalle]`; solo genera salida con una publicación pública y URL canónica segura.
- Compartir: enlaces absolutos codificados hacia Facebook y WhatsApp; ningún destino se emite cuando falta URL segura.
- Copia: botón con estado accesible y campo `readonly` con etiqueta “Enlace de esta publicación”; el campo sigue disponible sin JavaScript.
- Galería: el único origen admitido es `core/gallery` en `post_content`; el editor decide su posición y alternativas.

## Estrategia de pruebas

| Capa | Qué validar | Enfoque |
|---|---|---|
| PHPUnit | Estado público, HTML escapado, metadatos, fallback, URL y galería | Entradas completas, parciales y no públicas. |
| Playwright | Hero, contenido, galería, compartir, teclado, 320–1440 y axe | Navegación real, sin JavaScript y con Clipboard simulado. |
| Gates | Formato, análisis, pruebas y LF | Focal en APPLY; gate integral en VERIFY. |

## Migración / rollout

No hay migración, cambio de CPT ni modificación de base de datos. Publicaciones existentes conservan su contenido; las que no tengan miniatura o galería degradan u omiten esas regiones. Rollback: revertir los cinco archivos del tema y las pruebas, sin eliminar contenido.

## Preguntas abiertas

- [ ] Ninguna bloqueante: las decisiones de ubicación, galería y fallback de copia fueron aprobadas.
