<?php
// Comprobaciones focales de VERIFY; fixtures exclusivos y limpieza en finally.
require '/var/www/html/wp-load.php';
$deadline = microtime( true ) + 30;
$checks = 0;
function verify_check( $ok, $name ) {
	global $checks, $deadline;
	if ( microtime( true ) > $deadline ) {
		throw new RuntimeException( 'Timeout focal 30s' );
	}
	if ( ! $ok ) {
		throw new RuntimeException( 'FAIL: ' . $name );
	}
	++$checks;
	echo 'PASS: ' . $name . "\n";
}
$admins = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
$editors = get_users( array( 'role' => 'editor', 'number' => 1 ) );
verify_check( ! empty( $admins ) && ! empty( $editors ), 'Roles administrativos disponibles' );
$admin = (int) $admins[0]->ID;
$editor = (int) $editors[0]->ID;
$attachment = 0;
$post = 0;
$term = 0;
$path = '';
$limit_filter = static function () { return 5 * MB_IN_BYTES; };
try {
	wp_set_current_user( $admin );
	$upload = wp_upload_bits( 'labm-verify-' . uniqid() . '.pdf', null, "%PDF-1.7\n%%EOF\n" );
	verify_check( empty( $upload['error'] ), 'Fixture PDF exclusivo creado' );
	$path = $upload['file'];
	$attachment = wp_insert_attachment( array( 'post_title' => 'Fixture VERIFY', 'post_mime_type' => 'application/pdf', 'post_status' => 'inherit', 'post_author' => $admin ), $path, 0, true );
	verify_check( ! is_wp_error( $attachment ), 'Adjunto registrado' );
	add_filter( 'upload_size_limit', $limit_filter );
	verify_check( 5 * MB_IN_BYTES === labm_core_document_admin_effective_max_bytes(), 'WordPress menor que 30 MB determina límite efectivo 5 MB' );
	$handle = fopen( $path, 'c' );
	ftruncate( $handle, 6 * MB_IN_BYTES );
	fclose( $handle );
	clearstatcache( true, $path );
	$result = labm_core_document_admin_validate_pdf( $attachment, $admin );
	verify_check( is_wp_error( $result ) && 'labm_document_pdf_too_large' === $result->get_error_code(), 'PDF real 6 MB rechazado bajo límite 5 MB' );
	file_put_contents( $path, "%PDF-1.7\n%%EOF\n" );
	clearstatcache( true, $path );
	remove_filter( 'upload_size_limit', $limit_filter );
	$post = labm_core_document_admin_save_state( 0, array( 'post_title' => 'Original VERIFY', 'labm_documento_pdf_id' => $attachment ), $admin, 'rest' );
	verify_check( is_int( $post ), 'Documento exclusivo creado' );
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_POST = array( 'action' => 'editpost', '_labm_document_nonce' => 'invalido', 'labm_documento_pdf_id' => $attachment );
	$data = array( 'ID' => $post, 'post_type' => 'labm_documento', 'post_title' => 'No persistir VERIFY' );
	$error = labm_core_document_admin_classic_pre_insert( $data, $data );
	verify_check( is_wp_error( $error ) && 'labm_document_nonce_invalid' === $error->get_error_code(), 'Adaptador devuelve error documental específico para nonce inválido' );
	$result = wp_update_post( array( 'ID' => $post, 'post_title' => 'No persistir VERIFY' ), true );
	verify_check( is_wp_error( $result ) && 'empty_content' === $result->get_error_code() && 'Original VERIFY' === get_post_field( 'post_title', $post ), 'Hook soportado bloquea nonce inválido sin mutación' );
	$_POST['_labm_document_nonce'] = wp_create_nonce( 'labm_document_admin_save' );
	$_POST['labm_documento_fecha'] = '2026-09-17';
	$result = wp_update_post( array( 'ID' => $post, 'post_title' => 'Clásico VERIFY' ), true );
	verify_check( $post === $result && '2026-09-17' === get_post_meta( $post, 'labm_documento_fecha', true ), 'Hook clásico real persiste título y fecha con nonce válido' );
	$_POST = array();
	$_SERVER['REQUEST_METHOD'] = 'GET';
	$taxonomy = get_taxonomy( 'labm_documento_categoria' );
	$base = '/wp/v2/' . ( $taxonomy->rest_base ?: $taxonomy->name );
	$create = new WP_REST_Request( 'POST', $base );
	$create->set_param( 'name', 'VERIFY ' . uniqid() );
	$response = rest_do_request( $create );
	verify_check( 201 === $response->get_status(), 'Administrador crea tipo por REST' );
	$term = (int) $response->get_data()['id'];
	$rename = new WP_REST_Request( 'POST', $base . '/' . $term );
	$rename->set_param( 'name', 'VERIFY renombrado ' . uniqid() );
	verify_check( 200 === rest_do_request( $rename )->get_status(), 'Administrador renombra tipo por REST' );
	wp_set_current_user( $editor );
	$deny_create = new WP_REST_Request( 'POST', $base );
	$deny_create->set_param( 'name', 'No crear VERIFY' );
	verify_check( 403 === rest_do_request( $deny_create )->get_status(), 'Editor no crea tipo por REST' );
	verify_check( 403 === rest_do_request( $rename )->get_status(), 'Editor no renombra tipo por REST' );
	$delete = new WP_REST_Request( 'DELETE', $base . '/' . $term );
	$delete->set_param( 'force', true );
	verify_check( 403 === rest_do_request( $delete )->get_status(), 'Editor no retira tipo por REST' );
	wp_set_current_user( $admin );
	verify_check( 200 === rest_do_request( $delete )->get_status(), 'Administrador retira tipo por REST' );
	$term = 0;
	echo 'OK: ' . $checks . " comprobaciones focales\n";
} finally {
	$_POST = array();
	$_SERVER['REQUEST_METHOD'] = 'GET';
	remove_filter( 'upload_size_limit', $limit_filter );
	wp_set_current_user( $admin );
	if ( $term ) { wp_delete_term( $term, 'labm_documento_categoria' ); }
	if ( $post && ! is_wp_error( $post ) ) { wp_delete_post( $post, true ); }
	if ( $attachment && ! is_wp_error( $attachment ) ) { wp_delete_attachment( $attachment, true ); }
	if ( $path && is_file( $path ) ) { unlink( $path ); }
	echo "CLEANUP: fixtures exclusivos retirados\n";
}
