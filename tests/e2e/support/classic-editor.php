<?php
/**
 * Fuerza el fallback clásico exclusivamente para el recorrido E2E autenticado.
 *
 * @package LABM_E2E
 */

add_filter(
	'use_block_editor_for_post_type',
	static function ( $use_block_editor, $post_type ) {
		if ( 'labm_documento' !== $post_type || ! is_admin() || ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
			return $use_block_editor;
		}
		$e2e_fallback = isset( $_GET['labm-e2e-classic'] ) ? sanitize_key( wp_unslash( $_GET['labm-e2e-classic'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Alterna solo el render del editor en infraestructura E2E.
		return '1' === $e2e_fallback ? false : $use_block_editor;
	},
	PHP_INT_MAX,
	2
);
