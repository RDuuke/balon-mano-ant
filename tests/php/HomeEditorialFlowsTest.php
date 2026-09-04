<?php
/**
 * Correctivos de integracion para el contenido editorial de Inicio.
 *
 * @package LABM
 */

use PHPUnit\Framework\TestCase;

/** Cubre persistencia, autorizacion y valores limite con WordPress real. */
final class HomeEditorialFlowsTest extends TestCase {
	/** IDs creados durante la prueba. */
	private array $created = array();

	/** IDs de usuarios temporales creados por la prueba. */
	private array $created_users = array();

	/** Garantiza la migracion de capacidades antes de cada flujo aislado. */
	protected function setUp(): void {
		parent::setUp();
		labm_core_ensure_capabilities();
	}

	/** Restaura usuario, tema y contenido. */
	protected function tearDown(): void {
		wp_set_current_user( 0 );
		foreach ( array_reverse( $this->created ) as $post_id ) {
			wp_delete_post( $post_id, true );
		}
		if ( $this->created_users && ! function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}
		foreach ( $this->created_users as $user_id ) {
			wp_delete_user( $user_id );
		}
		parent::tearDown();
	}

	/** Crea un usuario aislado con el rol solicitado. */
	private function create_user_with_role( string $role ): WP_User {
		$user_id = wp_create_user( 'labm-' . $role . '-' . wp_generate_uuid4(), wp_generate_password(), 'labm-' . wp_generate_uuid4() . '@example.org' );
		self::assertIsInt( $user_id );
		$this->created_users[] = $user_id;
		$user                  = new WP_User( $user_id );
		$user->set_role( $role );
		return $user;
	}

	/** Crea un adjunto minimo valido como imagen destacada. */
	private function create_attachment(): int {
		$post_id         = wp_insert_attachment(
			array(
				'post_title'     => 'Medio de prueba',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/png',
			),
			false,
			0,
			true
		);
		self::assertIsInt( $post_id );
		$this->created[] = $post_id;
		return $post_id;
	}

	/** Ejecuta una solicitud REST contra el servidor interno. */
	private function rest( string $method, string $route, array $body = array() ): WP_REST_Response|WP_Error {
		$request = new WP_REST_Request( $method, $route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( wp_json_encode( $body ) );
		return rest_do_request( $request );
	}

	/** La API publica aliados completos y rechaza los que no tienen titulo o logo. */
	public function test_rest_valida_publicacion_de_aliados(): void {
		$editor = $this->create_user_with_role( 'editor' );
		wp_set_current_user( $editor->ID );
		$media_id = $this->create_attachment();

		$valid = $this->rest(
			'POST',
			'/wp/v2/labm_aliado',
			array(
				'title'          => 'Aliado valido',
				'status'         => 'publish',
				'featured_media' => $media_id,
			)
		);
		self::assertSame( 201, $valid->get_status(), wp_json_encode( $valid->get_data() ) );
		$this->created[] = (int) $valid->get_data()['id'];

		foreach (
			array(
				'sin titulo' => array( 'title' => '', 'status' => 'publish', 'featured_media' => $media_id ),
				'sin logo'   => array( 'title' => 'Aliado incompleto', 'status' => 'publish', 'featured_media' => 0 ),
			) as $case => $body
		) {
			$response = $this->rest( 'POST', '/wp/v2/labm_aliado', $body );
			self::assertSame( 400, $response->get_status(), $case . ': ' . wp_json_encode( $response->get_data() ) );
			self::assertSame( 'labm_incomplete_home_content', $response->get_data()['code'] );
		}
	}

	/** El guardado clasico autorizado conserva un aliado incompleto como borrador y avisa. */
	public function test_admin_impide_publicacion_incompleta_y_muestra_aviso(): void {
		$editor = $this->create_user_with_role( 'editor' );
		wp_set_current_user( $editor->ID );
		$nonce = wp_create_nonce( 'labm_save_home_content' );
		$data  = array( 'post_type' => 'labm_aliado', 'post_status' => 'publish', 'post_title' => 'Sin logo' );

		$filtered = apply_filters(
			'wp_insert_post_data',
			$data,
			array(
				'post_type'                => 'labm_aliado',
				'post_status'              => 'publish',
				'post_title'               => 'Sin logo',
				'_thumbnail_id'            => 0,
				'_labm_home_content_nonce' => $nonce,
			)
		);

		self::assertSame( 'draft', $filtered['post_status'] );
		$location = apply_filters( 'redirect_post_location', 'post.php?post=123&action=edit' );
		self::assertStringContainsString( 'labm_home_error=incomplete', $location );

		$_GET['labm_home_error'] = 'incomplete';
		ob_start();
		do_action( 'admin_notices' );
		$notice = (string) ob_get_clean();
		unset( $_GET['labm_home_error'] );
		self::assertStringContainsString( 'titulo y el logo', strtolower( wp_strip_all_tags( $notice ) ) );
	}

	/** La validacion administrativa no actua sin nonce valido ni capacidad editorial. */
	public function test_admin_validation_requires_nonce_and_capability(): void {
		$data = array( 'post_type' => 'labm_aliado', 'post_status' => 'publish', 'post_title' => 'Sin logo' );

		$editor = $this->create_user_with_role( 'editor' );
		wp_set_current_user( $editor->ID );
		$without_nonce = apply_filters( 'wp_insert_post_data', $data, $data );
		self::assertSame( 'publish', $without_nonce['post_status'] );

		$subscriber = $this->create_user_with_role( 'subscriber' );
		wp_set_current_user( $subscriber->ID );
		$without_capability = apply_filters(
			'wp_insert_post_data',
			$data,
			array_merge( $data, array( '_labm_home_content_nonce' => wp_create_nonce( 'labm_save_home_content' ) ) )
		);
		self::assertSame( 'publish', $without_capability['post_status'] );
	}

	/** Guardar titulo y orden no elimina cuerpo, extracto ni URL preexistentes. */
	public function test_actualizacion_de_aliado_conserva_datos_legados(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'labm_aliado',
				'post_status'  => 'draft',
				'post_title'   => 'Aliado legado',
				'post_content' => 'Contenido legado',
				'post_excerpt' => 'Extracto legado',
			)
		);
		self::assertIsInt( $post_id );
		$this->created[] = $post_id;
		update_post_meta( $post_id, 'labm_destino_url', 'https://example.org/legado' );

		wp_update_post( array( 'ID' => $post_id, 'post_title' => 'Aliado actualizado', 'menu_order' => 7 ) );
		$post = get_post( $post_id );
		self::assertSame( 'Contenido legado', $post->post_content );
		self::assertSame( 'Extracto legado', $post->post_excerpt );
		self::assertSame( 'https://example.org/legado', get_post_meta( $post_id, 'labm_destino_url', true ) );
	}

	/** Un editor puede crear, modificar, publicar y eliminar slides y aliados. */
	public function test_editor_realiza_flujo_editorial_completo_por_rest(): void {
		$editor = $this->create_user_with_role( 'editor' );
		wp_set_current_user( $editor->ID );
		self::assertTrue( current_user_can( 'edit_labm_slides' ), 'El editor carece de edit_labm_slides.' );
		self::assertTrue( current_user_can( get_post_type_object( 'labm_slide' )->cap->create_posts ), 'El editor carece de la capacidad REST create_posts.' );
		$media_id = $this->create_attachment();

		foreach ( array( 'labm_slide', 'labm_aliado' ) as $post_type ) {
			$rest_base = $post_type;
			$body = array(
				'title'          => 'Contenido autorizado',
				'status'         => 'publish',
				'featured_media' => $media_id,
			);
			if ( 'labm_slide' === $post_type ) {
				$body['meta'] = array( 'labm_destino_url' => 'https://example.org/seguro' );
			}
			$response  = $this->rest(
				'POST',
				'/wp/v2/' . $rest_base,
				$body
			);
			self::assertSame( 201, $response->get_status(), wp_json_encode( $response->get_data() ) );
			$post_id         = (int) $response->get_data()['id'];
			$this->created[] = $post_id;
			self::assertSame( 'publish', get_post_status( $post_id ) );
			$expected_url = 'labm_slide' === $post_type ? 'https://example.org/seguro' : '';
			self::assertSame( $expected_url, get_post_meta( $post_id, 'labm_destino_url', true ) );

			$response = $this->rest( 'POST', '/wp/v2/' . $rest_base . '/' . $post_id, array( 'title' => 'Contenido actualizado' ) );
			self::assertSame( 200, $response->get_status() );
			self::assertSame( 'Contenido actualizado', get_the_title( $post_id ) );

			$response = $this->rest( 'DELETE', '/wp/v2/' . $rest_base . '/' . $post_id, array( 'force' => true ) );
			self::assertSame( 200, $response->get_status() );
			self::assertNull( get_post( $post_id ) );
		}
	}

	/** Un suscriptor no puede crear, modificar, publicar ni eliminar contenido. */
	public function test_suscriptor_no_puede_mutar_contenido_por_rest(): void {
		$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
		wp_set_current_user( $admin->ID );
		$post_id         = wp_insert_post( array( 'post_type' => 'labm_slide', 'post_status' => 'draft', 'post_title' => 'Protegido' ) );
		$this->created[] = $post_id;

		$subscriber = $this->create_user_with_role( 'subscriber' );
		wp_set_current_user( $subscriber->ID );
		foreach (
			array(
				$this->rest( 'POST', '/wp/v2/labm_slide', array( 'title' => 'Intruso', 'status' => 'publish' ) ),
				$this->rest( 'POST', '/wp/v2/labm_slide/' . $post_id, array( 'title' => 'Manipulado', 'status' => 'publish' ) ),
				$this->rest( 'DELETE', '/wp/v2/labm_slide/' . $post_id, array( 'force' => true ) ),
			) as $response
		) {
			self::assertContains( $response->get_status(), array( 401, 403 ) );
		}
		self::assertSame( 'Protegido', get_the_title( $post_id ) );
		self::assertSame( 'draft', get_post_status( $post_id ) );
	}

	/** El plugin conserva entradas, estados, metadatos y capacidades al cambiar tema. */
	public function test_slides_y_aliados_persisten_al_cambiar_de_tema(): void {
		$original_theme = get_stylesheet();
		$themes         = array_keys( wp_get_themes() );
		$alternate      = current( array_values( array_diff( $themes, array( $original_theme ) ) ) );
		self::assertNotFalse( $alternate, 'Se requiere un segundo tema instalado para probar persistencia.' );

		foreach ( array( 'labm_slide' => 'draft', 'labm_aliado' => 'private' ) as $post_type => $status ) {
			$post_id         = wp_insert_post( array( 'post_type' => $post_type, 'post_status' => $status, 'post_title' => 'Persistente ' . $post_type ) );
			$this->created[] = $post_id;
			update_post_meta( $post_id, 'labm_destino_url', 'https://example.org/persistente' );
		}

		try {
			switch_theme( $alternate );
			foreach ( $this->created as $post_id ) {
				if ( 'attachment' === get_post_type( $post_id ) ) {
					continue;
				}
				self::assertNotNull( get_post( $post_id ) );
				self::assertSame( 'https://example.org/persistente', get_post_meta( $post_id, 'labm_destino_url', true ) );
			}
			self::assertTrue( get_role( 'editor' )->has_cap( 'edit_labm_slides' ) );
			self::assertTrue( get_role( 'editor' )->has_cap( 'edit_labm_aliados' ) );
		} finally {
			switch_theme( $original_theme );
		}
	}

	/** Estados no publicos, URL manipulada, texto largo y medio ausente son deterministas. */
	public function test_limites_y_datos_no_publicos_no_se_exponen(): void {
		$long_text = str_repeat( 'contenido seguro ', 80 );
		foreach ( array( 'draft', 'private' ) as $status ) {
			$post_id         = wp_insert_post( array( 'post_type' => 'labm_slide', 'post_status' => $status, 'post_title' => 'SECRETO-' . $status, 'post_content' => $long_text ) );
			$this->created[] = $post_id;
		}

		$slider = labm_theme_render_home_slider();
		self::assertStringNotContainsString( 'SECRETO-draft', $slider );
		self::assertStringNotContainsString( 'SECRETO-private', $slider );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_home_publishable( 'labm_slide', array( 'post_title' => $long_text, 'thumbnail_id' => 0 ) ) );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_home_publishable( 'labm_slide', array( 'post_title' => $long_text, 'thumbnail_id' => 1, 'labm_destino_url' => 'javascript:alert(1)' ) ) );
	}
}
