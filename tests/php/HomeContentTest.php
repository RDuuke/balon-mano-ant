<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if ( ! class_exists( 'WP_CLI' ) ) {
	class WP_CLI {
		public static array $messages = array();
		public static function warning( string $message ): void {
			self::$messages[] = $message;
		}
		public static function error( string $message ): void {
			throw new RuntimeException( $message );
		}
		public static function success( string $message ): void {
			self::$messages[] = $message;
		}
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
		self::assertSame(
			array( 'title', 'thumbnail', 'page-attributes' ),
			array_keys( get_all_post_type_supports( 'labm_aliado' ) )
		);
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
		self::assertFalse( registered_meta_key_exists( 'post', 'labm_destino_url', 'labm_aliado' ) );
		self::assertSame( '', sanitize_meta( 'labm_destino_url', 'javascript:alert(1)', 'post', 'labm_slide' ) );
		self::assertSame( 'https://example.org/ruta', sanitize_meta( 'labm_destino_url', 'https://example.org/ruta', 'post', 'labm_slide' ) );
		self::assertSame( 'Ver mas', sanitize_meta( 'labm_cta_texto', '<b>Ver mas</b>', 'post', 'labm_slide' ) );

		$subscriber_id = wp_create_user( 'labm-subscriber-' . wp_generate_uuid4(), wp_generate_password() );
		self::assertIsInt( $subscriber_id );
		$subscriber = new WP_User( $subscriber_id );
		$subscriber->set_role( 'subscriber' );
		wp_set_current_user( $subscriber_id );
		$post_id = wp_insert_post(
			array(
				'post_type'  => 'labm_slide',
				'post_title' => 'Sin permiso',
			)
		);
		self::assertFalse( labm_core_auth_post_meta( false, 'labm_destino_url', $post_id ) );

		wp_delete_post( $post_id, true );
		if ( ! function_exists( 'wp_delete_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}
		wp_delete_user( $subscriber_id );
		wp_set_current_user( 0 );
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
		$image_id = wp_insert_attachment(
			array(
				'post_title'     => 'Logo valido',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/png',
			),
			false,
			0,
			true
		);
		$pdf_id   = wp_insert_attachment(
			array(
				'post_title'     => 'Documento no valido',
				'post_status'    => 'inherit',
				'post_mime_type' => 'application/pdf',
			),
			false,
			0,
			true
		);

		self::assertIsInt( $image_id );
		self::assertIsInt( $pdf_id );
		self::assertInstanceOf(
			WP_Error::class,
			labm_core_validate_home_publishable(
				'labm_slide',
				array(
					'post_title'   => '',
					'thumbnail_id' => 0,
				)
			)
		);
		self::assertInstanceOf(
			WP_Error::class,
			labm_core_validate_home_publishable(
				'labm_aliado',
				array(
					'post_title'   => 'Aliado sin logo',
					'thumbnail_id' => 0,
				)
			)
		);
		self::assertInstanceOf(
			WP_Error::class,
			labm_core_validate_home_publishable(
				'labm_aliado',
				array(
					'post_title'   => 'Aliado con archivo',
					'thumbnail_id' => $pdf_id,
				)
			)
		);
		self::assertInstanceOf(
			WP_Error::class,
			labm_core_validate_home_publishable(
				'labm_slide',
				array(
					'post_title'       => 'Slide',
					'thumbnail_id'     => $image_id,
					'labm_destino_url' => 'javascript:alert(1)',
				)
			)
		);
		self::assertTrue(
			labm_core_validate_home_publishable(
				'labm_slide',
				array(
					'post_title'       => 'Slide',
					'thumbnail_id'     => $image_id,
					'labm_destino_url' => 'https://example.org',
				)
			)
		);
		self::assertTrue(
			labm_core_validate_home_publishable(
				'labm_aliado',
				array(
					'post_title'       => 'Aliado legado',
					'thumbnail_id'     => $image_id,
					'labm_destino_url' => 'javascript:legado',
				)
			)
		);

		wp_delete_attachment( $image_id, true );
		wp_delete_attachment( $pdf_id, true );
	}

	/** Los seis logos demo conservan nombres, formato, dimensiones y transparencia. */
	public function test_demo_allies_assets_are_stable_transparent_png_files(): void {
		$runtime_root = getenv( 'WP_TESTS_RUNTIME_ROOT' );
		$runtime_root = false !== $runtime_root ? $runtime_root : '/wordpress';
		$directory    = $runtime_root . '/wp-content/themes/labm/assets/images/aliados-demo';
		$expected     = array(
			'arco-comun.png'    => 'a4f86d84f72c53e8818caa1021ebf5f4325ff40bf8c6486bb6d19d1ce172adfa',
			'brote-activo.png'  => 'b228916f7f66eb723db78fbdb20720c51710c4726c8faebe9a63ef2def9781c3',
			'cumbre-viva.png'   => '9903b94b2baf1e3f34232821094e81cfb9e1cf38c1d928405b4e5b6d44dd2c07',
			'mosaico-unido.png' => '9089e05b013db0a29749c5cd625a409780a083b8b66a59e4a5cadfb03b3aaa2f',
			'rio-dinamico.png'  => '4ddcb66fc0847473b901e820aeed0b6a914ad79e4d229deb7666185e923642bf',
			'sol-abierto.png'   => '8dc41fc7b4afbdff4b6e0965ce317c9857c757de839b76c4b105583ea96a8c80',
		);
		$matches      = glob( $directory . '/*.png' );
		$actual       = array_map( 'basename', false !== $matches ? $matches : array() );
		sort( $actual );

		self::assertSame( array_keys( $expected ), $actual );
		$manifest_path = $directory . '/originality-manifest.json';
		self::assertFileExists( $manifest_path );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Manifiesto local controlado por el repositorio.
		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true, 512, JSON_THROW_ON_ERROR );
		self::assertSame( 'aprobado', $manifest['revision']['decision'] ?? null );
		self::assertSame( array( 'sin texto', 'identidades diferenciadas', 'sin similitud evidente con marcas reales' ), $manifest['revision']['criterios'] ?? null );
		self::assertStringContainsString( 'juicio humano', $manifest['revision']['limitacion'] ?? '' );
		self::assertSame( $expected, $manifest['archivos'] ?? null );

		foreach ( $expected as $filename => $hash ) {
			$path = $directory . '/' . $filename;
			$size = getimagesize( $path );
			self::assertIsArray( $size, $filename );
			self::assertSame( array( 800, 400 ), array_slice( $size, 0, 2 ), $filename );
			self::assertSame( IMAGETYPE_PNG, $size[2], $filename );
			self::assertSame( $hash, hash_file( 'sha256', $path ), $filename . ' no coincide con el recurso revisado.' );

			$image = imagecreatefrompng( $path );
			self::assertInstanceOf( GdImage::class, $image, $filename );
			$corner_alpha = ( imagecolorat( $image, 0, 0 ) >> 24 ) & 0x7F;
			imagedestroy( $image );
			self::assertSame( 127, $corner_alpha, $filename . ' debe conservar fondo transparente.' );
		}
	}

	public function test_home_fixtures_are_idempotent_and_marked_as_fictitious(): void {
		$runtime_root = getenv( 'WP_TESTS_RUNTIME_ROOT' ) ?: '/wordpress';
		require_once $runtime_root . '/wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php';
		$command = new LABM_Fixtures_Command();
		$command->load( array(), array() );
		$command->load( array(), array() );

		foreach ( array(
			'demo-labm-slide-bienvenida' => 'labm_slide',
			'demo-labm-aliado-ejemplo'   => 'labm_aliado',
		) as $slug => $post_type ) {
			$posts = get_posts(
				array(
					'name'           => $slug,
					'post_type'      => $post_type,
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => -1,
				)
			);
			self::assertCount( 1, $posts, $slug );
			self::assertStringContainsString( 'FICTICIO', $posts[0]->post_title );
		}
	}
}
