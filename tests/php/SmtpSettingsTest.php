<?php
/**
 * Pruebas de la configuracion SMTP de LABM.
 *
 * @package LABM_Core
 */

use PHPUnit\Framework\TestCase;

/** Prueba el contrato sin secretos de SMTP. */
final class SmtpSettingsTest extends TestCase {
	/** Elimina configuracion persistida antes de cada caso. */
	protected function setUp(): void {
		parent::setUp();
		delete_option( 'labm_smtp_settings' );
		delete_option( 'labm_smtp_recipients_migrated' );
		delete_option( 'labm_footer_settings' );
	}

	/** Elimina configuracion persistida despues de cada caso. */
	protected function tearDown(): void {
		delete_option( 'labm_smtp_settings' );
		delete_option( 'labm_smtp_recipients_migrated' );
		delete_option( 'labm_footer_settings' );
		parent::tearDown();
	}

	/** La opcion elimina claves ajenas y no tiene contrasena. */
	public function test_smtp_settings_use_a_closed_non_secret_schema(): void {
		$settings = labm_core_sanitize_smtp_settings(
			array(
				'host'       => ' smtp.gmail.com ',
				'port'       => '587',
				'encryption' => 'tls',
				'from_email' => ' remitente@example.test ',
				'from_name'  => ' LABM ',
				'recipients' => " destino@example.test,\notro@example.test ",
				'password'   => 'nunca-se-guarda',
				'unexpected' => 'descartar',
			)
		);

		self::assertSame(
			array(
				'host'       => 'smtp.gmail.com',
				'port'       => 587,
				'encryption' => 'tls',
				'from_email' => 'remitente@example.test',
				'from_name'  => 'LABM',
				'recipients' => 'destino@example.test, otro@example.test',
			),
			$settings
		);
		self::assertFalse( array_key_exists( 'password', $settings ) );
		self::assertFalse( array_key_exists( 'unexpected', $settings ) );
		self::assertIsBool( labm_core_smtp_password_available() );
	}

	/** Los destinatarios historicos del footer pasan una sola vez al panel SMTP. */
	public function test_smtp_migrates_legacy_footer_recipients_without_losing_existing_smtp_recipient(): void {
		$legacy_footer = static function () {
			return array( 'contact_recipients' => 'footer-uno@example.test, footer-dos@example.test' );
		};
		add_filter( 'pre_option_labm_footer_settings', $legacy_footer );
		add_option(
			'labm_smtp_settings',
			array( 'recipient' => 'smtp-existente@example.test' ),
			'',
			false
		);

		try {
			$settings = labm_core_get_smtp_settings();

			self::assertEqualsCanonicalizing(
				array( 'smtp-existente@example.test', 'footer-uno@example.test', 'footer-dos@example.test' ),
				array_filter( array_map( 'trim', explode( ',', $settings['recipients'] ) ) )
			);
			self::assertEqualsCanonicalizing(
				array( 'smtp-existente@example.test', 'footer-uno@example.test', 'footer-dos@example.test' ),
				labm_core_smtp_recipients()
			);
			self::assertTrue( (bool) get_option( 'labm_smtp_recipients_migrated', false ) );
		} finally {
			remove_filter( 'pre_option_labm_footer_settings', $legacy_footer );
		}
	}

	/** La previsualizacion usa datos ficticios y no depende de secretos SMTP. */
	public function test_contact_email_preview_is_static_and_has_no_sensitive_configuration(): void {
		$preview = labm_core_contact_email_preview();

		self::assertStringContainsString( 'Mariana Gómez', $preview );
		self::assertStringContainsString( 'mariana@example.test', $preview );
		self::assertStringContainsString( 'logo-color.jpg', $preview );
		self::assertStringNotContainsString( 'cid:labm-contact-logo', $preview );
		self::assertStringNotContainsString( 'LABM_SMTP_PASSWORD', $preview );
		self::assertStringNotContainsString( 'smtp.gmail.com', $preview );
	}
}
