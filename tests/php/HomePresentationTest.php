<?php
/**
 * Pruebas del renderizado de Inicio.
 *
 * @package LABM
 */

use PHPUnit\Framework\TestCase;

/** Verifica consultas, fallbacks y composición de la portada. */
final class HomePresentationTest extends TestCase {
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
			self::assertStringContainsString( 'data-labm-allies-pause', $allies );
			self::assertStringContainsString( 'aria-hidden="true"', $allies );
			self::assertStringContainsString( 'Aliado público', $allies );

			self::assertStringContainsString( 'data-labm-section="clubes"', labm_theme_render_home_clubs() );
			self::assertStringContainsString( 'data-labm-section="evento"', labm_theme_render_home_event() );
			self::assertStringContainsString( 'data-labm-section="actualidad"', labm_theme_render_home_news() );
		} finally {
			foreach ( $created as $post_id ) {
				wp_delete_post( $post_id, true );
			}
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
