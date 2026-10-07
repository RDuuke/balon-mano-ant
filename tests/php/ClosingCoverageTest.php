<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

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
	/** Repetir el registro conserva los contratos REST y las capacidades del dominio. */
	public function test_registration_preserves_public_types_taxonomies_and_meta_contracts(): void {
		for ( $attempt = 0; $attempt < 2; ++$attempt ) {
			labm_core_register_content_types();
			labm_core_register_meta();
			foreach ( array( 'labm_actualidad', 'labm_seleccion', 'labm_club', 'labm_integrante', 'labm_horario', 'labm_documento' ) as $type ) {
				$object = get_post_type_object( $type );
				self::assertTrue( $object->public );
				self::assertTrue( $object->show_in_rest );
				self::assertTrue( $object->map_meta_cap );
				self::assertTrue( post_type_supports( $type, 'custom-fields' ) );
			}
			self::assertSame( 'selecciones', get_post_type_object( 'labm_seleccion' )->rewrite['slug'] );
			foreach ( array( 'labm_modalidad', 'labm_categoria', 'labm_documento_categoria', 'labm_grupo_integrante' ) as $taxonomy ) {
				self::assertTrue( get_taxonomy( $taxonomy )->show_in_rest );
				self::assertTrue( get_taxonomy( $taxonomy )->hierarchical );
			}
			$meta = get_registered_meta_keys( 'post', 'labm_documento' );
			self::assertSame( 'integer', $meta['labm_documento_pdf_id']['type'] );
			self::assertSame( 0, $meta['labm_documento_pdf_id']['default'] );
			self::assertSame( 'absint', $meta['labm_documento_pdf_id']['sanitize_callback'] );
			self::assertSame( 'labm_core_sanitize_iso_date', $meta['labm_documento_fecha']['sanitize_callback'] );
			self::assertSame( 'labm_core_auth_post_meta', $meta['labm_documento_fecha']['auth_callback'] );
			self::assertSame( 'manage_labm_documento_types', get_taxonomy( 'labm_documento_categoria' )->cap->manage_terms );
		}
	}

	/** Una version vigente no oculta capacidades editoriales perdidas. */
	public function test_capability_repair_restores_editorial_access_without_granting_type_governance(): void {
		$roles = array();
		$version = get_option( 'labm_core_capabilities_version', null );
		foreach ( array( 'administrator', 'editor' ) as $name ) {
			$roles[ $name ] = get_role( $name )->capabilities;
		}
		try {
			get_role( 'editor' )->remove_cap( 'edit_labm_selecciones' );
			get_role( 'editor' )->remove_cap( 'assign_labm_documento_types' );
			get_role( 'editor' )->add_cap( 'manage_labm_documento_types' );
			update_option( 'labm_core_capabilities_version', LABM_CORE_CAPABILITIES_VERSION );
			labm_core_ensure_capabilities();
			labm_core_ensure_capabilities();
			self::assertTrue( get_role( 'editor' )->has_cap( 'edit_labm_selecciones' ) );
			self::assertTrue( get_role( 'editor' )->has_cap( 'assign_labm_documento_types' ) );
			self::assertFalse( get_role( 'editor' )->has_cap( 'manage_labm_documento_types' ) );
			self::assertTrue( get_role( 'administrator' )->has_cap( 'manage_labm_documento_types' ) );
			self::assertSame( LABM_CORE_CAPABILITIES_VERSION, get_option( 'labm_core_capabilities_version' ) );
		} finally {
			foreach ( $roles as $name => $capabilities ) {
				$role = get_role( $name );
				foreach ( array_diff_key( $role->capabilities, $capabilities ) as $cap => $grant ) {
					$role->remove_cap( $cap );
				}
				foreach ( $capabilities as $cap => $grant ) {
					$role->add_cap( $cap, $grant );
				}
			}
			null === $version ? delete_option( 'labm_core_capabilities_version' ) : update_option( 'labm_core_capabilities_version', $version );
		}
	}

	/**
	 * La suite completa reinyecta la opcion de rutas por estado compartido;
	 * el caso aislado sigue cubriendo el contrato de activacion.
	 */
	#[Group( 'labm-temporary-suite-state' )]
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
