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
		'event_enabled' => true,
		'event_post_id' => 0,
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
		'event_enabled' => array( 'label' => 'Mostrar Evento Destacado en Inicio', 'type' => 'checkbox' ),
		'event_post_id' => array( 'label' => 'Evento seleccionado', 'type' => 'event_select' ),
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
		if ( 'checkbox' === $field['type'] ) {
			$clean[ $key ] = ! empty( $input[ $key ] );
			continue;
		}
		$value = isset( $input[ $key ] ) ? $input[ $key ] : $defaults[ $key ];
		if ( 'event_select' === $field['type'] ) {
			$post_id = absint( $value );
			$post    = $post_id ? get_post( $post_id ) : null;
			$clean[ $key ] = $post && 'labm_actualidad' === $post->post_type && 'publish' === $post->post_status ? $post_id : 0;
			continue;
		}
		$clean[ $key ] = 'textarea' === $field['type'] ? sanitize_textarea_field( (string) $value ) : sanitize_text_field( (string) $value );
	}
	return wp_parse_args( $clean, $defaults );
}

/** Obtiene la configuración efectiva de la presentación. */
function labm_core_get_actualidad_intro() {
	$saved = get_option( 'labm_actualidad_intro', array() );
	return labm_core_sanitize_actualidad_intro( wp_parse_args( is_array( $saved ) ? $saved : array(), labm_core_actualidad_intro_defaults() ) );
}

/** Devuelve las publicaciones disponibles para la selección manual del evento. */
function labm_core_actualidad_event_options() {
	return get_posts(
		array(
			'post_type'      => 'labm_actualidad',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
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
		<?php if ( 'checkbox' === $field['type'] ) : ?>
		<input type="checkbox" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $values[ $key ] ); ?>>
		<p class="description">Si se desactiva, el bloque no aparecerá en la portada.</p>
		<?php elseif ( 'event_select' === $field['type'] ) : ?>
		<select class="regular-text" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]">
			<option value="0">Automático: elegir por fecha de evento</option>
			<?php foreach ( labm_core_actualidad_event_options() as $event ) : ?>
			<option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( (int) $values[ $key ], $event->ID ); ?>><?php echo esc_html( get_the_title( $event ) ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description">Solo se muestran publicaciones de Actualidad publicadas. Si la selección deja de estar disponible, se usa el modo automático.</p>
		<?php elseif ( 'textarea' === $field['type'] ) : ?>
		<textarea class="large-text" rows="8" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $values[ $key ] ); ?></textarea>
		<?php else : ?>
		<input class="regular-text" type="text" id="labm-actualidad-<?php echo esc_attr( $key ); ?>" name="labm_actualidad_intro[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ); ?>">
		<?php endif; ?>
	</td></tr>
	<?php endforeach; ?>
	</tbody></table><?php submit_button(); ?></form></div>
	<?php
}
