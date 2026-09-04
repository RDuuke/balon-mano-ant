<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if ( ! class_exists( 'WP_CLI' ) ) {
	class WP_CLI {
		public static array $messages = array();
		public static function warning( string $message ): void { self::$messages[] = $message; }
		public static function error( string $message ): void { throw new RuntimeException( $message ); }
		public static function success( string $message ): void { self::$messages[] = $message; }
	}
}

final class HomeContentTest extends TestCase {
	/** @return array<string, array{string}> */
	public static function home_type_provider(): array {
		return array(
			'slide'  => array( 'labm_slide' ),
			'aliado' => array( 'labm_aliado' ),
		);
	}

	#[DataProvider( 'home_type_provider' )]
	public function test_home_content_is_editorial_rest_enabled_and_not_publicly_queryable( string $post_type ): void {
		$object = get_post_type_object( $post_type );
		self::assertInstanceOf( WP_Post_Type::class, $object );
		self::assertFalse( $object->public );
		self::assertFalse( $object->publicly_queryable );
		self::assertFalse( $object->has_archive );
		self::assertTrue( $object->show_ui );
		self::assertTrue( $object->show_in_rest );
		self::assertTrue( $object->map_meta_cap );
		self::assertTrue( post_type_supports( $post_type, 'page-attributes' ) );
	}

	public function test_allies_only_expose_logo_catalog_editorial_supports(): void {
		self::assertTrue( post_type_supports( 'labm_aliado', 'title' ) );
		self::assertTrue( post_type_supports( 'labm_aliado', 'thumbnail' ) );
		self::assertTrue( post_type_supports( 'labm_aliado', 'page-attributes' ) );
		self::assertFalse( post_type_supports( 'labm_aliado', 'editor' ) );
		self::assertFalse( post_type_supports( 'labm_aliado', 'excerpt' ) );
		self::assertFalse( post_type_supports( 'labm_aliado', 'custom-fields' ) );
	}

	public function test_home_metadata_is_registered_sanitized_and_authorized_by_post(): void {
		self::assertTrue( registered_meta_key_exists( 'post', 'labm_destino_url', 'labm_slide' ) );
		self::assertTrue( registered_meta_key_exists( 'post', 'labm_cta_texto', 'labm_slide' ) );
		self::assertTrue( registered_meta_key_exists( 'post', 'labm_destino_url', 'labm_aliado' ) );
		self::assertSame( '', sanitize_meta( 'labm_destino_url', 'javascript:alert(1)', 'post', 'labm_slide' ) );
		self::assertSame( 'https://example.org/ruta', sanitize_meta( 'labm_destino_url', 'https://example.org/ruta', 'post', 'labm_slide' ) );
		self::assertSame( 'Ver mas', sanitize_meta( 'labm_cta_texto', '<b>Ver mas</b>', 'post', 'labm_slide' ) );

		$subscriber = get_users( array( 'role' => 'subscriber', 'number' => 1 ) )[0];
		wp_set_current_user( $subscriber->ID );
		$post_id = wp_insert_post( array( 'post_type' => 'labm_slide', 'post_title' => 'Sin permiso' ) );
		self::assertFalse( labm_core_auth_post_meta( false, 'labm_destino_url', $post_id ) );
	}

	public function test_only_editors_and_administrators_receive_home_capabilities(): void {
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			self::assertTrue( $role->has_cap( 'edit_labm_slides' ) );
			self::assertTrue( $role->has_cap( 'publish_labm_aliados' ) );
		}
		self::assertFalse( get_role( 'subscriber' )->has_cap( 'edit_labm_slides' ) );
	}

	public function test_invalid_home_items_are_rejected_before_publish(): void {
		self::assertInstanceOf( WP_Error::class, labm_core_validate_home_publishable( 'labm_slide', array( 'post_title' => '', 'thumbnail_id' => 0 ) ) );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_home_publishable( 'labm_aliado', array( 'post_title' => 'Aliado sin logo', 'thumbnail_id' => 0 ) ) );
		self::assertInstanceOf( WP_Error::class, labm_core_validate_home_publishable( 'labm_slide', array( 'post_title' => 'Slide', 'thumbnail_id' => 10, 'labm_destino_url' => 'javascript:alert(1)' ) ) );
		self::assertTrue( labm_core_validate_home_publishable( 'labm_slide', array( 'post_title' => 'Slide', 'thumbnail_id' => 10, 'labm_destino_url' => 'https://example.org' ) ) );
	}

	public function test_home_fixtures_are_idempotent_and_marked_as_fictitious(): void {
		$runtime_root = getenv( 'WP_TESTS_RUNTIME_ROOT' ) ?: '/wordpress';
		require_once $runtime_root . '/wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php';
		$command = new LABM_Fixtures_Command();
		$command->load( array(), array() );
		$command->load( array(), array() );

		foreach ( array( 'demo-labm-slide-bienvenida' => 'labm_slide', 'demo-labm-aliado-ejemplo' => 'labm_aliado' ) as $slug => $post_type ) {
			$posts = get_posts( array( 'name' => $slug, 'post_type' => $post_type, 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => -1 ) );
			self::assertCount( 1, $posts, $slug );
			self::assertStringContainsString( 'FICTICIO', $posts[0]->post_title );
		}
	}
}
