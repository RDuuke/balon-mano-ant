<?php
/**
 * Title: Documentos LABM
 * Slug: labm/documentos
 * Categories: text
 * Inserter: no
 *
 * @package LABM
 */

echo labm_theme_render_documents_banner(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper de renderizado seguro.
echo labm_core_render_document_catalog( array(), labm_core_document_catalog_current_page() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper de renderizado seguro.
