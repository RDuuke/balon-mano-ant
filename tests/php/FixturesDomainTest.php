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

final class FixturesDomainTest extends TestCase {
	/** Los aliados demo reutilizan seis adjuntos PNG ordenados al recargar fixtures. */
	public function test_home_allies_fixtures_are_image_only_ordered_and_idempotent(): void {
		$command = new LABM_Fixtures_Command();
		$slugs   = array( 'arco-comun', 'brote-activo', 'cumbre-viva', 'mosaico-unido', 'rio-dinamico', 'sol-abierto' );
		$first   = array();

		$command->load( array(), array() );
		foreach ( $slugs as $order => $slug ) {
			$post = get_page_by_path( 'demo-labm-aliado-' . $slug, OBJECT, 'labm_aliado' );
			self::assertInstanceOf( WP_Post::class, $post, $slug );
			self::assertSame( 'publish', $post->post_status );
			self::assertSame( $order, (int) $post->menu_order );
			self::assertSame( '', $post->post_content );
			self::assertSame( '', get_post_meta( $post->ID, 'labm_destino_url', true ) );
			$attachment_id = get_post_thumbnail_id( $post->ID );
			self::assertGreaterThan( 0, $attachment_id, $slug );
			self::assertSame( 'image/png', get_post_mime_type( $attachment_id ) );
			$first[ $slug ] = array( $post->ID, $attachment_id );
		}

		$command->load( array(), array() );
		foreach ( $slugs as $slug ) {
			$post = get_page_by_path( 'demo-labm-aliado-' . $slug, OBJECT, 'labm_aliado' );
			self::assertSame( $first[ $slug ], array( $post->ID, get_post_thumbnail_id( $post->ID ) ) );
		}
	}

	/** Una imagen demo ausente no impide consultar la noticia ni deja una asociacion rota. */
	public function test_home_news_fixture_without_available_media_remains_consultable_with_fallback(): void {
		$slug           = 'demo-labm-noticia-convocatoria';
		$unavailable    = 'assets/images/hero-balonmano-seleccion-v1.png';
		$missing_asset  = static function ( $path, $file ) use ( $unavailable ) {
			return $unavailable === $file ? dirname( $path ) . '/activo-demo-ausente.png' : $path;
		};
		$fixture_command = new LABM_Fixtures_Command();

		add_filter( 'theme_file_path', $missing_asset, 10, 2 );
		try {
			$fixture_command->load( array(), array() );
			$post = get_page_by_path( $slug, OBJECT, 'labm_actualidad' );
			self::assertInstanceOf( WP_Post::class, $post );
			self::assertSame( 'publish', $post->post_status );
			self::assertSame( '', get_post_meta( $post->ID, 'labm_demo_image', true ) );
			self::assertStringContainsString( 'hero-balonmano-antioquia-v1.png', labm_theme_home_news_media( $post, true ) );
			self::assertStringNotContainsString( 'activo-demo-ausente.png', labm_theme_home_news_media( $post, true ) );
		} finally {
			remove_filter( 'theme_file_path', $missing_asset, 10 );
			$fixture_command->load( array(), array() );
		}
	}

	/** Las noticias de portada forman una coleccion demo completa y estable. */
	public function test_home_news_fixtures_are_complete_categorized_and_deterministic(): void {
		$slugs = array(
			'demo-labm-noticia-resultado',
			'demo-labm-noticia-convocatoria',
			'demo-labm-noticia-clubes',
			'demo-labm-noticia-calendario',
			'demo-labm-noticia-formacion',
			'demo-labm-noticia-seleccion',
		);
		$dates = array();
		foreach ( $slugs as $slug ) {
			$post = get_page_by_path( $slug, OBJECT, 'labm_actualidad' );
			self::assertInstanceOf( WP_Post::class, $post, $slug );
			self::assertSame( 'publish', $post->post_status, $slug );
			self::assertStringContainsString( 'FICTICIO', $post->post_title, $slug );
			self::assertSame( array( 'Noticias demo' ), wp_get_post_terms( $post->ID, 'labm_categoria', array( 'fields' => 'names' ) ), $slug );
			self::assertContains(
				get_post_meta( $post->ID, 'labm_demo_image', true ),
				array( 'assets/images/hero-balonmano-antioquia-v1.png', 'assets/images/hero-balonmano-seleccion-v1.png' ),
				$slug
			);
			$dates[] = $post->post_date;
		}
		self::assertCount( 6, array_unique( $dates ) );
		$term = get_term_by( 'name', 'Noticias demo', 'labm_categoria' );
		self::assertInstanceOf( WP_Term::class, $term );
		self::assertSame( 6, (int) $term->count );
	}

	public function test_domain_fixtures_cover_public_draft_private_and_edge_states(): void {
		$expected = array(
			'demo-labm-actualidad-limite'   => array( 'labm_actualidad', 'publish' ),
			'demo-labm-actualidad-borrador' => array( 'labm_actualidad', 'draft' ),
			'demo-labm-seleccion-privada'   => array( 'labm_seleccion', 'private' ),
			'demo-labm-club-frontera'        => array( 'labm_club', 'publish' ),
			'demo-labm-integrante-vacante'   => array( 'labm_integrante', 'draft' ),
			'demo-labm-horario-limite'       => array( 'labm_horario', 'publish' ),
		);

		foreach ( $expected as $slug => $definition ) {
			$post = get_page_by_path( $slug, OBJECT, $definition[0] );
			self::assertInstanceOf( WP_Post::class, $post, $slug );
			self::assertSame( $definition[1], $post->post_status, $slug );
			self::assertStringContainsString( 'FICTICIO', $post->post_title, $slug );
		}
	}

	public function test_fixture_terms_and_metadata_are_present(): void {
		$actualidad = get_page_by_path( 'demo-labm-actualidad-limite', OBJECT, 'labm_actualidad' );
		$seleccion  = get_page_by_path( 'demo-labm-seleccion-privada', OBJECT, 'labm_seleccion' );
		self::assertSame( '2026-01-01', get_post_meta( $actualidad->ID, 'labm_fecha_evento', true ) );
		self::assertSame( array( 'Noticias' ), wp_get_post_terms( $actualidad->ID, 'labm_categoria', array( 'fields' => 'names' ) ) );
		self::assertSame( array( 'Piso' ), wp_get_post_terms( $seleccion->ID, 'labm_modalidad', array( 'fields' => 'names' ) ) );
	}

	public function test_fixtures_do_not_create_pdf_attachments(): void {
		$query = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'application/pdf',
			)
		);
		self::assertSame( 0, $query->post_count );
	}

	public function test_each_domain_fixture_slug_is_unique(): void {
		$fixtures = array(
			'labm_actualidad' => array( 'demo-labm-actualidad-limite', 'demo-labm-actualidad-borrador' ),
			'labm_seleccion'  => array( 'demo-labm-seleccion-privada' ),
			'labm_club'       => array( 'demo-labm-club-frontera' ),
			'labm_integrante' => array( 'demo-labm-integrante-vacante' ),
			'labm_horario'    => array( 'demo-labm-horario-limite' ),
		);
		foreach ( $fixtures as $post_type => $slugs ) {
			foreach ( $slugs as $slug ) {
				$posts = get_posts(
					array(
						'name'           => $slug,
						'post_type'      => $post_type,
						'post_status'    => array( 'publish', 'draft', 'private' ),
						'posts_per_page' => -1,
					)
				);
				self::assertCount( 1, $posts, $slug );
			}
		}
	}
}
