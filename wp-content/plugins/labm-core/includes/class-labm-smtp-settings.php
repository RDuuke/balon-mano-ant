<?php
/**
 * Configuracion SMTP hibrida y privada de LABM.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Devuelve la capacidad administrativa compartida por LABM. */
function labm_core_smtp_capability() {
	return function_exists( 'labm_core_footer_capability' ) ? labm_core_footer_capability() : 'edit_theme_options';
}

/** Define las claves no secretas permitidas en la opcion SMTP. */
function labm_core_smtp_field_schema() {
	return array(
		'host'       => array(
			'label' => 'Servidor SMTP',
			'type'  => 'text',
		),
		'port'       => array(
			'label' => 'Puerto',
			'type'  => 'number',
		),
		'encryption' => array(
			'label' => 'Cifrado',
			'type'  => 'select',
		),
		'from_email' => array(
			'label' => 'Correo remitente',
			'type'  => 'email',
		),
		'from_name'  => array(
			'label' => 'Nombre remitente',
			'type'  => 'text',
		),
		'recipients' => array(
			'label'       => 'Destinatarios de Contacto',
			'type'        => 'textarea',
			'description' => 'Separa varios correos con comas o saltos de linea.',
		),
	);
}

/** Valores seguros de una configuracion inicialmente vacia. */
function labm_core_smtp_defaults() {
	return array(
		'host'       => '',
		'port'       => 587,
		'encryption' => 'tls',
		'from_email' => '',
		'from_name'  => '',
		'recipients' => '',
	);
}

/**
 * Sanea una lista privada de correos, sin conservar valores invalidos o duplicados.
 *
 * @param mixed $value Lista de correos recibida desde configuracion heredada o el panel.
 * @return string Lista de correos valida, separada por comas.
 */
function labm_core_sanitize_smtp_recipients( $value ) {
	$recipients = array();
	$values     = preg_split( '/[\s,;]+/', (string) $value );
	foreach ( is_array( $values ) ? $values : array() as $candidate ) {
		$email = sanitize_email( $candidate );
		if ( is_email( $email ) && ! in_array( $email, $recipients, true ) ) {
			$recipients[] = $email;
		}
	}
	return implode( ', ', $recipients );
}

/**
 * Sanea exclusivamente los valores no secretos del esquema cerrado.
 *
 * @param mixed $input Valores procedentes de Settings API.
 * @return array
 */
function labm_core_sanitize_smtp_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	return array(
		'host'       => sanitize_text_field( $input['host'] ?? '' ),
		'port'       => min( 65535, max( 1, absint( $input['port'] ?? 0 ) ) ),
		'encryption' => in_array( $input['encryption'] ?? '', array( 'tls', 'ssl' ), true ) ? $input['encryption'] : '',
		'from_email' => sanitize_email( $input['from_email'] ?? '' ),
		'from_name'  => sanitize_text_field( $input['from_name'] ?? '' ),
		'recipients' => labm_core_sanitize_smtp_recipients( ( $input['recipients'] ?? '' ) . ',' . ( $input['recipient'] ?? '' ) ),
	);
}

/** Migra una vez los destinatarios privados del footer al origen canonico SMTP. */
function labm_core_migrate_smtp_recipients() {
	if ( get_option( 'labm_smtp_recipients_migrated', false ) ) {
		return;
	}
	$smtp_raw   = get_option( 'labm_smtp_settings', array() );
	$footer_raw = get_option( 'labm_footer_settings', array() );
	$smtp_raw   = is_array( $smtp_raw ) ? $smtp_raw : array();
	$footer_raw = is_array( $footer_raw ) ? $footer_raw : array();
	$legacy     = $footer_raw['contact_recipients'] ?? '';
	if ( array_key_exists( 'recipient', $smtp_raw ) || '' !== trim( (string) $legacy ) ) {
		$smtp_raw['recipients'] = ( $smtp_raw['recipients'] ?? '' ) . ',' . $legacy;
		update_option( 'labm_smtp_settings', labm_core_sanitize_smtp_settings( $smtp_raw ), false );
	}
	update_option( 'labm_smtp_recipients_migrated', 1, false );
}

/** Obtiene la configuracion guardada sin introducir secretos. */
function labm_core_get_smtp_settings() {
	labm_core_migrate_smtp_recipients();
	$saved = get_option( 'labm_smtp_settings', array() );
	return wp_parse_args( labm_core_sanitize_smtp_settings( $saved ), labm_core_smtp_defaults() );
}

/** Devuelve los destinatarios de Contacto definidos por el panel SMTP. */
function labm_core_smtp_recipients() {
	$settings   = labm_core_get_smtp_settings();
	$recipients = array_filter( array_map( 'trim', explode( ',', $settings['recipients'] ) ) );
	return $recipients ? $recipients : array( 'info@balonmanoantioquia.com' );
}

/** Indica si el secreto de aplicacion existe fuera de WordPress. */
function labm_core_smtp_password_available() {
	return defined( 'LABM_SMTP_PASSWORD' ) && is_string( LABM_SMTP_PASSWORD ) && '' !== trim( LABM_SMTP_PASSWORD );
}

/** Determina si se puede activar SMTP sin revelar el motivo de un fallo. */
function labm_core_smtp_is_configured() {
	$settings = labm_core_get_smtp_settings();
	return '' !== $settings['host'] && 0 < $settings['port'] && in_array( $settings['encryption'], array( 'tls', 'ssl' ), true ) && is_email( $settings['from_email'] ) && '' !== $settings['from_name'] && '' !== $settings['recipients'] && labm_core_smtp_password_available();
}

/** Registra la opcion cerrada sin exponerla en REST. */
function labm_core_register_smtp_settings() {
	register_setting(
		'labm_smtp',
		'labm_smtp_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'labm_core_sanitize_smtp_settings',
			'default'           => labm_core_smtp_defaults(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'labm_core_register_smtp_settings' );

/** Exige la capacidad LABM al actualizar la opcion mediante Settings API. */
function labm_core_smtp_option_page_capability() {
	return labm_core_smtp_capability();
}
add_filter( 'option_page_capability_labm_smtp', 'labm_core_smtp_option_page_capability' );

/**
 * Configura PHPMailer solo cuando la configuracion hibrida esta completa.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Instancia preparada por WordPress.
 * @return void
 */
function labm_core_configure_phpmailer( $phpmailer ) {
	if ( ! labm_core_smtp_is_configured() ) {
		return;
	}
	$settings = labm_core_get_smtp_settings();
	$phpmailer->isSMTP();
	$phpmailer->Host       = $settings['host']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
	$phpmailer->Port       = $settings['port']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
	$phpmailer->SMTPAuth   = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
	$phpmailer->Username   = $settings['from_email']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
	$phpmailer->Password   = (string) constant( 'LABM_SMTP_PASSWORD' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
	$phpmailer->SMTPSecure = 'ssl' === $settings['encryption'] ? 'ssl' : 'tls'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- API de PHPMailer.
}
add_action( 'phpmailer_init', 'labm_core_configure_phpmailer' );

/**
 * Usa el remitente autenticado solo mientras SMTP este habilitado.
 *
 * @param string $email Remitente original.
 * @return string
 */
function labm_core_smtp_from_email( $email ) {
	$settings = labm_core_get_smtp_settings();
	return labm_core_smtp_is_configured() ? $settings['from_email'] : $email;
}
add_filter( 'wp_mail_from', 'labm_core_smtp_from_email' );

/**
 * Usa el nombre autenticado solo mientras SMTP este habilitado.
 *
 * @param string $name Nombre original.
 * @return string
 */
function labm_core_smtp_from_name( $name ) {
	$settings = labm_core_get_smtp_settings();
	return labm_core_smtp_is_configured() ? $settings['from_name'] : $name;
}
add_filter( 'wp_mail_from_name', 'labm_core_smtp_from_name' );

/** Registra la pantalla secundaria de SMTP debajo de LABM. */
function labm_core_register_smtp_menu() {
	add_submenu_page( 'labm-footer', 'SMTP LABM', 'SMTP', labm_core_smtp_capability(), 'labm-smtp', 'labm_core_render_smtp_admin' );
}
add_action( 'admin_menu', 'labm_core_register_smtp_menu', 20 );

/** Devuelve una muestra estática de correo, sin secretos ni datos de formularios reales. */
function labm_core_contact_email_preview() {
	return function_exists( 'labm_core_render_contact_email' ) ? labm_core_render_contact_email(
		array(
			'nombre'    => 'Mariana',
			'apellidos' => 'Gómez',
			'correo'    => 'mariana@example.test',
			'telefono'  => '300 000 0000',
			'mensaje'   => "Hola, quisiera recibir información sobre las actividades de la Liga.\nGracias por su respuesta.",
		),
		false
	) : '';
}

/** Renderiza el panel sin campo ni valor de contrasena. */
function labm_core_render_smtp_admin() {
	if ( ! current_user_can( labm_core_smtp_capability() ) ) {
		wp_die( esc_html__( 'No tienes permiso para administrar SMTP.', 'labm-core' ) );
	}
	$values = labm_core_get_smtp_settings();
	$status = sanitize_key( (string) filter_input( INPUT_GET, 'labm_smtp_test', FILTER_UNSAFE_RAW ) );
	?>
	<div class="wrap"><h1><?php esc_html_e( 'SMTP LABM', 'labm-core' ); ?></h1>
	<p><?php esc_html_e( 'La contrasena de aplicacion se lee solamente desde LABM_SMTP_PASSWORD en wp-config.php.', 'labm-core' ); ?></p>
	<p><strong><?php echo esc_html( labm_core_smtp_password_available() ? __( 'Contrasena disponible.', 'labm-core' ) : __( 'Contrasena no disponible.', 'labm-core' ) ); ?></strong></p>
	<?php
	if ( 'success' === $status ) :
		?>
		<div class="notice notice-success"><p><?php esc_html_e( 'La prueba de correo fue aceptada para envio.', 'labm-core' ); ?></p></div>
		<?php
elseif ( 'failed' === $status ) :
	?>
		<div class="notice notice-error"><p><?php esc_html_e( 'No fue posible enviar la prueba. Revisa la configuracion sin compartir secretos.', 'labm-core' ); ?></p></div><?php endif; ?>
	<form action="options.php" method="post"><?php settings_fields( 'labm_smtp' ); ?><table class="form-table" role="presentation"><tbody>
	<?php foreach ( labm_core_smtp_field_schema() as $key => $field ) : ?>
	<tr><th scope="row"><label for="labm-smtp-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th><td>
		<?php
		if ( 'select' === $field['type'] ) :
			?>
			<select id="labm-smtp-<?php echo esc_attr( $key ); ?>" name="labm_smtp_settings[<?php echo esc_attr( $key ); ?>]"><option value="tls" <?php selected( 'tls', $values[ $key ] ); ?>>TLS</option><option value="ssl" <?php selected( 'ssl', $values[ $key ] ); ?>>SSL</option></select>
			<?php
		elseif ( 'textarea' === $field['type'] ) :
			?>
			<textarea class="large-text" rows="3" id="labm-smtp-<?php echo esc_attr( $key ); ?>" name="labm_smtp_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $values[ $key ] ); ?></textarea>
			<?php
	else :
		?>
		<input class="regular-text" type="<?php echo esc_attr( $field['type'] ); ?>" id="labm-smtp-<?php echo esc_attr( $key ); ?>" name="labm_smtp_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( (string) $values[ $key ] ); ?>"<?php echo 'number' === $field['type'] ? ' min="1" max="65535"' : ''; ?>><?php endif; ?>
		<?php if ( ! empty( $field['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
		<?php endif; ?>
	</td></tr>
	<?php endforeach; ?>
	</tbody></table><?php submit_button(); ?></form>
	<h2><?php esc_html_e( 'Vista previa del correo de Contacto', 'labm-core' ); ?></h2>
	<p><?php esc_html_e( 'Muestra estática con datos ficticios. No envía correo ni usa la configuración SMTP.', 'labm-core' ); ?></p>
	<iframe title="<?php esc_attr_e( 'Vista previa del correo de Contacto', 'labm-core' ); ?>" sandbox="" style="display:block;width:100%;max-width:640px;height:780px;border:1px solid #ccd0d4;background:#f3f6e8;" srcdoc="<?php echo esc_attr( labm_core_contact_email_preview() ); ?>"></iframe>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="labm_smtp_test">
	<?php
	wp_nonce_field( 'labm_smtp_test' );
	submit_button( __( 'Enviar prueba', 'labm-core' ), 'secondary', 'submit', false );
	?>
	</form></div>
	<?php
}

/** Ejecuta una prueba limitada al destinatario ya guardado y usa PRG. */
function labm_core_handle_smtp_test() {
	if ( ! current_user_can( labm_core_smtp_capability() ) ) {
		wp_die( esc_html__( 'No tienes permiso para enviar una prueba SMTP.', 'labm-core' ), 403 );
	}
	check_admin_referer( 'labm_smtp_test' );
	$settings = labm_core_get_smtp_settings();
	$sent     = labm_core_smtp_is_configured() && wp_mail( labm_core_smtp_recipients(), __( 'Prueba SMTP LABM', 'labm-core' ), __( 'Mensaje de prueba generado desde el panel administrativo de LABM.', 'labm-core' ) );
	wp_safe_redirect( add_query_arg( 'labm_smtp_test', $sent ? 'success' : 'failed', admin_url( 'admin.php?page=labm-smtp' ) ) );
	exit;
}
add_action( 'admin_post_labm_smtp_test', 'labm_core_handle_smtp_test' );
