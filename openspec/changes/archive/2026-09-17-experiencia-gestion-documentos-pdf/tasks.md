# Tasks: Experiencia administrativa para documentos PDF

Trazabilidad: `A1-A3` experiencia administrativa (9 escenarios), `C1-C4` clasificación/compatibilidad (12) y `V1-V4` validación (12).

## Fase 1: Contrato RED y fixtures

- [x] 1.1 Extender `tests/php/DocumentContactTest.php` con builders de usuarios, posts y adjuntos temporales: PDF válido, firma/MIME falsos, ilegible e ID textual histórico.
- [x] 1.2 Escribir pruebas RED del estado efectivo, límite, fecha, permisos y atomicidad REST/clásica en `tests/php/DocumentContactTest.php` (`V1-V4`, 12 escenarios).
- [x] 1.3 Escribir pruebas RED de tipo único, capacidades, siembra, fallback, históricos y preservación ante fallos en `tests/php/DocumentContactTest.php` (`C1-C4`, 12).
- [x] 1.4 Crear `tests/e2e/document-admin.spec.ts` con casos RED de Media Library, ambos editores, errores, teclado/foco y axe (`A1-A3`, 9).

## Fase 2: Dominio GREEN

- [x] 2.1 Ajustar `wp-content/plugins/labm-core/includes/class-labm-domain.php` para registrar `labm_documento_pdf_id` como entero REST, fecha `Y-m-d` y compatibilidad con IDs textuales (`V4`, `C3`).
- [x] 2.2 Implementar en `wp-content/plugins/labm-core/includes/class-labm-domain.php` tipo único, capacidades separadas y siembra versionada/idempotente de los nueve términos (`C1-C3`).
- [x] 2.3 Crear `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` con estado efectivo y validador compartido de título/PDF/fecha/tipo, acceso, firma, MIME, lectura y `min(30 MB, wp_max_upload_size())` (`A2`, `V1-V4`).
- [x] 2.4 Integrar en `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` guardados REST/clásico autenticados, saneados y atómicos, sin mutación parcial ni borrado de adjuntos (`C1`, `C4`, `V1`, `V3`).

## Fase 3: Interfaces GREEN

- [x] 3.1 Cargar el módulo y assets solo para `labm_documento` desde `wp-content/plugins/labm-core/labm-core.php`, con configuración y textos localizados.
- [x] 3.2 Crear `wp-content/plugins/labm-core/assets/js/admin-documento.js`: panel de bloques con Media Library, reemplazo/retiro, nombre/peso/estado/límite, fecha y tipo único (`A1-A3`, `C1-C3`).
- [x] 3.3 Renderizar y guardar en `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` un metabox clásico equivalente, con nonce y valores fallidos recuperables (`A1-A3`, `C4`, `V3-V4`).
- [x] 3.4 Añadir avisos, anuncios y foco accesible en `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` y `wp-content/plugins/labm-core/assets/js/admin-documento.js` (`A1.3`, `A3`, `V1.3`, `V2.3`, `V4.3`).

## Fase 4: REFACTOR y verificación

- [x] 4.1 Llevar `tests/php/DocumentContactTest.php` de RED a GREEN y refactorizar helpers sin duplicar reglas cliente/servidor; registrar evidencia RED/GREEN por tarea.
- [x] 4.2 Llevar `tests/e2e/document-admin.spec.ts` a GREEN en bloques y clásico; verificar teclado, foco, anuncios y WCAG con axe.
- [x] 4.3 Ejecutar PHPUnit focal/completo, `scripts/coverage.ps1`, PHPStan y PHPCS; exigir cobertura ≥80 % y corregir solo archivos del alcance.
- [x] 4.4 Ejecutar `scripts/gate.ps1 -IncludeBrowser` y documentar cualquier fallo de infraestructura separado de regresiones.

## Fase 5: Cierre

- [x] 5.1 Documentar en `docs/development.md` el flujo administrativo, límite, permisos, compatibilidad y reversión, excluyendo frontend.
- [x] 5.2 Verificar LF en cada archivo modificado y confirmar que catálogo, plantillas y estilos públicos permanecen intactos.

## Fase 6: Corrección focal tras VERIFY

- [x] 6.1 Corregir guardado clásico POST/post y hook inexistente; demostrar RED/GREEN con nonce inválido, actualización válida, fecha imposible y API programática sin contexto clásico; reconciliar diseño y verificar LF.
- [x] 6.2 Cerrar evidencia backend adicional: tipos múltiples con PDF válido y adjunto inaccesible aislado pasan; corregir actualización REST solo meta que pierde el título efectivo, completar reparación del histórico y re-verificar focalmente.
- [x] 6.3 Ejecutar E1–E5 focales reales de reemplazo, reintento HTTP clásico, ayuda, upload fallido/tamaño y teclado usando URL accesible desde runner; la tanda inicial falló en login sin ejecutar cuerpos.
