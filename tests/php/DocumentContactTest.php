<?php

use PHPUnit\Framework\TestCase;

final class DocumentContactTest extends TestCase {
	/** @var mixed */
	private $smtp_settings_option;

	/** @var mixed */
	private $smtp_migration_option;

	protected function setUp(): void {
		parent::setUp();
		$this->smtp_settings_option  = get_option( 'labm_smtp_settings', null );
		$this->smtp_migration_option = get_option( 'labm_smtp_recipients_migrated', null );
		update_option( 'labm_smtp_settings', labm_core_smtp_defaults(), false );
		update_option( 'labm_smtp_recipients_migrated', 1, false );
		foreach ( array( 'contacto-prueba-unico', 'contacto-fallo-reintento' ) as $token ) {
			delete_transient( 'labm_contact_' . hash( 'sha256', $token ) );
		}
		$test_documents = get_posts(
			array(
				'post_type'      => 'labm_documento',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => 'labm_test_fixture',
				'meta_value'     => '1',
			)
		);
		foreach ( $test_documents as $test_document ) {
			wp_delete_post( $test_document, true );
		}
	}

	protected function tearDown(): void {
		if ( null === $this->smtp_settings_option ) {
			delete_option( 'labm_smtp_settings' );
		} else {
			update_option( 'labm_smtp_settings', $this->smtp_settings_option, false );
		}
		if ( null === $this->smtp_migration_option ) {
			delete_option( 'labm_smtp_recipients_migrated' );
		} else {
			update_option( 'labm_smtp_recipients_migrated', $this->smtp_migration_option, false );
		}
		parent::tearDown();
	}

	public function test_document_domain_has_permissions_pdf_metadata_and_private_drafts(): void {
		$document = get_post_type_object( 'labm_documento' );
		self::assertInstanceOf( WP_Post_Type::class, $document );
		self::assertTrue( $document->map_meta_cap );
		self::assertTrue( registered_meta_key_exists( 'post', 'labm_documento_pdf_id', 'labm_documento' ) );
		self::assertTrue( registered_meta_key_exists( 'post', 'labm_documento_fecha', 'labm_documento' ) );
		self::assertTrue( get_role( 'editor' )->has_cap( 'publish_labm_documentos' ) );
		self::assertFalse( get_role( 'subscriber' )->has_cap( 'edit_labm_documentos' ) );
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'draft',
				'post_title'  => 'Documento privado',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);
		$query   = labm_core_document_catalog_query( array(), 1, 10 );
		self::assertNotContains( $post_id, wp_list_pluck( $query->posts, 'ID' ) );
	}

	public function test_pdf_validation_checks_real_type_and_size_without_exposing_paths(): void {
		$valid = wp_tempnam( 'labm-valido.pdf' );
		file_put_contents( $valid, "%PDF-1.4\n%%EOF" );
		$invalid = wp_tempnam( 'labm-invalido.pdf' );
		file_put_contents( $invalid, 'contenido de texto' );
		self::assertTrue( labm_core_validate_pdf_file( $valid, 1024 ) );
		$error = labm_core_validate_pdf_file( $invalid, 1024 );
		self::assertInstanceOf( WP_Error::class, $error );
		self::assertStringNotContainsString( dirname( $invalid ), $error->get_error_message() );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_pdf_file( $valid, 4 ) );
		unlink( $valid );
		unlink( $invalid );
	}

	public function test_catalog_ignores_legacy_filters_and_keeps_internal_metadata_private(): void {
		$term = term_exists( 'Circulares de prueba', 'labm_documento_categoria' );
		if ( ! $term ) {
			$term = wp_insert_term( 'Circulares de prueba', 'labm_documento_categoria' );
		}
		$one = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Circular deportiva alfa',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);
		$two = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Circular administrativa beta',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);
		wp_set_object_terms( $one, (int) $term['term_id'], 'labm_documento_categoria' );
		update_post_meta( $one, 'labm_documento_fecha', '2026-03-01' );
		update_post_meta( $two, 'labm_documento_fecha', '2025-03-01' );
		$query = labm_core_document_catalog_query( array( 'texto' => 'deportiva', 'categoria' => (int) $term['term_id'], 'anio' => 2026 ), 1, 10 );
		self::assertNotContains( $one, wp_list_pluck( $query->posts, 'ID' ) );
		self::assertNotContains( $two, wp_list_pluck( $query->posts, 'ID' ) );
		self::assertSame( '', labm_core_document_pdf_url( $one ) );
	}

	/** El catálogo público solo expone documentos completos, en páginas de diez. */
	public function test_document_catalog_is_simple_paginated_and_omits_invalid_attachments(): void {
		for ( $index = 1; $index <= 11; $index++ ) {
			$attachment = wp_insert_attachment(
				array(
					'post_mime_type' => 'application/pdf',
					'post_title'     => 'Adjunto ' . $index,
					'post_status'    => 'inherit',
				)
			);
			$document   = wp_insert_post(
				array(
					'post_type'   => 'labm_documento',
					'post_status' => 'publish',
					'post_title'  => 'Documento ' . $index,
					'post_date'   => sprintf( '2026-01-%02d 12:00:00', $index ),
					'meta_input'  => array(
						'labm_documento_pdf_id' => $attachment,
						'labm_test_fixture'      => 1,
					),
				)
			);
			self::assertIsInt( $document );
		}

		$query = labm_core_document_catalog_query( array( 'texto' => 'no debe filtrar' ), 0 );
		self::assertSame( 10, $query->post_count );
		self::assertGreaterThanOrEqual( 2, (int) $query->max_num_pages );
		self::assertSame( 1, (int) $query->get( 'paged' ) );
		self::assertNotEmpty( get_the_title( $query->posts[0] ) );
		self::assertStringNotContainsString( 'labm-filter', labm_core_render_document_catalog( array(), 1 ) );
	}

	/** El paginador conserva el parámetro de página y renderiza la página solicitada. */
	public function test_document_catalog_pagination_uses_the_current_documentos_page(): void {
		for ( $index = 1; $index <= 11; $index++ ) {
			$attachment = wp_insert_attachment(
				array(
					'post_mime_type' => 'application/pdf',
					'post_title'     => 'Adjunto paginado ' . $index,
					'post_status'    => 'inherit',
				)
			);
			wp_insert_post(
				array(
					'post_type'   => 'labm_documento',
					'post_status' => 'publish',
					'post_title'  => 'Página de documento ' . $index,
					'post_date'   => sprintf( '2026-02-%02d 12:00:00', $index ),
					'meta_input'  => array(
						'labm_documento_pdf_id' => $attachment,
						'labm_test_fixture'      => 1,
					),
				)
			);
		}

		$_GET['pagina'] = '2';
		$html            = labm_core_render_document_catalog( array(), labm_core_document_catalog_current_page() );
		self::assertStringContainsString( 'Página de documento 1', $html );
		self::assertStringContainsString( 'aria-current="page">2', $html );
		self::assertStringContainsString( 'pagina=1', labm_core_document_page_url( 1 ) );
		unset( $_GET['pagina'] );
	}

	/** La reconciliación descarta únicamente importaciones obsoletas y preserva contenido manual. */
	public function test_legal_document_reconciliation_trashes_only_stale_imported_documents(): void {
		$stale = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Importación obsoleta',
				'meta_input'  => array(
					'labm_document_source_key' => 'test-legal-document:obsolete',
					'labm_test_fixture'         => 1,
				),
			)
		);
		$manual = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Documento manual',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);

		$trashed = LABM_Fixtures_Command::reconcile_legal_documents( array( 'test-legal-document:vigente' ), 'test-legal-document:' );
		self::assertSame( array( $stale ), $trashed );
		self::assertSame( 'trash', get_post_status( $stale ) );
		self::assertSame( 'publish', get_post_status( $manual ) );
	}

	/** Los enlaces de documento conservan la previsualización nativa y descarga segura. */
	public function test_document_actions_use_a_safe_new_tab_and_download_endpoint(): void {
		$attachment = wp_insert_attachment(
			array(
				'post_mime_type' => 'application/pdf',
				'post_title'     => 'Adjunto público',
				'post_status'    => 'inherit',
			)
		);
		$document   = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Documento público',
				'meta_input'  => array(
					'labm_documento_pdf_id' => $attachment,
					'labm_test_fixture'      => 1,
				),
			)
		);
		$html       = labm_core_render_document_catalog( array(), 1 );

		self::assertStringContainsString( 'target="_blank"', $html );
		self::assertStringContainsString( 'rel="noopener"', $html );
		self::assertStringContainsString( 'action=labm_document_download', $html );
		self::assertStringContainsString( 'download', $html );
		self::assertStringContainsString( 'Documento público', $html );
		self::assertStringNotContainsString( 'labm_documento_fecha', $html );
		self::assertStringContainsString( 'action=labm_document_download', labm_core_document_download_url( $document ) );
	}

	/** El contrato editorial exige título y PDF, sin fecha administrativa obligatoria. */
	public function test_document_publication_requires_only_title_and_pdf(): void {
		self::assertInstanceOf( WP_Error::class, labm_core_validate_publishable( 'labm_documento', array( 'post_title' => 'Sin PDF' ) ) );
		self::assertTrue(
			labm_core_validate_publishable(
				'labm_documento',
				array(
					'post_title'            => 'Documento completo',
					'labm_documento_pdf_id' => 123,
				)
			)
		);
	}

	public function test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing(): void {
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'application/pdf',
				'post_title'     => 'PDF compartido',
				'post_status'    => 'inherit',
			)
		);
		$first         = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Uno',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);
		$second        = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Dos',
				'meta_input'  => array( 'labm_test_fixture' => 1 ),
			)
		);
		update_post_meta( $first, 'labm_documento_pdf_id', $attachment_id );
		update_post_meta( $second, 'labm_documento_pdf_id', $attachment_id );
		wp_set_current_user( 0 );
		self::assertInstanceOf( WP_Error::class, labm_core_delete_document_attachment( $first, true ) );
		self::assertNotNull( get_post( $attachment_id ) );
		$admin = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
			)
		)[0];
		wp_set_current_user( $admin->ID );
		self::assertFalse( labm_core_delete_document_attachment( $first, true ) );
		self::assertNotNull( get_post( $attachment_id ) );
		wp_delete_post( $first, true );
		wp_delete_post( $second, true );
		wp_delete_attachment( $attachment_id, true );
	}

	public function test_contact_validates_nonce_honeypot_delivery_and_duplicate_token(): void {
		$sent   = array();
		$filter = static function ( $return, $attributes ) use ( &$sent ) {
			$sent[] = $attributes;
			return true;
		};
		add_filter( 'pre_wp_mail', $filter, 10, 2 );
		$data   = array(
			'nombre'    => 'Ana',
			'apellidos' => 'PÃ©rez',
			'correo'    => 'ana@example.test',
			'asunto'    => 'Consulta',
			'mensaje'   => 'Necesito informaciÃ³n.',
			'telefono'  => '',
			'sitio_web' => '',
			'consentimiento' => '1',
			'token'     => 'contacto-prueba-unico',
			'nonce'     => wp_create_nonce( 'labm_contacto' ),
		);
		$result = labm_core_process_contact( $data );
		self::assertTrue( $result['ok'] );
		self::assertCount( 1, $sent );
		self::assertTrue( labm_core_process_contact( $data )['ok'] );
		self::assertCount( 1, $sent );
		$invalid           = $data;
		$invalid['token']  = 'contacto-invalido';
		$invalid['correo'] = 'correo-invalido';
		self::assertArrayHasKey( 'correo', labm_core_process_contact( $invalid )['errors'] );
		$spam              = $data;
		$spam['token']     = 'contacto-spam';
		$spam['sitio_web'] = 'https://spam.example';
		self::assertFalse( labm_core_process_contact( $spam )['ok'] );
		remove_filter( 'pre_wp_mail', $filter, 10 );
	}

	/** El correo de Contacto usa la plantilla institucional HTML y conserva Reply-To. */
	public function test_contact_sends_an_escaped_institutional_html_email(): void {
		$sent   = array();
		$filter = static function ( $return, $attributes ) use ( &$sent ) {
			$sent[] = $attributes;
			return true;
		};
		$data   = array(
			'nombre'        => 'Ana & Co',
			'apellidos'     => 'Pérez',
			'correo'        => 'ana@example.test',
			'asunto'        => 'Consulta de plantilla',
			'mensaje'       => "Primera línea\nSegunda línea <b>sin HTML</b>",
			'telefono'      => '320 123 4567',
			'sitio_web'     => '',
			'consentimiento' => '1',
			'token'         => 'contacto-plantilla-html',
			'nonce'         => wp_create_nonce( 'labm_contacto' ),
		);
		add_filter( 'pre_wp_mail', $filter, 10, 2 );

		try {
			self::assertTrue( labm_core_process_contact( $data )['ok'] );
			self::assertCount( 1, $sent );
			self::assertStringContainsString( 'LABM', $sent[0]['message'] );
			self::assertStringContainsString( '#AECD25', $sent[0]['message'] );
			self::assertStringContainsString( 'src="cid:labm-contact-logo"', $sent[0]['message'] );
			self::assertStringNotContainsString( 'logo-color.jpg', $sent[0]['message'] );
			self::assertStringContainsString( 'Ana &amp; Co', $sent[0]['message'] );
			self::assertStringNotContainsString( '<script>', $sent[0]['message'] );
			self::assertStringContainsString( 'Primera línea<br', $sent[0]['message'] );
			self::assertContains( 'Reply-To: ana@example.test', $sent[0]['headers'] );
			self::assertContains( 'Content-Type: text/html; charset=UTF-8', $sent[0]['headers'] );
		} finally {
			remove_filter( 'pre_wp_mail', $filter, 10 );
			delete_transient( 'labm_contact_' . hash( 'sha256', 'contacto-plantilla-html' ) );
		}
	}

	/** El logo se adjunta por CID solo a los mensajes de Contacto. */
	public function test_contact_email_embeds_its_logo_without_affecting_other_messages(): void {
		// phpcs:disable -- El doble replica de forma deliberada la API pública de PHPMailer.
		$mailer       = new class() {
			public $Body = '';
			private $attachments = array();

			public function addEmbeddedImage( $path, $cid, $name, $encoding, $type ) {
				$this->attachments[] = array( $path, '', $name, $encoding, $type, false, 'inline', $cid );
			}

			public function getAttachments() {
				return $this->attachments;
			}
		};
		$mailer->Body = labm_core_render_contact_email( array() );

		do_action( 'phpmailer_init', $mailer );
		do_action( 'phpmailer_init', $mailer );

		$attachments = $mailer->getAttachments();
		self::assertCount( 1, $attachments );
		self::assertSame( 'labm-contact-logo', $attachments[0][7] );
		self::assertSame( 'image/jpeg', $attachments[0][4] );

		$other_mailer       = new class() {
			public $Body = '';
			private $attachments = array();

			public function addEmbeddedImage( $path, $cid, $name, $encoding, $type ) {
				$this->attachments[] = array( $path, '', $name, $encoding, $type, false, 'inline', $cid );
			}

			public function getAttachments() {
				return $this->attachments;
			}
		};
		$other_mailer->Body = 'Mensaje ajeno a Contacto.';
		do_action( 'phpmailer_init', $other_mailer );
		self::assertCount( 0, $other_mailer->getAttachments() );
		// phpcs:enable
	}

	/** El contrato de Contacto expone solo datos institucionales aptos para la interfaz. */
	public function test_contact_settings_reuse_public_footer_data_without_recipients(): void {
		$settings = labm_core_get_contact_settings();

		self::assertSame( 'info@balonmanoantioquia.com', $settings['email'] );
		self::assertSame( '3233212981', $settings['phone'] );
		self::assertStringStartsWith( 'Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos', $settings['address'] );
		self::assertStringEndsWith( 'Colombia', $settings['address'] );
		self::assertStringContainsString( 'google.com/maps/search/', $settings['map_url'] );
		self::assertArrayHasKey( 'facebook', $settings['socials'] );
		self::assertArrayHasKey( 'instagram', $settings['socials'] );
		self::assertArrayNotHasKey( 'recipients', $settings );
	}

	/** El consentimiento es obligatorio y los destinatarios privados no pasan al contrato pÃºblico. */
	public function test_contact_requires_consent_and_uses_only_the_institutional_recipient_by_default(): void {
		$data = array(
			'nombre'    => 'Ana',
			'apellidos' => 'PÃ©rez',
			'correo'    => 'ana@example.test',
			'asunto'    => 'Consulta',
			'mensaje'   => 'Necesito informaciÃ³n.',
			'telefono'  => '',
			'sitio_web' => '',
			'token'     => 'contacto-consentimiento',
			'nonce'     => wp_create_nonce( 'labm_contacto' ),
		);

		$result = labm_core_process_contact( $data );
		self::assertFalse( $result['ok'] );
		self::assertArrayHasKey( 'consentimiento', $result['errors'] );
		self::assertSame( array( 'info@balonmanoantioquia.com' ), labm_core_contact_recipients() );
	}

	/** Un fallo de correo libera la reserva para permitir un reintento legÃ­timo. */
	/** Las copias solo operan fuera de produccion y nunca integran la salida publica. */
	public function test_contact_test_recipients_are_limited_to_explicit_non_production_environments(): void {
		$previous_recipients = getenv( 'LABM_CONTACT_TEST_RECIPIENTS' );
		putenv( 'LABM_CONTACT_TEST_RECIPIENTS=copia-uno@example.test,correo-invalido,copia-dos@example.test,copia-tres@example.test' );
		$environment = 'production';
		$filter      = static function () use ( &$environment ) {
			return $environment;
		};
		add_filter( 'labm_core_contact_environment', $filter );

		try {
			self::assertSame( array( 'info@balonmanoantioquia.com' ), labm_core_contact_recipients() );

			$environment = 'development';
			self::assertSame(
				array( 'info@balonmanoantioquia.com', 'copia-uno@example.test', 'copia-dos@example.test' ),
				labm_core_contact_recipients()
			);

			$environment = 'ambiguous';
			self::assertSame( array( 'info@balonmanoantioquia.com' ), labm_core_contact_recipients() );
			$html = labm_theme_render_contact();
			self::assertStringNotContainsString( 'copia-uno@example.test', $html );
			self::assertStringNotContainsString( 'copia-dos@example.test', $html );
		} finally {
			remove_filter( 'labm_core_contact_environment', $filter );
			if ( false === $previous_recipients ) {
				putenv( 'LABM_CONTACT_TEST_RECIPIENTS' );
			} else {
				putenv( 'LABM_CONTACT_TEST_RECIPIENTS=' . $previous_recipients );
			}
		}
	}

	/** Los destinatarios configurados se sanean y se combinan con las copias no productivas. */
	public function test_contact_uses_all_configured_recipients_and_preserves_non_production_copies(): void {
		$previous_settings   = get_option( 'labm_smtp_settings', null );
		$previous_recipients = getenv( 'LABM_CONTACT_TEST_RECIPIENTS' );
		$environment         = 'development';
		$filter              = static function () use ( &$environment ) {
			return $environment;
		};

		update_option(
			'labm_smtp_settings',
			array(
				'recipients' => "uno@example.test, correo-invalido\ndos@example.test, uno@example.test",
			)
		);
		putenv( 'LABM_CONTACT_TEST_RECIPIENTS=copia@example.test' );
		add_filter( 'labm_core_contact_environment', $filter );

		try {
			self::assertSame(
				array( 'uno@example.test', 'dos@example.test', 'copia@example.test' ),
				labm_core_contact_recipients()
			);
		} finally {
			remove_filter( 'labm_core_contact_environment', $filter );
			if ( null === $previous_settings ) {
				delete_option( 'labm_smtp_settings' );
			} else {
				update_option( 'labm_smtp_settings', $previous_settings );
			}
			if ( false === $previous_recipients ) {
				putenv( 'LABM_CONTACT_TEST_RECIPIENTS' );
			} else {
				putenv( 'LABM_CONTACT_TEST_RECIPIENTS=' . $previous_recipients );
			}
		}
	}

	public function test_contact_mail_failure_does_not_consume_the_idempotency_token(): void {
		$data = array(
			'nombre'          => 'Ana',
			'apellidos'       => 'PÃ©rez',
			'correo'          => 'ana@example.test',
			'asunto'          => 'Consulta',
			'mensaje'         => 'Necesito informaciÃ³n.',
			'telefono'        => '',
			'sitio_web'       => '',
			'consentimiento'  => '1',
			'token'           => 'contacto-fallo-reintento',
			'nonce'           => wp_create_nonce( 'labm_contacto' ),
		);
		$failure = static function () {
			return false;
		};
		add_filter( 'pre_wp_mail', $failure );
		self::assertFalse( labm_core_process_contact( $data )['ok'] );
		remove_filter( 'pre_wp_mail', $failure );

		$success = static function () {
			return true;
		};
		add_filter( 'pre_wp_mail', $success );
		self::assertTrue( labm_core_process_contact( $data )['ok'] );
		remove_filter( 'pre_wp_mail', $success );
	}

	/** Las solicitudes manipuladas sin token no pueden alcanzar la entrega. */
	public function test_contact_rejects_a_missing_idempotency_token(): void {
		$data = array(
			'nombre'         => 'Ana',
			'apellidos'      => 'Pérez',
			'correo'         => 'ana@example.test',
			'asunto'         => 'Consulta',
			'mensaje'        => 'Necesito información.',
			'consentimiento' => '1',
			'nonce'          => wp_create_nonce( 'labm_contacto' ),
		);
		$sent = false;
		$filter = static function () use ( &$sent ) {
			$sent = true;
			return true;
		};
		add_filter( 'pre_wp_mail', $filter );

		try {
			$result = labm_core_process_contact( $data );
			self::assertFalse( $result['ok'] );
			self::assertArrayHasKey( 'token', $result['errors'] );
			self::assertFalse( $sent );
		} finally {
			remove_filter( 'pre_wp_mail', $filter );
		}
	}

	/** El consentimiento enlaza a la política de privacidad aplicable. */
	/** El estado PRG conserva el identificador opaco generado con mayúsculas. */
	public function test_contact_prg_state_consumes_a_case_sensitive_opaque_identifier(): void {
		$state_id = 'AbCdEfGhIjKlMnOpQrStUvWxYz012345';
		$key      = 'labm_contact_state_' . hash( 'sha256', $state_id );
		$state    = array( 'ok' => false, 'errors' => array( 'nombre' ) );
		set_transient( $key, $state, 10 * MINUTE_IN_SECONDS );

		self::assertSame( $state, labm_core_contact_consume_state( $state_id ) );
		self::assertFalse( (bool) get_transient( $key ) );
	}

	/** El fallo de entrega muestra un aviso seguro, sin confundirse con errores de campos. */
	public function test_contact_renders_a_safe_delivery_error_separate_from_field_validation(): void {
		$state_id = 'DeliveryFailureState012345678901';
		$key      = 'labm_contact_state_' . hash( 'sha256', $state_id );
		set_transient(
			$key,
			array(
				'ok'     => false,
				'errors' => array( 'delivery' ),
			),
			10 * MINUTE_IN_SECONDS
		);
		$_GET['contacto_estado'] = $state_id;

		try {
			$html = labm_theme_render_contact();
			self::assertStringContainsString( 'No pudimos enviar el mensaje en este momento. Inténtalo de nuevo más tarde.', $html );
			self::assertStringNotContainsString( 'Revisa los campos marcados e inténtalo de nuevo.', $html );
			self::assertStringNotContainsString( 'SMTP', $html );
			self::assertStringNotContainsString( '<ul>', $html );
		} finally {
			unset( $_GET['contacto_estado'] );
			delete_transient( $key );
		}
	}

	public function test_contact_privacy_notice_renders_a_navigable_link(): void {
		$html = labm_theme_render_contact();

		self::assertMatchesRegularExpression( '/<a href="[^"]+">política de privacidad<\/a>/u', $html );
	}
}
