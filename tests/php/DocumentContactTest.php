<?php

use PHPUnit\Framework\TestCase;

final class DocumentContactTest extends TestCase {
	/** @var mixed */
	private $smtp_settings_option;

	/** @var mixed */
	private $smtp_migration_option;

	/** @var int[] */
	private array $document_admin_users = array();

	/** @var int[] */
	private array $document_admin_attachments = array();

	/** @var string[] */
	private array $document_admin_files = array();

	protected function setUp(): void {
		parent::setUp();
		$this->smtp_settings_option  = get_option( 'labm_smtp_settings', null );
		$this->smtp_migration_option = get_option( 'labm_smtp_recipients_migrated', null );
		update_option( 'labm_smtp_settings', labm_core_smtp_defaults(), false );
		update_option( 'labm_smtp_recipients_migrated', 1, false );
		foreach ( array( 'contacto-prueba-unico', 'contacto-fallo-reintento' ) as $token ) {
			$key = 'labm_contact_' . hash( 'sha256', $token );
			delete_transient( $key );
			delete_option( $key . '_lock' );
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
		foreach ( array( 'contacto-prueba-unico', 'contacto-fallo-reintento' ) as $token ) {
			$key = 'labm_contact_' . hash( 'sha256', $token );
			delete_transient( $key );
			delete_option( $key . '_lock' );
		}
		foreach ( $this->document_admin_attachments as $attachment_id ) {
			wp_delete_attachment( $attachment_id, true );
		}
		foreach ( $this->document_admin_files as $file ) {
			if ( file_exists( $file ) ) {
				chmod( $file, 0644 );
				unlink( $file );
			}
		}
		foreach ( $this->document_admin_users as $user_id ) {
			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}
			wp_delete_user( $user_id );
		}
		wp_set_current_user( 0 );
		parent::tearDown();
	}

	/** Crea un usuario aislado para los contratos administrativos RED. */
	private function create_document_admin_user( string $role ): int {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'labm-document-admin-' . $role . '-' . wp_generate_password( 8, false ),
				'user_pass'  => wp_generate_password( 20 ),
				'user_email' => wp_generate_uuid4() . '@example.invalid',
				'role'       => $role,
			)
		);
		self::assertIsInt( $user_id );
		$this->document_admin_users[] = $user_id;
		return $user_id;
	}

	/** Crea un Documento marcado como fixture y permite estado historico. */
	private function create_document_admin_post( array $overrides = array() ): int {
		$post_id = wp_insert_post(
			array_merge(
				array(
					'post_type'   => 'labm_documento',
					'post_status' => 'draft',
					'post_title'  => 'Documento administrativo de prueba',
					'meta_input'  => array( 'labm_test_fixture' => 1 ),
				),
				$overrides
			)
		);
		self::assertIsInt( $post_id );
		return $post_id;
	}

	/**
	 * Crea un adjunto temporal con combinaciones controladas de contenido y MIME.
	 *
	 * @param string $variant valid, false-signature, false-mime o unreadable.
	 */
	private function create_document_admin_attachment( string $variant = 'valid' ): int {
		$temp = wp_tempnam( 'labm-document-admin-' . $variant );
		self::assertIsString( $temp );
		$file = $temp . '.pdf';
		self::assertTrue( rename( $temp, $file ) );
		$content = 'false-signature' === $variant ? "no es un pdf\n" : "%PDF-1.7\n%%EOF\n";
		self::assertNotFalse( file_put_contents( $file, $content ) );
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'false-mime' === $variant ? 'text/plain' : 'application/pdf',
				'post_title'     => 'Adjunto ' . $variant,
				'post_status'    => 'inherit',
			),
			$file
		);
		self::assertIsInt( $attachment_id );
		update_attached_file( $attachment_id, $file );
		if ( 'unreadable' === $variant ) {
			self::assertTrue( unlink( $file ) );
		}
		$this->document_admin_files[]       = $file;
		$this->document_admin_attachments[] = $attachment_id;
		return $attachment_id;
	}

	/** Exige el punto de entrada futuro sin provocar un fatal no controlado. */
	private function require_document_admin_contract( string $function ): void {
		self::assertTrue( function_exists( $function ), 'Falta implementar el contrato administrativo ' . $function . '().' );
	}

	/** Ejecuta la validacion futura del estado efectivo. */
	private function validate_document_admin_state( int $post_id, array $input, int $user_id ) {
		$this->require_document_admin_contract( 'labm_core_document_admin_validate_state' );
		return labm_core_document_admin_validate_state( $post_id, $input, $user_id );
	}

	/** Ejecuta el guardado futuro por el canal solicitado. */
	private function save_document_admin_state( int $post_id, array $input, int $user_id, string $channel ) {
		$this->require_document_admin_contract( 'labm_core_document_admin_save_state' );
		return labm_core_document_admin_save_state( $post_id, $input, $user_id, $channel );
	}

	/** 1.1: los builders representan todas las entradas especiales del contrato. */
	public function test_document_admin_fixture_builders_cover_validation_inputs(): void {
		$editor          = $this->create_document_admin_user( 'editor' );
		$valid           = $this->create_document_admin_attachment();
		$false_signature = $this->create_document_admin_attachment( 'false-signature' );
		$false_mime      = $this->create_document_admin_attachment( 'false-mime' );
		$unreadable      = $this->create_document_admin_attachment( 'unreadable' );
		$post_id         = $this->create_document_admin_post();
		update_post_meta( $post_id, 'labm_documento_pdf_id', (string) $valid );

		self::assertSame( 'editor', get_userdata( $editor )->roles[0] );
		self::assertSame( (string) $valid, get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
		self::assertSame( 'text/plain', get_post_mime_type( $false_mime ) );
		self::assertStringNotContainsString( '%PDF-', (string) file_get_contents( get_attached_file( $false_signature ) ) );
		self::assertFalse( is_file( get_attached_file( $unreadable ) ) );
		$this->require_document_admin_contract( 'labm_core_document_admin_validate_state' );
	}

	/** V1.1: un estado completo se acepta por REST. */
	public function test_document_admin_accepts_valid_rest_state(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$result        = $this->save_document_admin_state( 0, array( 'post_title' => 'Acta valida', 'labm_documento_pdf_id' => $attachment_id ), $user_id, 'rest' );
		self::assertIsInt( $result );
	}

	/** V1.2: una actualizacion parcial usa los valores persistidos. */
	public function test_document_admin_builds_effective_state_for_partial_update(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->create_document_admin_post( array( 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => $attachment_id ) ) );
		$result        = $this->validate_document_admin_state( $post_id, array( 'labm_documento_fecha' => '2026-09-15' ), $user_id );
		self::assertTrue( $result );
	}

	/** V1.3: falta de titulo bloquea sin mutacion parcial. */
	public function test_document_admin_rejects_missing_required_data_atomically(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$post_id = $this->create_document_admin_post( array( 'post_title' => 'Titulo anterior' ) );
		$result  = $this->save_document_admin_state( $post_id, array( 'post_title' => '', 'labm_documento_fecha' => '2026-09-15' ), $user_id, 'classic' );
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( 'Titulo anterior', get_post_field( 'post_title', $post_id ) );
		self::assertSame( '', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
	}

	/** V2.1: un PDF autentico, legible y dentro del limite se acepta. */
	public function test_document_admin_accepts_authentic_pdf(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$result        = $this->validate_document_admin_state( 0, array( 'post_title' => 'PDF autentico', 'labm_documento_pdf_id' => $attachment_id ), $user_id );
		self::assertTrue( $result );
	}

	/** V2.2: no se revela informacion de un adjunto inaccesible. */
	public function test_document_admin_rejects_inaccessible_attachment_without_path_leak(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$owner_id      = $this->create_document_admin_user( 'administrator' );
		$attachment_id = $this->create_document_admin_attachment();
		wp_update_post( array( 'ID' => $attachment_id, 'post_author' => $owner_id ) );
		$user = get_userdata( $user_id );
		$user->add_cap( 'edit_others_posts', false );
		self::assertTrue( user_can( $user_id, 'edit_labm_documentos' ) );
		self::assertFalse( user_can( $user_id, 'edit_post', $attachment_id ) );
		$result        = $this->validate_document_admin_state( 0, array( 'post_title' => 'Sin acceso', 'labm_documento_pdf_id' => $attachment_id ), $user_id );
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( 'labm_document_pdf_forbidden', $result->get_error_code() );
		self::assertStringNotContainsString( dirname( get_attached_file( $attachment_id ) ), $result->get_error_message() );
	}

	/** V2.3: ID, lectura, MIME, firma y limite invalidos se rechazan. */
	public function test_document_admin_rejects_all_invalid_pdf_variants(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		foreach ( array( 99999999, $this->create_document_admin_attachment( 'unreadable' ), $this->create_document_admin_attachment( 'false-mime' ), $this->create_document_admin_attachment( 'false-signature' ) ) as $attachment_id ) {
			self::assertInstanceOf( WP_Error::class, $this->validate_document_admin_state( 0, array( 'post_title' => 'PDF invalido', 'labm_documento_pdf_id' => $attachment_id ), $user_id ) );
		}
		$this->require_document_admin_contract( 'labm_core_document_admin_effective_max_bytes' );
		self::assertSame( min( 30 * MB_IN_BYTES, wp_max_upload_size() ), labm_core_document_admin_effective_max_bytes() );
	}

	/** V3.1: un editor autenticado guarda exclusivamente valores saneados. */
	public function test_document_admin_authorized_request_sanitizes_values(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->save_document_admin_state( 0, array( 'post_title' => '  Acta segura  ', 'labm_documento_pdf_id' => (string) $attachment_id ), $user_id, 'rest' );
		self::assertSame( 'Acta segura', get_post_field( 'post_title', $post_id ) );
		self::assertSame( $attachment_id, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
	}

	/** V3.2: contenido activo o formatos inesperados no se persisten. */
	public function test_document_admin_rejects_unexpected_active_values(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$result        = $this->save_document_admin_state( 0, array( 'post_title' => '<script>alert(1)</script>', 'labm_documento_pdf_id' => $attachment_id, 'labm_documento_fecha' => 'javascript:alert(1)' ), $user_id, 'rest' );
		self::assertInstanceOf( WP_Error::class, $result );
	}

	/** V3.3: capacidad o nonce invalidos preservan todo el estado. */
	public function test_document_admin_unauthorized_classic_request_is_atomic(): void {
		$user_id       = $this->create_document_admin_user( 'subscriber' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->create_document_admin_post( array( 'post_title' => 'Original' ) );
		$result        = $this->save_document_admin_state( $post_id, array( 'post_title' => 'Alterado', 'labm_documento_pdf_id' => $attachment_id, '_labm_document_nonce' => 'invalido' ), $user_id, 'classic' );
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( 'Original', get_post_field( 'post_title', $post_id ) );
	}

	/** El formulario clásico autorizado rechaza su nonce propio antes de mutar. */
	public function test_document_admin_classic_adapter_rejects_nonce_before_real_update(): void {
		$user_id = $this->create_document_admin_user( 'administrator' );
		$pdf_id  = $this->create_document_admin_attachment();
		$post_id = $this->create_document_admin_post( array( 'post_title' => 'Original nonce', 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => $pdf_id ) ) );
		$original_post   = $_POST;
		$original_method = $_SERVER['REQUEST_METHOD'] ?? null;
		wp_set_current_user( $user_id );
		try {
			$_SERVER['REQUEST_METHOD'] = 'POST';
			$_POST = array( 'action' => 'editpost', '_labm_document_nonce' => 'invalido', 'labm_documento_pdf_id' => $pdf_id );
			$data = array( 'ID' => $post_id, 'post_type' => 'labm_documento', 'post_title' => 'No guardar' );
			$error = labm_core_document_admin_classic_pre_insert( $data, $data );
			self::assertInstanceOf( WP_Error::class, $error );
			self::assertSame( 'labm_document_nonce_invalid', $error->get_error_code() );
			$result = wp_update_post( $data, true );
			self::assertInstanceOf( WP_Error::class, $result );
			self::assertSame( 'empty_content', $result->get_error_code() );
			self::assertSame( 'Original nonce', get_post_field( 'post_title', $post_id ) );
			self::assertSame( $pdf_id, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
		} finally {
			$_POST = $original_post;
			if ( null === $original_method ) { unset( $_SERVER['REQUEST_METHOD'] ); } else { $_SERVER['REQUEST_METHOD'] = $original_method; }
			unset( $GLOBALS['labm_core_document_admin_pending_classic_state'] );
		}
	}

	/** Un POST clásico válido persiste metadatos mediante los hooks de WordPress. */
	public function test_document_admin_classic_adapter_persists_real_update(): void {
		$user_id = $this->create_document_admin_user( 'administrator' );
		$pdf_id  = $this->create_document_admin_attachment();
		$post_id = $this->create_document_admin_post( array( 'post_title' => 'Original clásico' ) );
		$original_post   = $_POST;
		$original_method = $_SERVER['REQUEST_METHOD'] ?? null;
		wp_set_current_user( $user_id );
		try {
			$_SERVER['REQUEST_METHOD'] = 'POST';
			$_POST = array( 'action' => 'editpost', '_labm_document_nonce' => wp_create_nonce( 'labm_document_admin_save' ), 'labm_documento_pdf_id' => $pdf_id, 'labm_documento_fecha' => '2026-09-17' );
			$result = wp_update_post( array( 'ID' => $post_id, 'post_title' => 'Clásico válido' ), true );
			self::assertSame( $post_id, $result );
			self::assertSame( 'Clásico válido', get_post_field( 'post_title', $post_id ) );
			self::assertSame( $pdf_id, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
			self::assertSame( '2026-09-17', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
			self::assertSame( array( 'documento-general' ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'slugs' ) ) );
			$_POST['labm_documento_fecha'] = '2026-02-30';
			$invalid = wp_update_post( array( 'ID' => $post_id, 'post_title' => 'No persistir fecha' ), true );
			self::assertInstanceOf( WP_Error::class, $invalid );
			self::assertSame( 'Clásico válido', get_post_field( 'post_title', $post_id ) );
			self::assertSame( '2026-09-17', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
			$_POST = array();
			self::assertSame( $post_id, wp_update_post( array( 'ID' => $post_id, 'post_title' => 'API interna sin formulario' ), true ) );
		} finally {
			$_POST = $original_post;
			if ( null === $original_method ) { unset( $_SERVER['REQUEST_METHOD'] ); } else { $_SERVER['REQUEST_METHOD'] = $original_method; }
			unset( $GLOBALS['labm_core_document_admin_pending_classic_state'] );
		}
	}

	/** V4.1: fecha calendario valida se conserva como Y-m-d. */
	public function test_document_admin_persists_valid_document_date(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->save_document_admin_state( 0, array( 'post_title' => 'Con fecha', 'labm_documento_pdf_id' => $attachment_id, 'labm_documento_fecha' => '2024-02-29' ), $user_id, 'rest' );
		self::assertSame( '2024-02-29', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
	}

	/** V4.2: fecha vacia sigue siendo opcional. */
	public function test_document_admin_accepts_empty_document_date(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$result        = $this->validate_document_admin_state( 0, array( 'post_title' => 'Sin fecha', 'labm_documento_pdf_id' => $attachment_id, 'labm_documento_fecha' => '' ), $user_id );
		self::assertTrue( $result );
	}

	/** V4.3: fecha imposible bloquea REST y clasico sin mutacion. */
	public function test_document_admin_rejects_impossible_date_atomically(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->create_document_admin_post( array( 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => $attachment_id, 'labm_documento_fecha' => '2026-01-01' ) ) );
		foreach ( array( 'rest', 'classic' ) as $channel ) {
			$result = $this->save_document_admin_state( $post_id, array( 'labm_documento_fecha' => '2026-02-30' ), $user_id, $channel );
			self::assertInstanceOf( WP_Error::class, $result );
			self::assertSame( '2026-01-01', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		}
	}

	/** C1.1: se persiste exactamente un tipo existente. */
	public function test_document_admin_assigns_exactly_one_existing_type(): void {
		$this->require_document_admin_contract( 'labm_core_document_admin_seed_types' );
		labm_core_document_admin_seed_types();
		$term    = get_term_by( 'slug', 'acta', 'labm_documento_categoria' );
		$user_id = $this->create_document_admin_user( 'editor' );
		$pdf_id  = $this->create_document_admin_attachment();
		$post_id = $this->save_document_admin_state( 0, array( 'post_title' => 'Acta', 'labm_documento_pdf_id' => $pdf_id, 'labm_documento_categoria' => array( $term->term_id ) ), $user_id, 'rest' );
		self::assertSame( array( $term->term_id ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'ids' ) ) );
	}

	/** C1.2: un nuevo Documento sin tipo recibe Documento general. */
	public function test_document_admin_defaults_new_document_to_general_type(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$pdf_id  = $this->create_document_admin_attachment();
		$post_id = $this->save_document_admin_state( 0, array( 'post_title' => 'General', 'labm_documento_pdf_id' => $pdf_id ), $user_id, 'rest' );
		self::assertSame( array( 'documento-general' ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'slugs' ) ) );
	}

	/** C1.3: tipo multiple o desconocido no altera la asociacion previa. */
	public function test_document_admin_rejects_multiple_or_unknown_types_atomically(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$pdf_id = $this->create_document_admin_attachment();
		$post_id = $this->create_document_admin_post( array( 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => $pdf_id ) ) );
		$general = get_term_by( 'slug', 'documento-general', 'labm_documento_categoria' );
		$acta = get_term_by( 'slug', 'acta', 'labm_documento_categoria' );
		wp_set_object_terms( $post_id, array( $general->term_id ), 'labm_documento_categoria', false );
		self::assertTrue( $this->validate_document_admin_state( $post_id, array(), $user_id ) );
		foreach ( array( 'labm_document_multiple_types' => array( $general->term_id, $acta->term_id ), 'labm_document_type_invalid' => array( 99999999 ) ) as $code => $terms ) {
			$result = $this->save_document_admin_state( $post_id, array( 'labm_documento_categoria' => $terms ), $user_id, 'rest' );
			self::assertInstanceOf( WP_Error::class, $result );
			self::assertSame( $code, $result->get_error_code() );
			self::assertSame( array( $general->term_id ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'ids' ) ) );
		}
	}

	/** Un histórico publicado con PDF perdido conserva publicación y valores. */
	public function test_document_admin_invalid_published_history_is_preserved_until_repaired(): void {
		$user_id = $this->create_document_admin_user( 'administrator' );
		$pdf_id = $this->create_document_admin_attachment( 'unreadable' );
		$post_id = $this->create_document_admin_post( array( 'post_status' => 'publish', 'post_title' => 'Histórico publicado', 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => (string) $pdf_id, 'labm_documento_fecha' => '2024-02-29' ) ) );
		$before = array( get_post_status( $post_id ), get_post_field( 'post_title', $post_id ), get_post_meta( $post_id, 'labm_documento_pdf_id', true ), get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		labm_core_document_admin_seed_types();
		wp_set_current_user( $user_id );
		$request = new WP_REST_Request( 'POST', '/wp/v2/labm_documento/' . $post_id );
		$request->set_param( 'title', 'No persistir histórico' );
		$result = rest_do_request( $request );
		self::assertSame( 400, $result->get_status() );
		self::assertSame( 'labm_document_pdf_unreadable', $result->get_data()['code'] );
		self::assertSame( $before, array( get_post_status( $post_id ), get_post_field( 'post_title', $post_id ), get_post_meta( $post_id, 'labm_documento_pdf_id', true ), get_post_meta( $post_id, 'labm_documento_fecha', true ) ) );
		$valid_pdf = $this->create_document_admin_attachment();
		$repair = new WP_REST_Request( 'POST', '/wp/v2/labm_documento/' . $post_id );
		$repair->set_param( 'meta', array( 'labm_documento_pdf_id' => $valid_pdf ) );
		$repaired = rest_do_request( $repair );
		self::assertSame( 200, $repaired->get_status(), wp_json_encode( $repaired->get_data() ) );
		self::assertSame( 'publish', get_post_status( $post_id ) );
		self::assertSame( 'Histórico publicado', get_post_field( 'post_title', $post_id ) );
		self::assertSame( $valid_pdf, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
		self::assertSame( '2024-02-29', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		$empty_title = new WP_REST_Request( 'POST', '/wp/v2/labm_documento/' . $post_id );
		$empty_title->set_param( 'title', '' );
		$empty_title->set_param( 'meta', array( 'labm_documento_fecha' => '2026-09-17' ) );
		$rejected = rest_do_request( $empty_title );
		self::assertSame( 400, $rejected->get_status() );
		self::assertSame( 'labm_document_title_required', $rejected->get_data()['code'] );
		self::assertSame( 'Histórico publicado', get_post_field( 'post_title', $post_id ) );
		self::assertSame( '2024-02-29', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
	}

	/** C2.1: administradores pueden gestionar el vocabulario. */
	public function test_document_admin_administrator_can_manage_types(): void {
		$admin_id = $this->create_document_admin_user( 'administrator' );
		wp_set_current_user( $admin_id );
		self::assertTrue( current_user_can( 'manage_labm_documento_types' ) );
	}

	/** C2.2: editores pueden asignar tipos existentes. */
	public function test_document_admin_editor_can_assign_existing_types(): void {
		$editor_id = $this->create_document_admin_user( 'editor' );
		wp_set_current_user( $editor_id );
		self::assertTrue( current_user_can( 'assign_labm_documento_types' ) );
		self::assertFalse( current_user_can( 'manage_labm_documento_types' ) );
	}

	/** C2.3: editores no pueden crear, renombrar ni retirar tipos. */
	public function test_document_admin_editor_cannot_manage_types(): void {
		$editor_id = $this->create_document_admin_user( 'editor' );
		wp_set_current_user( $editor_id );
		$before = wp_count_terms( array( 'taxonomy' => 'labm_documento_categoria', 'hide_empty' => false ) );
		$result = wp_insert_term( 'Tipo no autorizado', 'labm_documento_categoria' );
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( $before, wp_count_terms( array( 'taxonomy' => 'labm_documento_categoria', 'hide_empty' => false ) ) );
	}

	/** C3.1: la siembra es idempotente y crea los nueve tipos definidos. */
	public function test_document_admin_type_seed_is_idempotent(): void {
		$this->require_document_admin_contract( 'labm_core_document_admin_seed_types' );
		labm_core_document_admin_seed_types();
		labm_core_document_admin_seed_types();
		$names = get_terms( array( 'taxonomy' => 'labm_documento_categoria', 'hide_empty' => false, 'fields' => 'names' ) );
		foreach ( array( 'Documento general', 'Acta', 'Certificado', 'Circular', 'Resolución', 'Reglamento', 'Informe', 'Convocatoria', 'Otro' ) as $expected ) {
			self::assertContains( $expected, $names );
		}
	}

	/** C3.2: un ID historico numerico almacenado como texto se resuelve. */
	public function test_document_admin_accepts_historical_text_attachment_id(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->create_document_admin_post();
		update_post_meta( $post_id, 'labm_documento_pdf_id', (string) $attachment_id );
		$result = $this->validate_document_admin_state( $post_id, array(), $user_id );
		self::assertTrue( $result );
	}

	/** C3.3: el fallback general administrativo no muta un historico al abrirlo. */
	public function test_document_admin_general_fallback_does_not_mutate_historical_document(): void {
		$post_id = $this->create_document_admin_post();
		$this->require_document_admin_contract( 'labm_core_document_admin_type_state' );
		$state = labm_core_document_admin_type_state( $post_id );
		self::assertSame( 'documento-general', $state['slug'] );
		self::assertTrue( $state['fallback'] );
		self::assertSame( array(), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'ids' ) ) );
	}

	/** La configuracion administrativa describe el PDF sin filtrar URL ni ruta. */
	public function test_document_admin_config_exposes_safe_attachment_state(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		$post_id       = $this->create_document_admin_post(
			array(
				'meta_input' => array(
					'labm_test_fixture'       => 1,
					'labm_documento_pdf_id'   => $attachment_id,
					'labm_documento_fecha'    => '2026-09-15',
				),
			)
		);
		labm_core_document_admin_seed_types();
		$term = get_term_by( 'slug', 'acta', 'labm_documento_categoria' );
		self::assertInstanceOf( WP_Term::class, $term );
		wp_set_object_terms( $post_id, array( $term->term_id ), 'labm_documento_categoria', false );
		wp_set_current_user( $user_id );

		$previous_post   = $GLOBALS['post'] ?? null;
		$GLOBALS['post'] = get_post( $post_id );
		$config          = labm_core_document_admin_config();
		$GLOBALS['post'] = $previous_post;

		self::assertSame( $post_id, $config['postId'] );
		self::assertSame( $term->term_id, $config['generalTermId'] );
		self::assertSame( $attachment_id, $config['attachment']['id'] );
		self::assertSame( wp_basename( get_attached_file( $attachment_id ) ), $config['attachment']['name'] );
		self::assertGreaterThan( 0, $config['attachment']['size'] );
		self::assertNotSame( '', $config['attachment']['sizeLabel'] );
		self::assertTrue( $config['attachment']['valid'] );
		self::assertSame( 1, wp_verify_nonce( $config['restNonce'], 'wp_rest' ) );
		self::assertContains( 'Acta', wp_list_pluck( $config['terms'], 'name' ) );
		self::assertArrayNotHasKey( 'url', $config['attachment'] );
		self::assertArrayNotHasKey( 'path', $config['attachment'] );
	}

	/** REST real cubre creacion stdClass, fallback y actualizacion atomica. */
	public function test_document_admin_rest_controller_persists_effective_state(): void {
		$user_id       = $this->create_document_admin_user( 'editor' );
		$attachment_id = $this->create_document_admin_attachment();
		wp_set_current_user( $user_id );
		labm_core_document_admin_seed_types();

		$create = new WP_REST_Request( 'POST', '/wp/v2/labm_documento' );
		$create->set_body_params(
			array(
				'title' => 'Documento REST real',
				'status' => 'draft',
				'meta'   => array(
					'labm_documento_pdf_id' => $attachment_id,
					'labm_documento_fecha'  => '2026-09-15',
				),
			)
		);
		$created = rest_do_request( $create );
		self::assertSame( 201, $created->get_status(), wp_json_encode( $created->get_data() ) );
		$created_data = $created->get_data();
		$post_id      = (int) $created_data['id'];
		update_post_meta( $post_id, 'labm_test_fixture', 1 );
		self::assertSame( $attachment_id, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
		self::assertSame( '2026-09-15', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		self::assertSame( array( 'documento-general' ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'slugs' ) ) );

		$term = get_term_by( 'slug', 'acta', 'labm_documento_categoria' );
		self::assertInstanceOf( WP_Term::class, $term );
		$update = new WP_REST_Request( 'PUT', '/wp/v2/labm_documento/' . $post_id );
		$update->set_body_params(
			array(
				'title'                       => 'Documento REST actualizado',
				'meta'                        => array( 'labm_documento_fecha' => '2026-09-16' ),
				'labm_documento_categoria'    => array( $term->term_id ),
			)
		);
		$updated = rest_do_request( $update );
		self::assertSame( 200, $updated->get_status(), wp_json_encode( $updated->get_data() ) );
		self::assertSame( 'Documento REST actualizado', get_post_field( 'post_title', $post_id ) );
		self::assertSame( $attachment_id, (int) get_post_meta( $post_id, 'labm_documento_pdf_id', true ) );
		self::assertSame( '2026-09-16', get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		self::assertSame( array( 'acta' ), wp_get_object_terms( $post_id, 'labm_documento_categoria', array( 'fields' => 'slugs' ) ) );
	}

	/** C4.1: corregir tras un fallo actualiza el mismo Documento. */
	public function test_document_admin_correction_reuses_existing_document(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$post_id = $this->create_document_admin_post();
		$pdf_id  = $this->create_document_admin_attachment();
		$result  = $this->save_document_admin_state( $post_id, array( 'labm_documento_pdf_id' => $pdf_id ), $user_id, 'classic' );
		self::assertSame( $post_id, $result );
	}

	/** C4.2: reabrir tras un error conserva la version persistida. */
	public function test_document_admin_retry_keeps_previously_persisted_values(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$post_id = $this->create_document_admin_post( array( 'post_title' => 'Persistido' ) );
		$this->save_document_admin_state( $post_id, array( 'post_title' => '' ), $user_id, 'rest' );
		self::assertSame( 'Persistido', get_post_field( 'post_title', $post_id ) );
	}

	/** C4.3: una combinacion invalida no produce ninguna mutacion parcial. */
	public function test_document_admin_rejected_combination_preserves_all_fields(): void {
		$user_id = $this->create_document_admin_user( 'editor' );
		$pdf_id  = $this->create_document_admin_attachment();
		$post_id = $this->create_document_admin_post( array( 'meta_input' => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => $pdf_id, 'labm_documento_fecha' => '2026-09-01' ) ) );
		$before  = array( get_post_field( 'post_title', $post_id ), get_post_meta( $post_id, 'labm_documento_pdf_id', true ), get_post_meta( $post_id, 'labm_documento_fecha', true ) );
		$result  = $this->save_document_admin_state( $post_id, array( 'post_title' => 'Mutado', 'labm_documento_pdf_id' => 99999999, 'labm_documento_fecha' => '2026-02-30' ), $user_id, 'classic' );
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( $before, array( get_post_field( 'post_title', $post_id ), get_post_meta( $post_id, 'labm_documento_pdf_id', true ), get_post_meta( $post_id, 'labm_documento_fecha', true ) ) );
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

	public function test_catalog_applies_text_category_and_year_filters(): void {
		$term = term_exists( 'Circulares de prueba', 'labm_documento_categoria' );
		if ( ! $term ) {
			$term = wp_insert_term( 'Circulares de prueba', 'labm_documento_categoria' );
		}
		$one = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Circular deportiva alfa',
				'meta_input'  => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => 9101 ),
			)
		);
		$two = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Circular administrativa beta',
				'meta_input'  => array( 'labm_test_fixture' => 1, 'labm_documento_pdf_id' => 9102 ),
			)
		);
		wp_set_object_terms( $one, (int) $term['term_id'], 'labm_documento_categoria' );
		update_post_meta( $one, 'labm_documento_fecha', '2026-03-01' );
		update_post_meta( $two, 'labm_documento_fecha', '2025-03-01' );
		$query = labm_core_document_catalog_query( array( 'texto' => 'deportiva', 'categoria' => (int) $term['term_id'], 'anio' => 2026 ), 1, 10 );
		self::assertSame( array( $one ), wp_list_pluck( $query->posts, 'ID' ) );
		self::assertSame( '', labm_core_document_pdf_url( $one ) );
	}

	public function test_document_catalog_renders_public_metadata_and_preserves_filters(): void {
		$term = term_exists( 'Actas publicas', 'labm_documento_categoria' );
		if ( ! $term ) {
			$term = wp_insert_term( 'Actas publicas', 'labm_documento_categoria' );
		}
		$attachment = $this->create_document_admin_attachment();
		$document   = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Acta pública filtrable',
				'meta_input'  => array(
					'labm_test_fixture'       => 1,
					'labm_documento_pdf_id'   => $attachment,
					'labm_documento_fecha'    => '2026-03-01',
				),
			)
		);
		wp_set_object_terms( $document, (int) $term['term_id'], 'labm_documento_categoria' );
		$filters = array( 'texto' => 'Acta', 'categoria' => (int) $term['term_id'], 'anio' => 2026, 'orden' => 'antiguos' );
		$html    = labm_core_render_document_catalog( $filters, 1, 10 );
		$url     = labm_core_document_page_url( 2, $filters );

		self::assertStringContainsString( 'Actas publicas', $html );
		self::assertStringContainsString( '2026', $html );
		self::assertStringContainsString( 'Acta pública filtrable', $html );
		self::assertStringContainsString( 'texto=Acta', $url );
		self::assertStringContainsString( 'categoria=' . (int) $term['term_id'], $url );
		self::assertStringContainsString( 'anio=2026', $url );
		self::assertStringContainsString( 'orden=antiguos', $url );
	}

	public function test_document_catalog_empty_filter_offers_clear_action(): void {
		$html = labm_core_render_document_catalog( array( 'texto' => 'sin-resultados-ficticios' ), 1, 10 );
		self::assertStringContainsString( 'No encontramos documentos disponibles', $html );
		self::assertStringContainsString( 'Limpiar filtros', $html );
		self::assertStringContainsString( 'name="texto"', $html );
	}

	public function test_document_catalog_renders_unavailable_pdf_state(): void {
		$document = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Documento con PDF no disponible',
				'meta_input'  => array(
					'labm_test_fixture'     => 1,
					'labm_documento_pdf_id' => 99999999,
				),
			)
		);

		$html = labm_core_render_document_catalog( array( 'texto' => 'PDF no disponible' ), 1, 10 );
		self::assertIsInt( $document );
		self::assertStringContainsString( 'PDF no disponible', $html );
		self::assertStringContainsString( 'role="status"', $html );
		self::assertStringNotContainsString( 'labm-documents-catalog__view', $html );
	}

	public function test_document_without_editorial_date_is_visible_without_year_and_excluded_by_year(): void {
		$attachment = $this->create_document_admin_attachment();
		$document   = wp_insert_post(
			array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => 'Documento público sin fecha editorial',
				'meta_input'  => array(
					'labm_test_fixture'     => 1,
					'labm_documento_pdf_id' => $attachment,
				),
			)
		);

		$without_year = labm_core_document_catalog_query( array(), 1, 10 );
		$with_year    = labm_core_document_catalog_query( array( 'anio' => 2026 ), 1, 10 );

		self::assertContains( $document, wp_list_pluck( $without_year->posts, 'ID' ) );
		self::assertNotContains( $document, wp_list_pluck( $with_year->posts, 'ID' ) );
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
