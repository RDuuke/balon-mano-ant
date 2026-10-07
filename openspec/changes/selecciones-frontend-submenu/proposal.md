# Propuesta: Selecciones y submenú

## Intención
Presentar la sección principal de Selecciones con listados Piso/Playa y submenú accesible conforme a Pencil.

## Alcance
### Incluido
Sección principal /selecciones/ (Piso por defecto), heroes/filas Piso y Playa, contadores reales, paginación, vacío, medios opcionales y submenú desktop/móvil compartido.
### Excluido
Fichas individuales, plantillas single/detalle y rutas individuales: otro flujo, sin diseño Pencil. Ampliar backend/capacidades, inventar datos oficiales, reescribir configuración global o ejecutar APPLY.

## Enfoque
Fuente: exploration.md y exploration-design.md; frames neIe6/p1GT2/coPGt, opciones H3swWG/YCm3F/rp20t/TaEj4. Sin href/flows observados: proponemos `/selecciones/` por defecto Piso, `?modalidad=Piso|Playa`, `&pagina=N`. Filas informativas sin enlace individual ni CTA VER PUBLICACIÓN; no inventar destinos.

WP_Query publish; recuentos exclusivos labm_seleccion, sin Clubes. Mostrar vacío con recuperación; sin imagen, placeholder neutro. Reutilizar campos editoriales existentes.

Panel móvil en flujo, disclosure independiente y enlaces sin JS. Teclado/touch, Escape/cierre exterior, foco significativo/restaurado, aria-expanded, ocultos sin foco y áreas 44px; estado activo según modalidad. Unificar header existente. Filas móviles apiladas: adaptación responsive aprobada sin frame completo.

## Áreas afectadas
| Archivo | Impacto |
|---|---|
| wp-content/themes/labm/functions.php | Consulta/render/estado activo |
| wp-content/themes/labm/style.css | Presentación responsive |
| wp-content/themes/labm/parts/header.html | Submenú/panel |
| wp-content/themes/labm/templates/archive-labm_seleccion.html | Listado |
| wp-content/themes/labm/assets/navigation.js | Nuevo, si core requiere adaptación |
| tests/php/PublicExperienceTest.php | Regresiones focales |
| tests/e2e/public-experience.spec.ts | Selecciones/accesibilidad |
| tests/e2e/verify-correctives.spec.ts | Jerarquía/header |

## Riesgos
| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Header/estado query | Alta | Regresión focal compartida |
| Overrides persistidos | Desconocida | Inspección previa, preservar personalizaciones |
| Gaps visuales/demos | Media | Adaptaciones explícitas, datos editoriales |

## Reversión
Restaurar archivos enumerados a ecab4ca; retirar assets nuevos. Sin migraciones/flags. Si hubiera cambios persistidos, respaldo oficial previo y restauración selectiva documentada, sin eliminar contenido ajeno; content-sync con versión/hash, paquete y exclusión users/usermeta/credenciales.

## Dependencias
Backend archivado; «Ok» del usuario aprueba submenú/listados y excepción browser: VERIFY inicial solo Selecciones/submenú/header afectado, reemplazando suite Playwright global legacy para este cambio. PHP >=80% con evidencia vigente; filtros no acreditan suites completas. Reintentos solo fallidas/pendientes/invalidadas, reutilizando PASS válido. WPCS/PHPStan/LF aplicables, TDD strict; review budget deshabilitado. Lighthouse según impacto en DESIGN.

## Criterios de éxito
- [ ] Piso por defecto, Playa navegable, privados excluidos, conteos/paginación correctos; sin enlaces individuales rotos.
- [ ] Filas/Pencil, fallbacks, responsive sin desborde.
- [ ] Navegación WCAG2.2AA con/sin JS; header externo sin regresión.
- [ ] Evidencia focal y cobertura válidas; LF y sincronización condicional comprobados.
