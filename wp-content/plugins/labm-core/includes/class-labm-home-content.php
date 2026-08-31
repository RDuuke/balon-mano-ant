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
		register_post_type(
			$post_type,
			array(
				'labels'             => array(
					'name'          => $names[0],
					'singular_name' => $names[1],
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'has_archive'        => false,
				'rewrite'            => false,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
				'capability_type'    => array( $names[2], $names[3] ),
				'map_meta_cap'       => true,
			)
		);
	}
}
add_action( 'init', 'labm_core_register_home_content_types', 5 );

/** Registra los campos REST saneados de portada. */
function labm_core_register_home_meta() {
	foreach ( array( 'labm_slide', 'labm_aliado' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'labm_destino_url',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => 'labm_core_auth_post_meta',
			)
		);
	}

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

	if ( '' === trim( (string) ( $data['post_title'] ?? '' ) ) || empty( $data['thumbnail_id'] ) ) {
		return new WP_Error( 'labm_incomplete_home_content', __( 'El titulo y la imagen destacada son obligatorios antes de publicar.', 'labm-core' ) );
	}

	$url = (string) ( $data['labm_destino_url'] ?? '' );
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
	$meta       = (array) $request->get_param( 'meta' );
	$validation = labm_core_validate_home_publishable(
		$prepared_post->post_type,
		array(
			'post_title'       => $prepared_post->post_title,
			'thumbnail_id'     => absint( $request->get_param( 'featured_media' ) ),
			'labm_destino_url' => $meta['labm_destino_url'] ?? '',
		)
	);
	return true === $validation ? $prepared_post : $validation;
}
add_filter( 'rest_pre_insert_labm_slide', 'labm_core_validate_home_rest_insert', 10, 2 );
add_filter( 'rest_pre_insert_labm_aliado', 'labm_core_validate_home_rest_insert', 10, 2 );
