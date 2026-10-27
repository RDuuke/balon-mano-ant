<?php
/**
 * Pruebas funcionales del footer administrable.
 *
 * @package LABM_Core
 */

use PHPUnit\Framework\TestCase;

/** Verifica configuración, seguridad y salida pública del footer. */
final class FooterSettingsTest extends TestCase {
	/** Limpia la opción después de cada prueba. */
	protected function tearDown(): void {
		delete_option( 'labm_footer_settings' );
		parent::tearDown();
	}

	/** Los defaults cubren todo el contenido visible aprobado. */
	public function test_footer_defaults_cover_all_visible_content(): void {
		$defaults = labm_core_footer_defaults();
		self::assertSame( 'LABM', $defaults['brand_name'] );
		self::assertSame( "Liga Antioqueña de Balonmano\nInformación institucional administrable.", $defaults['description'] );
		self::assertSame( 'info@balonmanoantioquia.com', $defaults['contact_email'] );
		self::assertSame( '3233212981', $defaults['contact_phone'] );
		self::assertSame( 'Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos, Medellín, Colombia', $defaults['contact_address'] );
		self::assertSame( '© 2026 Liga Antioqueña de Balonmano · Año dinámico', $defaults['copyright'] );
		foreach ( labm_core_footer_field_schema() as $key => $field ) {
			self::assertArrayHasKey( $key, $defaults );
			self::assertContains( $field['type'], array( 'text', 'textarea', 'email', 'url', 'internal_url' ) );
		}
	}

	/** El contacto recupera los defaults exactos con una opcion ausente o parcial. */
	public function test_contact_settings_fallback_is_deterministic_for_missing_or_partial_footer_option(): void {
		$expected_phone   = '3233212981';
		$expected_address = 'Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos, Medellín, Colombia';
		$footer_option    = array();
		$filter           = static function () use ( &$footer_option ) {
			return $footer_option;
		};
		add_filter( 'pre_option_labm_footer_settings', $filter );

		try {
			$fixtures = array(
				'ausente'  => array(),
				'parcial'  => array( 'contact_email' => 'parcial@example.test' ),
			);
			foreach ( $fixtures as $fixture_name => $fixture ) {
				$footer_option = $fixture;
				$settings      = labm_core_get_contact_settings();

				self::assertSame( $expected_phone, $settings['phone'], $fixture_name );
				self::assertSame( $expected_address, $settings['address'], $fixture_name );
			}
		} finally {
			remove_filter( 'pre_option_labm_footer_settings', $filter );
		}
	}

	/** Los destinatarios son privados de SMTP y no se editan desde el footer. */
	public function test_footer_schema_does_not_expose_contact_recipients(): void {
		self::assertArrayNotHasKey( 'contact_recipients', labm_core_footer_field_schema() );
		self::assertArrayNotHasKey( 'contact_recipients', labm_core_footer_defaults() );
	}

	/** El saneado elimina marcado, correos inválidos y esquemas inseguros. */
	public function test_footer_settings_are_sanitized_by_field_type(): void {
		$clean = labm_core_sanitize_footer_settings(
			array(
				'brand_name'       => '<b>LABM</b>',
				'description'      => "Texto <b>visible</b>\nsegunda línea",
				'contact_email'    => 'correo-invalido',
				'contact_phone'    => '<b>3233212981</b>',
				'contact_address'  => '<script>Dirección</script>, Medellín',
				'facebook_url'     => 'javascript:alert(1)',
				'navigation_1_url' => '/nosotros/',
			)
		);
		self::assertSame( 'LABM', $clean['brand_name'] );
		self::assertSame( "Texto visible\nsegunda línea", $clean['description'] );
		self::assertSame( '', $clean['contact_email'] );
		self::assertSame( '3233212981', $clean['contact_phone'] );
		self::assertSame( 'Dirección, Medellín', $clean['contact_address'] );
		self::assertSame( '', $clean['facebook_url'] );
		self::assertSame( '/nosotros/', $clean['navigation_1_url'] );
	}

	/** Los enlaces internos se editan como texto para aceptar rutas relativas. */
	public function test_footer_internal_urls_accept_root_relative_paths(): void {
		$schema = labm_core_footer_field_schema();
		$clean  = labm_core_sanitize_footer_settings( array( 'navigation_1_url' => '/' ) );
		self::assertSame( 'internal_url', $schema['navigation_1_url']['type'] );
		self::assertSame( '/', $clean['navigation_1_url'] );
	}

	/** El render escapa valores, conserva la composición y omite vacíos. */
	public function test_footer_render_uses_saved_values_and_omits_empty_items(): void {
		$settings                       = labm_core_footer_defaults();
		$settings['brand_name']         = 'Liga <script>alert(1)</script>';
		$settings['navigation_1_label'] = '';
		$settings['navigation_1_url']   = '';
		$settings['policy_label']       = 'Tratamiento de datos';
		update_option( 'labm_footer_settings', $settings, false );
		$html = labm_core_render_footer();
		self::assertStringContainsString( 'is-layout-constrained', $html );
		self::assertStringContainsString( 'labm-footer__main', $html );
		self::assertStringContainsString( 'alignwide', $html );
		self::assertStringContainsString( '>Liga</h2>', $html );
		self::assertStringNotContainsString( 'alert(1)', $html );
		self::assertStringNotContainsString( '<script>', $html );
		self::assertStringNotContainsString( '>Inicio</a>', $html );
		self::assertStringContainsString( '>Tratamiento de datos</a>', $html );
	}

	/** La configuración y el menú requieren la capacidad administrativa declarada. */
	public function test_footer_admin_contract_uses_theme_management_capability(): void {
		self::assertSame( 'edit_theme_options', labm_core_footer_capability() );
		self::assertSame( 'edit_theme_options', apply_filters( 'option_page_capability_labm_footer', 'manage_options' ) );
		labm_core_register_footer_settings();
		$registered = get_registered_settings();
		self::assertSame( 'array', $registered['labm_footer_settings']['type'] );
		self::assertSame( 'labm_core_sanitize_footer_settings', $registered['labm_footer_settings']['sanitize_callback'] );
	}
}
