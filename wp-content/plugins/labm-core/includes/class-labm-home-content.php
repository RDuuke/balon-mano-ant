<?php
/**
 * Contenido editorial persistente para la portada.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Registra slides y aliados sin rutas publicas propias. */
function labm_core_register_home_content_types() {
	$types = array(
		'labm_slide'  => array( __( 'Slides de Inicio', 'labm-core' ), __( 'Slide de Inicio', 'labm-core' ), 'labm_slide', 'labm_slides' ),
		'labm_aliado' => array( __( 'Aliados Oficiales', 'labm-core' ), __( 'Aliado Oficial', 'labm-core' ), 'labm_aliado', 'labm_aliados' ),
	);

	foreach ( $types as $post_type => $names ) {
		$supports = 'labm_aliado' === $post_type
			? array( 'title', 'thumbnail', 'page-attributes' )
			: array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' );
		$singular = $names[1];
		register_post_type(
			$post_type,
			array(
				'labels'             => array(
					'name'              => $names[0],
					'singular_name'     => $singular,
					'add_new'           => __( 'Añadir', 'labm-core' ),
					'add_new_item'      => sprintf( __( 'Añadir %s', 'labm-core' ), $singular ),
					'edit_item'         => sprintf( __( 'Editar %s', 'labm-core' ), $singular ),
					'new_item'          => sprintf( __( 'Nuevo %s', 'labm-core' ), $singular ),
					'view_item'         => sprintf( __( 'Ver %s', 'labm-core' ), $singular ),
					'search_items'      => sprintf( __( 'Buscar %s', 'labm-core' ), $names[0] ),
					'not_found'         => sprintf( __( 'No se encontraron %s.', 'labm-core' ), strtolower( $names[0] ) ),
					'not_found_in_trash' => sprintf( __( 'No se encontraron %s en la papelera.', 'labm-core' ), strtolower( $names[0] ) ),
					'all_items'         => sprintf( __( 'Todos los %s', 'labm-core' ), $names[0] ),
					'attributes'        => __( 'Atributos', 'labm-core' ),
					'menu_name'         => $names[0],
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'has_archive'        => false,
				'rewrite'            => false,
				'supports'           => $supports,
				'capability_type'    => array( $names[2], $names[3] ),
				'map_meta_cap'       => true,
			)
		);
	}
}
add_action( 'init', 'labm_core_register_home_content_types', 5 );

/** Localiza etiquetas heredadas del editor clásico solo en Slides y Aliados. */
function labm_core_translate_home_classic_labels( $translation, $text, $domain ) {
	if ( 'default' !== $domain ) {
		return $translation;
	}
	$screen = $GLOBALS['current_screen'] ?? null;
	if ( ! $screen || ! in_array( $screen->post_type, array( 'labm_slide', 'labm_aliado' ), true ) ) {
		return $translation;
	}

	$translations = array(
		'Add Post'        => 'labm_slide' === $screen->post_type ? __( 'Añadir slide', 'labm-core' ) : __( 'Añadir aliado', 'labm-core' ),
		'Featured image'  => __( 'Imagen destacada', 'labm-core' ),
		'Post Attributes' => __( 'Atributos', 'labm-core' ),
		'Order'           => __( 'Orden', 'labm-core' ),
	);
	return $translations[ $text ] ?? $translation;
}
add_filter( 'gettext', 'labm_core_translate_home_classic_labels', 10, 3 );

/** Registra los campos REST saneados de portada. */
function labm_core_register_home_meta() {
	register_post_meta(
		'labm_slide',
		'labm_destino_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => 'labm_core_auth_post_meta',
		)
	);

	register_post_meta(
		'labm_slide',
		'labm_cta_texto',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => 'labm_core_auth_post_meta',
		)
	);
}
add_action( 'init', 'labm_core_register_home_meta', 6 );

/**
 * Valida los datos obligatorios antes de publicar contenido de portada.
 *
 * @param string $post_type Tipo editorial.
 * @param array  $data Datos propuestos.
 * @return true|WP_Error
 */
function labm_core_validate_home_publishable( $post_type, $data ) {
	if ( ! in_array( $post_type, array( 'labm_slide', 'labm_aliado' ), true ) ) {
		return new WP_Error( 'labm_invalid_home_type', __( 'El tipo editorial no pertenece a la portada.', 'labm-core' ) );
	}

	$thumbnail_id = absint( $data['thumbnail_id'] ?? 0 );
	$is_image     = $thumbnail_id && 'attachment' === get_post_type( $thumbnail_id ) && str_starts_with( (string) get_post_mime_type( $thumbnail_id ), 'image/' );
	if ( '' === trim( (string) ( $data['post_title'] ?? '' ) ) || ! $is_image ) {
		return new WP_Error(
			'labm_incomplete_home_content',
			__( 'El titulo y la imagen destacada son obligatorios antes de publicar.', 'labm-core' ),
			array( 'status' => 400 )
		);
	}

	$url = 'labm_slide' === $post_type ? (string) ( $data['labm_destino_url'] ?? '' ) : '';
	if ( '' !== $url && '' === esc_url_raw( $url ) ) {
		return new WP_Error( 'labm_invalid_home_url', __( 'El destino editorial no es una URL segura.', 'labm-core' ) );
	}

	return true;
}

/**
 * Rechaza solicitudes REST de publicacion incompletas.
 *
 * @param stdClass        $prepared_post Entrada preparada.
 * @param WP_REST_Request $request Solicitud REST.
 * @return stdClass|WP_Error
 */
function labm_core_validate_home_rest_insert( $prepared_post, $request ) {
	if ( 'publish' !== ( $prepared_post->post_status ?? '' ) ) {
		return $prepared_post;
	}
	$post_id      = absint( $request->get_param( 'id' ) );
	$thumbnail_id = $request->has_param( 'featured_media' )
		? absint( $request->get_param( 'featured_media' ) )
		: get_post_thumbnail_id( $post_id );
	$meta         = (array) $request->get_param( 'meta' );
	$validation   = labm_core_validate_home_publishable(
		$prepared_post->post_type,
		array(
			'post_title'       => $prepared_post->post_title,
			'thumbnail_id'     => $thumbnail_id,
			'labm_destino_url' => $meta['labm_destino_url'] ?? '',
		)
	);
	return true === $validation ? $prepared_post : $validation;
}
add_filter( 'rest_pre_insert_labm_slide', 'labm_core_validate_home_rest_insert', 10, 2 );
add_filter( 'rest_pre_insert_labm_aliado', 'labm_core_validate_home_rest_insert', 10, 2 );

/** Añade ayuda editorial y el nonce requerido por el editor clásico de Aliados. */
function labm_core_render_home_content_nonce() {
	global $post;
	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, array( 'labm_slide', 'labm_aliado' ), true ) ) {
		return;
	}
	if ( 'labm_aliado' === $post->post_type ) {
		wp_nonce_field( 'labm_save_home_content', '_labm_home_content_nonce' );
	}
	$title = 'labm_slide' === $post->post_type ? __( 'Cómo completar un slide', 'labm-core' ) : __( 'Cómo completar un aliado', 'labm-core' );
	$help  = 'labm_slide' === $post->post_type
		? __( 'La imagen destacada y el título son obligatorios. En Atributos > Orden, un número menor aparece antes. Si tu pantalla muestra Campos personalizados, usa solo Destino del enlace (labm_destino_url) y Texto del botón (labm_cta_texto).', 'labm-core' )
		: __( 'La imagen destacada funciona como logo y el título identifica al aliado. Define el orden en Atributos > Orden. Esta versión no tiene un campo editorial para enlace externo.', 'labm-core' );
	echo '<div class="notice notice-info inline"><p><strong>' . esc_html( $title ) . '</strong> ' . esc_html( $help ) . '</p></div>';
}
add_action( 'edit_form_after_title', 'labm_core_render_home_content_nonce' );

/**
 * Conserva como borrador un aliado incompleto enviado desde wp-admin.
 *
 * @param array $data Datos saneados del post.
 * @param array $postarr Datos originales del formulario.
 * @return array
 */
function labm_core_validate_home_admin_insert( $data, $postarr ) {
	if ( 'labm_aliado' !== ( $data['post_type'] ?? '' ) || 'publish' !== ( $data['post_status'] ?? '' ) ) {
		return $data;
	}

	$nonce = (string) ( $postarr['_labm_home_content_nonce'] ?? '' );
	// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Capacidad registrada por labm_core_ensure_capabilities().
	if ( ! wp_verify_nonce( $nonce, 'labm_save_home_content' ) || ! current_user_can( 'publish_labm_aliados' ) ) {
		return $data;
	}

	$post_id      = absint( $postarr['ID'] ?? 0 );
	$thumbnail_id = array_key_exists( '_thumbnail_id', $postarr )
		? absint( $postarr['_thumbnail_id'] )
		: get_post_thumbnail_id( $post_id );
	$validation   = labm_core_validate_home_publishable(
		'labm_aliado',
		array(
			'post_title'   => $data['post_title'] ?? '',
			'thumbnail_id' => $thumbnail_id,
		)
	);

	if ( is_wp_error( $validation ) ) {
		$data['post_status']                      = 'draft';
		$GLOBALS['labm_home_content_admin_error'] = 'incomplete';
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'labm_core_validate_home_admin_insert', 10, 2 );

/**
 * Propaga el resultado invalido al redirect seguro del editor clasico.
 *
 * @param string $location URL de retorno al editor.
 * @return string
 */
function labm_core_home_content_redirect_location( $location ) {
	if ( empty( $GLOBALS['labm_home_content_admin_error'] ) ) {
		return $location;
	}

	$error = sanitize_key( $GLOBALS['labm_home_content_admin_error'] );
	unset( $GLOBALS['labm_home_content_admin_error'] );
	return add_query_arg( 'labm_home_error', $error, $location );
}
add_filter( 'redirect_post_location', 'labm_core_home_content_redirect_location' );

/** Muestra el motivo por el que el aliado no se publico. */
function labm_core_home_content_admin_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- El parametro solo selecciona un aviso de solo lectura.
	if ( 'incomplete' !== sanitize_key( wp_unslash( $_GET['labm_home_error'] ?? '' ) ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>' . esc_html__( 'El titulo y el logo son obligatorios antes de publicar.', 'labm-core' ) . '</p></div>';
}
add_action( 'admin_notices', 'labm_core_home_content_admin_notice' );
