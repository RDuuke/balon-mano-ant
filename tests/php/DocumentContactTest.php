<?php

use PHPUnit\Framework\TestCase;

final class DocumentContactTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		delete_transient( 'labm_contact_' . hash( 'sha256', 'contacto-prueba-unico' ) );
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
}
