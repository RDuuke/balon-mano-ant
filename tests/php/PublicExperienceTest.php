<?php

use PHPUnit\Framework\TestCase;

final class PublicExperienceTest extends TestCase {
	/** Portada y Nosotros reutilizan una única salida semántica para vinculación. */
	public function test_about_reuses_home_join_cta_after_team(): void {
		$html = labm_theme_render_join_cta();
		self::assertStringContainsString( 'data-labm-section="vinculacion"', $html );
		self::assertStringContainsString( 'Haz parte del balonmano antioqueño', $html );
		self::assertStringContainsString( 'Conecta con la Liga, sus clubes y procesos deportivos.', $html );
		self::assertStringContainsString( '>Contáctanos</a>', $html );
		self::assertStringContainsString( 'href="/contacto/"', $html );

		$root  = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/patterns/';
		$home  = file_get_contents( $root . 'inicio.php' );
		$about = file_get_contents( $root . 'nosotros.php' );
		$team  = strpos( $about, 'labm_theme_render_about_team()' );
		$join  = strpos( $about, 'labm_theme_render_join_cta()' );
		self::assertSame( 1, substr_count( $home, 'labm_theme_render_join_cta()' ) );
		self::assertSame( 1, substr_count( $about, 'labm_theme_render_join_cta()' ) );
		self::assertIsInt( $team );
		self::assertIsInt( $join );
		self::assertLessThan( $join, $team );
	}

	/** El CTA declara y usa Barlow Condensed como activo local con licencia. */
	public function test_join_cta_uses_local_barlow_condensed_font(): void {
		$theme_root = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/';
		$theme      = json_decode( (string) file_get_contents( $theme_root . 'theme.json' ), true );
		$families   = $theme['settings']['typography']['fontFamilies'] ?? array();
		$barlow     = null;
		foreach ( $families as $family ) {
			if ( 'barlow-condensed' === ( $family['slug'] ?? '' ) ) {
				$barlow = $family;
				break;
			}
		}

		self::assertIsArray( $barlow );
		self::assertSame( 'Barlow Condensed', $barlow['name'] );
		self::assertSame( 'Barlow Condensed', $barlow['fontFamily'] );
		self::assertSame( 'file:./assets/fonts/barlow-condensed-latin-wght-normal.woff2', $barlow['fontFace'][0]['src'][0] );
		self::assertSame( '700', $barlow['fontFace'][0]['fontWeight'] );
		self::assertSame( 'normal', $barlow['fontFace'][0]['fontStyle'] );
		self::assertSame( 'swap', $barlow['fontFace'][0]['fontDisplay'] );

		$font_path = $theme_root . 'assets/fonts/barlow-condensed-latin-wght-normal.woff2';
		self::assertFileExists( $font_path );
		self::assertGreaterThan( 10000, filesize( $font_path ) );
		self::assertStringContainsString( 'SIL OPEN FONT LICENSE', (string) file_get_contents( $theme_root . 'assets/fonts/OFL.txt' ) );

		$css = (string) file_get_contents( $theme_root . 'style.css' );
		self::assertMatchesRegularExpression( '/\.labm-home-join h2\s*\{[^}]*font-family:\s*var\(--wp--preset--font-family--barlow-condensed\),\s*sans-serif;/s', $css );
	}

	/** Nosotros muestra integrantes completos y permite filtrar por grupo. */
	public function test_about_team_renders_editable_published_members_and_filters(): void {
		$html = labm_theme_render_about_team();
		self::assertStringContainsString( 'data-labm-section="nosotros-equipo"', $html );
		self::assertStringContainsString( 'Quiénes hacen posible la Liga', $html );
		self::assertSame( 4, substr_count( $html, 'data-labm-team-card' ) );
		self::assertStringContainsString( 'Andrés Montoya', $html );
		self::assertStringContainsString( 'Director técnico', $html );

		$filtered = labm_theme_render_about_team( 'entrenadores' );
		self::assertSame( 2, substr_count( $filtered, 'data-labm-team-card' ) );
		self::assertStringContainsString( 'aria-current="true"', $filtered );
		self::assertStringNotContainsString( 'Mateo Giraldo', $filtered );
	}

	/** Integrantes incompletos o no públicos nunca se revelan. */
	public function test_about_team_omits_incomplete_and_restricted_members(): void {
		$draft = get_page_by_path( 'demo-labm-integrante-vacante', OBJECT, 'labm_integrante' );
		self::assertInstanceOf( WP_Post::class, $draft );
		$html = labm_theme_render_about_team();
		self::assertStringNotContainsString( 'Vacante de ejemplo', $html );

		$member = get_page_by_path( 'demo-labm-integrante-andres-montoya', OBJECT, 'labm_integrante' );
		self::assertInstanceOf( WP_Post::class, $member );
		$thumbnail = get_post_thumbnail_id( $member->ID );
		delete_post_thumbnail( $member->ID );
		self::assertStringNotContainsString( 'Andrés Montoya', labm_theme_render_about_team() );
		set_post_thumbnail( $member->ID, $thumbnail );
	}

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
		wp_update_post(
			array(
				'ID'          => $mission->ID,
				'post_status' => 'draft',
			)
		);
		$html = labm_theme_render_about_purpose();
		self::assertStringNotContainsString( 'data-labm-purpose="mision"', $html );
		self::assertStringContainsString( 'data-labm-purpose="vision"', $html );
		wp_update_post(
			array(
				'ID'          => $vision->ID,
				'post_status' => 'draft',
			)
		);
		self::assertSame( '', labm_theme_render_about_purpose() );
		wp_update_post(
			array(
				'ID'          => $mission->ID,
				'post_status' => 'publish',
			)
		);
		wp_update_post(
			array(
				'ID'          => $vision->ID,
				'post_status' => 'publish',
			)
		);
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

		wp_update_post(
			array(
				'ID'          => $post->ID,
				'post_status' => 'draft',
			)
		);
		self::assertSame( '', labm_theme_render_about_banner() );

		wp_update_post(
			array(
				'ID'          => $post->ID,
				'post_status' => $original_status,
			)
		);
		delete_post_thumbnail( $post->ID );
		$without_image = labm_theme_render_about_banner();
		self::assertStringContainsString( 'Somos la Liga', $without_image );
		self::assertStringNotContainsString( 'labm-about-banner__media', $without_image );

		set_post_thumbnail( $post->ID, $original_thumbnail );
	}

	/** Documentos obtiene su encabezado de un artículo editorial editable y publicado. */
	public function test_documents_page_renders_editable_editorial_banner(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_name'    => 'banner-documentos',
				'post_title'   => 'Documentos',
				'post_excerpt' => 'Resoluciones, circulares y archivos públicos de la Liga.',
			)
		);

		try {
			$html = labm_theme_render_documents_banner();

			self::assertStringContainsString( 'data-labm-section="documentos-banner"', $html );
			self::assertStringContainsString( '<article', $html );
			self::assertStringContainsString( 'Transparencia y consulta', $html );
			self::assertStringContainsString( '<h1', $html );
			self::assertStringContainsString( '>Documentos</h1>', $html );
			self::assertStringContainsString( 'Resoluciones, circulares y archivos públicos de la Liga.', $html );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** Documentos no muestra contenido editorial no público o incompleto. */
	public function test_documents_banner_omits_unpublishable_content_and_escapes_text(): void {
		$post = get_page_by_path( 'banner-documentos', OBJECT, 'post' );
		self::assertInstanceOf( WP_Post::class, $post );
		$original = array(
			'post_status'  => $post->post_status,
			'post_title'   => $post->post_title,
			'post_excerpt' => $post->post_excerpt,
			'post_content' => $post->post_content,
		);

		try {
			wp_update_post(
				array(
					'ID'          => $post->ID,
					'post_status' => 'draft',
				)
			);
			self::assertSame( '', labm_theme_render_documents_banner() );
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_status'  => 'publish',
					'post_title'   => '<script>Documentos privados</script>',
					'post_excerpt' => '',
					'post_content' => '<strong>Resumen público</strong>',
				)
			);
			$html = labm_theme_render_documents_banner();
			self::assertStringContainsString( 'Documentos privados', $html );
			self::assertStringContainsString( 'Resumen público', $html );
			self::assertStringNotContainsString( '<script>', $html );
			self::assertStringNotContainsString( '<strong>', $html );
			wp_update_post(
				array(
					'ID'         => $post->ID,
					'post_title' => '',
				)
			);
			self::assertSame( '', labm_theme_render_documents_banner() );
		} finally {
			wp_update_post( array_merge( array( 'ID' => $post->ID ), $original ) );
		}
	}

	/** La plantilla y el patrón aíslan la composición de Documentos. */
	public function test_documents_pattern_and_template_compose_the_editorial_banner(): void {
		$root     = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/';
		$pattern  = (string) file_get_contents( $root . 'patterns/documentos.php' );
		$template = (string) file_get_contents( $root . 'templates/page-documentos.html' );
		$about    = (string) file_get_contents( $root . 'templates/page-nosotros.html' );
		$css      = (string) file_get_contents( $root . 'style.css' );

		self::assertStringContainsString( 'labm_theme_render_documents_banner()', $pattern );
		self::assertStringContainsString( '"slug":"labm/documentos"', $template );
		self::assertStringContainsString( '"slug":"header"', $template );
		self::assertStringContainsString( '"slug":"footer"', $template );
		self::assertStringNotContainsString( 'labm/documentos', $about );
		self::assertMatchesRegularExpression( '/\.labm-documents-banner\s*\{[^}]*background:\s*#000;[^}]*color:\s*#fff;/s', $css );
		self::assertMatchesRegularExpression( '/\.labm-documents-banner\s*\{[^}]*min-height:\s*22\.3125rem;/s', $css );
		self::assertMatchesRegularExpression( '/\.labm-documents-banner__content\s*\{[^}]*min-width:\s*0;[^}]*padding-block:/s', $css );
		self::assertMatchesRegularExpression( '/\.labm-documents-banner__content\s*\{[^}]*width:\s*100%;[^}]*margin:\s*0\s*!important;[^}]*padding-inline:\s*clamp\(2rem,\s*8\.5vw,\s*8rem\);/s', $css );
		self::assertMatchesRegularExpression( '/\.labm-documents-banner h1\s*\{[^}]*font-size:\s*clamp\(/s', $css );
	}

	/** El patrón añade exclusivamente el catálogo accesible debajo del encabezado. */
	public function test_documents_pattern_composes_the_simple_pdf_catalog_without_filters(): void {
		$root    = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/';
		$pattern = (string) file_get_contents( $root . 'patterns/documentos.php' );
		$css     = (string) file_get_contents( $root . 'style.css' );

		$catalog_call = 'labm_core_render_document_catalog( array(), labm_core_document_catalog_current_page() )';
		self::assertStringContainsString( $catalog_call, $pattern );
		self::assertLessThan( strpos( $pattern, $catalog_call ), strpos( $pattern, 'labm_theme_render_documents_banner()' ) );
		self::assertStringNotContainsString( 'labm-filter', $pattern );
		self::assertMatchesRegularExpression( '/\.labm-documents-catalog\s*\{[^}]*max-width:/s', $css );
		self::assertMatchesRegularExpression( '/\.labm-documents-pagination\s*\{[^}]*display:\s*flex;/s', $css );
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

	/** Actualidad combina texto y categoria, pagina cuatro publicadas y separa una destacada de tres tarjetas. */
	public function test_actualidad_query_and_listing_preserve_the_public_editorial_contract(): void {
		$term = get_term_by( 'name', 'Noticias', 'labm_categoria' );
		self::assertNotFalse( $term );
		$post_ids = array();

		try {
			for ( $index = 1; $index <= 5; $index++ ) {
				$post_ids[] = wp_insert_post(
					array(
						'post_type'    => 'labm_actualidad',
						'post_status'  => 'publish',
						'post_title'   => sprintf( 'Contrato actualidad %d', $index ),
						'post_excerpt' => 'Resumen para comprobar la composicion editorial.',
						'post_date'    => sprintf( '2026-09-%02d 12:00:00', $index ),
					)
				);
				wp_set_object_terms( $post_ids[ $index - 1 ], (int) $term->term_id, 'labm_categoria' );
			}
			$draft_id = wp_insert_post(
				array(
					'post_type'   => 'labm_actualidad',
					'post_status' => 'draft',
					'post_title'  => 'Contrato actualidad privada',
				)
			);
			wp_set_object_terms( $draft_id, (int) $term->term_id, 'labm_categoria' );

			$query = labm_theme_public_query(
				'labm_actualidad',
				array(
					'texto'     => 'Contrato actualidad',
					'categoria' => 'Noticias',
				),
				1,
				4
			);
			self::assertSame( 4, $query->post_count );
			self::assertSame( 'Contrato actualidad 5', $query->posts[0]->post_title );
			foreach ( $query->posts as $post ) {
				self::assertSame( 'publish', $post->post_status );
				self::assertTrue( has_term( 'Noticias', 'labm_categoria', $post ) );
				self::assertStringContainsString( 'Contrato actualidad', $post->post_title );
			}

			$html = labm_theme_render_listing(
				'labm_actualidad',
				array(
					'texto'     => 'Contrato actualidad',
					'categoria' => 'Noticias',
				)
			);
			self::assertStringContainsString( 'name="texto"', $html );
			self::assertStringContainsString( 'value="Contrato actualidad"', $html );
			self::assertSame( 1, substr_count( $html, 'data-labm-actualidad-destacada' ) );
			self::assertSame( 3, substr_count( $html, 'data-labm-actualidad-tarjeta' ) );
			self::assertStringContainsString( 'data-labm-actualidad-media-fallback', $html );
			self::assertStringContainsString( 'texto=Contrato+actualidad', $html );
			self::assertStringContainsString( 'categoria=Noticias', $html );
		} finally {
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			if ( isset( $draft_id ) ) {
				wp_delete_post( $draft_id, true );
			}
		}
	}

	/** El bloque editorial compartido conserva los campos visibles de cada noticia. */
	public function test_actualidad_article_content_renders_the_editorial_fields_once(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'labm_actualidad',
				'post_status'  => 'publish',
				'post_title'   => 'Contrato editorial compartido',
				'post_excerpt' => 'Resumen compartido de la noticia.',
			)
		);

		try {
			$content = labm_theme_actualidad_article_content( get_post( $post_id ) );
			self::assertStringContainsString( 'labm-actualidad-meta', $content );
			self::assertStringContainsString( 'Contrato editorial compartido', $content );
			self::assertStringContainsString( 'Resumen compartido de la noticia.', $content );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	public function test_theme_has_safe_fallback_when_domain_is_unavailable(): void {
		self::assertTrue( function_exists( 'labm_theme_render_listing' ) );
		$fallback = labm_theme_render_listing( 'tipo_ausente', array() );
		self::assertStringContainsString( 'no está disponible', html_entity_decode( $fallback, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}
