# VERIFY final tras FIX2: selecciones-backend

status: failed

## Resultado
Diez tareas completas y doce escenarios de Selecciones COMPLIANT. Gate final obligatorio ejecutado una sola vez hasta terminar, salida 1: siete fallos PHP, cobertura global actual 73.47 % frente al 80 %, PHPCS/PHPStan y Playwright con 28 fallos, 160 aprobadas y cuatro sin ejecutar. Ambos FIX operacionales están resueltos: carga PDF y salida Clover fresca con uid33. Se agotaron los dos intentos automáticos; no FIX3 ni ARCHIVE. El usuario autorizó abrir el cambio separado corregir-gate-global. Sin editar código ni pruebas durante VERIFY.

## Matriz de Validación
Todos los tests asociados pertenecen a `tests/php/DomainModelTest.php`; la suite focal de APPLY pasó con 14 pruebas y 431 aserciones, y no presenta fallos de Selecciones en la ejecución global de VERIFY.

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| arquitectura-cms | Instalación actualizada con permisos incompletos | COMPLIANT | test_current_version_repairs_selection_capabilities_additively | — |
| arquitectura-cms | Comprobación repetida | COMPLIANT | test_current_version_repairs_selection_capabilities_additively | — |
| arquitectura-cms | Usuario sin rol editorial | COMPLIANT | test_current_version_repairs_selection_capabilities_additively | — |
| arquitectura-cms | Ficha clasificada | COMPLIANT | test_selection_editorial_fields_and_extensible_terms_through_rest | — |
| arquitectura-cms | Ficha sin clasificación y clasificación nueva | COMPLIANT | test_selection_editorial_fields_and_extensible_terms_through_rest | — |
| arquitectura-cms | Cambio de clasificación no autorizado | COMPLIANT | test_selection_editorial_fields_and_extensible_terms_through_rest | — |
| arquitectura-cms | Edición autorizada | COMPLIANT | test_selection_detail_metadata_rest_sanitization_and_authorization | — |
| arquitectura-cms | Texto con marcado y valor vacío | COMPLIANT | test_selection_detail_metadata_rest_sanitization_and_authorization | — |
| arquitectura-cms | Escritura sin autorización | COMPLIANT | test_selection_detail_metadata_rest_sanitization_and_authorization | — |
| arquitectura-cms | Consulta pública | COMPLIANT | test_selection_public_rest_excludes_restricted_content | — |
| arquitectura-cms | Ninguna ficha publicada | COMPLIANT | test_selection_public_rest_excludes_restricted_content | — |
| arquitectura-cms | Acceso directo a contenido restringido | COMPLIANT | test_selection_public_rest_excludes_restricted_content | — |

## Coherencia del diseño
IMPLEMENTED: reparación aditiva de Selecciones antes del guard de versión; Editor/Administrador, marcador vigente conservado, sin tablas/controladores nuevos, REST y autorización nativos. La regresión prueba preservación e idempotencia sin escrituras. La omisión segura del rol inexistente está implementada; esa rama no fue ejecutada en Clover.

## Evidencia TDD
RED auténtico y GREEN documentados para la regresión y el cambio productivo (1.2/2.1), seguido de revisión 2.2 y suite final. WARNING documental: las tareas operativas 1.1, 3.4, 4.1, 4.2 y los contratos existentes 3.1–3.3 carecen de secciones individuales RED/GREEN con el formato estricto de la skill; las tres últimas están agrupadas. No se fabricaron fallos para funcionalidades previamente correctas. La evidencia productiva no presenta una implementación anterior al RED.

## Ejecución real
- `scripts/gate.ps1 -IncludeBrowser`: ejecución única finalizada salida 1.
- Compose PASS; PHPUnit unitario PASS: una prueba/dos aserciones.
- Integración PCOV FAIL: 167 pruebas, 1663 aserciones, siete fallos. Ninguno DomainModelTest.
- PHPCS FAIL: 35 errores/68 warnings: documents-contact 16/34, fixtures-command 8/23, functions.php 11/11.
- PHPStan FAIL: ensure_demo_attachment sin uso en fixtures-command:269.
- Playwright FAIL: 28 fallos, 160 aprobadas, cuatro sin ejecutar, 6.1 minutos. Las cuatro pruebas de carga A1.1 siguen aprobadas. Variación frente a VERIFY anterior: E1 de documentos en tablet espera un nombre exacto aunque WordPress lo renombra con sufijo -1; cuatro posteriores del grupo omitidas. No atribuir ese fallo a uploads/permisos ni a Selecciones sin diagnóstico adicional.
- Logs completos finalizados: artifacts/gate/summary.json y composer-test, php-coverage, composer-lint, composer-analyse, browser-portable.

## Cobertura de Código
Clover fresco generated1790885595, de esta ejecución; PHPUnit informa generación completada sin error de escritura. Cobertura global real 73.47 % = 2038/2774 líneas; mínimo 80 % intacto. El runner conserva esta evidencia y métrica antes de devolver fallo de suite. No se usa Clover anterior/focal. CRITICAL: umbral global incumplido. FIX2 GREEN operacional confirmado; no autoriza reducir umbral ni excluir código.

## Carga de Revisión
Aprobación anterior review_budget: approved conservada. Configuración max_diff_lines 0, rutas sensibles vacías. No se repite aprobación; el usuario autorizó un cambio separado para corregir el gate global.

## Alcance y causalidad
Persisten contratos fuera de Selecciones: importación de fixtures acoplada al orden, recuento Nosotros, composición Documentos, detalle Actualidad y fallback Portada. PHPCS/PHPStan afectan documentos-contact, fixtures-command y functions.php; cobertura global requiere pruebas reales de ramas existentes. El fallo Documentos se reprodujo en baseline HEAD previamente; esa evidencia no demuestra causalidad histórica de los demás. Detalle Actualidad está pausado y debe reconciliarse con su flujo. Ambos FIX consumidos resuelven permisos operacionales; no se cambiaron assertions, exclusiones ni umbrales.

## Persistencia y LF
Backup previo oficial backup-manual-20261001T201243871Z-rduuqe-RDUUQE.zip. Backup posterior backup-manual-20261001T202151584Z-rduuqe-RDUUQE.zip, SHA256 d9bb870b0c5a47e0ab68da0838dffb25bc4c3b67b5553a9dda423bb5ce08ca30. Auditoría verify-final-runtime-audit.json: 65 posts temporales añadidos y ocho eliminados (demos/autodraft), sin cambios de Selecciones; único post_content cambiado galería noticia demo.

Restore oficial finalizado salida 0 con backup-pre-restore-20261001T202234766Z-rduuqe-RDUUQE.zip. Backup limpio final backup-manual-20261001T202308367Z-rduuqe-RDUUQE.zip, auditado en verify-final-runtime-clean-audit.json: contenido/posts y demás tablas iguales al canónico salvo cron y marcador. Local/canónico misma versión. Sin exportar fixtures al paquete ni reemplazar cuentas.

Paquete vigente intacto: versión20261001T194755441Z-rduuqe-RDUUQE, SHA256 ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e. Auditoría valida los 152 archivos/tamaños/hashes; manifest/pointer/versiones concordantes. Exclusión wp_users/wp_usermeta y credenciales previamente validada; paquete idéntico. Artefactos VERIFY y logs de esta ejecución LF.

## Fallos Detectados

### Tests fallidos
- `FixturesDomainTest::test_home_allies_fixture_reports_upload_failures`: esperaba arco-comun; recibe hero-seleccion (CRITICAL).
- `PublicExperienceTest::test_about_team_renders_editable_published_members_and_filters`: esperado2 observado4 (CRITICAL).
- `PublicExperienceTest::test_documents_pattern_and_template_compose_the_editorial_banner`: min-height22.3125rem ausente; reproducido HEAD (CRITICAL).
- `PublicExperienceTest::test_actualidad_detail_renders_black_hero_before_featured_media`: posición esperada false (CRITICAL).
- `PublicExperienceTest::test_actualidad_detail_omits_restricted_posts_and_uses_native_content_template`: sin wp:post-content (CRITICAL).
- `HomePresentationTest::test_home_news_media_priority_and_missing_category_are_safe`: fallback antioquia frente selección (CRITICAL).
- `HomePresentationTest::test_home_news_helpers_omit_invalid_cta_and_use_safe_media_fallbacks`: fallback antioquia frente selección (CRITICAL).

### Errores de build
- `artifacts/coverage/clover.xml`: cobertura global actual 73.47 % (2038/2774) inferior al 80 % requerido (CRITICAL).
- `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`: PHPCS16errores/34warnings (CRITICAL).
- `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`: PHPCS8errores/23warnings (CRITICAL).
- `wp-content/themes/labm/functions.php`: PHPCS11errores/11warnings (CRITICAL).
- `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php:269`: PHPStan método sin uso (CRITICAL).

### Tests de navegador fallidos
- `tests/e2e/document-admin.spec.ts:187/200`: tablet espera labm-e2e-replacement.pdf y recibe labm-e2e-replacement-1.pdf; cuatro pruebas posteriores omitidas (CRITICAL).
- `tests/e2e/public-experience.spec.ts:161`: recuento Nosotros,4tamaños (CRITICAL).
- `tests/e2e/public-experience.spec.ts:360`: filtros/estado Actualidad,4tamaños (CRITICAL).
- `tests/e2e/public-experience.spec.ts:375`: limpiar filtros Documentos resuelve2enlaces,4tamaños (CRITICAL).
- `tests/e2e/public-experience.spec.ts:408`: axe color-contrast,4tamaños (CRITICAL).
- `tests/e2e/public-experience.spec.ts:433`: regiones Actualidad,4tamaños (CRITICAL).
- `tests/e2e/public-experience.spec.ts:514`: detalle Actualidad,4tamaños (CRITICAL).
- `tests/e2e/home.spec.ts:213`: axe color-contrast,tablet/desktop/wide (CRITICAL).

### Tareas incompletas
- Ninguna:10/10completas; gate global bloqueado.

## Siguiente paso autorizado
Abrir corregir-gate-global para investigar y corregir contratos/calidad globales preservando el trabajo ajeno y el flujo pausado detalle-actualidad. Usuario decidió este alcance; awaiting_approval false. Selecciones queda VERIFY failed, completed false y sin archivar hasta gate global aprobado. No crear FIX3 automático. El nuevo cambio debe separar defectos reales de contratos desactualizados mediante pruebas/baseline y mantener gate íntegro (PHPUnit, 80 %, PHPCS, PHPStan y Playwright completo).
