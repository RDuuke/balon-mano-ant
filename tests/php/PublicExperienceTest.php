<?php

use PHPUnit\Framework\TestCase;

final class PublicExperienceTest extends TestCase {
	public function test_theme_declares_patterns_and_public_templates(): void {
		foreach ( array( 'inicio', 'nosotros' ) as $pattern ) {
			self::assertFileExists( dirname( __DIR__, 2 ) . "/wp-content/themes/labm/patterns/{$pattern}.php" );
		}
		foreach ( array( 'page-nosotros', 'archive-labm_actualidad', 'archive-labm_seleccion', 'single-labm_actualidad', 'single-labm_seleccion', '404' ) as $template ) {
			self::assertFileExists( dirname( __DIR__, 2 ) . "/wp-content/themes/labm/templates/{$template}.html" );
		}
	}

	public function test_public_domain_routes_use_canonical_slugs(): void {
		self::assertSame( 'actualidad', get_post_type_object( 'labm_actualidad' )->rewrite['slug'] );
		self::assertSame( 'selecciones', get_post_type_object( 'labm_seleccion' )->rewrite['slug'] );
	}

	public function test_public_query_filters_and_excludes_non_public_content(): void {
		self::assertTrue( function_exists( 'labm_theme_public_query' ) );
		$actualidad = labm_theme_public_query( 'labm_actualidad', array( 'categoria' => 'Noticias' ), 1, 20 );
		self::assertGreaterThanOrEqual( 1, $actualidad->post_count );
		foreach ( $actualidad->posts as $post ) {
			self::assertSame( 'publish', $post->post_status );
			self::assertTrue( has_term( 'Noticias', 'labm_categoria', $post ) );
		}

		$piso  = labm_theme_public_query( 'labm_seleccion', array( 'modalidad' => 'Piso' ), 1, 20 );
		$playa = labm_theme_public_query( 'labm_seleccion', array( 'modalidad' => 'Playa' ), 1, 20 );
		self::assertGreaterThanOrEqual( 1, $piso->post_count );
		self::assertGreaterThanOrEqual( 1, $playa->post_count );
		self::assertStringNotContainsString( 'privada', strtolower( wp_json_encode( $piso->posts ) ) );
	}

	public function test_theme_has_safe_fallback_when_domain_is_unavailable(): void {
		self::assertTrue( function_exists( 'labm_theme_render_listing' ) );
		$fallback = labm_theme_render_listing( 'tipo_ausente', array() );
		self::assertStringContainsString( 'no está disponible', html_entity_decode( $fallback, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}
