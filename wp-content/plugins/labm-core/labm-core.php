<?php
/**
 * Plugin Name: LABM Core
 * Description: Funcionalidad persistente de LABM, independiente del tema.
 * Version: 0.1.0
 * Requires at least: 6.8
 * Requires PHP: 8.3
 * Text Domain: labm-core
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve un resumen seguro para temas compatibles.
 *
 * @return string
 */
function labm_core_summary() {
	return '<p>' . esc_html__( 'Contenido administrado por LABM Core.', 'labm-core' ) . '</p>';
}

require_once __DIR__ . '/includes/class-labm-domain.php';
require_once __DIR__ . '/includes/class-labm-home-content.php';
require_once __DIR__ . '/includes/class-labm-documents-contact.php';
register_activation_hook( __FILE__, 'labm_core_activate' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/includes/class-labm-fixtures-command.php';
	WP_CLI::add_command( 'labm fixtures', 'LABM_Fixtures_Command' );
}
