<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DomainModelTest extends TestCase {
	private array $selection_posts = array();
	private array $selection_terms = array();
	private array $selection_users = array();
	private int $selection_previous_user;

	protected function setUp(): void {
		parent::setUp();
		$this->selection_previous_user = get_current_user_id();
	}

	protected function tearDown(): void {
		wp_set_current_user( $this->selection_previous_user );
		foreach ( $this->selection_posts as $id ) { wp_delete_post( $id, true ); }
		foreach ( $this->selection_terms as $term ) { wp_delete_term( $term[0], $term[1] ); }
		if ( $this->selection_users ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
			foreach ( $this->selection_users as $id ) { wp_delete_user( $id ); }
		}
		parent::tearDown();
	}

	private function selection_user( string $role ): int {
		$id = wp_insert_user( array( 'user_login' => 'selection-test-' . wp_generate_uuid4(), 'user_pass' => wp_generate_password(), 'role' => $role ) );
		self::assertIsInt( $id );
		$this->selection_users[] = $id;
		wp_set_current_user( $id );
		return $id;
	}

	private function selection_post( string $status = 'publish' ): int {
		$id = wp_insert_post( array( 'post_type' => 'labm_seleccion', 'post_status' => $status, 'post_title' => 'Selección temporal', 'post_content' => 'Contenido temporal', 'post_excerpt' => 'Extracto temporal' ), true );
		self::assertIsInt( $id );
		$this->selection_posts[] = $id;
		return $id;
	}

	private function selection_request( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, $route );
		foreach ( $params as $key => $value ) { $request->set_param( $key, $value ); }
		return rest_do_request( $request );
	}

	public function test_selection_editorial_fields_and_extensible_terms_through_rest(): void {
		$this->selection_user( 'administrator' );
		$id = $this->selection_post();
		$route = '/wp/v2/labm_seleccion/' . $id;
		$response = $this->selection_request( 'GET', $route );
		self::assertSame( 200, $response->get_status() );
		foreach ( array( 'labm_modalidad', 'labm_categoria' ) as $taxonomy ) {
			self::assertSame( array(), $response->get_data()[ $taxonomy ] );
			$term = $this->selection_request( 'POST', '/wp/v2/' . $taxonomy, array( 'name' => 'Clasificación temporal ' . wp_generate_uuid4() ) );
			self::assertSame( 201, $term->get_status() );
			$term_id = $term->get_data()['id'];
			$this->selection_terms[] = array( $term_id, $taxonomy );
			$update = $this->selection_request( 'POST', $route, array( $taxonomy => array( $term_id ) ) );
			self::assertSame( 200, $update->get_status() );
			self::assertSame( array( $term_id ), $update->get_data()[ $taxonomy ] );
		}
		$media_id = wp_insert_attachment( array( 'post_mime_type' => 'image/png', 'post_title' => 'Imagen temporal', 'post_status' => 'inherit', 'guid' => home_url( '/selection-test.png' ) ), false, $id, true );
		self::assertIsInt( $media_id );
		$this->selection_posts[] = $media_id;
		update_post_meta( $media_id, '_wp_attached_file', 'selection-test.png' );
		wp_update_attachment_metadata( $media_id, array( 'width' => 1, 'height' => 1, 'file' => 'selection-test.png', 'sizes' => array() ) );
		$this->selection_user( 'editor' );
		$update = $this->selection_request( 'POST', $route, array( 'title' => 'Título editorial', 'content' => 'Contenido editorial', 'excerpt' => 'Extracto editorial', 'featured_media' => $media_id ) );
		self::assertSame( 200, $update->get_status() );
		wp_set_current_user( 0 );
		$public = $this->selection_request( 'GET', $route );
		self::assertSame( 200, $public->get_status() );
		$data = $public->get_data();
		self::assertSame( 'Título editorial', $data['title']['rendered'] );
		self::assertStringContainsString( 'Contenido editorial', $data['content']['rendered'] );
		self::assertStringContainsString( 'Extracto editorial', $data['excerpt']['rendered'] );
		self::assertSame( $media_id, $data['featured_media'] );
		foreach ( array( 'labm_modalidad', 'labm_categoria' ) as $taxonomy ) {
			$before = wp_count_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
			$denied = $this->selection_request( 'POST', '/wp/v2/' . $taxonomy, array( 'name' => 'No autorizado' ) );
			self::assertGreaterThanOrEqual( 400, $denied->get_status() );
			self::assertSame( $before, wp_count_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) ) );
			$denied = $this->selection_request( 'POST', $route, array( $taxonomy => array() ) );
			self::assertGreaterThanOrEqual( 400, $denied->get_status() );
			self::assertSame( $data[ $taxonomy ], $this->selection_request( 'GET', $route )->get_data()[ $taxonomy ] );
		}
	}

	public function test_selection_detail_metadata_rest_sanitization_and_authorization(): void {
		$this->selection_user( 'editor' );
		$id = $this->selection_post();
		$route = '/wp/v2/labm_seleccion/' . $id;
		foreach ( array( 'Balonmano playa' => 'Balonmano playa', ' <b>Playa</b><script>alert(1)</script> ' => 'Playa', '' => '' ) as $input => $expected ) {
			$response = $this->selection_request( 'POST', $route, array( 'meta' => array( 'labm_modalidad_detalle' => $input ) ) );
			self::assertSame( 200, $response->get_status() );
			wp_set_current_user( 0 );
			self::assertSame( $expected, $this->selection_request( 'GET', $route )->get_data()['meta']['labm_modalidad_detalle'] );
			wp_set_current_user( $this->selection_users[0] );
		}
		update_post_meta( $id, 'labm_modalidad_detalle', 'Valor preservado' );
		$this->selection_user( 'subscriber' );
		foreach ( array( get_current_user_id(), 0 ) as $user ) {
			wp_set_current_user( $user );
			$denied = $this->selection_request( 'POST', $route, array( 'meta' => array( 'labm_modalidad_detalle' => 'No autorizado' ) ) );
			self::assertGreaterThanOrEqual( 400, $denied->get_status() );
			self::assertSame( 'Valor preservado', get_post_meta( $id, 'labm_modalidad_detalle', true ) );
		}
	}

	public function test_selection_public_rest_excludes_restricted_content(): void {
		$this->selection_user( 'editor' );
		$published = $this->selection_post();
		$restricted = array( $this->selection_post( 'private' ), $this->selection_post( 'draft' ) );
		foreach ( $restricted as $id ) { update_post_meta( $id, 'labm_modalidad_detalle', 'Detalle restringido' ); }
		wp_set_current_user( 0 );
		$response = $this->selection_request( 'GET', '/wp/v2/labm_seleccion', array( 'include' => array_merge( array( $published ), $restricted ) ) );
		self::assertSame( 200, $response->get_status() );
		self::assertSame( array( $published ), array_column( $response->get_data(), 'id' ) );
		$empty = $this->selection_request( 'GET', '/wp/v2/labm_seleccion', array( 'include' => $restricted ) );
		self::assertSame( 200, $empty->get_status() );
		self::assertSame( array(), $empty->get_data() );
		foreach ( $restricted as $id ) {
			$response = $this->selection_request( 'GET', '/wp/v2/labm_seleccion/' . $id );
			self::assertGreaterThanOrEqual( 400, $response->get_status() );
			foreach ( array( 'title', 'content', 'meta' ) as $field ) { self::assertArrayNotHasKey( $field, $response->get_data() ); }
		}
	}

	public function test_current_version_repairs_selection_capabilities_additively(): void {
		$roles_key = wp_roles()->role_key;
		$roles     = get_option( $roles_key );
		$version   = get_option( 'labm_core_capabilities_version' );
		$user_id   = get_current_user_id();
		$subscriber_id = 0;
		$writes = 0;
		$count_writes = static function () use ( &$writes ): void { ++$writes; };
		try {
			update_option( 'labm_core_capabilities_version', LABM_CORE_CAPABILITIES_VERSION );
			$caps = array_unique( (array) get_post_type_object( 'labm_seleccion' )->cap );
			foreach ( array( 'administrator', 'editor' ) as $role_name ) {
				$role = get_role( $role_name );
				foreach ( $caps as $cap ) {
					if ( 'read' !== $cap ) { $role->remove_cap( $cap ); }
				}
			}
			labm_core_ensure_capabilities();
			foreach ( array( 'administrator', 'editor' ) as $role_name ) {
				$role = get_role( $role_name );
				foreach ( $caps as $cap ) {
					self::assertTrue( $role->has_cap( $cap ), $role_name . ':' . $cap );
				}
				foreach ( $roles[ $role_name ]['capabilities'] as $cap => $value ) {
					if ( ! in_array( $cap, $caps, true ) ) { self::assertSame( $value, $role->capabilities[ $cap ], $cap ); }
				}
			}
			self::assertSame( LABM_CORE_CAPABILITIES_VERSION, get_option( 'labm_core_capabilities_version' ) );
			$repaired = get_option( $roles_key );
			add_action( 'updated_option', $count_writes );
			labm_core_ensure_capabilities();
			self::assertSame( 0, $writes );
			self::assertSame( $repaired, get_option( $roles_key ) );
			self::assertSame( $roles['subscriber'], $repaired['subscriber'] );
			$subscriber_id = wp_insert_user( array( 'user_login' => 'selection-test-' . wp_generate_uuid4(), 'user_pass' => wp_generate_password(), 'role' => 'subscriber' ) );
			self::assertIsInt( $subscriber_id );
			wp_set_current_user( $subscriber_id );
			foreach ( array( 'create_posts', 'edit_posts', 'publish_posts' ) as $cap ) {
				self::assertFalse( current_user_can( get_post_type_object( 'labm_seleccion' )->cap->$cap ) );
			}
			$denied = $this->selection_request( 'POST', '/wp/v2/labm_seleccion', array( 'title' => 'No autorizado', 'status' => 'publish' ) );
			self::assertGreaterThanOrEqual( 400, $denied->get_status() );
		} finally {
			remove_action( 'updated_option', $count_writes );
			wp_set_current_user( $user_id );
			if ( is_int( $subscriber_id ) && $subscriber_id > 0 ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
				wp_delete_user( $subscriber_id );
			}
			update_option( $roles_key, $roles );
			wp_roles()->for_site();
			if ( false === $version ) { delete_option( 'labm_core_capabilities_version' ); } else { update_option( 'labm_core_capabilities_version', $version ); }
		}
	}

	/** @return array<string> */
	public static function post_type_provider(): array {
		return array(
			'actualidad' => array( 'labm_actualidad' ),
			'seleccion'  => array( 'labm_seleccion' ),
			'club'       => array( 'labm_club' ),
			'integrante' => array( 'labm_integrante' ),
			'horario'    => array( 'labm_horario' ),
		);
	}

	#[DataProvider( 'post_type_provider' )]
	public function test_domain_post_types_are_rest_enabled_and_persistent( string $post_type ): void {
		$object = get_post_type_object( $post_type );
		self::assertInstanceOf( WP_Post_Type::class, $object );
		self::assertTrue( $object->show_in_rest );
		self::assertTrue( $object->map_meta_cap );
		self::assertTrue( post_type_supports( $post_type, 'title' ) );
	}

	public function test_taxonomies_are_extensible_and_rest_enabled(): void {
		$modalidad = get_taxonomy( 'labm_modalidad' );
		$categoria = get_taxonomy( 'labm_categoria' );
		$grupo      = get_taxonomy( 'labm_grupo_integrante' );
		self::assertInstanceOf( WP_Taxonomy::class, $modalidad );
		self::assertInstanceOf( WP_Taxonomy::class, $categoria );
		self::assertTrue( $modalidad->show_in_rest );
		self::assertTrue( $categoria->show_in_rest );
		self::assertContains( 'labm_seleccion', $modalidad->object_type );
		self::assertContains( 'labm_actualidad', $categoria->object_type );
		self::assertInstanceOf( WP_Taxonomy::class, $grupo );
		self::assertTrue( $grupo->show_in_rest );
		self::assertTrue( $grupo->hierarchical );
		self::assertContains( 'labm_integrante', $grupo->object_type );
	}

	public function test_metadata_is_registered_and_sanitized(): void {
		$fields = array(
			'labm_actualidad' => 'labm_fecha_evento',
			'labm_seleccion'  => 'labm_modalidad_detalle',
			'labm_club'       => 'labm_ciudad',
			'labm_integrante' => 'labm_cargo',
			'labm_horario'    => 'labm_inicio',
		);
		foreach ( $fields as $post_type => $meta_key ) {
			self::assertTrue( registered_meta_key_exists( 'post', $meta_key, $post_type ), $meta_key );
		}
		self::assertSame( 'Medellin', sanitize_meta( 'labm_ciudad', ' <b>Medellin</b> ', 'post', 'labm_club' ) );
		self::assertTrue( labm_core_validate_iso_date( '2026-02-28' ) );
		self::assertFalse( labm_core_validate_iso_date( '2026-02-30' ) );
		self::assertFalse( labm_core_validate_iso_date( 'no-es-fecha' ) );
	}

	public function test_editor_and_administrator_have_domain_capabilities(): void {
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			self::assertInstanceOf( WP_Role::class, $role );
			self::assertTrue( $role->has_cap( 'edit_labm_actualidades' ) );
			self::assertTrue( $role->has_cap( 'publish_labm_actualidades' ) );
		}
		$subscriber = get_role( 'subscriber' );
		self::assertInstanceOf( WP_Role::class, $subscriber );
		self::assertFalse( $subscriber->has_cap( 'edit_labm_actualidades' ) );
	}

	public function test_drafts_are_excluded_from_public_queries(): void {
		$post = get_page_by_path( 'prueba-dominio-borrador', OBJECT, 'labm_actualidad' );
		$data = array(
			'ID'          => $post ? $post->ID : 0,
			'post_type'   => 'labm_actualidad',
			'post_status' => 'draft',
			'post_name'   => 'prueba-dominio-borrador',
			'post_title'  => '[DEMO LABM — FICTICIO] Prueba borrador',
		);
		$post_id = wp_insert_post( wp_slash( $data ) );
		$query   = new WP_Query(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'publish',
				'post__in'    => array( $post_id ),
			)
		);
		self::assertSame( 0, $query->post_count );
	}

	public function test_visible_strings_are_translation_ready_in_spanish(): void {
		self::assertNotFalse( has_action( 'init', 'labm_core_load_textdomain' ) );
		self::assertSame( 'Actualidad', get_post_type_object( 'labm_actualidad' )->labels->name );
		self::assertSame( 'Modalidades', get_taxonomy( 'labm_modalidad' )->labels->name );
	}
}
