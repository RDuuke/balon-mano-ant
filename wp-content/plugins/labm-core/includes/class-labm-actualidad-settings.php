<?php
/**
 * Configuración institucional editable de la página de Actualidad.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Capacidad requerida para administrar la presentación de Actualidad. */
function labm_core_actualidad_intro_capability() {
	return 'edit_theme_options';
}

/** Valores iniciales de la presentación institucional. */
function labm_core_actualidad_intro_defaults() {
	return array(
		'eyebrow'   => 'FORMACIÓN DEPORTIVA',
		'title'     => 'ESCUELA DE BALONMANO LABM',
		'body'      => "La Escuela de Balonmano LABM tiene como fin la formación de los nuevos talentos del handball antioqueño y la masificación de este deporte en el Departamento.\n\nLas clases son todos los sábados en la mañana en el coliseo Jorge Valderrama de la Unidad Deportiva Atanasio Girardot de Medellín.\n\nSe reciben niños entre los 5 y 13 años de edad, los cuales cuentan con varios formadores que también los preparan para los distintos festivales que se juegan durante el año, especialmente los de Comfama.",
		'schedule'  => 'SÁBADOS · MAÑANA',
		'location'  => 'COLISEO JORGE VALDERRAMA',
		'ages'      => 'NIÑOS DE 5 A 13 AÑOS',
		'festivals' => 'FESTIVALES DE COMFAMA',
	);
}

/** Esquema de campos de la presentación institucional. */
function labm_core_actualidad_intro_field_schema() {
	return array(
		'eyebrow'   => array( 'label' => 'Etiqueta superior', 'type' => 'text' ),
		'title'     => array( 'label' => 'Título', 'type' => 'text' ),
		'body'      => array( 'label' => 'Texto institucional', 'type' => 'textarea' ),
		'schedule'  => array( 'label' => 'Horario', 'type' => 'text' ),
		'location'  => array( 'label' => 'Lugar', 'type' => 'text' ),
		'ages'      => array( 'label' => 'Edades', 'type' => 'text' ),
		'festivals' => array( 'label' => 'Festivales', 'type' => 'text' ),
	);
}

/** Sanea la presentación institucional. */
function labm_core_sanitize_actualidad_intro( $input ) {
	$input    = is_array( $input ) ? $input : array();
	$defaults = labm_core_actualidad_intro_defaults();
	$clean    = array();
	foreach ( labm_core_actualidad_intro_field_schema() as $key => $field ) {
		$value = isset( $input[ $key ] ) ? (string) $input[ $key ] : $defaults[ $key ];
		$clean[ $key ] = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	}
	return wp_parse_args( $clean, $defaults );
}

/** Obtiene la configuración efectiva de la presentación. */
function labm_core_get_actualidad_intro() {
	$saved = get_option( 'labm_actualidad_intro', array() );
	return labm_core_sanitize_actualidad_intro( wp_parse_args( is_array( $saved ) ? $saved : array(), labm_core_actualidad_intro_defaults() ) );
}

/** Registra la opción institucional. */
function labm_core_register_actualidad_intro_settings() {
	register_setting(
		'labm_actualidad_intro',
		'labm_actualidad_intro',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'labm_core_sanitize_actualidad_intro',
			'default'           => labm_core_actualidad_intro_defaults(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'labm_core_register_actualidad_intro_settings' );

/** Registra la pantalla bajo el menú LABM. */
function labm_core_register_actualidad_intro_menu() {
	add_submenu_page( 'labm-footer', 'Presentación de Actualidad', 'Actualidad', labm_core_actualidad_intro_capability(), 'labm-actualidad', 'labm_core_render_actualidad_intro_admin' );
}
add_action( 'admin_menu', 'labm_core_register_actualidad_intro_menu' );

/** Renderiza el formulario administrativo. */
function labm_core_render_actualidad_intro_admin() {
	if ( ! current_user_can( labm_core_actualidad_intro_capability() ) ) {
		wp_die( esc_html__( 'No tienes permiso para administrar la presentación de Actualidad.', 'labm-core' ) );
	}
	$values = labm_core_get_actualidad_intro();
	?>
	<div class="wrap"><h1>Presentación de Actualidad</h1><p>Contenido institucional visible antes del listado de noticias.</p>
	<form action="options.php" method="post"><?php settings_fields( 'labm_actualidad_intro' ); ?><table class="form-table" role="presentation"><tbody>
	<?php foreach ( labm_core_actualidad_intro_field_schema() as $key => $field ) : ?>
	<tr><th scope="row"><label for="labm-actualidad-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th><td>
		<?php if ( 'textarea' === $field['type'] ) : ?>
		<textarea class="large-text" rows="8" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $values[ $key ] ); ?></textarea>
		<?php else : ?>
		<input class="regular-text" type="text" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ); ?>">
		<?php endif; ?>
	</td></tr>
	<?php endforeach; ?>
	</tbody></table><?php submit_button(); ?></form></div>
	<?php
}
