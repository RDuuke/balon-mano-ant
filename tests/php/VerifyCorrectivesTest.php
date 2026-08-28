<?php

use PHPUnit\Framework\TestCase;

final class VerifyCorrectivesTest extends TestCase {
	public function test_publicacion_autorizada_y_contenido_incompleto(): void {
		$editor = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
		wp_set_current_user( $editor->ID );
		self::assertTrue( current_user_can( 'publish_labm_actualidades' ) );
		self::assertTrue( labm_core_validate_publishable( 'labm_actualidad', array( 'post_title' => 'Noticia', 'post_content' => 'Contenido' ) ) );
		$error = labm_core_validate_publishable( 'labm_actualidad', array( 'post_title' => '', 'post_content' => '' ) );
		self::assertInstanceOf( WP_Error::class, $error );
		self::assertSame( array( 'post_title', 'post_content' ), $error->get_error_data() );
	}

	public function test_presentacion_generica_e_identificador_invalido(): void {
		$term = wp_insert_term( 'Modalidad correctiva', 'labm_modalidad' );
		if ( is_wp_error( $term ) ) {
			$term = term_exists( 'Modalidad correctiva', 'labm_modalidad' );
		}
		$post = wp_insert_post( array( 'post_type' => 'labm_seleccion', 'post_status' => 'publish', 'post_title' => 'SelecciÃ³n genÃ©rica', 'post_content' => 'Contenido usable' ) );
		wp_set_object_terms( $post, (int) $term['term_id'], 'labm_modalidad' );
		$html = labm_theme_render_listing( 'labm_seleccion', array( 'modalidad' => 'Modalidad correctiva' ) );
		self::assertStringContainsString( 'SelecciÃ³n genÃ©rica', $html );
		self::assertSame( 'modalidad-valida', labm_core_validate_identifier( ' Modalidad vÃ¡lida ' ) );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_identifier( '---' ) );
		wp_delete_post( $post, true );
	}

	public function test_actualizacion_incompatible_bloquea_sin_alterar_contenido(): void {
		$post = wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Conservar', 'post_status' => 'draft' ) );
		$result = labm_core_validate_runtime_compatibility(
			array( 'php' => '8.3', 'wordpress' => '6.8' ),
			array( 'php' => '8.2.0', 'wordpress' => '6.7.0' )
		);
		self::assertInstanceOf( WP_Error::class, $result );
		self::assertStringContainsString( 'PHP', $result->get_error_message() );
		self::assertSame( 'Conservar', get_post( $post )->post_title );
		wp_delete_post( $post, true );
	}

	public function test_documento_publicado_consulta_combinada_paginada_y_consulta_vacia(): void {
		self::assertInstanceOf( WP_Error::class, labm_core_validate_publishable( 'labm_documento', array( 'post_title' => 'Circular' ) ) );
		$empty = labm_core_render_document_catalog( array( 'texto' => 'sin-coincidencia-correctiva' ), 1, 2 );
		self::assertStringContainsString( 'No hay documentos', $empty );
		self::assertStringContainsString( 'Limpiar filtros', $empty );
		self::assertStringContainsString( 'texto=', labm_core_document_page_url( 2, array( 'texto' => 'circular', 'categoria' => 7, 'anio' => 2026 ) ) );
		self::assertStringContainsString( 'categoria=7', labm_core_document_page_url( 2, array( 'texto' => 'circular', 'categoria' => 7, 'anio' => 2026 ) ) );
	}

	public function test_archivo_exclusivo_sigue_politica_explicita(): void {
		$attachment = wp_insert_attachment( array( 'post_mime_type' => 'application/pdf', 'post_title' => 'Exclusivo', 'post_status' => 'inherit' ) );
		$document = wp_insert_post( array( 'post_type' => 'labm_documento', 'post_status' => 'draft', 'post_title' => 'Documento exclusivo' ) );
		update_post_meta( $document, 'labm_documento_pdf_id', $attachment );
		$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
		wp_set_current_user( $admin->ID );
		self::assertTrue( labm_core_delete_document_attachment( $document, true ) );
		self::assertNull( get_post( $attachment ) );
		wp_delete_post( $document, true );
	}

	public function test_validacion_accesible_y_error_entrega_sin_datos_personales(): void {
		$attrs = labm_core_contact_error_attributes( array( 'correo' => 'InvÃ¡lido', 'mensaje' => 'Obligatorio' ) );
		self::assertSame( 'correo', $attrs['focus'] );
		self::assertSame( 'labm-error-correo', $attrs['fields']['correo']['aria-describedby'] );
		$logged = array();
		add_action( 'labm_core_contact_delivery_failed', static function ( $context ) use ( &$logged ) { $logged = $context; } );
		add_filter( 'pre_wp_mail', '__return_false' );
		$result = labm_core_process_contact( array( 'nombre' => 'Ana', 'apellidos' => 'PÃ©rez', 'correo' => 'ana@example.test', 'asunto' => 'Consulta', 'mensaje' => 'Dato personal', 'sitio_web' => '', 'token' => 'fallo-correctivo', 'nonce' => wp_create_nonce( 'labm_contacto' ) ) );
		remove_filter( 'pre_wp_mail', '__return_false' );
		self::assertFalse( $result['ok'] );
		self::assertArrayHasKey( 'delivery', $result['errors'] );
		self::assertSame( array( 'code' => 'mail_delivery_failed' ), $logged );
	}

	public function test_navegacion_completa_portada_completa_y_seccion_opcional(): void {
		$header = file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/parts/header.html' );
		foreach ( array( 'Inicio', 'Nosotros', 'Actualidad', 'Selecciones', 'Documentos', 'Contacto' ) as $label ) {
			self::assertStringContainsString( $label, $header );
		}
		$sections = labm_theme_home_sections( array( 'modalidades' => true, 'actualidad' => false, 'contacto' => true ) );
		self::assertSame( array( 'modalidades', 'contacto' ), $sections );
		self::assertNotContains( 'actualidad', $sections );
	}
}
