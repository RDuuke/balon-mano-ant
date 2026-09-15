# Informe de verificación

## Matriz de validación

| Dominio | Escenario | Estado | Evidencia | Severidad |
|---|---|---|---|---|
| Editorial | Título y PDF obligatorios | ✅ COMPLIANT | `DocumentContactTest::test_document_publication_requires_only_title_and_pdf` | — |
| Catálogo | Diez elementos, orden y página normalizada | ✅ COMPLIANT | `DocumentContactTest::test_document_catalog_is_simple_paginated_and_omits_invalid_attachments` | — |
| Catálogo | Enlace de paginación renderiza la página solicitada | ✅ COMPLIANT | `DocumentContactTest::test_document_catalog_pagination_uses_the_current_documentos_page` (2 pruebas focales, 6 aserciones) | — |
| Acciones | Nueva pestaña segura y descarga | ✅ COMPLIANT | `DocumentContactTest::test_document_actions_use_a_safe_new_tab_and_download_endpoint` | — |
| Integración | Patrón sin buscador/filtros | ✅ COMPLIANT | `PublicExperienceTest::test_documents_pattern_composes_the_simple_pdf_catalog_without_filters` | — |
| Importación | Primera carga y repetición | ⚠️ PARTIAL | Dos ejecuciones WP-CLI informaron 11 actualizados; conteo interno: 11 | WARNING |
| Importación | Registro marcado sin fuente actual | ✅ COMPLIANT | `DocumentContactTest::test_legal_document_reconciliation_trashes_only_stale_imported_documents` | — |
| Importación | Archivo faltante o inválido | ❌ UNTESTED | No hay prueba automática específica | CRITICAL |
| Navegador | Teclado, pestaña nueva y WCAG | ❌ UNTESTED | La ejecución focal de PHPUnit posterior quedó agotada; no se inició Playwright | CRITICAL |

## Ejecución

- PHPUnit focal anterior: `9 tests, 55 assertions`, correcto.
- Importador: `wp labm fixtures legal_documents --source=/app/docs/legal-documents` ejecutado dos veces; ambas devolvieron 11 documentos actualizados. El conteo dentro de WordPress devolvió `11`.
- Corrección posterior: la importación real devolvió `11` documentos actualizados y `0` enviados a papelera. El intento de auditoría SQL final se interrumpió por bloqueo de Docker; no se certifica el conjunto desde esa consulta.
- PHPStan focal detectó dos errores de tipo en la paginación, corregidos antes de este informe. No se pudo repetir porque la ejecución focal posterior agotó el tiempo de Docker.
- PHPCS focal de producción quedó con avisos y un error de formato; el error fue corregido. No se repitió tras la corrección por el mismo límite de tiempo.

## Higiene

La comprobación de sintaxis PHP, finales LF y `git diff --check` se ejecutó antes de la última corrección puntual; debe repetirse en APPLY antes del cierre.

Tras la corrección puntual se repitieron los finales LF y `git diff --check` sobre los archivos modificados; ambos resultados fueron correctos. La sintaxis y el análisis estático siguen pendientes por el bloqueo de Docker.

## Fallos Detectados

### Tests fallidos
- `Playwright Documentos`: no ejecutado; falta evidencia de teclado, nueva pestaña y WCAG.

### Errores de build
- `PHPStan/PHPCS`: se corrigieron hallazgos, pero falta repetición concluyente.

### Tareas incompletas
- 4.1 / 4.2 / 4.3: falta prueba automática RED/GREEN del importador y error parcial.
- 5.1 / 5.2: falta validación estática final y navegador.
