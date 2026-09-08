<?php
/**
 * Configuración y renderizado del footer global.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Capacidad requerida para administrar el footer. */
function labm_core_footer_capability() {
	return 'edit_theme_options';
}

/** Esquema cerrado de campos editoriales. */
function labm_core_footer_field_schema() {
	return array(
		'brand_name'         => array(
			'label' => 'Identidad mostrada',
			'type'  => 'text',
		),
		'description'        => array(
			'label' => 'Descripción',
			'type'  => 'textarea',
		),
		'navigation_heading' => array(
			'label' => 'Encabezado de navegación',
			'type'  => 'text',
		),
		'navigation_1_label' => array(
			'label' => 'Navegación 1: etiqueta',
			'type'  => 'text',
		),
		'navigation_1_url'   => array(
			'label' => 'Navegación 1: URL',
			'type'  => 'url',
		),
		'navigation_2_label' => array(
			'label' => 'Navegación 2: etiqueta',
			'type'  => 'text',
		),
		'navigation_2_url'   => array(
			'label' => 'Navegación 2: URL',
			'type'  => 'url',
		),
		'navigation_3_label' => array(
			'label' => 'Navegación 3: etiqueta',
			'type'  => 'text',
		),
		'navigation_3_url'   => array(
			'label' => 'Navegación 3: URL',
			'type'  => 'url',
		),
		'navigation_4_label' => array(
			'label' => 'Navegación 4: etiqueta',
			'type'  => 'text',
		),
		'navigation_4_url'   => array(
			'label' => 'Navegación 4: URL',
			'type'  => 'url',
		),
		'resources_heading'  => array(
			'label' => 'Encabezado de recursos',
			'type'  => 'text',
		),
		'resources_1_label'  => array(
			'label' => 'Recurso 1: etiqueta',
			'type'  => 'text',
		),
		'resources_1_url'    => array(
			'label' => 'Recurso 1: URL',
			'type'  => 'url',
		),
		'resources_2_label'  => array(
			'label' => 'Recurso 2: etiqueta',
			'type'  => 'text',
		),
		'resources_2_url'    => array(
			'label' => 'Recurso 2: URL',
			'type'  => 'url',
		),
		'resources_3_label'  => array(
			'label' => 'Recurso 3: etiqueta',
			'type'  => 'text',
		),
		'resources_3_url'    => array(
			'label' => 'Recurso 3: URL',
			'type'  => 'url',
		),
		'contact_heading'    => array(
			'label' => 'Encabezado de contacto',
			'type'  => 'text',
		),
		'contact_email'      => array(
			'label' => 'Correo',
			'type'  => 'email',
		),
		'facebook_label'     => array(
			'label' => 'Facebook: etiqueta',
			'type'  => 'text',
		),
		'facebook_url'       => array(
			'label' => 'Facebook: URL',
			'type'  => 'url',
		),
		'instagram_label'    => array(
			'label' => 'Instagram: etiqueta',
			'type'  => 'text',
		),
		'instagram_url'      => array(
			'label' => 'Instagram: URL',
			'type'  => 'url',
		),
		'copyright'          => array(
			'label' => 'Copyright',
			'type'  => 'text',
		),
		'policy_label'       => array(
			'label' => 'Política: etiqueta',
			'type'  => 'text',
		),
		'policy_url'         => array(
			'label' => 'Política: URL',
			'type'  => 'url',
		),
	);
}

/** Valores iniciales equivalentes al footer aprobado. */
function labm_core_footer_defaults() {
	return array(
		'brand_name'         => 'LABM',
		'description'        => "Liga Antioqueña de Balonmano\nInformación institucional administrable.",
		'navigation_heading' => 'NAVEGACIÓN',
		'navigation_1_label' => 'Inicio',
		'navigation_1_url'   => '/',
		'navigation_2_label' => 'Nosotros',
		'navigation_2_url'   => '/nosotros/',
		'navigation_3_label' => 'Actualidad',
		'navigation_3_url'   => '/actualidad/',
		'navigation_4_label' => 'Selecciones',
		'navigation_4_url'   => '/selecciones/',
		'resources_heading'  => 'RECURSOS',
		'resources_1_label'  => 'Documentos',
		'resources_1_url'    => '/documentos/',
		'resources_2_label'  => 'Contacto',
		'resources_2_url'    => '/contacto/',
		'resources_3_label'  => 'Privacidad',
		'resources_3_url'    => '/privacidad/',
		'contact_heading'    => 'CONTACTO DEMO',
		'contact_email'      => 'info@balonmanoantioquia.com',
		'facebook_label'     => 'Facebook',
		'facebook_url'       => 'https://www.facebook.com/balonmanoantioquia',
		'instagram_label'    => 'Instagram',
		'instagram_url'      => 'https://www.instagram.com/balonmanoantioquia',
		'copyright'          => '© 2026 Liga Antioqueña de Balonmano · Año dinámico',
		'policy_label'       => 'Política de tratamiento de datos',
		'policy_url'         => '/privacidad/',
	);
}

/**
 * Sanea todos los valores conforme al esquema.
 *
 * @param mixed $input Valores enviados desde Settings API.
 * @return array
 */
function labm_core_sanitize_footer_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array();
	foreach ( labm_core_footer_field_schema() as $key => $field ) {
		$value = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
		if ( 'textarea' === $field['type'] ) {
			$clean[ $key ] = sanitize_textarea_field( $value );
		} elseif ( 'email' === $field['type'] ) {
			$clean[ $key ] = sanitize_email( $value );
		} elseif ( 'url' === $field['type'] ) {
			$clean[ $key ] = esc_url_raw( $value, array( 'http', 'https' ) );
		} else {
			$clean[ $key ] = sanitize_text_field( $value );
		}
	}
	return $clean;
}

/** Obtiene la configuración efectiva. */
function labm_core_get_footer_settings() {
	$saved = get_option( 'labm_footer_settings', array() );
	if ( ! is_array( $saved ) || array() === $saved ) {
		return labm_core_footer_defaults();
	}
	return wp_parse_args( labm_core_sanitize_footer_settings( $saved ), labm_core_footer_defaults() );
}

/** Registra la opción del footer. */
function labm_core_register_footer_settings() {
	register_setting(
		'labm_footer',
		'labm_footer_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'labm_core_sanitize_footer_settings',
			'default'           => labm_core_footer_defaults(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'labm_core_register_footer_settings' );

/** Capacidad exigida por options.php para este grupo. */
function labm_core_footer_option_page_capability() {
	return labm_core_footer_capability();
}
add_filter( 'option_page_capability_labm_footer', 'labm_core_footer_option_page_capability' );

/** Registra la pantalla administrativa dedicada. */
function labm_core_register_footer_menu() {
	add_menu_page( 'Footer LABM', 'LABM', labm_core_footer_capability(), 'labm-footer', 'labm_core_render_footer_admin', 'dashicons-layout', 59 );
}
add_action( 'admin_menu', 'labm_core_register_footer_menu' );

/** Renderiza el formulario administrativo. */
function labm_core_render_footer_admin() {
	if ( ! current_user_can( labm_core_footer_capability() ) ) {
		wp_die( esc_html__( 'No tienes permiso para administrar el footer.', 'labm-core' ) );
	}
	$values = labm_core_get_footer_settings();
	?>
	<div class="wrap"><h1><?php esc_html_e( 'Footer LABM', 'labm-core' ); ?></h1><p><?php esc_html_e( 'Fuente única del contenido visible del pie de página.', 'labm-core' ); ?></p>
	<form action="options.php" method="post"><?php settings_fields( 'labm_footer' ); ?><table class="form-table" role="presentation"><tbody>
	<?php foreach ( labm_core_footer_field_schema() as $key => $field ) : ?>
	<tr><th scope="row"><label for="labm-footer-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th><td>
		<?php
		if ( 'textarea' === $field['type'] ) :
			?>
			<textarea class="large-text" rows="3" id="labm-footer-<?php echo esc_attr( $key ); ?>" name="labm_footer_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $values[ $key ] ); ?></textarea>
			<?php
		else :
			?>
			<input class="regular-text" type="<?php echo esc_attr( in_array( $field['type'], array( 'email', 'url' ), true ) ? $field['type'] : 'text' ); ?>" id="labm-footer-<?php echo esc_attr( $key ); ?>" name="labm_footer_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ); ?>">
		<?php endif; ?></td></tr>
	<?php endforeach; ?>
	</tbody></table><?php submit_button(); ?></form></div>
	<?php
}

/**
 * Compone una lista de enlaces omitiendo pares incompletos.
 *
 * @param array  $settings Configuración efectiva.
 * @param string $prefix Prefijo de los campos.
 * @param int    $count Cantidad máxima de enlaces.
 * @return string
 */
function labm_core_footer_links( $settings, $prefix, $count ) {
	$html = '';
	for ( $index = 1; $index <= $count; $index++ ) {
		$label = $settings[ $prefix . '_' . $index . '_label' ];
		$url   = $settings[ $prefix . '_' . $index . '_url' ];
		if ( '' !== $label && '' !== $url ) {
			$html .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	return $html;
}

/** Renderiza el footer público con salida escapada. */
function labm_core_render_footer() {
	$s          = labm_core_get_footer_settings();
	$navigation = labm_core_footer_links( $s, 'navigation', 4 );
	$resources  = labm_core_footer_links( $s, 'resources', 3 );
	$contact    = '';
	if ( '' !== $s['contact_email'] ) {
		$contact .= '<li><a href="mailto:' . esc_attr( $s['contact_email'] ) . '">' . esc_html( $s['contact_email'] ) . '</a></li>';
	}
	foreach ( array( 'facebook', 'instagram' ) as $network ) {
		if ( '' !== $s[ $network . '_label' ] && '' !== $s[ $network . '_url' ] ) {
			$contact .= '<li><a href="' . esc_url( $s[ $network . '_url' ] ) . '">' . esc_html( $s[ $network . '_label' ] ) . '</a></li>';
		}
	}
	$html  = '<footer class="wp-block-group is-layout-constrained labm-footer"><div class="wp-block-group alignwide labm-footer__main">';
	$html .= '<div class="wp-block-group labm-footer__brand">';
	$html .= '' !== $s['brand_name'] ? '<h2 class="wp-block-heading labm-footer__logo">' . esc_html( $s['brand_name'] ) . '</h2>' : '';
	$html .= '' !== $s['description'] ? '<p>' . nl2br( esc_html( $s['description'] ) ) . '</p>' : '';
	$html .= '</div>';
	foreach ( array( array( 'navigation_heading', $navigation ), array( 'resources_heading', $resources ), array( 'contact_heading', $contact ) ) as $column ) {
		$html .= '<div class="wp-block-group labm-footer__column">';
		$html .= '' !== $s[ $column[0] ] ? '<h2 class="wp-block-heading labm-footer__heading">' . esc_html( $s[ $column[0] ] ) . '</h2>' : '';
		$html .= '' !== $column[1] ? '<ul class="wp-block-list labm-footer__links">' . $column[1] . '</ul>' : '';
		$html .= '</div>';
	}
	$html .= '</div><div class="wp-block-group alignwide labm-footer__legal">';
	$html .= '' !== $s['copyright'] ? '<p>' . esc_html( $s['copyright'] ) . '</p>' : '';
	if ( '' !== $s['policy_label'] && '' !== $s['policy_url'] ) {
		$html .= '<p><a href="' . esc_url( $s['policy_url'] ) . '">' . esc_html( $s['policy_label'] ) . '</a></p>';
	}
	return $html . '</div></footer>';
}

/** Registra el punto único de renderizado. */
function labm_core_register_footer_shortcode() {
	add_shortcode( 'labm_footer', 'labm_core_render_footer' );
}
add_action( 'init', 'labm_core_register_footer_shortcode' );
