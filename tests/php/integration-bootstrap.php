<?php
/** Bootstrap de integracion contra el WordPress local de Docker. */

$wordpress_root = getenv( 'WP_TESTS_RUNTIME_ROOT' ) ?: '/wordpress';
if ( ! file_exists( $wordpress_root . '/wp-load.php' ) ) {
	throw new RuntimeException( 'No se encontro el runtime WordPress para integracion.' );
}
require_once $wordpress_root . '/wp-load.php';

