<?php
/**
 * Documentos y contacto seguro de LABM.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wp_tempnam' ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
}

/**
 * Valida que un archivo sea un PDF real y no exceda el limite indicado.
 *
 * @param string $path Ruta local del archivo.
 * @param int    $max_bytes Tamano maximo permitido.
 * @return true|WP_Error
 */
function labm_core_validate_pdf_file( $path, $max_bytes ) {
	if ( ! is_readable( $path ) || filesize( $path ) > $max_bytes ) {
		return new WP_Error( 'labm_pdf_size', __( 'El PDF no existe o supera el tamaño permitido.', 'labm-core' ) );
	}

	// Se leen solo cinco bytes de un archivo local temporal; WP_Filesystem no aporta transporte aqui.
	$handle = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	$header = $handle ? fread( $handle, 5 ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
	if ( $handle ) {
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	}
	if ( '%PDF-' !== $header ) {
		return new WP_Error( 'labm_pdf_type', __( 'El archivo debe ser un PDF válido.', 'labm-core' ) );
	}

	return true;
}

/**
 * Construye la consulta publica combinada del catalogo documental.
 *
 * @param array $filters Parámetro heredado, ignorado por el catálogo simple.
 * @param int   $page Pagina solicitada.
 * @param int   $per_page Elementos por pagina.
 * @return WP_Query
 */
function labm_core_document_catalog_query( $filters = array(), $page = 1, $per_page = 10 ) {
	$args = array(
		'post_type'      => 'labm_documento',
		'post_status'    => 'publish',
		'paged'          => max( 1, absint( $page ) ),
		'posts_per_page' => 10,
		'meta_key'       => 'labm_documento_pdf_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Solo documentos con PDF asociado.
		'meta_compare'   => 'EXISTS',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	return new WP_Query( $args );
}

/**
 * Devuelve la URL publica del PDF solo cuando el adjunto es valido.
 *
 * @param int $post_id ID del documento.
 * @return string URL publica segura o cadena vacia.
 */
function labm_core_document_pdf_url( $post_id ) {
	$attachment_id = absint( get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
	if ( ! $attachment_id || 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
		return '';
	}
	$url = wp_get_attachment_url( $attachment_id );
	return $url ? esc_url_raw( $url ) : '';
}

/**
 * Construye una URL de descarga segura para un documento público.
 *
 * @param int $post_id ID del documento.
 * @return string URL de descarga o cadena vacía.
 */
function labm_core_document_download_url( $post_id ) {
	if ( ! labm_core_document_pdf_url( $post_id ) ) {
		return '';
	}
	return add_query_arg(
		array(
			'action'   => 'labm_document_download',
			'document' => absint( $post_id ),
		),
		admin_url( 'admin-post.php' )
	);
}

/** Entrega un PDF público con Content-Disposition cuando el navegador lo necesita. */
function labm_core_download_document() {
	$post_id       = isset( $_GET['document'] ) ? absint( wp_unslash( $_GET['document'] ) ) : 0;
	$attachment_id = absint( get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
	$post          = get_post( $post_id );
	$path          = $attachment_id ? get_attached_file( $attachment_id ) : '';
	if ( ! $post || 'labm_documento' !== $post->post_type || 'publish' !== $post->post_status || ! labm_core_document_pdf_url( $post_id ) || ! is_readable( $path ) ) {
		wp_die( esc_html__( 'Documento no disponible.', 'labm-core' ), 404 );
	}
	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( basename( $path ) ) . '"' );
	header( 'X-Content-Type-Options: nosniff' );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	exit;
}
add_action( 'admin_post_labm_document_download', 'labm_core_download_document' );
add_action( 'admin_post_nopriv_labm_document_download', 'labm_core_download_document' );

/**
 * Conserva filtros al construir una pagina del catalogo.
 *
 * @param int   $page Pagina.
 * @param array $filters Filtros.
 * @return string
 */
function labm_core_document_page_url( $page, $filters = array() ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Compatibilidad de firma.
	$args        = array( 'pagina' => max( 1, absint( $page ) ) );
	$page_object = get_page_by_path( 'documentos' );
	return add_query_arg( $args, $page_object ? get_permalink( $page_object ) : get_post_type_archive_link( 'labm_documento' ) );
}

/**
 * Obtiene la página solicitada del catálogo desde la consulta pública actual.
 *
 * @return int
 */
function labm_core_document_catalog_current_page() {
	return isset( $_GET['pagina'] ) ? max( 1, absint( wp_unslash( $_GET['pagina'] ) ) ) : 1;
}

/**
 * Renderiza resultados documentales o un estado vacio accionable.
 *
 * @param array $filters Parámetro heredado, ignorado por el catálogo simple.
 * @param int   $page Pagina.
 * @param int   $per_page Tamano de pagina.
 * @return string
 */
function labm_core_render_document_catalog( $filters = array(), $page = 1, $per_page = 10 ) {
	$page  = max( 1, absint( $page ) );
	$query = labm_core_document_catalog_query( array(), $page, 10 );
	if ( ! $query->have_posts() ) {
		return '<section class="labm-documents-empty" aria-labelledby="labm-documents-empty-title"><span aria-hidden="true">⌕</span><h2 id="labm-documents-empty-title">' . esc_html__( 'Estado vacío de referencia', 'labm-core' ) . '</h2><p>' . esc_html__( 'No encontramos documentos disponibles por el momento.', 'labm-core' ) . '</p></section>';
	}
	$html = '<section class="labm-documents-catalog" aria-label="' . esc_attr__( 'Documentos publicados', 'labm-core' ) . '"><div class="labm-documents-catalog__list">';
	foreach ( $query->posts as $post ) {
		$url   = labm_core_document_pdf_url( $post->ID );
		$html .= '<article class="labm-documents-catalog__item"><h2>' . esc_html( get_the_title( $post ) ) . '</h2>';
		if ( $url ) {
			$html .= '<p class="labm-documents-catalog__actions"><a class="labm-documents-catalog__view" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Ver PDF', 'labm-core' ) . '</a><a class="labm-documents-catalog__download" href="' . esc_url( labm_core_document_download_url( $post->ID ) ) . '" download>' . esc_html__( 'Descargar', 'labm-core' ) . '</a></p>';
		}
		$html .= '</article>';
	}
	$html .= '</div>';
	if ( $query->max_num_pages > 1 ) {
		$html .= '<nav class="labm-documents-pagination" aria-label="' . esc_attr__( 'Paginación de documentos', 'labm-core' ) . '"><ul>';
		if ( $page > 1 ) {
			$html .= '<li><a href="' . esc_url( labm_core_document_page_url( $page - 1 ) ) . '" aria-label="' . esc_attr__( 'Página anterior', 'labm-core' ) . '">←</a></li>';
		}
		for ( $number = 1; $number <= (int) $query->max_num_pages; $number++ ) {
			$html .= $number === $page ? '<li><span aria-current="page">' . esc_html( (string) $number ) . '</span></li>' : '<li><a href="' . esc_url( labm_core_document_page_url( $number ) ) . '">' . esc_html( (string) $number ) . '</a></li>';
		}
		if ( $page < (int) $query->max_num_pages ) {
			$html .= '<li><a href="' . esc_url( labm_core_document_page_url( $page + 1 ) ) . '" aria-label="' . esc_attr__( 'Página siguiente', 'labm-core' ) . '">→</a></li>';
		}
		$html .= '</ul></nav>';
	}
	return $html . '</section>';
}

/**
 * Describe asociaciones accesibles de errores y el primer foco esperado.
 *
 * @param array $errors Errores por campo.
 * @return array
 */
function labm_core_contact_error_attributes( $errors ) {
	$fields = array();
	foreach ( $errors as $field => $message ) {
		$fields[ $field ] = array(
			'aria-invalid'     => 'true',
			'aria-describedby' => 'labm-error-' . sanitize_key( $field ),
			'message'          => sanitize_text_field( $message ),
		);
	}
	return array(
		'focus'  => $fields ? (string) array_key_first( $fields ) : '',
		'fields' => $fields,
	);
}

/**
 * Elimina un adjunto exclusivo cuando la politica lo autoriza.
 *
 * @param int  $post_id ID del documento.
 * @param bool $delete_file Si la politica permite borrado fisico.
 * @return bool|WP_Error False cuando se conserva el adjunto.
 */
function labm_core_delete_document_attachment( $post_id, $delete_file ) {
	if ( ! current_user_can( 'delete_post', $post_id ) ) {
		return new WP_Error( 'labm_document_forbidden', __( 'No tienes permiso para eliminar este documento.', 'labm-core' ) );
	}
	$attachment_id = absint( get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
	if ( ! $delete_file || ! $attachment_id ) {
		return false;
	}
	$references = get_posts(
		array(
			'post_type'      => 'labm_documento',
			'post_status'    => 'any',
			'posts_per_page' => 2,
			'fields'         => 'ids',
			'post__not_in'   => array( absint( $post_id ) ),
			'meta_key'       => 'labm_documento_pdf_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Detecta referencias compartidas.
			'meta_value'     => (string) $attachment_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Detecta referencias compartidas.
		)
	);
	if ( $references ) {
		return false;
	}
	return (bool) wp_delete_attachment( $attachment_id, true );
}

/**
 * Procesa contacto sin persistir datos personales.
 *
 * @param array $data Datos enviados por el formulario.
 * @return array Resultado seguro del procesamiento.
 */
function labm_core_process_contact( $data ) {
	$errors = array();
	$nonce  = isset( $data['nonce'] ) ? sanitize_text_field( $data['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'labm_contacto' ) ) {
		$errors['nonce'] = __( 'La solicitud caducó. Recarga la página.', 'labm-core' );
	}
	if ( ! empty( $data['sitio_web'] ) ) {
		$errors['antispam'] = __( 'No fue posible procesar el formulario.', 'labm-core' );
	}
	foreach ( array( 'nombre', 'apellidos', 'asunto', 'mensaje' ) as $field ) {
		if ( empty( trim( (string) ( $data[ $field ] ?? '' ) ) ) ) {
			$errors[ $field ] = __( 'Este campo es obligatorio.', 'labm-core' );
		}
	}
	$email = sanitize_email( $data['correo'] ?? '' );
	if ( ! is_email( $email ) ) {
		$errors['correo'] = __( 'Escribe un correo electrónico válido.', 'labm-core' );
	}
	if ( $errors ) {
		return array(
			'ok'     => false,
			'errors' => $errors,
		);
	}

	$token     = sanitize_key( $data['token'] ?? '' );
	$token_key = 'labm_contact_' . hash( 'sha256', $token );
	if ( $token && get_transient( $token_key ) ) {
		return array(
			'ok'     => true,
			'errors' => array(),
		);
	}
	$subject = sanitize_text_field( $data['asunto'] );
	$message = sanitize_textarea_field( $data['mensaje'] );
	$sent    = wp_mail( get_option( 'admin_email' ), $subject, $message, array( 'Reply-To: ' . $email ) );
	if ( ! $sent ) {
		do_action( 'labm_core_contact_delivery_failed', array( 'code' => 'mail_delivery_failed' ) );
		return array(
			'ok'     => false,
			'errors' => array( 'delivery' => __( 'No pudimos enviar el mensaje. Inténtalo de nuevo.', 'labm-core' ) ),
		);
	}
	if ( $token ) {
		set_transient( $token_key, 1, HOUR_IN_SECONDS );
	}
	return array(
		'ok'     => true,
		'errors' => array(),
	);
}
