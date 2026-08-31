# Propuesta: Estabilizar layout responsive y slider

## Intención

Corregir el centrado deficiente en ordenadores y el salto de altura del slider. El diseño conservará las alineaciones de Pen, con contenido de máximo 1200 px centrado en viewports mayores.

## Alcance

### Incluido

- Unificar contenedores, gutters y alineaciones de las secciones públicas relevantes.
- Reorganizar breakpoints para móvil, tableta, ordenador y pantallas amplias.
- Estabilizar el slider ante diferencias de texto o medios.
- Ampliar pruebas responsive y de estabilidad visual.

### Excluido

- Rediseñar contenido, identidad o navegación.
- Cambiar CPT, datos o panel administrativo.
- Sustituir el slider por una librería externa.

## Enfoque

Definir un contrato CSS de ancho máximo y gutters fluidos, alineado con `theme.json`, para cabecera, portada, secciones y pie, conservando fondos de ancho completo. Revisar anchos intermedios. Para el slider, fijar una altura estable por breakpoint con recorte controlado, legibilidad, controles accesibles y movimiento reducido. Añadir aserciones geométricas E2E al navegar.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/themes/labm/style.css` | Modificado | Contenedores, gutters, breakpoints y altura del slider. |
| `wp-content/themes/labm/theme.json` | Modificado | Coherencia del ancho amplio de 1200 px. |
| `wp-content/themes/labm/assets/home.js` | Posible | Sincronizar altura si CSS no cubre contenido dinámico. |
| `tests/e2e/home.spec.ts` | Modificado | Estabilidad al cambiar slides. |
| `tests/e2e/public-experience.spec.ts` | Modificado | Centrado, gutters y desborde en anchos objetivo. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Regresión en bloques | Media | Selectores acotados y pruebas por ruta/ancho. |
| Recorte de contenido del slider | Media | Altura adaptativa por breakpoint y casos con contenido extremo. |

## Plan de reversión

Restaurar los cinco archivos afectados a su versión anterior; no hay migraciones, datos ni feature flags que revertir.

## Dependencias

- Diseño Pen vigente y fixtures actuales para validación visual.

## Criterios de éxito

- [ ] A más de 1200 px, el contenido mide como máximo 1200 px y queda centrado.
- [ ] No hay desborde ni desalineación en 320, 768, 1024, 1200 y 1440 px.
- [ ] La altura exterior del slider no cambia al usar anterior, siguiente o indicadores.
- [ ] Texto, medios y controles siguen visibles, accesibles y coherentes con Pen.
- [ ] Playwright/axe y gates existentes aprueban.

## Aprobación requerida

Se requiere aprobación del usuario antes de avanzar a especificación.
