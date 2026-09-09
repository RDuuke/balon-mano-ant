<?php

use PHPUnit\Framework\TestCase;

final class PublicExperienceTest extends TestCase {
	/** Misión y Visión se renderizan como artículos independientes. */
	public function test_about_page_renders_mission_and_vision_editorial_section(): void {
		$html = labm_theme_render_about_purpose();
		self::assertStringContainsString( 'data-labm-section="nosotros-proposito"', $html );
		self::assertStringContainsString( 'data-labm-purpose="mision"', $html );
		self::assertStringContainsString( 'data-labm-purpose="vision"', $html );
		self::assertStringContainsString( '>01<', $html );
		self::assertStringContainsString( '>02<', $html );
		self::assertLessThan( strpos( $html, 'data-labm-purpose="vision"' ), strpos( $html, 'data-labm-purpose="mision"' ) );
		self::assertStringNotContainsString( '[DEMO LABM', $html );
	}

	/** Cada artículo inválido se omite sin afectar al otro. */
	public function test_about_purpose_omits_each_unpublishable_article_independently(): void {
		$mission = get_page_by_path( 'mision-nosotros', OBJECT, 'post' );
		$vision  = get_page_by_path( 'vision-nosotros', OBJECT, 'post' );
		self::assertInstanceOf( WP_Post::class, $mission );
		self::assertInstanceOf( WP_Post::class, $vision );
		wp_update_post( array( 'ID' => $mission->ID, 'post_status' => 'draft' ) );
		$html = labm_theme_render_about_purpose();
		self::assertStringNotContainsString( 'data-labm-purpose="mision"', $html );
		self::assertStringContainsString( 'data-labm-purpose="vision"', $html );
		wp_update_post( array( 'ID' => $vision->ID, 'post_status' => 'draft' ) );
		self::assertSame( '', labm_theme_render_about_purpose() );
		wp_update_post( array( 'ID' => $mission->ID, 'post_status' => 'publish' ) );
		wp_update_post( array( 'ID' => $vision->ID, 'post_status' => 'publish' ) );
	}

	/** Nosotros obtiene un banner estático semántico de una entrada publicada. */
	public function test_about_page_renders_published_editorial_banner(): void {
		$post = get_page_by_path( 'banner-nosotros', OBJECT, 'post' );
		self::assertInstanceOf( WP_Post::class, $post );
		$html = labm_theme_render_about_banner();

		self::assertStringContainsString( 'data-labm-section="nosotros-banner"', $html );
		self::assertStringContainsString( '<article', $html );
		self::assertStringContainsString( '<h1', $html );
		self::assertStringContainsString( 'Somos la Liga', $html );
		self::assertStringNotContainsString( '[DEMO LABM', $html );
		self::assertStringContainsString( 'labm-about-banner__media', $html );
		self::assertStringNotContainsString( 'data-labm-slider', $html );
		self::assertStringNotContainsString( '<button', $html );
	}

	/** Un artículo no público se omite y uno sin imagen conserva el panel editorial. */
	public function test_about_banner_omits_unpublishable_or_incomplete_article(): void {
		$post = get_page_by_path( 'banner-nosotros', OBJECT, 'post' );
		self::assertInstanceOf( WP_Post::class, $post );
		$original_status    = $post->post_status;
		$original_thumbnail = get_post_thumbnail_id( $post->ID );

		wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'draft' ) );
		self::assertSame( '', labm_theme_render_about_banner() );

		wp_update_post( array( 'ID' => $post->ID, 'post_status' => $original_status ) );
		delete_post_thumbnail( $post->ID );
		$without_image = labm_theme_render_about_banner();
		self::assertStringContainsString( 'Somos la Liga', $without_image );
		self::assertStringNotContainsString( 'labm-about-banner__media', $without_image );

		set_post_thumbnail( $post->ID, $original_thumbnail );
	}
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
