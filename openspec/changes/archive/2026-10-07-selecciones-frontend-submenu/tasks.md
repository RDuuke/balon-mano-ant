# Tasks: Selecciones y submenú

## Phase 1: Foundation
- [x] 1.1 Inspeccionar `wp-content/themes/labm/` y `wp-content/plugins/labm-core/` para localizar query, header, overrides persistidos y colisiones de slug; documentar límites antes de editar.
- [x] 1.2 Añadir en `tests/php/PublicExperienceTest.php` pruebas RED para selección Piso/Playa, términos adicionales, privados/Clubes excluidos, conteos y paginación (R1–R2, S1–S6).
- [x] 1.3 Añadir pruebas RED de filas, extractos, metadatos, placeholder y ausencia de enlaces/CTA individuales (R3, S7–S9).

## Phase 2: Core Implementation
- [x] 2.1 Implementar en `wp-content/themes/labm/functions.php` la normalización escalar de `modalidad`/`pagina`, términos permitidos y consulta principal `publish` con filtro exclusivo `labm_seleccion` (R1–R2, S1–S6).
- [x] 2.2 Implementar en `wp-content/themes/labm/functions.php` contadores independientes de página, orden fecha/ID estable, última página válida y paginación que conserva modalidad o reinicia a uno al cambiarla (R1–R2, S4–S6).
- [x] 2.3 Implementar helpers de render en `functions.php` para resumen saneado, metadatos existentes, imagen segura/placeholder y estado vacío recuperable sin datos inventados (R3, S7–S9).

## Phase 3: Integration
- [x] 3.1 Componer `wp-content/themes/labm/templates/archive-labm_seleccion.html` con hero por modalidad, introducción, filas y paginación/vacío sin enlaces individuales (R3–R4, S7–S12).
- [x] 3.2 Sustituir únicamente la navegación de `wp-content/themes/labm/parts/header.html` por enlaces padre/Piso/Playa y estados `aria-current` correctos fuera y dentro del archivo (R1, S1–S3).
- [x] 3.3 Implementar en `wp-content/themes/labm/assets/navigation.js` disclosure progresivo, Escape, foco, clic exterior, resize y cierre anidado del panel móvil sin roles de menú (R2, S4–S9).
- [x] 3.4 Añadir en `wp-content/themes/labm/style.css` estilos acotados para desktop/móvil, 44 px, foco, contraste, hero, filas apiladas y ausencia de desborde (R3–R4, S10–S12).

## Phase 4: Testing
- [x] 4.1 Convertir las pruebas PHP a GREEN y verificar todos los escenarios S1–S9, incluidos arrays, términos ausentes, estados restringidos y página desbordada.
- [x] 4.2 Añadir en `tests/e2e/public-experience.spec.ts` cobertura focal de Selecciones: navegación, responsive, filas, vacío, paginación, teclado, touch y sin JavaScript (R1–R4, S1–S12).
- [x] 4.3 Actualizar `tests/e2e/verify-correctives.spec.ts` para comprobar jerarquía/header en Inicio y Actualidad, sin regresiones de sticky, foco y cierre móvil (R1–R2, S1–S10).
- [x] 4.4 Ejecutar pruebas focales a 320, 768, 1024, 1200 y 1440 px, texto 200 %, contraste y ausencia de desborde; registrar evidencia reproducible.

## Phase 5: Cleanup
- [x] 5.1 Ejecutar PHPCS, PHPStan, LF y gate focal; corregir solo hallazgos del cambio y conservar cualquier personalización existente.
- [x] 5.2 Si APPLY modifica contenido/uploads persistidos, crear respaldo y ejecutar `scripts/content-sync.ps1`, verificando versión, hash y exclusión de usuarios; si no, dejar constancia de que no aplica.
- [x] 5.3 Revisar diff contra los requisitos, confirmar rollback a `ecab4ca` y actualizar el progreso de tareas antes de VERIFY.
