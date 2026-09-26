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
 * Normaliza los filtros publicos del catalogo documental.
 *
 * @param array $filters Filtros recibidos desde la consulta publica.
 * @return array
 */
function labm_core_document_catalog_normalize_filters( $filters ) {
	$filters = (array) $filters;
	$text    = isset( $filters['texto'] ) ? sanitize_text_field( (string) $filters['texto'] ) : '';
	$term_id = isset( $filters['categoria'] ) ? absint( $filters['categoria'] ) : 0;
	$year    = isset( $filters['anio'] ) ? absint( $filters['anio'] ) : 0;
	$order   = isset( $filters['orden'] ) ? sanitize_key( $filters['orden'] ) : 'recientes';

	if ( $term_id && ! term_exists( $term_id, 'labm_documento_categoria' ) ) {
		$term_id = 0;
	}
	if ( $year < 1000 || $year > 9999 ) {
		$year = 0;
	}
	if ( ! in_array( $order, array( 'recientes', 'antiguos' ), true ) ) {
		$order = 'recientes';
	}

	return array(
		'texto'     => $text,
		'categoria' => $term_id,
		'anio'      => $year,
		'orden'     => $order,
	);
}

/**
 * Construye la consulta publica combinada del catalogo documental.
 *
 * @param array $filters Filtros públicos normalizados o recibidos del formulario.
 * @param int   $page Pagina solicitada.
 * @param int   $per_page Elementos por pagina.
 * @return WP_Query
 */
function labm_core_document_catalog_query( $filters = array(), $page = 1, $per_page = 10 ) {
	$filters = labm_core_document_catalog_normalize_filters( $filters );
	$args = array(
		'post_type'      => 'labm_documento',
		'post_status'    => 'publish',
		'paged'          => max( 1, absint( $page ) ),
		'posts_per_page' => max( 1, absint( $per_page ) ),
		'meta_key'       => 'labm_documento_pdf_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Solo documentos con PDF asociado.
		'meta_compare'   => 'EXISTS',
		'orderby'        => 'date',
		'order'          => 'antiguos' === $filters['orden'] ? 'ASC' : 'DESC',
	);
	if ( '' !== $filters['texto'] ) {
		$args['s'] = $filters['texto'];
	}
	if ( $filters['categoria'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'labm_documento_categoria',
				'field'    => 'term_id',
				'terms'    => $filters['categoria'],
			),
		);
	}
	if ( $filters['anio'] ) {
		$args['meta_query'] = array(
			array(
				'key'     => 'labm_documento_fecha',
				'value'   => '^' . $filters['anio'] . '-[0-9]{2}-[0-9]{2}$',
				'compare' => 'REGEXP',
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
	$normalized  = labm_core_document_catalog_normalize_filters( $filters );
	$args        = array( 'pagina' => max( 1, absint( $page ) ) );
	if ( '' !== $normalized['texto'] ) {
		$args['texto'] = $normalized['texto'];
	}
	if ( $normalized['categoria'] ) {
		$args['categoria'] = $normalized['categoria'];
	}
	if ( $normalized['anio'] ) {
		$args['anio'] = $normalized['anio'];
	}
	if ( 'recientes' !== $normalized['orden'] ) {
		$args['orden'] = $normalized['orden'];
	}
	$page_object = get_page_by_path( 'documentos' );
	return add_query_arg( $args, $page_object ? get_permalink( $page_object ) : get_post_type_archive_link( 'labm_documento' ) );
}

/** Devuelve categorías y años que pueden usarse como filtros públicos. */
function labm_core_document_catalog_filter_options() {
	$categories = get_terms(
		array(
			'taxonomy'   => 'labm_documento_categoria',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	$years = array();
	$posts = get_posts(
		array(
			'post_type'      => 'labm_documento',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => 'labm_documento_fecha', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Opciones públicas del catálogo.
			'meta_compare'   => 'EXISTS',
		)
	);
	foreach ( $posts as $post_id ) {
		$date = (string) get_post_meta( $post_id, 'labm_documento_fecha', true );
		if ( preg_match( '/^([0-9]{4})-[0-9]{2}-[0-9]{2}$/', $date, $matches ) ) {
			$years[ (int) $matches[1] ] = (int) $matches[1];
		}
	}
	rsort( $years, SORT_NUMERIC );
	return array(
		'categories' => is_wp_error( $categories ) ? array() : $categories,
		'years'      => $years,
	);
}

/**
 * Obtiene la página solicitada del catálogo desde la consulta pública actual.
 *
 * @return int
 */
function labm_core_document_catalog_current_page() {
	return isset( $_GET['pagina'] ) ? max( 1, absint( wp_unslash( $_GET['pagina'] ) ) ) : 1;
}

/** Obtiene los filtros públicos activos desde la URL del catálogo. */
function labm_core_document_catalog_current_filters() {
	$filters = array();
	foreach ( array( 'texto', 'categoria', 'anio', 'orden' ) as $key ) {
		if ( isset( $_GET[ $key ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Se desescapa, valida escalar y sanea inmediatamente en la asignación siguiente.
			$raw            = wp_unslash( $_GET[ $key ] );
			$filters[ $key ] = is_scalar( $raw ) ? sanitize_text_field( (string) $raw ) : '';
		}
	}
	return $filters;
}

/**
 * Renderiza resultados documentales o un estado vacio accionable.
 *
 * @param array $filters Filtros públicos del catálogo.
 * @param int   $page Pagina.
 * @param int   $per_page Tamano de pagina.
 * @return string
 */
function labm_core_render_document_catalog( $filters = array(), $page = 1, $per_page = 10 ) {
	$page    = max( 1, absint( $page ) );
	$filters = labm_core_document_catalog_normalize_filters( $filters );
	$query   = labm_core_document_catalog_query( $filters, $page, $per_page );
	$options = labm_core_document_catalog_filter_options();
	$page_object = get_page_by_path( 'documentos' );
	$action      = $page_object ? get_permalink( $page_object ) : get_post_type_archive_link( 'labm_documento' );
	$html        = '<section class="labm-documents-filters alignfull" aria-label="' . esc_attr__( 'Filtrar documentos', 'labm-core' ) . '"><form method="get" action="' . esc_url( $action ) . '">';
	$html       .= '<div class="labm-documents-filters__field"><label for="labm-documentos-texto">' . esc_html__( 'Buscar', 'labm-core' ) . '</label><input id="labm-documentos-texto" name="texto" type="search" placeholder="' . esc_attr__( 'Buscar documentos', 'labm-core' ) . '" value="' . esc_attr( $filters['texto'] ) . '"></div>';
	$html       .= '<div class="labm-documents-filters__field"><label for="labm-documentos-categoria">' . esc_html__( 'Categoría', 'labm-core' ) . '</label><select id="labm-documentos-categoria" name="categoria"><option value="">' . esc_html__( 'Todas', 'labm-core' ) . '</option>';
	foreach ( $options['categories'] as $category ) {
		$html .= '<option value="' . esc_attr( (string) $category->term_id ) . '" ' . selected( $filters['categoria'], $category->term_id, false ) . '>' . esc_html( $category->name ) . '</option>';
	}
	$html .= '</select></div><div class="labm-documents-filters__field"><label for="labm-documentos-anio">' . esc_html__( 'Año', 'labm-core' ) . '</label><select id="labm-documentos-anio" name="anio"><option value="">' . esc_html__( 'Todos', 'labm-core' ) . '</option>';
	foreach ( $options['years'] as $year ) {
		$html .= '<option value="' . esc_attr( (string) $year ) . '" ' . selected( $filters['anio'], $year, false ) . '>' . esc_html( (string) $year ) . '</option>';
	}
	$html .= '</select></div><div class="labm-documents-filters__field"><label for="labm-documentos-orden">' . esc_html__( 'Orden', 'labm-core' ) . '</label><select id="labm-documentos-orden" name="orden"><option value="recientes" ' . selected( $filters['orden'], 'recientes', false ) . '>' . esc_html__( 'Más recientes', 'labm-core' ) . '</option><option value="antiguos" ' . selected( $filters['orden'], 'antiguos', false ) . '>' . esc_html__( 'Más antiguos', 'labm-core' ) . '</option></select></div><div class="labm-documents-filters__submit"><button type="submit">' . esc_html__( 'Aplicar filtros', 'labm-core' ) . '</button>';
	if ( $filters['texto'] || $filters['categoria'] || $filters['anio'] || 'recientes' !== $filters['orden'] ) {
		$html .= '<a href="' . esc_url( $action ) . '">' . esc_html__( 'Limpiar filtros', 'labm-core' ) . '</a>';
	}
	$html .= '</div></form></section>';
	$has_active_filters = $filters['texto'] || $filters['categoria'] || $filters['anio'] || 'recientes' !== $filters['orden'];
	if ( ! $query->have_posts() ) {
		$empty_action = $has_active_filters ? '<a class="labm-documents-empty__clear" href="' . esc_url( $action ) . '">' . esc_html__( 'Limpiar filtros', 'labm-core' ) . '</a>' : '';
		return $html . '<section class="labm-documents-empty" aria-labelledby="labm-documents-empty-title"><span aria-hidden="true">⌕</span><h2 id="labm-documents-empty-title">' . esc_html__( 'No encontramos documentos', 'labm-core' ) . '</h2><p>' . esc_html__( 'Prueba con otra combinación de filtros.', 'labm-core' ) . '</p>' . $empty_action . '</section>';
	}
	/* translators: %s: cantidad de documentos encontrados. */
	$summary = sprintf( _n( '%s documento publicado', '%s documentos publicados', (int) $query->found_posts, 'labm-core' ), number_format_i18n( $query->found_posts ) );
	$html  .= '<section class="labm-documents-catalog" aria-label="' . esc_attr__( 'Documentos publicados', 'labm-core' ) . '"><p class="labm-documents-catalog__summary">' . esc_html( $summary ) . '</p><div class="labm-documents-catalog__list">';
	foreach ( $query->posts as $post ) {
		$url       = labm_core_document_pdf_url( $post->ID );
		$date      = (string) get_post_meta( $post->ID, 'labm_documento_fecha', true );
		$terms     = get_the_terms( $post->ID, 'labm_documento_categoria' );
		$category  = ! is_wp_error( $terms ) && $terms ? $terms[0]->name : '';
		$attachment = absint( get_post_meta( $post->ID, 'labm_documento_pdf_id', true ) );
		$path       = $attachment ? get_attached_file( $attachment ) : '';
		$size       = $path && is_readable( $path ) ? size_format( (int) filesize( $path ) ) : '';
		$html      .= '<article class="labm-documents-catalog__item"><span class="labm-documents-catalog__icon" aria-hidden="true">PDF</span><div class="labm-documents-catalog__body"><p class="labm-documents-catalog__category">' . esc_html( $category ) . '</p><h2>' . esc_html( get_the_title( $post ) ) . '</h2><p class="labm-documents-catalog__meta">' . esc_html( trim( $date . ( $size ? ' · ' . $size : '' ) ) ) . '</p></div>';
		if ( $url ) {
			$html .= '<p class="labm-documents-catalog__actions"><a class="labm-documents-catalog__view" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Ver PDF', 'labm-core' ) . '</a><a class="labm-documents-catalog__download" href="' . esc_url( labm_core_document_download_url( $post->ID ) ) . '" download>' . esc_html__( 'Descargar', 'labm-core' ) . '</a></p>';
		} else {
			$html .= '<p class="labm-documents-catalog__unavailable" role="status">' . esc_html__( 'PDF no disponible', 'labm-core' ) . '</p>';
		}
		$html .= '</article>';
	}
	$html .= '</div>';
	if ( $query->max_num_pages > 1 ) {
		$html .= '<nav class="labm-documents-pagination" aria-label="' . esc_attr__( 'Paginación de documentos', 'labm-core' ) . '"><ul>';
		if ( $page > 1 ) {
			$html .= '<li><a href="' . esc_url( labm_core_document_page_url( $page - 1, $filters ) ) . '" aria-label="' . esc_attr__( 'Página anterior', 'labm-core' ) . '">←</a></li>';
		}
		for ( $number = 1; $number <= (int) $query->max_num_pages; $number++ ) {
			$html .= $number === $page ? '<li><span aria-current="page">' . esc_html( (string) $number ) . '</span></li>' : '<li><a href="' . esc_url( labm_core_document_page_url( $number, $filters ) ) . '">' . esc_html( (string) $number ) . '</a></li>';
		}
		if ( $page < (int) $query->max_num_pages ) {
			$html .= '<li><a href="' . esc_url( labm_core_document_page_url( $page + 1, $filters ) ) . '" aria-label="' . esc_attr__( 'Página siguiente', 'labm-core' ) . '">→</a></li>';
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
function labm_core_process_contact_legacy( $data ) {
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

/** Devuelve solo los datos institucionales publicos de Contacto. */
function labm_core_get_contact_settings() {
	$footer  = function_exists( 'labm_core_get_footer_settings' ) ? labm_core_get_footer_settings() : array();
	$email   = sanitize_email( $footer['contact_email'] ?? '' );
	$address = 'Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos, Medellín, Colombia';
	$socials = array();
	foreach ( array( 'facebook', 'instagram' ) as $network ) {
		$url   = esc_url_raw( $footer[ $network . '_url' ] ?? '', array( 'http', 'https' ) );
		$label = sanitize_text_field( $footer[ $network . '_label' ] ?? '' );
		if ( '' !== $url && '' !== $label ) {
			$socials[ $network ] = array( 'label' => $label, 'url' => $url );
		}
	}
	return array(
		'email'   => is_email( $email ) ? $email : 'info@balonmanoantioquia.com',
		'phone'   => '3233212981',
		'address' => $address,
		'map_url' => esc_url_raw( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address ), array( 'https' ) ),
		'socials' => $socials,
	);
}

/** Obtiene los destinatarios privados permitidos por el entorno actual. */
function labm_core_contact_recipients() {
	$recipients  = function_exists( 'labm_core_smtp_recipients' ) ? labm_core_smtp_recipients() : array( 'info@balonmanoantioquia.com' );
	$environment = apply_filters( 'labm_core_contact_environment', wp_get_environment_type() );
	if ( ! in_array( $environment, array( 'local', 'development', 'staging' ), true ) ) {
		return $recipients;
	}
	$raw_recipients = getenv( 'LABM_CONTACT_TEST_RECIPIENTS' );
	if ( ! is_string( $raw_recipients ) ) {
		return $recipients;
	}
	$test_recipient_count = 0;
	foreach ( array_filter( array_map( 'trim', explode( ',', $raw_recipients ) ) ) as $candidate ) {
		$email = sanitize_email( $candidate );
		if ( is_email( $email ) && ! in_array( $email, $recipients, true ) ) {
			$recipients[] = $email;
			++$test_recipient_count;
		}
		if ( 2 === $test_recipient_count ) {
			break;
		}
	}
	return $recipients;
}

/** Reserva de forma atomica un token mientras se entrega un mensaje. */
function labm_core_contact_reserve_token( $token ) {
	if ( '' === $token ) {
		return '';
	}
	$key  = 'labm_contact_' . hash( 'sha256', $token );
	$lock = $key . '_lock';
	if ( get_transient( $key ) ) {
		return false;
	}
	if ( absint( get_option( $lock, 0 ) ) < time() ) {
		delete_option( $lock );
	}
	return add_option( $lock, time() + HOUR_IN_SECONDS, '', false ) ? $lock : false;
}

/** Libera una reserva fallida o completada. */
function labm_core_contact_release_token( $lock ) {
	if ( is_string( $lock ) && '' !== $lock ) {
		delete_option( $lock );
	}
}

/**
 * Construye el correo HTML institucional de Contacto sin incluir datos no saneados.
 *
 * @param mixed $contact Datos ya validados del formulario.
 * @param bool  $use_cid_logo Si se debe usar una imagen incrustada para correo.
 * @return string
 */
function labm_core_render_contact_email( $contact, $use_cid_logo = true ) {
	$contact     = is_array( $contact ) ? $contact : array();
	$logo_path   = labm_core_contact_logo_path();
	$logo_source = '';
	if ( '' !== $logo_path ) {
		$logo_source = $use_cid_logo ? 'cid:' . labm_core_contact_logo_cid() : ( function_exists( 'get_theme_file_uri' ) ? get_theme_file_uri( 'assets/images/logo-color.jpg' ) : '' );
	}
	$logo        = '' !== $logo_source ? '<img src="' . ( $use_cid_logo ? $logo_source : esc_url( $logo_source ) ) . '" width="240" alt="LABM — Liga Antioqueña de Balonmano" style="display:block;width:240px;max-width:100%;height:auto;border:0;outline:none;text-decoration:none;">' : '';
	$name        = trim( (string) ( $contact['nombre'] ?? '' ) . ' ' . (string) ( $contact['apellidos'] ?? '' ) );
	$email       = (string) ( $contact['correo'] ?? '' );
	$phone       = (string) ( $contact['telefono'] ?? '' );
	$message     = nl2br( esc_html( (string) ( $contact['mensaje'] ?? '' ) ) );
	$details     = sprintf(
		'<tr><td style="padding:0 0 10px;font:700 14px/20px Arial,sans-serif;color:#202020;">Nombre</td><td style="padding:0 0 10px;font:400 14px/20px Arial,sans-serif;color:#202020;">%1$s</td></tr><tr><td style="padding:0 0 10px;font:700 14px/20px Arial,sans-serif;color:#202020;">Correo</td><td style="padding:0 0 10px;font:400 14px/20px Arial,sans-serif;color:#202020;"><a href="mailto:%2$s" style="color:#202020;">%2$s</a></td></tr>',
		esc_html( $name ),
		esc_attr( $email )
	);
	if ( '' !== $phone ) {
		$details .= sprintf( '<tr><td style="padding:0 0 10px;font:700 14px/20px Arial,sans-serif;color:#202020;">Teléfono</td><td style="padding:0 0 10px;font:400 14px/20px Arial,sans-serif;color:#202020;">%s</td></tr>', esc_html( $phone ) );
	}
	return sprintf(
		'<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Nuevo mensaje de Contacto</title></head><body style="margin:0;padding:0;background:#F3F6E8;color:#202020;"><table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="width:100%%;background:#F3F6E8;"><tr><td align="center" style="padding:32px 16px;"><table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:600px;max-width:100%%;background:#ffffff;"><tr><td style="padding:26px 32px 22px;background:#202020;">%1$s<p style="margin:18px 0 0;font:700 12px/16px Arial,sans-serif;letter-spacing:1.2px;text-transform:uppercase;color:#AECD25;">Liga Antioqueña de Balonmano</p></td></tr><tr><td style="height:8px;background:#AECD25;font-size:0;line-height:0;">&nbsp;</td></tr><tr><td style="padding:32px;"><h1 style="margin:0 0 12px;font:700 28px/34px Arial,sans-serif;color:#202020;">Nuevo mensaje de contacto</h1><p style="margin:0 0 26px;font:400 16px/24px Arial,sans-serif;color:#202020;">Una persona ha enviado una consulta desde el formulario de la Liga Antioqueña de Balonmano.</p><table role="presentation" width="100%%" cellspacing="0" cellpadding="0" border="0" style="width:100%%;margin:0 0 26px;border-bottom:1px solid #d9e4a8;">%2$s</table><div style="padding:20px;background:#F3F6E8;"><p style="margin:0 0 8px;font:700 14px/20px Arial,sans-serif;color:#202020;">Mensaje</p><p style="margin:0;font:400 16px/24px Arial,sans-serif;color:#202020;">%3$s</p></div></td></tr><tr><td style="padding:20px 32px;background:#202020;"><p style="margin:0;font:400 12px/18px Arial,sans-serif;color:#ffffff;">Este correo fue generado desde el formulario de contacto de LABM.</p></td></tr></table></td></tr></table></body></html>',
		$logo,
		$details,
		$message
	);
}

/** Devuelve el identificador estable de la imagen incrustada del correo. */
function labm_core_contact_logo_cid() {
	return 'labm-contact-logo';
}

/** Localiza el logo institucional que se puede incrustar en el correo. */
function labm_core_contact_logo_path() {
	$path = function_exists( 'get_theme_file_path' ) ? get_theme_file_path( 'assets/images/logo-color.jpg' ) : '';
	return is_string( $path ) && is_readable( $path ) ? $path : '';
}

/**
 * Determina si PHPMailer ya contiene el logo de Contacto.
 *
 * @param object $phpmailer Instancia de PHPMailer.
 * @return bool
 */
function labm_core_contact_mailer_has_logo( $phpmailer ) {
	if ( ! is_object( $phpmailer ) || ! method_exists( $phpmailer, 'getAttachments' ) ) {
		return false;
	}
	foreach ( $phpmailer->getAttachments() as $attachment ) {
		if ( is_array( $attachment ) && isset( $attachment[7] ) && labm_core_contact_logo_cid() === $attachment[7] ) {
			return true;
		}
	}
	return false;
}

/**
 * Incrusta el logo solo en correos HTML de Contacto que usan su CID.
 *
 * @param object $phpmailer Instancia de PHPMailer.
 * @return void
 */
function labm_core_embed_contact_logo( $phpmailer ) {
	if ( ! is_object( $phpmailer ) || ! isset( $phpmailer->Body ) || ! method_exists( $phpmailer, 'addEmbeddedImage' ) || false === strpos( (string) $phpmailer->Body, 'cid:' . labm_core_contact_logo_cid() ) || labm_core_contact_mailer_has_logo( $phpmailer ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
		return;
	}
	$logo_path = labm_core_contact_logo_path();
	if ( '' === $logo_path ) {
		return;
	}
	try {
		$phpmailer->addEmbeddedImage( $logo_path, labm_core_contact_logo_cid(), 'logo-color.jpg', 'base64', 'image/jpeg' );
	} catch ( Exception $exception ) {
		// La marca textual de la plantilla se conserva si el archivo no se puede adjuntar.
		return;
	}
}
add_action( 'phpmailer_init', 'labm_core_embed_contact_logo', 20 );

/** Procesa contacto sin retener datos personales fuera de la entrega. */
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
		if ( '' === trim( sanitize_textarea_field( $data[ $field ] ?? '' ) ) ) {
			$errors[ $field ] = __( 'Este campo es obligatorio.', 'labm-core' );
		}
	}
	$email = sanitize_email( $data['correo'] ?? '' );
	if ( ! is_email( $email ) ) {
		$errors['correo'] = __( 'Escribe un correo electrónico válido.', 'labm-core' );
	}
	if ( empty( $data['consentimiento'] ) ) {
		$errors['consentimiento'] = __( 'Debes aceptar el tratamiento de datos para enviar el mensaje.', 'labm-core' );
	}
	if ( $errors ) {
		return array( 'ok' => false, 'errors' => $errors );
	}
	$token = sanitize_key( $data['token'] ?? '' );
	if ( '' === $token ) {
		return array( 'ok' => false, 'errors' => array( 'token' => __( 'No fue posible procesar el formulario.', 'labm-core' ) ) );
	}
	$lock  = labm_core_contact_reserve_token( $token );
	if ( false === $lock ) {
		return array( 'ok' => true, 'errors' => array() );
	}
	$phone = preg_replace( '/[^0-9+()\-\s]/', '', (string) ( $data['telefono'] ?? '' ) );
	$body  = labm_core_render_contact_email(
		array(
			'nombre'    => sanitize_text_field( $data['nombre'] ),
			'apellidos' => sanitize_text_field( $data['apellidos'] ),
			'correo'    => $email,
			'telefono'  => $phone,
			'mensaje'   => sanitize_textarea_field( $data['mensaje'] ),
		)
	);
	$recipients = labm_core_contact_recipients();
	$sent = wp_mail( $recipients, sanitize_text_field( $data['asunto'] ), $body, array( 'Reply-To: ' . $email, 'Content-Type: text/html; charset=UTF-8' ) );
	if ( ! $sent ) {
		labm_core_contact_release_token( $lock );
		do_action( 'labm_core_contact_delivery_failed', array( 'code' => 'mail_delivery_failed' ) );
		return array( 'ok' => false, 'errors' => array( 'delivery' => __( 'No pudimos enviar el mensaje. Inténtalo de nuevo.', 'labm-core' ) ) );
	}
	if ( '' !== $token ) {
		set_transient( 'labm_contact_' . hash( 'sha256', $token ), 1, HOUR_IN_SECONDS );
	}
	labm_core_contact_release_token( $lock );
	return array( 'ok' => true, 'errors' => array() );
}

/** Conserva el alfabeto del identificador opaco generado para el estado PRG. */
function labm_core_sanitize_contact_state_id( $state_id ) {
	$state_id = preg_replace( '/[^A-Za-z0-9]/', '', (string) $state_id );
	return is_string( $state_id ) ? $state_id : '';
}

/** Recupera una sola vez el estado opaco de una redireccion POST-Redirect-GET. */
function labm_core_contact_consume_state( $state_id ) {
	$state_id = labm_core_sanitize_contact_state_id( $state_id );
	if ( '' === $state_id ) {
		return array();
	}
	$key   = 'labm_contact_state_' . hash( 'sha256', $state_id );
	$state = get_transient( $key );
	delete_transient( $key );
	return is_array( $state ) ? $state : array();
}

/** Atiende exclusivamente POST y redirige a Contacto sin datos personales. */
function labm_core_handle_contact_send() {
	$result = array( 'ok' => false, 'errors' => array( 'request' => __( 'No fue posible procesar el formulario.', 'labm-core' ) ) );
	if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		$result = labm_core_process_contact( wp_unslash( $_POST ) );
	}
	$state_id = wp_generate_password( 32, false, false );
	$state    = array( 'ok' => ! empty( $result['ok'] ), 'errors' => array_keys( is_array( $result['errors'] ?? null ) ? $result['errors'] : array() ) );
	set_transient( 'labm_contact_state_' . hash( 'sha256', $state_id ), $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'contacto_estado', rawurlencode( $state_id ), home_url( '/contacto/#formulario' ) ) );
	exit;
}
add_action( 'admin_post_labm_contact_send', 'labm_core_handle_contact_send' );
add_action( 'admin_post_nopriv_labm_contact_send', 'labm_core_handle_contact_send' );
