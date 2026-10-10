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
		self::assertSame( 2, substr_count( $filtered, 'data-labm-team-group="entrenadores">' ) );
		self::assertStringContainsString( 'aria-current="true"', $filtered );
		self::assertSame( 2, preg_match_all( '/<article\b[^>]*data-labm-team-card[^>]* hidden>/', $filtered ) );
		self::assertStringContainsString( 'Mateo Giraldo', $filtered );
	}

	/** Un grupo seleccionado sin miembros mantiene filtros y anuncia el vacío. */
	public function test_about_team_selected_empty_group_keeps_filters(): void {
		$members = get_posts( array( 'post_type' => 'labm_integrante', 'posts_per_page' => -1, 'tax_query' => array( array( 'taxonomy' => 'labm_grupo_integrante', 'field' => 'slug', 'terms' => 'entrenadores' ) ) ) );
		self::assertNotEmpty( $members );
		try {
			foreach ( $members as $member ) {
				wp_update_post( array( 'ID' => $member->ID, 'post_status' => 'private' ) );
			}
			$html = labm_theme_render_about_team( 'entrenadores' );
			self::assertStringContainsString( 'No hay integrantes publicados en este grupo.', $html );
			self::assertStringContainsString( 'data-labm-team-empty role="status"', $html );
			self::assertStringContainsString( 'data-labm-team-filter-group="entrenadores" aria-current="true"', $html );
			self::assertSame( 4, substr_count( $html, 'data-labm-team-filter' ) - substr_count( $html, 'data-labm-team-filter-group' ) );
			foreach ( $members as $member ) {
				self::assertStringNotContainsString( preg_replace( '/^\[DEMO LABM[^\]]*\]\s*/u', '', $member->post_title ), $html );
			}
		} finally {
			foreach ( $members as $member ) {
				wp_update_post( array( 'ID' => $member->ID, 'post_status' => $member->post_status ) );
			}
		}
		self::assertSame( labm_theme_render_about_team( '' ), labm_theme_render_about_team( 'desconocido' ) );
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

	/** El patrón conecta el catálogo accesible con los filtros y la página actuales. */
	public function test_documents_pattern_composes_the_filterable_pdf_catalog(): void {
		$root    = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/';
		$pattern = (string) file_get_contents( $root . 'patterns/documentos.php' );
		$css     = (string) file_get_contents( $root . 'style.css' );

		$catalog_call = 'labm_core_render_document_catalog( labm_core_document_catalog_current_filters(), labm_core_document_catalog_current_page() )';
		self::assertStringContainsString( $catalog_call, $pattern );
		self::assertLessThan( strpos( $pattern, $catalog_call ), strpos( $pattern, 'labm_theme_render_documents_banner()' ) );
		self::assertStringContainsString( 'labm_core_document_catalog_current_filters()', $pattern );
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

	/** El listado de actualidad conserva roles, orden, filtros, privacidad y paginas parciales. */
	public function test_actualidad_listing_keeps_editorial_roles_unique_across_pages_and_filters(): void {
		$term = get_term_by( 'name', 'Noticias', 'labm_categoria' );
		self::assertNotFalse( $term );
		$post_ids = array();

		try {
			for ( $index = 1; $index <= 9; $index++ ) {
				$post_ids[] = wp_insert_post(
					array(
						'post_type'    => 'labm_actualidad',
						'post_status'  => 'publish',
						'post_title'   => sprintf( 'Gate global actualidad %d', $index ),
						'post_excerpt' => 'Resumen del contrato de listado editorial.',
						'post_date'    => sprintf( '2026-09-%02d 12:00:00', $index ),
					)
				);
				wp_set_object_terms( end( $post_ids ), (int) $term->term_id, 'labm_categoria' );
			}
			$private_id = wp_insert_post(
				array(
					'post_type'   => 'labm_actualidad',
					'post_status' => 'draft',
					'post_title'  => 'Gate global actualidad privada',
				)
			);
			wp_set_object_terms( $private_id, (int) $term->term_id, 'labm_categoria' );

			$filters = array(
				'texto'     => 'Gate global actualidad',
				'categoria' => 'Noticias',
			);
			$page_one = labm_theme_render_listing( 'labm_actualidad', $filters + array( 'pagina' => 1 ) );
			preg_match_all( '/data-labm-actualidad-post-id="(\d+)"/', $page_one, $page_one_ids );
			$expected_page_one = array_map( 'strval', array_slice( array_reverse( $post_ids ), 0, 4 ) );

			self::assertSame( $expected_page_one, $page_one_ids[1] );
			self::assertSame( 1, substr_count( $page_one, 'data-labm-actualidad-destacada' ) );
			self::assertSame( 3, substr_count( $page_one, 'data-labm-actualidad-tarjeta' ) );
			self::assertSame( count( $page_one_ids[1] ), count( array_unique( $page_one_ids[1] ) ) );
			self::assertStringContainsString( 'texto=Gate+global+actualidad', $page_one );
			self::assertStringContainsString( 'categoria=Noticias', $page_one );
			self::assertStringNotContainsString( 'Gate global actualidad privada', $page_one );

			$page_three = labm_theme_render_listing( 'labm_actualidad', $filters + array( 'pagina' => 3 ) );
			preg_match_all( '/data-labm-actualidad-post-id="(\d+)"/', $page_three, $page_three_ids );
			self::assertSame( array( (string) $post_ids[0] ), $page_three_ids[1] );
			self::assertSame( 1, substr_count( $page_three, 'data-labm-actualidad-destacada' ) );
			self::assertSame( 0, substr_count( $page_three, 'data-labm-actualidad-tarjeta' ) );
			self::assertStringNotContainsString( 'pagina=4', $page_three );

			$page_out_of_range = labm_theme_render_listing( 'labm_actualidad', $filters + array( 'pagina' => 4 ) );
			self::assertStringContainsString( 'data-labm-actualidad-vacio', $page_out_of_range );
			self::assertStringNotContainsString( 'data-labm-actualidad-post-id', $page_out_of_range );
			self::assertStringNotContainsString( 'data-labm-actualidad-destacada', $page_out_of_range );
			self::assertStringNotContainsString( 'data-labm-actualidad-tarjeta', $page_out_of_range );
			self::assertStringNotContainsString( 'labm-pagination', $page_out_of_range );
			self::assertStringNotContainsString( 'Gate global actualidad privada', $page_out_of_range );
		} finally {
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			if ( isset( $private_id ) ) {
				wp_delete_post( $private_id, true );
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

	/** El detalle solo comparte publicaciones de actualidad públicas y canónicas. */
	public function test_actualidad_detail_renders_safe_public_metadata_and_share_controls(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'labm_actualidad',
				'post_status'  => 'publish',
				'post_title'   => 'Actualidad <script>no segura</script>',
				'post_date'    => '2026-09-20 12:00:00',
				'post_content' => '<!-- wp:gallery {"columns":2} --><figure class="wp-block-gallery has-nested-images columns-2"><figure class="wp-block-image size-large"><img src="https://example.test/galeria.jpg" alt="Imagen editorial"></figure></figure><!-- /wp:gallery -->',
			)
		);
		$term = get_term_by( 'name', 'Noticias', 'labm_categoria' );

		try {
			self::assertNotFalse( $term );
			wp_set_object_terms( $post_id, (int) $term->term_id, 'labm_categoria' );
			$hero = labm_theme_render_actualidad_hero( get_post( $post_id ) );
			$html = $hero . labm_theme_render_actualidad_detail( get_post( $post_id ) );

			self::assertStringContainsString( 'data-labm-actualidad-detail', $html );
			self::assertStringContainsString( 'Noticias', $html );
			self::assertStringContainsString( '20 Sep 2026', $html );
			self::assertStringContainsString( 'facebook.com/sharer/sharer.php?u=', $html );
			self::assertStringContainsString( 'api.whatsapp.com/send?text=', $html );
			self::assertStringContainsString( 'data-labm-actualidad-copy', $html );
			self::assertStringContainsString( 'readonly', $html );
			self::assertStringContainsString( 'Enlace de esta publicación', $html );
			self::assertStringNotContainsString( '<script>', $html );
			self::assertStringContainsString( 'wp-block-gallery', (string) get_post_field( 'post_content', $post_id ) );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** El hero editorial precede al medio y conserva una única jerarquía H1. */
	public function test_actualidad_detail_renders_black_hero_before_featured_media(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'publish',
				'post_title'  => 'Resultado editorial',
				'post_date'   => '2026-09-20 12:00:00',
			)
		);
		$term = get_term_by( 'name', 'Noticias', 'labm_categoria' );

		try {
			self::assertNotFalse( $term );
			wp_set_object_terms( $post_id, (int) $term->term_id, 'labm_categoria' );
			$hero = labm_theme_render_actualidad_hero( get_post( $post_id ) );

			self::assertStringContainsString( 'data-labm-actualidad-hero', $hero );
			self::assertStringContainsString( 'labm-actualidad-detail__meta', $hero );
			self::assertStringContainsString( '<h1', $hero );
			self::assertStringContainsString( 'Resultado editorial', $hero );
			self::assertStringNotContainsString( 'labm-actualidad-detail__media', $hero );
			$template = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/templates/single-labm_actualidad.html' );
			self::assertLessThan( strpos( $template, 'labm_actualidad_detalle' ), strpos( $template, 'labm_actualidad_hero' ) );
			self::assertLessThan( strpos( $template, 'labm_actualidad_detalle' ), strpos( $template, 'wp:post-content' ) );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** El panel no revela datos de actualidad restringida y la plantilla usa contenido Gutenberg nativo. */
	public function test_actualidad_detail_omits_restricted_posts_and_uses_native_content_template(): void {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'draft',
				'post_title'  => 'Actualidad restringida',
			)
		);

		try {
			self::assertSame( '', labm_theme_render_actualidad_detail( get_post( $post_id ) ) );
			$template = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/templates/single-labm_actualidad.html' );
			self::assertStringContainsString( 'wp:post-content', $template );
			self::assertStringContainsString( 'labm_actualidad_detalle', $template );
			self::assertStringNotContainsString( 'labm_ubicacion', $template );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** La consulta de Selecciones normaliza filtros, cuenta solo publicaciones y limita por modalidad. */
	public function test_selection_query_normalizes_filters_and_counts_only_public_posts(): void {
		$term = wp_insert_term( 'Playa TDD', 'labm_modalidad' );
		self::assertIsArray( $term );
		$post_ids = array();

		try {
			for ( $index = 1; $index <= 7; $index++ ) {
				$post_ids[] = wp_insert_post(
					array(
						'post_type'   => 'labm_seleccion',
						'post_status' => 'publish',
						'post_title'  => sprintf( 'Seleccion playa TDD %d', $index ),
						'post_date'   => '2026-10-01 12:00:00',
					)
				);
				wp_set_object_terms( end( $post_ids ), (int) $term['term_id'], 'labm_modalidad' );
			}
			$restricted = wp_insert_post(
				array(
					'post_type'   => 'labm_seleccion',
					'post_status' => 'private',
					'post_title'  => 'Seleccion privada playa TDD',
				)
			);
			wp_set_object_terms( $restricted, (int) $term['term_id'], 'labm_modalidad' );

			$state = labm_theme_selection_request_state(
				array(
					'modalidad' => 'Playa TDD',
					'pagina'    => '9',
				)
			);
			self::assertSame( 'Piso', labm_theme_selection_request_state( array( 'modalidad' => array( 'Playa TDD' ) ) )['modalidad'] );
			self::assertSame( 'Playa TDD', $state['modalidad'] );
			self::assertSame( 9, $state['pagina'] );

			$query = labm_theme_selection_query( $state );
			self::assertSame( 7, (int) $query->found_posts );
			self::assertSame( 3, (int) $query->get( 'posts_per_page' ) );
			self::assertSame( 3, (int) $query->max_num_pages );
			self::assertCount( 1, $query->posts );
			self::assertSame( 'Seleccion playa TDD 1', $query->posts[0]->post_title );
			self::assertSame( 'publish', $query->posts[0]->post_status );
			self::assertNotContains( $restricted, wp_list_pluck( $query->posts, 'ID' ) );
			$html = labm_theme_render_listing( 'labm_seleccion', array( 'modalidad' => 'Playa TDD' ) );
			self::assertStringContainsString( 'modalidad=Playa+TDD', $html );
			self::assertStringContainsString( 'pagina=2', $html );
		} finally {
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			if ( isset( $restricted ) ) {
				wp_delete_post( $restricted, true );
			}
			wp_delete_term( (int) $term['term_id'], 'labm_modalidad' );
		}
	}

	/** La paginacion cubre cada pagina, resuelve desbordes y conserva un vacio recuperable. */
	public function test_selection_pagination_covers_pages_empty_and_invalid_requests(): void {
		$term = wp_insert_term( 'Playa 4.1 TDD', 'labm_modalidad' );
		self::assertIsArray( $term );
		$post_ids = array();

		try {
			for ( $index = 1; $index <= 7; $index++ ) {
				$post_id    = wp_insert_post(
					array(
						'post_type'   => 'labm_seleccion',
						'post_status' => 'publish',
						'post_title'  => sprintf( 'Seleccion paginada 4.1 %d', $index ),
						'post_date'   => '2026-10-02 12:00:00',
					)
				);
				$post_ids[] = $post_id;
				wp_set_object_terms( $post_id, (int) $term['term_id'], 'labm_modalidad' );
			}

			self::assertSame( 'Piso', labm_theme_selection_request_state( array( 'modalidad' => array( 'Playa 4.1 TDD' ), 'pagina' => array( '3' ) ) )['modalidad'] );
			self::assertSame( 1, labm_theme_selection_request_state( array( 'modalidad' => 'Playa 4.1 TDD', 'pagina' => array( '3' ) ) )['pagina'] );

			foreach ( array( 1 => 3, 2 => 3, 3 => 1, 99 => 1 ) as $page => $expected_count ) {
				$query = labm_theme_selection_query( array( 'modalidad' => 'Playa 4.1 TDD', 'pagina' => (string) $page ) );
				self::assertSame( $expected_count, $query->post_count, "cantidad de filas en pagina {$page}" );
				self::assertSame( min( $page, 3 ), (int) $query->get( 'labm_selection_effective_page' ), "pagina efectiva {$page}" );
			}

			$original_piso = get_posts(
				array(
					'post_type'      => 'labm_seleccion',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'labm_modalidad',
							'field'    => 'name',
							'terms'    => 'Piso',
						),
					),
				)
			);
			foreach ( $original_piso as $post ) {
				wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'private' ) );
			}
			$empty_html = labm_theme_render_listing( 'labm_seleccion', array( 'modalidad' => 'Piso' ) );
			self::assertStringContainsString( 'data-labm-selecciones-vacio', $empty_html );
			self::assertStringContainsString( 'Consultar Piso', $empty_html );
			self::assertStringContainsString( 'Consultar Playa', $empty_html );
			self::assertStringNotContainsString( 'labm-selecciones__pagination', $empty_html );
		} finally {
			if ( isset( $original_piso ) ) {
				foreach ( $original_piso as $post ) {
					wp_update_post( array( 'ID' => $post->ID, 'post_status' => $post->post_status ) );
				}
			}
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			wp_delete_term( (int) $term['term_id'], 'labm_modalidad' );
		}
	}

	/** Los terminos adicionales provienen solo de Selecciones publicas y los terminos base permanecen disponibles. */
	public function test_selection_terms_exclude_global_club_terms_and_keep_base_modalities(): void {
		$selection_term = wp_insert_term( 'Juvenil TDD', 'labm_modalidad' );
		$club_term      = wp_insert_term( 'Clubes TDD', 'labm_modalidad' );
		self::assertIsArray( $selection_term );
		self::assertIsArray( $club_term );
		$selection_id = wp_insert_post(
			array(
				'post_type'   => 'labm_seleccion',
				'post_status' => 'publish',
				'post_title'  => 'Seleccion juvenil TDD',
			)
		);
		$club_id = wp_insert_post(
			array(
				'post_type'   => 'labm_club',
				'post_status' => 'publish',
				'post_title'  => 'Club TDD',
			)
		);

		try {
			wp_set_object_terms( $selection_id, (int) $selection_term['term_id'], 'labm_modalidad' );
			wp_set_object_terms( $club_id, (int) $club_term['term_id'], 'labm_modalidad' );
			$terms = wp_list_pluck( labm_theme_selection_terms(), 'name' );
			self::assertContains( 'Piso', $terms );
			self::assertContains( 'Playa', $terms );
			self::assertContains( 'Juvenil TDD', $terms );
			self::assertNotContains( 'Clubes TDD', $terms );
		} finally {
			wp_delete_post( $selection_id, true );
			wp_delete_post( $club_id, true );
			wp_delete_term( (int) $selection_term['term_id'], 'labm_modalidad' );
			wp_delete_term( (int) $club_term['term_id'], 'labm_modalidad' );
		}
	}

	/** Las filas muestran contenido editorial seguro, metadatos existentes, placeholder y enlace a la publicacion. */
	public function test_selection_rows_render_editorial_content_with_publication_link(): void {
		$term     = get_term_by( 'name', 'Piso', 'labm_modalidad' );
		$category = get_term_by( 'name', 'Noticias', 'labm_categoria' );
		self::assertNotFalse( $term );
		self::assertNotFalse( $category );
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'labm_seleccion',
				'post_status'  => 'publish',
				'post_title'   => 'Seleccion editorial TDD',
				'post_excerpt' => 'Resumen editorial editable de la seleccion.',
				'post_content' => '<p>Contenido que no debe introducir HTML inseguro.</p>',
			)
		);

		try {
			wp_set_object_terms( $post_id, (int) $term->term_id, 'labm_modalidad' );
			wp_set_object_terms( $post_id, (int) $category->term_id, 'labm_categoria' );
			update_post_meta( $post_id, 'labm_modalidad_detalle', 'Categoria adulta' );
			$html = labm_theme_render_listing( 'labm_seleccion', array( 'modalidad' => 'Piso' ) );
			preg_match( '/<article[^>]*data-labm-seleccion-row[^>]*>[\\s\\S]*?<\\/article>/', $html, $matches );
			self::assertNotEmpty( $matches );
			self::assertStringContainsString( 'Seleccion editorial TDD', $matches[0] );
			self::assertStringContainsString( 'Resumen editorial editable de la seleccion.', $matches[0] );
			self::assertStringContainsString( 'Categoria adulta', $matches[0] );
			self::assertStringContainsString( 'Noticias', $matches[0] );
			self::assertStringContainsString( 'labm-selecciones__placeholder', $matches[0] );
			self::assertStringContainsString( 'labm-selecciones__publication-link', $matches[0] );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** La plantilla delega el archivo y la introduccion usa la descripcion editable de la modalidad. */
	public function test_selection_archive_uses_editorial_template_and_term_description(): void {
		$template = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/templates/archive-labm_seleccion.html' );
		self::assertStringContainsString( 'wp:template-part {"slug":"header"', $template );
		self::assertStringContainsString( '[labm_selecciones_listado]', $template );
		self::assertStringContainsString( 'wp:template-part {"slug":"footer"', $template );
		self::assertStringNotContainsString( '[DEMO LABM', $template );

		$term = wp_insert_term(
			'Piso editorial 3.1 TDD',
			'labm_modalidad',
			array( 'description' => 'Descripcion editorial editable del listado Piso.' )
		);
		self::assertIsArray( $term );
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_seleccion',
				'post_status' => 'publish',
				'post_title'  => 'Seleccion plantilla 3.1 TDD',
			)
		);

		try {
			wp_set_object_terms( $post_id, (int) $term['term_id'], 'labm_modalidad' );
			$html = labm_theme_render_listing( 'labm_seleccion', array( 'modalidad' => 'Piso editorial 3.1 TDD' ) );
			self::assertStringContainsString( 'Descripcion editorial editable del listado Piso.', $html );
		} finally {
			wp_delete_post( $post_id, true );
			wp_delete_term( (int) $term['term_id'], 'labm_modalidad' );
		}
	}

	/** El header ordena Selecciones antes de Documentos y marca solo la modalidad efectiva. */
	public function test_selection_header_navigation_orders_parent_and_marks_effective_child(): void {
		$original_uri = $_SERVER['REQUEST_URI'] ?? null;
		$original_get = $_GET;

		try {
			$_SERVER['REQUEST_URI'] = '/selecciones/?modalidad=Piso';
			$_GET                  = array( 'modalidad' => 'Piso' );
			$html                   = labm_theme_header_navigation_shortcode();
			self::assertLessThan( strpos( $html, 'Documentos' ), strpos( $html, 'Selecciones' ) );
			self::assertDoesNotMatchRegularExpression( '/<a[^>]*>Selecciones<\/a>/', $html );
			self::assertMatchesRegularExpression( '/<button[^>]*data-labm-submenu-toggle[^>]*>\s*Selecciones/', $html );
			self::assertStringContainsString( 'is-current-section', $html );
			self::assertSame( 1, substr_count( $html, 'aria-current="page"' ) );
			self::assertMatchesRegularExpression( '/Balonmano Piso<\/a>[^<]*<\/li>/', $html );
			self::assertStringContainsString( '/selecciones/?modalidad=Playa', $html );
			$_SERVER['REQUEST_URI'] = '/selecciones/?modalidad=Playa';
			$_GET                  = array( 'modalidad' => 'Playa' );
			$playa                  = labm_theme_header_navigation_shortcode();
			self::assertStringContainsString( 'is-current-section', $playa );
			self::assertSame( 1, substr_count( $playa, 'aria-current="page"' ) );
			self::assertMatchesRegularExpression( '/<a[^>]*aria-current="page"[^>]*>Balonmano Playa<\/a>/', $playa );

			$_SERVER['REQUEST_URI'] = '/actualidad/';
			$_GET                  = array();
			$outside                = labm_theme_header_navigation_shortcode();
			preg_match( '/<li[^>]*data-labm-submenu[\s\S]*?<\/li>\s*<\/ul>/', $outside, $submenu_matches );
			self::assertNotEmpty( $submenu_matches );
			self::assertSame( 0, substr_count( $submenu_matches[0], 'aria-current="page"' ) );
		} finally {
			if ( null === $original_uri ) {
				unset( $_SERVER['REQUEST_URI'] );
			} else {
				$_SERVER['REQUEST_URI'] = $original_uri;
			}
			$_GET = $original_get;
		}
	}

	/** El detalle conserva la seccion y modalidad del articulo, incluso con un filtro ajeno en la URL. */
	public function test_selection_header_navigation_marks_single_selection_modality(): void {
		$original_query = $GLOBALS['wp_query'];
		$original_get   = $_GET;
		$original_uri   = $_SERVER['REQUEST_URI'] ?? '/';
		$post_ids       = array();
		try {
			foreach ( array( 'Piso', 'Playa' ) as $modality ) {
				$post_id    = wp_insert_post( array( 'post_type' => 'labm_seleccion', 'post_status' => 'publish', 'post_title' => 'Navigation single TDD ' . $modality ) );
				$post_ids[] = $post_id;
				wp_set_object_terms( $post_id, $modality, 'labm_modalidad' );
				$GLOBALS['wp_query']    = new WP_Query( array( 'p' => $post_id, 'post_type' => 'labm_seleccion' ) );
				$_SERVER['REQUEST_URI'] = '/seleccion/navigation-single/?modalidad=' . ( 'Piso' === $modality ? 'Playa' : 'Piso' );
				$_GET                  = array( 'modalidad' => 'Piso' === $modality ? 'Playa' : 'Piso' );
				$html                  = labm_theme_header_navigation_shortcode();
				self::assertStringContainsString( 'is-current-section', $html );
				self::assertMatchesRegularExpression( '/<a[^>]*aria-current="location"[^>]*>Balonmano ' . $modality . '<\/a>/', $html );
				self::assertSame( 1, substr_count( $html, 'aria-current="location"' ) );
				self::assertSame( 0, substr_count( $html, 'aria-current="page"' ) );
			}
		} finally {
			$GLOBALS['wp_query']    = $original_query;
			$_GET                  = $original_get;
			$_SERVER['REQUEST_URI'] = $original_uri;
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
		}
	}

	/** El script de navegacion mantiene disclosure progresivo, foco, cierre y estados sin roles de menu. */
	public function test_selection_navigation_asset_declares_accessible_disclosure_contract(): void {
		$script = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/assets/navigation.js' );
		self::assertStringContainsString( '[data-labm-menu-toggle]', $script );
		self::assertStringContainsString( '[data-labm-submenu-toggle]', $script );
		self::assertStringContainsString( "'Escape'", $script );
		self::assertStringContainsString( 'focusout', $script );
		self::assertStringContainsString( 'addEventListener(\'click\'', $script );
		self::assertStringContainsString( 'addEventListener(\'resize\'', $script );
		self::assertStringContainsString( 'submenuPanel.hidden', $script );
		self::assertStringNotContainsString( 'role="menu"', $script );
	}

	/** El CSS focal conserva superficies, controles de 44 px, foco y adaptacion movil. */
	public function test_selection_styles_declare_responsive_accessible_surfaces(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/style.css' );
		self::assertStringContainsString( '.labm-selecciones__hero', $css );
		self::assertStringContainsString( '.labm-selecciones__row', $css );
		self::assertStringContainsString( 'min-height: 2.75rem', $css );
		self::assertStringContainsString( '.labm-site-navigation :is(a, button):focus-visible', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertStringContainsString( '@media (prefers-reduced-motion: reduce)', $css );
	}

	/** Las pruebas publicas declaran la cobertura E2E requerida sin sustituir su ejecucion real. */
	public function test_selection_e2e_contracts_cover_testing_block(): void {
		$root       = dirname( __DIR__, 2 );
		$public_e2e = (string) file_get_contents( $root . '/tests/e2e/public-experience.spec.ts' );
		$corrective = (string) file_get_contents( $root . '/tests/e2e/verify-correctives.spec.ts' );

		foreach ( array( '/selecciones/', 'pagina=999', 'data-labm-selecciones-vacio', 'Escape', 'touchscreen', 'javaScriptEnabled: false', '200%', '320, 768, 1024, 1200, 1440' ) as $contract ) {
			self::assertStringContainsString( $contract, $public_e2e, "contrato publico ausente: {$contract}" );
		}
		foreach ( array( '/actualidad/', 'aria-current', 'sticky', 'Escape', 'focus-visible', 'Selecciones' ) as $contract ) {
			self::assertStringContainsString( $contract, $corrective, "contrato correctivo ausente: {$contract}" );
		}
	}
}
