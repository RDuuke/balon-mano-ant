<?php
/**
 * Pruebas del renderizado de Inicio.
 *
 * @package LABM
 */

use PHPUnit\Framework\TestCase;

/** Verifica consultas, fallbacks y composición de la portada. */
final class HomePresentationTest extends TestCase {
	/** Conserva y restaura el estado de las noticias existentes durante escenarios acotados. */
	private function with_existing_news_unpublished( callable $callback ): void {
		$existing = get_posts(
			array(
				'post_type'      => 'labm_actualidad',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( $existing as $post_id ) {
			wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );
		}
		try {
			$callback();
		} finally {
			foreach ( $existing as $post_id ) {
				wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
			}
		}
	}

	/** Una coleccion de una a tres noticias no duplica tarjetas ni deja huecos. */
	public function test_home_news_renders_partial_collection_with_stable_hierarchy(): void {
		$this->with_existing_news_unpublished(
			function (): void {
				$created = array();
				try {
					foreach ( array( 'Primera', 'Segunda', 'Tercera' ) as $index => $title ) {
						$created[] = wp_insert_post(
							array(
								'post_type'   => 'labm_actualidad',
								'post_status' => 'publish',
								'post_title'  => $title,
								'post_date'   => '2020-09-0' . ( 3 - $index ) . ' 12:00:00',
							)
						);
					}
					$html = labm_theme_render_home_news();
					self::assertSame( 1, substr_count( $html, 'class="labm-home-news__featured"' ) );
					self::assertSame( 2, substr_count( $html, 'class="labm-home-news__side-card"' ) );
					self::assertSame( 3, substr_count( $html, 'class="labm-home-news__article-link"' ) );
					self::assertMatchesRegularExpression( '/labm-home-news__featured.*>Primera<\/h3>/s', $html );
					self::assertSame( 1, substr_count( $html, '>Primera</h3>' ) );
					self::assertStringNotContainsString( 'labm-home-news__side-card"></article>', $html );
				} finally {
					foreach ( $created as $post_id ) {
						wp_delete_post( $post_id, true );
					}
				}
			}
		);
	}

	/** Sin noticias publicadas se omite por completo la seccion. */
	public function test_home_news_omits_section_when_collection_is_empty(): void {
		$this->with_existing_news_unpublished(
			function (): void {
				self::assertSame( 0, labm_theme_home_news_query()->post_count );
				self::assertSame( '', labm_theme_render_home_news() );
			}
		);
	}

	/** El render omite el CTA completo cuando el archivo no tiene URL valida. */
	public function test_home_news_render_omits_archive_cta_when_url_is_unavailable(): void {
		$without_archive = static fn() => '';
		add_filter( 'post_type_archive_link', $without_archive );
		try {
			$html = labm_theme_render_home_news();
			self::assertStringContainsString( 'class="labm-home-news__featured"', $html );
			self::assertStringNotContainsString( 'class="labm-home-news__archive"', $html );
			self::assertStringContainsString( 'class="labm-home-news__article-link"', $html );
		} finally {
			remove_filter( 'post_type_archive_link', $without_archive );
		}
	}

	/** Los medios priorizan miniatura, meta permitida y fallback; la categoria puede faltar. */
	public function test_home_news_media_priority_and_missing_category_are_safe(): void {
		$post_id       = wp_insert_post( array( 'post_type' => 'labm_actualidad', 'post_status' => 'publish', 'post_title' => 'Prioridad visual' ) );
		$attachment_id = wp_insert_attachment( array( 'post_title' => 'Miniatura editorial', 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ) );
		$image_source  = static fn() => array( 'https://example.test/miniatura-editorial.png', 10, 10, false );
		try {
			wp_update_attachment_metadata( $attachment_id, array( 'width' => 10, 'height' => 10, 'file' => '2026/09/miniatura-editorial.png' ) );
			update_post_meta( $post_id, 'labm_demo_image', 'assets/images/hero-balonmano-antioquia-v1.png' );
			update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
			add_filter( 'wp_get_attachment_image_src', $image_source );
			$thumbnail = labm_theme_home_news_media( get_post( $post_id ), true );
			self::assertStringContainsString( 'miniatura-editorial.png', $thumbnail );
			self::assertStringNotContainsString( 'hero-balonmano-antioquia-v1.png', $thumbnail );

			delete_post_thumbnail( $post_id );
			$meta_media = labm_theme_home_news_media( get_post( $post_id ), true );
			self::assertStringContainsString( 'hero-balonmano-antioquia-v1.png', $meta_media );

			update_post_meta( $post_id, 'labm_demo_image', 'https://example.org/insegura.png' );
			$fallback = labm_theme_home_news_media( get_post( $post_id ), false );
			self::assertStringContainsString( 'hero-balonmano-seleccion-v1.png', $fallback );
			self::assertStringNotContainsString( 'example.org', $fallback );
			self::assertStringNotContainsString( '<span>', labm_theme_home_news_meta( get_post( $post_id ) ) );
			self::assertStringContainsString( '<time ', labm_theme_home_news_meta( get_post( $post_id ) ) );
		} finally {
			remove_filter( 'wp_get_attachment_image_src', $image_source );
			wp_delete_attachment( $attachment_id, true );
			wp_delete_post( $post_id, true );
		}
	}
	/** Noticias y eventos usan consultas publicas, estables y excluyentes. */
	public function test_home_news_query_orders_limits_and_excludes_events(): void {
		$created = array();
		try {
			$event_id  = wp_insert_post(
				array(
					'post_type'   => 'labm_actualidad',
					'post_status' => 'publish',
					'post_title'  => 'Evento aislado',
					'post_date'   => '2026-09-01 12:00:00',
				)
			);
			$created[] = $event_id;
			update_post_meta( $event_id, 'labm_fecha_evento', '2026-12-12' );

			foreach ( array( '27', '28', '29', '30', '31' ) as $day ) {
				$created[] = wp_insert_post(
					array(
						'post_type'    => 'labm_actualidad',
						'post_status'  => 'publish',
						'post_title'   => 'Noticia orden ' . $day,
						'post_content' => 'Contenido de prueba.',
						'post_date'    => '2026-08-' . $day . ' 12:00:00',
					)
				);
			}
			$created[] = wp_insert_post(
				array(
					'post_type'   => 'labm_actualidad',
					'post_status' => 'draft',
					'post_title'  => 'Noticia privada del home',
					'post_date'   => '2026-09-03 10:00:00',
				)
			);

			$query = labm_theme_home_news_query();
			self::assertSame( 4, $query->post_count );
			self::assertSame(
				array( 'Noticia orden 31', 'Noticia orden 30', 'Noticia orden 29', 'Noticia orden 28' ),
				array_map( 'get_the_title', $query->posts )
			);
			self::assertNotContains( $event_id, wp_list_pluck( $query->posts, 'ID' ) );
			$event_query = labm_theme_home_event_query();
			self::assertSame( 1, $event_query->post_count );
			self::assertNotEmpty( get_post_meta( $event_query->posts[0]->ID, 'labm_fecha_evento', true ) );
			self::assertSame( array(), array_intersect( wp_list_pluck( $query->posts, 'ID' ), wp_list_pluck( $event_query->posts, 'ID' ) ) );
		} finally {
			foreach ( $created as $post_id ) {
				wp_delete_post( $post_id, true );
			}
		}
	}

	/** La seccion compone una noticia destacada y hasta tres laterales. */
	public function test_home_news_renders_featured_sidebar_metadata_and_archive_cta(): void {
		$html = labm_theme_render_home_news();
		self::assertStringContainsString( 'class="labm-home-news__featured"', $html );
		self::assertSame( 3, substr_count( $html, 'class="labm-home-news__side-card"' ) );
		self::assertStringContainsString( 'class="labm-home-news__archive"', $html );
		self::assertStringContainsString( '<time ', $html );
		self::assertStringContainsString( 'Noticias demo', $html );
		self::assertSame( 4, substr_count( $html, 'class="labm-home-news__article-link"' ) );
		self::assertStringNotContainsString( '[DEMO LABM — FICTICIO]', $html );
	}

	/** CTA y medios degradan sin emitir destinos o rutas inseguras. */
	public function test_home_news_helpers_omit_invalid_cta_and_use_safe_media_fallbacks(): void {
		self::assertSame( '', labm_theme_home_news_archive_url( 'tipo_inexistente' ) );
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'publish',
				'post_title'  => 'Noticia sin medio seguro',
			)
		);
		try {
			update_post_meta( $post_id, 'labm_demo_image', '../fuera.png' );
			$media = labm_theme_home_news_media( get_post( $post_id ), false );
			self::assertStringNotContainsString( '../fuera.png', $media );
			self::assertStringContainsString( 'hero-balonmano-seleccion-v1.png', $media );
			self::assertStringContainsString( 'alt=""', $media );
		} finally {
			wp_delete_post( $post_id, true );
		}
	}

	/** Las consultas públicas conservan estado, orden y límite. */
	public function test_consultas_de_portada_solo_publican_en_orden_editorial_y_con_limite(): void {
		self::assertTrue( function_exists( 'labm_theme_home_query' ) );

		$query = labm_theme_home_query( 'labm_actualidad', 2 );
		self::assertSame( 'publish', $query->query_vars['post_status'] );
		self::assertSame( 2, $query->query_vars['posts_per_page'] );
		self::assertSame(
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			$query->query_vars['orderby']
		);
		self::assertSame( 'ASC', $query->query_vars['order'] );
	}

	/** Los renderizadores omiten tipos ausentes de forma segura. */
	public function test_renderizadores_de_portada_tienen_fallback_y_salida_segura(): void {
		foreach ( array( 'labm_theme_render_home_slider', 'labm_theme_render_home_clubs', 'labm_theme_render_home_event', 'labm_theme_render_home_news', 'labm_theme_render_home_allies' ) as $function ) {
			self::assertTrue( function_exists( $function ), "Falta el renderizador {$function}." );
		}

		self::assertSame( '', labm_theme_render_home_slider( 'tipo_inexistente' ) );
		self::assertSame( '', labm_theme_render_home_allies( 'tipo_inexistente' ) );
	}

	/** Los aliados filtran imágenes inválidas antes del límite y generan dos grupos sin interacción. */
	public function test_allies_render_image_only_marquee_with_stable_valid_selection(): void {
		$created     = array();
		$attachments = array();
		$image_src   = static function ( $image, $attachment_id ) use ( &$attachments ) {
			return in_array( $attachment_id, $attachments, true ) ? array( 'https://example.test/logo-' . $attachment_id . '.png', 800, 400, false ) : $image;
		};

		try {
			for ( $index = 0; $index < 14; $index++ ) {
				$post_id   = wp_insert_post( array( 'post_type' => 'labm_aliado', 'post_status' => 'publish', 'post_title' => sprintf( 'Aliado %02d', $index ), 'menu_order' => $index ) );
				$created[] = $post_id;
				if ( 0 !== $index ) {
					$attachment_id = wp_insert_attachment( array( 'post_title' => 'Logo', 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ) );
					$attachments[] = $attachment_id;
					update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
				}
			}
			add_filter( 'wp_get_attachment_image_src', $image_src, 10, 2 );
			$html = labm_theme_render_home_allies();
			self::assertSame( 24, substr_count( $html, '<img ' ) );
			self::assertSame( 2, substr_count( $html, '<ul' ) );
			self::assertStringContainsString( 'aria-hidden="true" inert', $html );
			self::assertStringNotContainsString( '<a ', $html );
			self::assertStringNotContainsString( '<button', $html );
			self::assertStringNotContainsString( 'Aliado 00', $html );
			self::assertStringContainsString( 'alt="Aliado 01"', $html );
			self::assertStringNotContainsString( '<span', $html );
		} finally {
			remove_filter( 'wp_get_attachment_image_src', $image_src, 10 );
			foreach ( $created as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			foreach ( $attachments as $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
		}
	}

	/** El tema omite el contenido dependiente si labm-core no registra sus tipos. */
	public function test_portada_degrada_sin_registros_de_labm_core(): void {
		self::assertTrue( unregister_post_type( 'labm_slide' ) );
		self::assertTrue( unregister_post_type( 'labm_aliado' ) );

		try {
			self::assertSame( '', labm_theme_render_home_slider() );
			self::assertSame( '', labm_theme_render_home_allies() );
			ob_start();
			require dirname( __DIR__, 2 ) . '/wp-content/themes/labm/patterns/inicio.php';
			$html = (string) ob_get_clean();
			self::assertStringContainsString( 'data-labm-section="presentacion"', $html );
			self::assertStringNotContainsString( 'data-labm-slider', $html );
			self::assertStringNotContainsString( 'data-labm-allies', $html );
		} finally {
			labm_core_register_home_content_types();
		}
	}

	/** Los renderizadores incluyen únicamente contenido publicado y controles útiles. */
	public function test_renderizadores_de_portada_componen_contenido_publicado(): void {
		$created = array();
		try {
			foreach ( array( 'Uno', 'Dos' ) as $index => $title ) {
				$post_id   = wp_insert_post(
					array(
						'post_type'    => 'labm_slide',
						'post_status'  => 'publish',
						'post_title'   => "Slide {$title}",
						'post_content' => 'Contenido público del destacado.',
						'menu_order'   => $index,
					)
				);
				$created[] = $post_id;
				update_post_meta( $post_id, 'labm_destino_url', '/nosotros/' );
				update_post_meta( $post_id, 'labm_cta_texto', 'Conocer la Liga' );
			}

			$ally_id   = wp_insert_post(
				array(
					'post_type'    => 'labm_aliado',
					'post_status'  => 'publish',
					'post_title'   => 'Aliado público',
					'post_content' => 'Contenido institucional.',
				)
			);
			$created[] = $ally_id;
			update_post_meta( $ally_id, 'labm_destino_url', 'https://example.org/' );

			$slider = labm_theme_render_home_slider();
			self::assertStringContainsString( 'data-labm-slider-next', $slider );
			self::assertStringContainsString( 'Conocer la Liga', $slider );
			self::assertStringNotContainsString( 'javascript:', $slider );

			$allies = labm_theme_render_home_allies();
			self::assertStringNotContainsString( 'data-labm-allies-pause', $allies );

			$clubs = labm_theme_render_home_clubs();
			self::assertStringContainsString( 'data-labm-section="clubes"', $clubs );
			self::assertStringContainsString( 'labm-card--club', $clubs );
			$event = labm_theme_render_home_event();
			self::assertStringContainsString( 'data-labm-section="evento"', $event );
			self::assertStringContainsString( 'labm-featured-event__cta', $event );
			self::assertStringContainsString( 'data-labm-section="actualidad"', labm_theme_render_home_news() );
		} finally {
			foreach ( $created as $post_id ) {
				wp_delete_post( $post_id, true );
			}
		}
	}

	/** El slider limita texto extremo y conserva una caja identificable sin imagen editorial. */
	public function test_slider_presenta_contenido_extremo_sin_medio_editorial(): void {
		register_post_type( 'labm_slide_test', array( 'public' => true ) );
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'labm_slide_test',
				'post_status'  => 'publish',
				'post_title'   => 'Destacado sin medio',
				'post_content' => str_repeat( 'contenido editorial extenso ', 80 ),
				'menu_order'   => -10,
			)
		);

		try {
			$html = labm_theme_render_home_slider( 'labm_slide_test' );
			self::assertStringContainsString( 'class="labm-home-slider__slide"', $html );
			self::assertStringContainsString( 'tabindex="0"', $html );
			self::assertStringContainsString( 'hero-balonmano-antioquia-v1.png', $html );
			self::assertLessThanOrEqual( 35, str_word_count( wp_strip_all_tags( $html ) ) );
		} finally {
			wp_delete_post( $post_id, true );
			unregister_post_type( 'labm_slide_test' );
		}
	}

	/** El patrón ejecuta la composición sin exponer secciones retiradas. */
	public function test_patron_de_inicio_se_puede_renderizar(): void {
		ob_start();
		require dirname( __DIR__, 2 ) . '/wp-content/themes/labm/patterns/inicio.php';
		$html = (string) ob_get_clean();
		self::assertStringContainsString( 'data-labm-section="presentacion"', $html );
		self::assertStringContainsString( 'data-labm-section="vinculacion"', $html );
		self::assertStringNotContainsString( 'Balonmano de piso', $html );
	}

	/** El patrón conserva el orden aprobado y no reintroduce secciones retiradas. */
	public function test_patron_compone_el_orden_aprobado_y_excluye_secciones_retiradas(): void {
		$root     = dirname( __DIR__, 2 ) . '/wp-content/themes/labm/';
		$pattern  = file_get_contents( $root . 'patterns/inicio.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local de fixture.
		$template = file_get_contents( $root . 'templates/front-page.html' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local de fixture.

		$positions = array();
		foreach ( array( 'slider', 'presentacion', 'clubes', 'evento', 'actualidad', 'vinculacion', 'aliados' ) as $section ) {
			$position = strpos( $pattern, 'data-labm-section="' . $section . '"' );
			self::assertNotFalse( $position, "Falta la seccion {$section}." );
			$positions[] = $position;
		}
		$sorted = $positions;
		sort( $sorted );
		self::assertSame( $sorted, $positions );
		self::assertDoesNotMatchRegularExpression( '/Balonmano de piso|Balonmano playa|horarios|escenarios/i', $pattern );
		self::assertStringContainsString( '"type":"default"', $template );
	}
}
