# Diseño: Estabilizar layout responsive y slider

## Enfoque técnico

Se establecerá un contrato de layout compartido entre `theme.json` y CSS: ancho amplio de 1200 px, gutter fluido y centrado mediante márgenes automáticos. Los fondos podrán ocupar todo el viewport, mientras su contenido respetará el mismo eje. Se adoptarán tres rangos orientados al contenido: base/móvil, tableta desde 768 px y escritorio desde 1024 px; 1200 px es un límite de contenido, no otro breakpoint.

El slider conservará su marcado y controlador. Cada slide usará una altura exterior explícita y responsive, en lugar del `min-height` actual que crece con el contenido. Texto y CTA tendrán una zona interna con espacio reservado para controles; si el contenido editorial extremo excede la altura, esa zona permitirá desplazamiento vertical en vez de recortarlo. La imagen seguirá siendo decorativa de fondo con `object-fit: cover`; su ausencia conservará el fondo negro y el contraste.

## Decisiones de arquitectura

| Decisión | Elección | Alternativas descartadas | Justificación |
|---|---|---|---|
| Fuente del ancho | `wideSize: 1200px` y variables CSS `--labm-content-max`/`--labm-gutter` | Anchos repetidos por selector | Mantiene Gutenberg y componentes propios bajo el mismo contrato. |
| Contenedores | Fondo full-width con contenido centrado por padding calculado; grupos constrained con ancho y margen automático | Envolver cada sección con marcado nuevo | Evita migrar bloques guardados y conserva el diseño Pen. |
| Breakpoints | Base, 768 px y 1024 px; gutter fluido dentro de cada rango | Breakpoint único de 480 px o uno en 1200 px | Responde a cambios reales de navegación, columnas y densidad. |
| Altura del slider | `block-size` responsive compartido por slides y overflow interno solo ante contenido extremo | Medir slides con JavaScript; truncar texto; sustituir slider | Elimina el salto de forma determinista, no oculta contenido y evita dependencia de carga de fuentes/imágenes. |
| Estado del slider | Conservar `hidden`, `aria-current`, pausa y movimiento reducido | Apilar slides invisibles | Mantiene accesibilidad, foco y comportamiento probado. |

## Flujo de datos

`theme.json`/tokens CSS → contenedores Gutenberg y LABM → geometría común de secciones

CPT `labm_slide` → PHP existente → slides → `home.js` cambia `hidden` sin alterar la caja exterior

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/themes/labm/theme.json` | Modificar | Exponer gutter y confirmar `wideSize` de 1200 px. |
| `wp-content/themes/labm/style.css` | Modificar | Tokens, contenedores, breakpoints y caja estable del slider. |
| `tests/php/FrontendTokensTest.php` | Modificar | Contrato estático de tokens y ancho amplio. |
| `tests/php/HomePresentationTest.php` | Modificar | Contrato de clases/estructura del slider si resulta necesario. |
| `tests/e2e/home.spec.ts` | Modificar | Recorrer controles e indicadores y comparar alturas. |
| `tests/e2e/public-experience.spec.ts` | Modificar | Medir centrado, ancho, gutters y overflow en 320/768/1024/1200/1440. |

`assets/home.js` solo se modificará si las pruebas demuestran una necesidad de sincronización; el diseño base no la requiere.

## Interfaces / contratos

- `--labm-content-max: 1200px` y `--labm-gutter`: contrato CSS reutilizable.
- Los márgenes equivalentes admitirán diferencia máxima de 1 px por redondeo.
- La altura exterior del slider deberá ser idéntica antes y después de cada control en un viewport dado.
- Bloques `alignfull` conservan fondo completo; contenido normal/`alignwide` no excede 1200 px.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| PHPUnit | Tokens, `wideSize`, marcado y límites editoriales | Aserciones sobre archivos/renderizado existente. |
| Integración | Compatibilidad WordPress y fixtures con/sin imagen | Gates PHP actuales. |
| E2E | Centrado, ancho, gutters, overflow y slider estable | `getBoundingClientRect`, todos los controles y anchos objetivo; tolerancia de 1 px. |
| Accesibilidad | Contenido extremo, foco, controles, zoom y reduced motion | Axe más recorridos por teclado y media emulada. |

## Migración / despliegue

No requiere migración de datos ni cambios editoriales. Desplegar tema y ejecutar gates completos. Para rollback, revertir CSS, `theme.json` y pruebas; el contenido y el slider existente permanecen compatibles.

## Riesgos

- CSS global de Gutenberg puede competir con selectores de layout; se usará especificidad acotada a clases LABM.
- Texto ampliado puede activar scroll interno; se validará que CTA y controles sean alcanzables y visibles.
- Imágenes con sujeto en bordes pueden perder encuadre por `cover`; se conservará el comportamiento vigente y se revisará visualmente contra Pen.

## Preguntas abiertas

- Ninguna bloqueante.
