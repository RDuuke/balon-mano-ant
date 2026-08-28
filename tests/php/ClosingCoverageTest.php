<?php

use PHPUnit\Framework\TestCase;

if ( ! class_exists( 'WP_CLI' ) ) {
	/** Doble minimo para ejecutar el comando de fixtures dentro de PHPUnit. */
	class WP_CLI {
		/** @var array<int, string> */
		public static array $messages = array();

		public static function warning( string $message ): void {
			self::$messages[] = 'warning:' . $message;
		}

		public static function error( string $message ): void {
			throw new RuntimeException( $message );
		}

		public static function success( string $message ): void {
			self::$messages[] = 'success:' . $message;
		}
	}
}

$labm_runtime_root = getenv( 'WP_TESTS_RUNTIME_ROOT' ) ?: '/wordpress';
require_once $labm_runtime_root . '/wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php';

final class ClosingCoverageTest extends TestCase {
	public function test_domain_registration_and_activation_are_idempotent(): void {
		labm_core_load_textdomain();
		labm_core_register_content_types();
		labm_core_register_meta();
		delete_option( 'labm_core_capabilities_version' );
		delete_option( 'labm_core_rewrite_version' );
		labm_core_ensure_capabilities();
		labm_core_ensure_capabilities();
		labm_core_ensure_rewrite_rules();
		labm_core_ensure_rewrite_rules();
		labm_core_activate();

		self::assertTrue( post_type_exists( 'labm_documento' ) );
		self::assertTrue( taxonomy_exists( 'labm_documento_categoria' ) );
		self::assertSame( LABM_CORE_CAPABILITIES_VERSION, get_option( 'labm_core_capabilities_version' ) );
		self::assertFalse( get_option( 'labm_core_rewrite_version' ) );
	}

	public function test_domain_helpers_cover_valid_invalid_and_authorized_values(): void {
		self::assertFalse( labm_core_validate_iso_date( array() ) );
		self::assertSame( '2026-08-27', labm_core_sanitize_iso_date( '2026-08-27' ) );
		self::assertSame( '', labm_core_sanitize_iso_date( '2026-02-30' ) );

		$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
		wp_set_current_user( $admin->ID );
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'draft',
				'post_title'  => 'Autorizacion de metadatos',
			)
		);
		self::assertTrue( labm_core_auth_post_meta( false, 'labm_fecha_evento', $post_id ) );
		wp_delete_post( $post_id, true );
	}

	public function test_fixture_command_is_idempotent_and_preserves_foreign_content(): void {
		$foreign = get_page_by_path( 'demo-labm-inicio', OBJECT, 'page' );
		self::assertInstanceOf( WP_Post::class, $foreign );
		$foreign_id     = $foreign->ID;
		$original_title = $foreign->post_title;
		wp_update_post( array( 'ID' => $foreign_id, 'post_title' => 'Contenido editorial ajeno' ) );
		WP_CLI::$messages = array();
		$command = new LABM_Fixtures_Command();
		$command->load( array(), array() );
		$command->load( array(), array() );

		self::assertSame( 'Contenido editorial ajeno', get_post( $foreign_id )->post_title );
		self::assertNotEmpty( array_filter( WP_CLI::$messages, static fn( string $message ): bool => str_starts_with( $message, 'warning:' ) ) );
		self::assertCount( 1, get_posts( array( 'name' => 'demo-labm-seleccion-playa', 'post_type' => 'labm_seleccion', 'post_status' => 'publish' ) ) );
		self::assertSame( array( 'Playa' ), wp_get_post_terms( get_page_by_path( 'demo-labm-seleccion-playa', OBJECT, 'labm_seleccion' )->ID, 'labm_modalidad', array( 'fields' => 'names' ) ) );
		wp_update_post( array( 'ID' => $foreign_id, 'post_title' => $original_title ) );
	}

	public function test_theme_public_hooks_fallback_and_listing_states_render_safely(): void {
		labm_theme_enqueue_public_style();
		labm_theme_setup_public_experience();
		ob_start();
		labm_theme_skip_link();
		$skip_link = (string) ob_get_clean();

		self::assertStringContainsString( '#contenido-principal', $skip_link );
		self::assertStringContainsString( 'LABM Core', labm_theme_domain_summary() );
		self::assertSame( 'labm_theme_actualidad_shortcode', $GLOBALS['shortcode_tags']['labm_actualidad_listado'] );

		$listing = labm_theme_render_listing( 'labm_actualidad', array( 'categoria' => 'Noticias', 'pagina' => 1 ) );
		self::assertStringContainsString( 'data-labm-listado="actualidad"', $listing );
		self::assertStringContainsString( 'Aplicar filtro', $listing );

		$_GET = array( 'categoria' => 'Categoria inexistente' );
		self::assertStringContainsString( 'No hay publicaciones', labm_theme_actualidad_shortcode() );
		$_GET = array( 'modalidad' => 'Piso' );
		self::assertStringContainsString( 'data-labm-listado="selecciones"', labm_theme_selecciones_shortcode() );
		$_GET = array();
	}
}
