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

	public function test_catalog_combines_text_category_year_and_keeps_safe_links(): void {
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
		$query = labm_core_document_catalog_query(
			array(
				'texto'     => 'deportiva',
				'categoria' => (int) $term['term_id'],
				'anio'      => 2026,
			),
			1,
			10
		);
		self::assertSame( array( $one ), wp_list_pluck( $query->posts, 'ID' ) );
		self::assertSame( '', labm_core_document_pdf_url( $one ) );
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
