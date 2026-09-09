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
		self::assertSame( '© 2026 Liga Antioqueña de Balonmano · Año dinámico', $defaults['copyright'] );
		foreach ( labm_core_footer_field_schema() as $key => $field ) {
			self::assertArrayHasKey( $key, $defaults );
			self::assertContains( $field['type'], array( 'text', 'textarea', 'email', 'url' ) );
		}
	}

	/** El saneado elimina marcado, correos inválidos y esquemas inseguros. */
	public function test_footer_settings_are_sanitized_by_field_type(): void {
		$clean = labm_core_sanitize_footer_settings(
			array(
				'brand_name'       => '<b>LABM</b>',
				'description'      => "Texto <b>visible</b>\nsegunda línea",
				'contact_email'    => 'correo-invalido',
				'facebook_url'     => 'javascript:alert(1)',
				'navigation_1_url' => '/nosotros/',
			)
		);
		self::assertSame( 'LABM', $clean['brand_name'] );
		self::assertSame( "Texto visible\nsegunda línea", $clean['description'] );
		self::assertSame( '', $clean['contact_email'] );
		self::assertSame( '', $clean['facebook_url'] );
		self::assertSame( '/nosotros/', $clean['navigation_1_url'] );
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
