<?php
/**
 * Registro del dominio persistente de LABM.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Version del esquema de capacidades. */
const LABM_CORE_CAPABILITIES_VERSION = '3';

/** Version de las rutas publicas del dominio. */
const LABM_CORE_REWRITE_VERSION = '1';

/** Carga traducciones del plugin. */
function labm_core_load_textdomain() {
	load_plugin_textdomain( 'labm-core', false, dirname( plugin_basename( __DIR__ ) ) . '/languages' );
}
add_action( 'init', 'labm_core_load_textdomain', 1 );

/** Registra tipos de contenido y taxonomias del dominio. */
function labm_core_register_content_types() {
	$types = array(
		'labm_actualidad' => array( __( 'Actualidad', 'labm-core' ), __( 'Entrada de actualidad', 'labm-core' ), 'labm_actualidad', 'labm_actualidades' ),
		'labm_seleccion'  => array( __( 'Selecciones', 'labm-core' ), __( 'Selección', 'labm-core' ), 'labm_seleccion', 'labm_selecciones' ),
		'labm_club'       => array( __( 'Clubes', 'labm-core' ), __( 'Club', 'labm-core' ), 'labm_club', 'labm_clubes' ),
		'labm_integrante' => array( __( 'Integrantes', 'labm-core' ), __( 'Integrante', 'labm-core' ), 'labm_integrante', 'labm_integrantes' ),
		'labm_horario'    => array( __( 'Horarios', 'labm-core' ), __( 'Horario', 'labm-core' ), 'labm_horario', 'labm_horarios' ),
	);

	foreach ( $types as $post_type => $names ) {
		$singular = $names[2];
		$plural   = $names[3];
		register_post_type(
			$post_type,
			array(
				'labels'          => array(
					'name'          => $names[0],
					'singular_name' => $names[1],
					'add_new_item'  => sprintf(
						/* translators: %s: singular content type label. */
						__( 'Añadir %s', 'labm-core' ),
						$names[1]
					),
				),
				'public'          => true,
				'show_in_rest'    => true,
				'has_archive'     => true,
				'rewrite'         => array(
					'slug' => 'labm_seleccion' === $post_type ? 'selecciones' : substr( $post_type, 5 ),
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
				'capability_type' => array( $singular, $plural ),
				'map_meta_cap'    => true,
			)
		);
	}

	register_taxonomy(
		'labm_modalidad',
		array( 'labm_seleccion', 'labm_club' ),
		array(
			'labels'       => array(
				'name'          => __( 'Modalidades', 'labm-core' ),
				'singular_name' => __( 'Modalidad', 'labm-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'labm_categoria',
		array( 'labm_actualidad', 'labm_seleccion' ),
		array(
			'labels'       => array(
				'name'          => __( 'Categorías', 'labm-core' ),
				'singular_name' => __( 'Categoría', 'labm-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'labm_core_register_content_types', 5 );

/**
 * Valida una fecha ISO real.
 *
 * @param mixed $value Valor recibido.
 * @return bool
 */
function labm_core_validate_iso_date( $value ) {
	if ( ! is_string( $value ) ) {
		return false;
	}
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value );
	return $date instanceof DateTimeImmutable && $date->format( 'Y-m-d' ) === $value;
}

/**
 * Sanitiza una fecha ISO o devuelve cadena vacia.
 *
 * @param mixed $value Valor recibido.
 * @return string
 */
function labm_core_sanitize_iso_date( $value ) {
	return labm_core_validate_iso_date( $value ) ? $value : '';
}

/**
 * Autoriza metadatos usando la capacidad de edicion del contenido.
 *
 * @param bool   $allowed Valor previo.
 * @param string $meta_key Clave.
 * @param int    $post_id ID del contenido.
 * @return bool
 */
function labm_core_auth_post_meta( $allowed, $meta_key, $post_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	return current_user_can( 'edit_post', $post_id );
}

/** Registra metadatos editables por REST. */
function labm_core_register_meta() {
	$fields = array(
		'labm_actualidad' => array( 'labm_fecha_evento' => 'labm_core_sanitize_iso_date' ),
		'labm_seleccion'  => array( 'labm_modalidad_detalle' => 'sanitize_text_field' ),
		'labm_club'       => array( 'labm_ciudad' => 'sanitize_text_field' ),
		'labm_integrante' => array( 'labm_cargo' => 'sanitize_text_field' ),
		'labm_horario'    => array( 'labm_inicio' => 'sanitize_text_field' ),
	);

	foreach ( $fields as $post_type => $meta_fields ) {
		foreach ( $meta_fields as $key => $sanitize_callback ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $sanitize_callback,
					'auth_callback'     => 'labm_core_auth_post_meta',
				)
			);
		}
	}
}
add_action( 'init', 'labm_core_register_meta', 6 );

/** Concede capacidades de dominio a roles editoriales autorizados. */
function labm_core_ensure_capabilities() {
	if ( LABM_CORE_CAPABILITIES_VERSION === get_option( 'labm_core_capabilities_version' ) ) {
		return;
	}

	foreach ( array( 'administrator', 'editor' ) as $role_name ) {
		$role = get_role( $role_name );
		if ( ! $role ) {
			continue;
		}
		foreach ( array( 'labm_actualidad', 'labm_seleccion', 'labm_club', 'labm_integrante', 'labm_horario' ) as $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( $object ) {
				foreach ( array_unique( (array) $object->cap ) as $capability ) {
					$role->add_cap( $capability );
				}
			}
		}
	}
	update_option( 'labm_core_capabilities_version', LABM_CORE_CAPABILITIES_VERSION, false );
}
add_action( 'init', 'labm_core_ensure_capabilities', 20 );

/** Actualiza reglas solo cuando cambia la version declarada. */
function labm_core_ensure_rewrite_rules() {
	if ( LABM_CORE_REWRITE_VERSION === get_option( 'labm_core_rewrite_version' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'labm_core_rewrite_version', LABM_CORE_REWRITE_VERSION, false );
}
add_action( 'init', 'labm_core_ensure_rewrite_rules', 30 );

/** Activa registro, capacidades y reglas de URL. */
function labm_core_activate() {
	labm_core_register_content_types();
	labm_core_register_meta();
	delete_option( 'labm_core_capabilities_version' );
	delete_option( 'labm_core_rewrite_version' );
	labm_core_ensure_capabilities();
	flush_rewrite_rules();
}
