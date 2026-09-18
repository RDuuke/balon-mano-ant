<?php
/**
 * Contrato administrativo y validación autoritativa de Documentos.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Devuelve el menor límite entre 30 MB y la configuración de WordPress. */
function labm_core_document_admin_effective_max_bytes() {
	return min( 30 * MB_IN_BYTES, wp_max_upload_size() );
}

/**
 * Construye un error administrativo asociado a un campo.
 *
 * @param string $code Código estable.
 * @param string $field Campo que debe corregirse.
 * @param string $message Mensaje accionable.
 * @param int    $status Estado HTTP.
 * @return WP_Error
 */
function labm_core_document_admin_error( $code, $field, $message, $status = 400 ) {
	return new WP_Error(
		$code,
		$message,
		array(
			'field'  => $field,
			'status' => $status,
		)
	);
}

/**
 * Normaliza una lista de términos sin aceptar selecciones múltiples.
 *
 * @param mixed $value Valor recibido.
 * @return int[]|WP_Error
 */
function labm_core_document_admin_normalize_terms( $value ) {
	if ( null === $value || '' === $value ) {
		return array();
	}
	$terms = is_array( $value ) ? $value : array( $value );
	$terms = array_values(
		array_filter(
			array_map( 'absint', $terms )
		)
	);
	if ( count( $terms ) > 1 ) {
		return labm_core_document_admin_error(
			'labm_document_multiple_types',
			'labm_documento_categoria',
			__( 'Selecciona un solo tipo de documento.', 'labm-core' )
		);
	}
	return $terms;
}

/**
 * Construye el estado efectivo combinando valores persistidos y enviados.
 *
 * @param int   $post_id Documento existente o cero.
 * @param array $input Valores propuestos.
 * @return array|WP_Error
 */
function labm_core_document_admin_effective_state( $post_id, $input ) {
	$post = $post_id ? get_post( $post_id ) : null;
	if ( $post_id && ( ! $post || 'labm_documento' !== $post->post_type ) ) {
		return labm_core_document_admin_error( 'labm_document_not_found', 'post_id', __( 'El documento solicitado no existe.', 'labm-core' ), 404 );
	}

	$title = array_key_exists( 'post_title', $input ) ? sanitize_text_field( trim( (string) $input['post_title'] ) ) : ( $post ? $post->post_title : '' );
	$pdf   = array_key_exists( 'labm_documento_pdf_id', $input ) ? absint( $input['labm_documento_pdf_id'] ) : absint( $post_id ? get_post_meta( $post_id, 'labm_documento_pdf_id', true ) : 0 );
	$date  = array_key_exists( 'labm_documento_fecha', $input ) ? trim( (string) $input['labm_documento_fecha'] ) : (string) ( $post_id ? get_post_meta( $post_id, 'labm_documento_fecha', true ) : '' );
	$terms = array_key_exists( 'labm_documento_categoria', $input ) ? labm_core_document_admin_normalize_terms( $input['labm_documento_categoria'] ) : ( $post_id ? wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'ids' ) ) : array() );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	return array(
		'post_id'                   => absint( $post_id ),
		'post_title'                => $title,
		'labm_documento_pdf_id'     => $pdf,
		'labm_documento_fecha'      => $date,
		'labm_documento_categoria'  => array_map( 'absint', $terms ),
		'type_explicitly_submitted' => array_key_exists( 'labm_documento_categoria', $input ),
	);
}

/**
 * Verifica acceso y contenido real de un adjunto PDF.
 *
 * @param int $attachment_id ID del adjunto.
 * @param int $user_id Usuario solicitante.
 * @return true|WP_Error
 */
function labm_core_document_admin_validate_pdf( $attachment_id, $user_id ) {
	$attachment = get_post( $attachment_id );
	if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
		return labm_core_document_admin_error( 'labm_document_pdf_missing', 'labm_documento_pdf_id', __( 'Selecciona un PDF disponible en la biblioteca.', 'labm-core' ) );
	}
	if ( ! user_can( $user_id, 'edit_post', $attachment_id ) ) {
		return labm_core_document_admin_error( 'labm_document_pdf_forbidden', 'labm_documento_pdf_id', __( 'No puedes usar el archivo seleccionado. Elige otro PDF.', 'labm-core' ), 403 );
	}
	if ( 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
		return labm_core_document_admin_error( 'labm_document_pdf_mime', 'labm_documento_pdf_id', __( 'El archivo seleccionado no está declarado como PDF.', 'labm-core' ) );
	}

	$file = get_attached_file( $attachment_id );
	if ( ! is_string( $file ) || ! is_file( $file ) || ! is_readable( $file ) ) {
		return labm_core_document_admin_error( 'labm_document_pdf_unreadable', 'labm_documento_pdf_id', __( 'No se puede leer el PDF. Vuelve a subirlo o elige otro archivo.', 'labm-core' ) );
	}
	if ( filesize( $file ) > labm_core_document_admin_effective_max_bytes() ) {
		return labm_core_document_admin_error(
			'labm_document_pdf_too_large',
			'labm_documento_pdf_id',
			sprintf(
				/* translators: %s: límite efectivo de tamaño del archivo. */
				__( 'El PDF supera el límite de %s. Elige un archivo más pequeño.', 'labm-core' ),
				size_format( labm_core_document_admin_effective_max_bytes() )
			)
		);
	}

	$checked = wp_check_filetype_and_ext( $file, basename( $file ), array( 'pdf' => 'application/pdf' ) );
	if ( 'pdf' !== $checked['ext'] || 'application/pdf' !== $checked['type'] ) {
		return labm_core_document_admin_error( 'labm_document_pdf_extension', 'labm_documento_pdf_id', __( 'El archivo no tiene una extensión y un tipo PDF válidos.', 'labm-core' ) );
	}

	$handle = fopen( $file, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Lectura local mínima de un adjunto ya validado.
	if ( false === $handle ) {
		return labm_core_document_admin_error( 'labm_document_pdf_unreadable', 'labm_documento_pdf_id', __( 'No se puede leer el PDF. Vuelve a subirlo o elige otro archivo.', 'labm-core' ) );
	}
	$signature = fread( $handle, 5 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread -- Solo se leen los cinco bytes de firma.
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- El recurso local debe cerrarse inmediatamente.
	if ( '%PDF-' !== $signature ) {
		return labm_core_document_admin_error( 'labm_document_pdf_signature', 'labm_documento_pdf_id', __( 'El contenido del archivo no corresponde a un PDF válido.', 'labm-core' ) );
	}
	return true;
}

/**
 * Valida el estado efectivo completo sin mutarlo.
 *
 * @param int   $post_id Documento existente o cero.
 * @param array $input Valores propuestos.
 * @param int   $user_id Usuario solicitante.
 * @return true|WP_Error
 */
function labm_core_document_admin_validate_state( $post_id, $input, $user_id ) {
	if ( $post_id ) {
		if ( ! user_can( $user_id, 'edit_post', $post_id ) ) {
			return labm_core_document_admin_error( 'labm_document_forbidden', 'post_id', __( 'No tienes permisos para editar este documento.', 'labm-core' ), 403 );
		}
		// La capacidad personalizada se registra en labm_core_ensure_capabilities().
	} elseif ( ! user_can( $user_id, 'edit_labm_documentos' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
		return labm_core_document_admin_error( 'labm_document_forbidden', 'post_id', __( 'No tienes permisos para crear documentos.', 'labm-core' ), 403 );
	}

	$state = labm_core_document_admin_effective_state( $post_id, $input );
	if ( is_wp_error( $state ) ) {
		return $state;
	}
	if ( '' === $state['post_title'] ) {
		return labm_core_document_admin_error( 'labm_document_title_required', 'post_title', __( 'Escribe el título del documento.', 'labm-core' ) );
	}
	if ( '' !== $state['labm_documento_fecha'] && ! labm_core_validate_iso_date( $state['labm_documento_fecha'] ) ) {
		return labm_core_document_admin_error( 'labm_document_date_invalid', 'labm_documento_fecha', __( 'Selecciona una fecha calendario válida.', 'labm-core' ) );
	}
	if ( count( $state['labm_documento_categoria'] ) > 1 ) {
		return labm_core_document_admin_error( 'labm_document_multiple_types', 'labm_documento_categoria', __( 'Selecciona un solo tipo de documento.', 'labm-core' ) );
	}
	if ( $state['labm_documento_categoria'] ) {
		$term_id = reset( $state['labm_documento_categoria'] );
		$term    = get_term( $term_id, 'labm_documento_categoria' );
		if ( ! $term || is_wp_error( $term ) ) {
			return labm_core_document_admin_error( 'labm_document_type_invalid', 'labm_documento_categoria', __( 'Selecciona un tipo de documento disponible.', 'labm-core' ) );
		}
		if ( ! user_can( $user_id, 'assign_labm_documento_types' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Registrada por el dominio.
			return labm_core_document_admin_error( 'labm_document_type_forbidden', 'labm_documento_categoria', __( 'No tienes permisos para asignar tipos de documento.', 'labm-core' ), 403 );
		}
	}

	return labm_core_document_admin_validate_pdf( $state['labm_documento_pdf_id'], $user_id );
}

/**
 * Devuelve el tipo persistido o el fallback administrativo sin mutar.
 *
 * @param int $post_id Documento consultado.
 * @return array
 */
function labm_core_document_admin_type_state( $post_id ) {
	$terms = wp_get_object_terms( $post_id, 'labm_documento_categoria' );
	if ( ! is_wp_error( $terms ) && $terms ) {
		return array(
			'id'       => (int) $terms[0]->term_id,
			'slug'     => $terms[0]->slug,
			'name'     => $terms[0]->name,
			'fallback' => false,
		);
	}

	labm_core_document_admin_seed_types();
	$general = get_term_by( 'slug', 'documento-general', 'labm_documento_categoria' );
	return array(
		'id'       => $general ? (int) $general->term_id : 0,
		'slug'     => 'documento-general',
		'name'     => $general ? $general->name : __( 'Documento general', 'labm-core' ),
		'fallback' => true,
	);
}

/**
 * Persiste metadatos y tipo previamente validados.
 *
 * @param int   $post_id Documento objetivo.
 * @param array $state Estado validado.
 * @return true|WP_Error
 */
function labm_core_document_admin_persist_state( $post_id, $state ) {
	update_post_meta( $post_id, 'labm_documento_pdf_id', absint( $state['labm_documento_pdf_id'] ) );
	if ( '' === $state['labm_documento_fecha'] ) {
		delete_post_meta( $post_id, 'labm_documento_fecha' );
	} else {
		update_post_meta( $post_id, 'labm_documento_fecha', $state['labm_documento_fecha'] );
	}

	$term_ids = $state['labm_documento_categoria'];
	if ( ! $term_ids ) {
		labm_core_document_admin_seed_types();
		$general  = get_term_by( 'slug', 'documento-general', 'labm_documento_categoria' );
		$term_ids = $general ? array( (int) $general->term_id ) : array();
	}
	$result = wp_set_object_terms( $post_id, array_map( 'intval', $term_ids ), 'labm_documento_categoria', false );
	return is_wp_error( $result ) ? $result : true;
}

/**
 * Guarda un estado validado para pruebas y adaptadores REST/clásico.
 *
 * @param int    $post_id Documento existente o cero.
 * @param array  $input Valores propuestos.
 * @param int    $user_id Usuario solicitante.
 * @param string $channel Canal rest o classic.
 * @return int|WP_Error
 */
function labm_core_document_admin_save_state( $post_id, $input, $user_id, $channel ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$validation = labm_core_document_admin_validate_state( $post_id, $input, $user_id );
	if ( is_wp_error( $validation ) ) {
		return $validation;
	}
	$state = labm_core_document_admin_effective_state( $post_id, $input );
	if ( is_wp_error( $state ) ) {
		return $state;
	}

	$post_data = array(
		'post_type'  => 'labm_documento',
		'post_title' => $state['post_title'],
	);
	if ( $post_id ) {
		$post_data['ID'] = $post_id;
	} else {
		$post_data['post_status'] = 'draft';
		$post_data['post_author'] = $user_id;
	}
	$saved_id = wp_insert_post( $post_data, true );
	if ( is_wp_error( $saved_id ) ) {
		return $saved_id;
	}
	$persisted = labm_core_document_admin_persist_state( $saved_id, $state );
	return is_wp_error( $persisted ) ? $persisted : $saved_id;
}

/**
 * Extrae el contrato relevante de una solicitud REST.
 *
 * @param WP_REST_Request  $request Solicitud REST.
 * @param WP_Post|stdClass $prepared_post Documento preparado por WordPress.
 * @return array
 */
function labm_core_document_admin_rest_input( WP_REST_Request $request, $prepared_post ) {
	$input = array();
	if ( $request->has_param( 'title' ) ) {
		$input['post_title'] = $prepared_post->post_title ?? '';
	}
	$meta = $request->get_param( 'meta' );
	if ( is_array( $meta ) ) {
		foreach ( array( 'labm_documento_pdf_id', 'labm_documento_fecha' ) as $key ) {
			if ( array_key_exists( $key, $meta ) ) {
				$input[ $key ] = $meta[ $key ];
			}
		}
	}
	if ( $request->has_param( 'labm_documento_categoria' ) ) {
		$input['labm_documento_categoria'] = $request->get_param( 'labm_documento_categoria' );
	}
	return $input;
}

/**
 * Bloquea una escritura REST inválida antes de insertar el post.
 *
 * @param WP_Post|WP_Error $prepared_post Documento preparado o error previo.
 * @param WP_REST_Request  $request Solicitud REST.
 * @return WP_Post|WP_Error
 */
function labm_core_document_admin_rest_pre_insert( $prepared_post, $request ) {
	if ( is_wp_error( $prepared_post ) ) {
		return $prepared_post;
	}
	$post_id = absint( $request->get_param( 'id' ) );
	$result  = labm_core_document_admin_validate_state( $post_id, labm_core_document_admin_rest_input( $request, $prepared_post ), get_current_user_id() );
	return is_wp_error( $result ) ? $result : $prepared_post;
}
add_filter( 'rest_pre_insert_labm_documento', 'labm_core_document_admin_rest_pre_insert', 10, 2 );

/**
 * Asigna el fallback general después de un guardado REST válido.
 *
 * @param WP_Post $post Documento guardado.
 */
function labm_core_document_admin_rest_after_insert( $post ) {
	if ( ! wp_get_object_terms( $post->ID, 'labm_documento_categoria', array( 'fields' => 'ids' ) ) ) {
		$state = labm_core_document_admin_type_state( $post->ID );
		if ( $state['id'] ) {
			wp_set_object_terms( $post->ID, array( $state['id'] ), 'labm_documento_categoria', false );
		}
	}
}
add_action( 'rest_after_insert_labm_documento', 'labm_core_document_admin_rest_after_insert', 10, 1 );

/** Devuelve la clave transitoria de recuperación del usuario actual. */
function labm_core_document_admin_failed_state_key() {
	return 'labm_document_admin_failed_' . get_current_user_id();
}

/**
 * Conserva temporalmente valores clásicos rechazados para poder corregirlos.
 *
 * @param array    $input Valores saneados del formulario.
 * @param WP_Error $error Error autoritativo.
 */
function labm_core_document_admin_store_failed_state( $input, WP_Error $error ) {
	set_transient(
		labm_core_document_admin_failed_state_key(),
		array(
			'input'   => $input,
			'message' => $error->get_error_message(),
			'field'   => $error->get_error_data()['field'] ?? '',
		),
		5 * MINUTE_IN_SECONDS
	);
}

/**
 * Describe un adjunto sin exponer su ruta o URL.
 *
 * @param int $attachment_id ID del adjunto.
 * @return array
 */
function labm_core_document_admin_attachment_state( $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	if ( ! $attachment_id ) {
		return array(
			'id'        => 0,
			'name'      => '',
			'size'      => 0,
			'sizeLabel' => '',
			'valid'     => false,
		);
	}
	$file = get_attached_file( $attachment_id );
	$size = is_string( $file ) && is_file( $file ) ? (int) filesize( $file ) : 0;
	return array(
		'id'        => $attachment_id,
		'name'      => is_string( $file ) && '' !== $file ? wp_basename( $file ) : get_the_title( $attachment_id ),
		'size'      => $size,
		'sizeLabel' => $size ? size_format( $size ) : '',
		'valid'     => true === labm_core_document_admin_validate_pdf( $attachment_id, get_current_user_id() ),
	);
}

/** Construye la configuración compartida por ambos adaptadores administrativos. */
function labm_core_document_admin_config() {
	global $post;
	$post_id = $post instanceof WP_Post ? (int) $post->ID : 0;
	$pdf_id  = $post_id ? absint( get_post_meta( $post_id, 'labm_documento_pdf_id', true ) ) : 0;
	$type    = labm_core_document_admin_type_state( $post_id );
	$terms   = get_terms(
		array(
			'taxonomy'   => 'labm_documento_categoria',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	return array(
		'effectiveMaxBytes' => labm_core_document_admin_effective_max_bytes(),
		'effectiveMaxLabel' => size_format( labm_core_document_admin_effective_max_bytes() ),
		'generalTermId'     => (int) $type['id'],
		'restNonce'         => wp_create_nonce( 'wp_rest' ),
		'postId'            => $post_id,
		'attachment'        => labm_core_document_admin_attachment_state( $pdf_id ),
		'terms'             => array_map(
			static function ( $term ) {
				return array(
					'id'   => (int) $term->term_id,
					'name' => $term->name,
				);
			},
			$terms
		),
		'strings'           => array(
			'panelTitle'       => __( 'Archivo y clasificación', 'labm-core' ),
			'pdfLegend'        => __( 'Archivo PDF', 'labm-core' ),
			'selectPdf'        => __( 'Seleccionar o subir PDF', 'labm-core' ),
			'replacePdf'       => __( 'Reemplazar PDF', 'labm-core' ),
			'removePdf'        => __( 'Quitar PDF', 'labm-core' ),
			'noPdf'            => __( 'Ningún PDF asociado.', 'labm-core' ),
			'keptInLibrary'    => __( 'El archivo permanece en la biblioteca al quitarlo.', 'labm-core' ),
			'validPdf'         => __( 'PDF válido', 'labm-core' ),
			'invalidPdf'       => __( 'El PDF necesita reparación.', 'labm-core' ),
			'documentDate'     => __( 'Fecha del documento', 'labm-core' ),
			'documentType'     => __( 'Tipo de documento', 'labm-core' ),
			'mediaTitle'       => __( 'Seleccionar un PDF', 'labm-core' ),
			'usePdf'           => __( 'Usar este PDF', 'labm-core' ),
			'mediaError'       => __( 'No se pudo completar la operación en la biblioteca. La selección anterior se conservó; intenta de nuevo.', 'labm-core' ),
			'tooLarge'         => __( 'El PDF supera el límite permitido. Elige un archivo más pequeño.', 'labm-core' ),
			'wrongType'        => __( 'Selecciona un archivo PDF.', 'labm-core' ),
			'titleRequired'    => __( 'Escribe el título del documento.', 'labm-core' ),
			'pdfRequired'      => __( 'Selecciona un PDF antes de guardar.', 'labm-core' ),
			'dateInvalid'      => __( 'Selecciona una fecha calendario válida.', 'labm-core' ),
			'limitDescription' => sprintf(
				/* translators: %s: límite efectivo de carga. */
				__( 'Límite efectivo: el menor entre 30 MB y el permitido por WordPress (%s).', 'labm-core' ),
				size_format( labm_core_document_admin_effective_max_bytes() )
			),
		),
	);
}

/** Carga Media Library y el adaptador administrativo solo para Documentos. */
function labm_core_document_admin_enqueue_assets() {
	$screen = get_current_screen();
	if ( ! $screen || 'labm_documento' !== $screen->post_type || ! in_array( $screen->base, array( 'post', 'post-new' ), true ) ) {
		return;
	}

	wp_enqueue_media();
	$script = plugin_dir_path( LABM_CORE_FILE ) . 'assets/js/admin-documento.js';
	wp_enqueue_script(
		'labm-document-admin',
		plugins_url( 'assets/js/admin-documento.js', LABM_CORE_FILE ),
		array( 'wp-api-fetch', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-plugins' ),
		file_exists( $script ) ? (string) filemtime( $script ) : '1',
		true
	);
	wp_localize_script( 'labm-document-admin', 'labmDocumentAdmin', labm_core_document_admin_config() );
	wp_set_script_translations( 'labm-document-admin', 'labm-core' );
}
add_action( 'admin_enqueue_scripts', 'labm_core_document_admin_enqueue_assets' );

/** Registra el metabox cuando WordPress usa el editor clásico. */
function labm_core_document_admin_register_meta_box() {
	if ( use_block_editor_for_post_type( 'labm_documento' ) ) {
		return;
	}
	add_meta_box(
		'labm-document-admin',
		__( 'Archivo y clasificación', 'labm-core' ),
		'labm_core_document_admin_render_meta_box',
		'labm_documento',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_labm_documento', 'labm_core_document_admin_register_meta_box' );

/**
 * Renderiza el adaptador equivalente para el editor clásico.
 *
 * @param WP_Post $post Documento editado.
 */
function labm_core_document_admin_render_meta_box( $post ) {
	$failed = get_transient( labm_core_document_admin_failed_state_key() );
	if ( $failed ) {
		delete_transient( labm_core_document_admin_failed_state_key() );
	}
	$input      = is_array( $failed ) ? ( $failed['input'] ?? array() ) : array();
	$pdf_id     = array_key_exists( 'labm_documento_pdf_id', $input ) ? absint( $input['labm_documento_pdf_id'] ) : absint( get_post_meta( $post->ID, 'labm_documento_pdf_id', true ) );
	$date       = array_key_exists( 'labm_documento_fecha', $input ) ? (string) $input['labm_documento_fecha'] : (string) get_post_meta( $post->ID, 'labm_documento_fecha', true );
	$type_state = labm_core_document_admin_type_state( $post->ID );
	$type_id    = array_key_exists( 'labm_documento_categoria', $input ) ? absint( is_array( $input['labm_documento_categoria'] ) ? reset( $input['labm_documento_categoria'] ) : $input['labm_documento_categoria'] ) : (int) $type_state['id'];
	$attachment = labm_core_document_admin_attachment_state( $pdf_id );
	$terms      = get_terms(
		array(
			'taxonomy'   => 'labm_documento_categoria',
			'hide_empty' => false,
		)
	);
	$limit      = labm_core_document_admin_effective_max_bytes();

	wp_nonce_field( 'labm_document_admin_save', '_labm_document_nonce' );
	?>
	<div data-labm-document-admin data-labm-editor="classic">
		<div data-labm-admin-alert role="alert" tabindex="-1"<?php echo empty( $failed['message'] ) ? ' hidden' : ''; ?>><?php echo esc_html( $failed['message'] ?? '' ); ?></div>
		<fieldset aria-describedby="labm-pdf-help labm-pdf-limit">
			<legend><strong><?php esc_html_e( 'Archivo PDF', 'labm-core' ); ?></strong></legend>
			<input type="hidden" name="labm_documento_pdf_id" value="<?php echo esc_attr( (string) $pdf_id ); ?>" data-labm-pdf-id>
			<p data-labm-pdf-summary><?php echo $pdf_id ? esc_html( $attachment['name'] . ' · ' . $attachment['sizeLabel'] . ' · ' . ( $attachment['valid'] ? __( 'PDF válido', 'labm-core' ) : __( 'El PDF necesita reparación.', 'labm-core' ) ) ) : esc_html__( 'Ningún PDF asociado.', 'labm-core' ); ?></p>
			<p>
				<button type="button" class="button" data-labm-select-pdf><?php echo esc_html( $pdf_id ? __( 'Reemplazar PDF', 'labm-core' ) : __( 'Seleccionar o subir PDF', 'labm-core' ) ); ?></button>
				<button type="button" class="button-link-delete" data-labm-remove-pdf<?php echo $pdf_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Quitar PDF', 'labm-core' ); ?></button>
			</p>
			<p id="labm-pdf-help" class="description"><?php esc_html_e( 'El archivo permanece en la biblioteca al quitarlo.', 'labm-core' ); ?></p>
			<p id="labm-pdf-limit" class="description" data-labm-effective-max-bytes="<?php echo esc_attr( $limit ); ?>"><?php /* translators: %s: límite efectivo de carga. */ echo esc_html( sprintf( __( 'Límite efectivo: el menor entre 30 MB y el permitido por WordPress (%s).', 'labm-core' ), size_format( $limit ) ) ); ?></p>
		</fieldset>
		<p>
			<label for="labm-documento-fecha"><strong><?php esc_html_e( 'Fecha del documento', 'labm-core' ); ?></strong></label><br>
			<input type="date" id="labm-documento-fecha" name="labm_documento_fecha" value="<?php echo esc_attr( $date ); ?>" aria-describedby="labm-documento-fecha-help">
			<span id="labm-documento-fecha-help" class="description"><?php esc_html_e( 'Opcional. Usa la fecha oficial del documento.', 'labm-core' ); ?></span>
		</p>
		<p>
			<label for="labm-documento-tipo"><strong><?php esc_html_e( 'Tipo de documento', 'labm-core' ); ?></strong></label><br>
			<select id="labm-documento-tipo" name="labm_documento_categoria" aria-describedby="labm-documento-tipo-help">
				<?php foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) : ?>
					<option value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php selected( $type_id, $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<span id="labm-documento-tipo-help" class="description"><?php esc_html_e( 'Selecciona un solo tipo.', 'labm-core' ); ?></span>
		</p>
		<p class="screen-reader-text" aria-live="polite" data-labm-admin-live></p>
	</div>
	<?php
}

/**
 * Valida el formulario clásico antes de cualquier mutación.
 *
 * @param array $data Datos saneados por WordPress.
 * @param array $postarr Datos de la solicitud de guardado.
 * @return array|WP_Error
 */
function labm_core_document_admin_classic_pre_insert( $data, $postarr ) {
	$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
	if ( 'labm_documento' !== ( $data['post_type'] ?? '' ) || 'post' !== $request_method ) {
		return $data;
	}
	$action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
	if ( 'editpost' !== $action && ! isset( $_POST['_labm_document_nonce'] ) ) {
		return $data;
	}
	$nonce = isset( $_POST['_labm_document_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_labm_document_nonce'] ) ) : '';

	$raw_terms = wp_unslash( $_POST['labm_documento_categoria'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Se sanea por forma en $input.
	$input     = array(
		'post_title'               => $data['post_title'] ?? '',
		'labm_documento_pdf_id'    => absint( wp_unslash( $_POST['labm_documento_pdf_id'] ?? '' ) ),
		'labm_documento_fecha'     => sanitize_text_field( wp_unslash( $_POST['labm_documento_fecha'] ?? '' ) ),
		'labm_documento_categoria' => is_array( $raw_terms ) ? array_map( 'absint', $raw_terms ) : absint( $raw_terms ),
	);
	if ( ! wp_verify_nonce( $nonce, 'labm_document_admin_save' ) ) {
		$error = labm_core_document_admin_error( 'labm_document_nonce_invalid', '_labm_document_nonce', __( 'La sesión de edición venció. Recarga la página e inténtalo de nuevo.', 'labm-core' ), 403 );
		labm_core_document_admin_store_failed_state( $input, $error );
		return $error;
	}

	$post_id = absint( $postarr['ID'] ?? 0 );
	$result  = labm_core_document_admin_validate_state( $post_id, $input, get_current_user_id() );
	if ( is_wp_error( $result ) ) {
		labm_core_document_admin_store_failed_state( $input, $result );
		return $result;
	}

	$GLOBALS['labm_core_document_admin_pending_classic_state'] = labm_core_document_admin_effective_state( $post_id, $input );
	$data['post_title']                                        = sanitize_text_field( $input['post_title'] );
	return $data;
}

/**
 * Bloquea un formulario clásico inválido antes de que WordPress escriba el post.
 *
 * WordPress devuelve empty_content; el error por campo queda en el estado fallido.
 * Las llamadas sin el contexto de formulario clásico no activan el adaptador.
 *
 * @param bool  $is_empty Si WordPress ya considera vacío el contenido.
 * @param array $postarr Valores de la solicitud de guardado.
 * @return bool
 */
function labm_core_document_admin_classic_empty_content( $is_empty, $postarr ) {
	if ( $is_empty ) {
		return $is_empty;
	}
	$result = labm_core_document_admin_classic_pre_insert( $postarr, $postarr );
	if ( is_wp_error( $result ) ) {
		unset( $GLOBALS['labm_core_document_admin_pending_classic_state'] );
		return true;
	}
	return false;
}
add_filter( 'wp_insert_post_empty_content', 'labm_core_document_admin_classic_empty_content', 10, 2 );

/**
 * Persiste el estado clásico solo después de insertar el post válido.
 *
 * @param int     $post_id Documento guardado.
 * @param WP_Post $post Objeto guardado.
 */
function labm_core_document_admin_classic_after_insert( $post_id, $post ) {
	if ( 'labm_documento' !== $post->post_type || empty( $GLOBALS['labm_core_document_admin_pending_classic_state'] ) ) {
		return;
	}
	$state = $GLOBALS['labm_core_document_admin_pending_classic_state'];
	unset( $GLOBALS['labm_core_document_admin_pending_classic_state'] );
	labm_core_document_admin_persist_state( $post_id, $state );
}
add_action( 'wp_after_insert_post', 'labm_core_document_admin_classic_after_insert', 10, 2 );
