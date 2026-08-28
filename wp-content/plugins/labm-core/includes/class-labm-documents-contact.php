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
 * @param array $filters Filtros de texto, categoria y ano.
 * @param int   $page Pagina solicitada.
 * @param int   $per_page Elementos por pagina.
 * @return WP_Query
 */
function labm_core_document_catalog_query( $filters = array(), $page = 1, $per_page = 10 ) {
	$args = array(
		'post_type'      => 'labm_documento',
		'post_status'    => 'publish',
		'paged'          => max( 1, absint( $page ) ),
		'posts_per_page' => max( 1, absint( $per_page ) ),
		'meta_key'       => 'labm_documento_fecha', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Orden funcional del catalogo.
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
	);

	if ( ! empty( $filters['texto'] ) ) {
		$args['s'] = sanitize_text_field( $filters['texto'] );
	}
	if ( ! empty( $filters['categoria'] ) ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Filtro solicitado del catalogo.
			array(
				'taxonomy' => 'labm_documento_categoria',
				'field'    => 'term_id',
				'terms'    => array( absint( $filters['categoria'] ) ),
			),
		);
	}
	if ( ! empty( $filters['anio'] ) ) {
		$year               = absint( $filters['anio'] );
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Filtro anual solicitado.
			array(
				'key'     => 'labm_documento_fecha',
				'value'   => array( sprintf( '%04d-01-01', $year ), sprintf( '%04d-12-31', $year ) ),
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			),
		);
	}

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
 * Conserva filtros al construir una pagina del catalogo.
 *
 * @param int   $page Pagina.
 * @param array $filters Filtros.
 * @return string
 */
function labm_core_document_page_url( $page, $filters ) {
	$args = array( 'pagina' => max( 1, absint( $page ) ) );
	foreach ( array( 'texto', 'categoria', 'anio' ) as $key ) {
		if ( isset( $filters[ $key ] ) && '' !== (string) $filters[ $key ] ) {
			$args[ $key ] = sanitize_text_field( (string) $filters[ $key ] );
		}
	}
	return add_query_arg( $args, get_post_type_archive_link( 'labm_documento' ) );
}

/**
 * Renderiza resultados documentales o un estado vacio accionable.
 *
 * @param array $filters Filtros.
 * @param int   $page Pagina.
 * @param int   $per_page Tamano de pagina.
 * @return string
 */
function labm_core_render_document_catalog( $filters = array(), $page = 1, $per_page = 10 ) {
	$query = labm_core_document_catalog_query( $filters, $page, $per_page );
	if ( ! $query->have_posts() ) {
		return '<div class="labm-empty"><p>' . esc_html__( 'No hay documentos para estos filtros.', 'labm-core' ) . '</p><a href="' . esc_url( get_post_type_archive_link( 'labm_documento' ) ) . '">' . esc_html__( 'Limpiar filtros', 'labm-core' ) . '</a></div>';
	}
	$html = '<div class="labm-document-catalog">';
	foreach ( $query->posts as $post ) {
		$url   = labm_core_document_pdf_url( $post->ID );
		$html .= '<article><h2>' . esc_html( get_the_title( $post ) ) . '</h2>';
		if ( $url ) {
			$html .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Ver PDF', 'labm-core' ) . '</a> <a href="' . esc_url( $url ) . '" download>' . esc_html__( 'Descargar', 'labm-core' ) . '</a>';
		}
		$html .= '</article>';
	}
	if ( $query->max_num_pages > 1 ) {
		$html .= '<a href="' . esc_url( labm_core_document_page_url( $page + 1, $filters ) ) . '">' . esc_html__( 'PÃ¡gina siguiente', 'labm-core' ) . '</a>';
	}
	return $html . '</div>';
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
