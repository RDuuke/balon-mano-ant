# Progreso de aplicación

## Tarea 1.1 — Contrato editorial

- **RED:** test `DocumentContactTest::test_document_publication_requires_only_title_and_pdf` falla porque se exigía `labm_documento_fecha`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php`; test pasa.

## Tarea 1.2 — Consulta simplificada

- **RED:** test `DocumentContactTest::test_document_catalog_is_simple_paginated_and_omits_invalid_attachments` falla porque la consulta dependía de fecha y devolvía cero resultados.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; test pasa.

## Tarea 1.3 — Acciones PDF

- **RED:** test `DocumentContactTest::test_document_actions_use_a_safe_new_tab_and_download_endpoint` falla porque el render vacío no tenía enlaces seguros.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; test pasa.

## Tarea 1.4 — Composición aislada

- **RED:** test `PublicExperienceTest::test_documents_pattern_composes_the_simple_pdf_catalog_without_filters` falla porque el patrón no renderizaba el catálogo.
- **GREEN:** implementación en `wp-content/themes/labm/patterns/documentos.php` y `wp-content/themes/labm/style.css`; test pasa.

## Tarea 5.3 — Higiene

- **GREEN:** `php -l`, comprobación LF sobre los archivos modificados y `git diff --check` pasan.

## Tarea 4.3 — Corrección de paginación y reconciliación

- **RED:** `DocumentContactTest::test_legal_document_reconciliation_trashes_only_stale_imported_documents` falló con `Call to undefined method LABM_Fixtures_Command::reconcile_legal_documents()`; la página no se leía desde la consulta pública.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`, `wp-content/themes/labm/patterns/documentos.php` y `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`; `test_document_catalog_pagination_uses_the_current_documentos_page` y `test_legal_document_reconciliation_trashes_only_stale_imported_documents` pasan (2 pruebas, 6 aserciones).
- **REFACTOR:** la reconciliación acepta un prefijo explícito para aislar la prueba; en producción conserva el prefijo `legal-document:`.
