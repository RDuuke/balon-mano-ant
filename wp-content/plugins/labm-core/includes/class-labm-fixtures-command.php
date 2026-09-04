<?php
/**
 * Fixtures ficticios para desarrollo local.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Comando idempotente de fixtures. */
class LABM_Fixtures_Command {
	/** Marcador obligatorio para contenido administrado por fixtures. */
	const MARKER = '[DEMO LABM — FICTICIO]';

	/**
	 * Noticias ficticias para probar la composicion editorial de portada.
	 *
	 * @return array
	 */
	private static function home_news_fixtures() {
		$definitions = array(
			array( 'resultado', 'Un resultado que celebra todo el balonmano antioqueño', 'La comunidad acompaña una jornada ficticia de celebración deportiva.', '2026-08-27 16:00:00' ),
			array( 'convocatoria', 'Proceso deportivo abierto', 'Convocatoria demostrativa para nuevos procesos deportivos.', '2026-08-27 15:00:00' ),
			array( 'clubes', 'La Liga fortalece sus clubes', 'Encuentro institucional ficticio para probar contenido editorial.', '2026-08-27 14:00:00' ),
			array( 'calendario', 'Próxima fecha del calendario', 'Programación inventada para validar el acceso a la actualidad.', '2026-08-27 13:00:00' ),
			array( 'formacion', 'Jornada técnica para entrenadores', 'Actividad pedagógica ficticia para la comunidad deportiva.', '2026-08-27 12:00:00' ),
			array( 'seleccion', 'Encuentro de selecciones departamentales', 'Noticia de demostración sin resultados ni personas reales.', '2026-08-27 11:00:00' ),
		);
		$images      = array(
			'assets/images/hero-balonmano-antioquia-v1.png',
			'assets/images/hero-balonmano-seleccion-v1.png',
		);

		return array_map(
			static function ( $definition, $index ) use ( $images ) {
				return array(
					'post_name'    => 'demo-labm-noticia-' . $definition[0],
					'post_title'   => self::MARKER . ' ' . $definition[1],
					'post_excerpt' => self::MARKER . ' ' . $definition[2],
					'post_content' => '<p>' . self::MARKER . ' ' . $definition[2] . '</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'post_date'    => $definition[3],
					'meta'         => array( 'labm_demo_image' => $images[ $index % count( $images ) ] ),
					'terms'        => array( 'labm_categoria' => array( 'Noticias demo' ) ),
				);
			},
			$definitions,
			array_keys( $definitions )
		);
	}

	/**
	 * Aliados ficticios compuestos exclusivamente por logos ordenados.
	 *
	 * @return array
	 */
	private static function home_allies_fixtures() {
		$definitions = array(
			array( 'arco-comun', 'Arco Comun' ),
			array( 'brote-activo', 'Brote Activo' ),
			array( 'cumbre-viva', 'Cumbre Viva' ),
			array( 'mosaico-unido', 'Mosaico Unido' ),
			array( 'rio-dinamico', 'Rio Dinamico' ),
			array( 'sol-abierto', 'Sol Abierto' ),
		);

		return array_map(
			static function ( $definition, $order ) {
				return array(
					'post_name'      => 'demo-labm-aliado-' . $definition[0],
					'post_title'     => self::MARKER . ' ' . $definition[1],
					'post_content'   => '',
					'post_excerpt'   => '',
					'post_type'      => 'labm_aliado',
					'post_status'    => 'publish',
					'menu_order'     => $order,
					'featured_image' => 'assets/images/aliados-demo/' . $definition[0] . '.png',
				);
			},
			$definitions,
			array_keys( $definitions )
		);
	}

	/**
	 * Importa o reutiliza un logo demo como adjunto de WordPress.
	 *
	 * @param string $relative_path Ruta relativa dentro del tema.
	 * @return int
	 */
	private static function ensure_demo_attachment( $relative_path ) {
		$slug     = sanitize_title( pathinfo( $relative_path, PATHINFO_FILENAME ) );
		$existing = get_page_by_path( 'demo-labm-logo-' . $slug, OBJECT, 'attachment' );
		if ( $existing && 'image/png' === get_post_mime_type( $existing ) ) {
			return (int) $existing->ID;
		}

		$source = get_theme_file_path( $relative_path );
		if ( ! is_file( $source ) ) {
			return 0;
		}
		$upload = wp_upload_bits( basename( $source ), null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- activo local controlado.
		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}
		$attachment_id = wp_insert_attachment(
			array(
				'post_name'      => 'demo-labm-logo-' . $slug,
				'post_title'     => self::MARKER . ' Logo ' . $slug,
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/png',
			),
			$upload['file'],
			0,
			true
		);
		if ( is_wp_error( $attachment_id ) ) {
			return 0;
		}
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		return (int) $attachment_id;
	}

	/**
	 * Carga o actualiza exclusivamente paginas ficticias con slug estable.
	 *
	 * ## EXAMPLES
	 *
	 *     wp labm fixtures load
	 *
	 * @param array $args Argumentos posicionales.
	 * @param array $assoc_args Argumentos nombrados.
	 */
	public function load( $args, $assoc_args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$fixtures = array_merge(
			array(
				array(
					'post_name'    => 'demo-labm-slide-bienvenida',
					'post_title'   => self::MARKER . ' Slide editorial en borrador',
					'post_content' => '<p>' . self::MARKER . ' Slide editorial de demostracion.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'draft',
					'meta'         => array(
						'labm_cta_texto'   => 'Conocer la Liga',
						'labm_destino_url' => '/nosotros/',
					),
				),
				array(
					'post_name'    => 'demo-labm-slide-publicado-uno',
					'post_title'   => self::MARKER . ' Destacado publicado uno',
					'post_content' => '<p>' . self::MARKER . ' Primer slide publico para recorridos de navegador.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'publish',
					'meta'         => array(
						'labm_cta_texto'   => 'Conocer la Liga',
						'labm_destino_url' => '/nosotros/',
					),
				),
				array(
					'post_name'    => 'demo-labm-slide-publicado-dos',
					'post_title'   => self::MARKER . ' Destacado publicado dos',
					'post_content' => '<p>' . self::MARKER . ' Segundo slide publico para controles e indicadores.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'publish',
					'meta'         => array(
						'labm_cta_texto'   => 'Ver actualidad',
						'labm_destino_url' => '/actualidad/',
					),
				),
				array(
					'post_name'    => 'demo-labm-inicio',
					'post_title'   => self::MARKER . ' Inicio',
					'post_content' => '<p>' . self::MARKER . ' Contenido de ejemplo sin datos reales.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'demo-labm-nosotros',
					'post_title'   => self::MARKER . ' Nosotros',
					'post_content' => '<p>' . self::MARKER . ' Informacion institucional ficticia.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'nosotros',
					'post_title'   => self::MARKER . ' Nosotros',
					'post_content' => '<p>' . self::MARKER . ' Pagina institucional de demostracion.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'demo-labm-actualidad-limite',
					'post_title'   => self::MARKER . ' Evento en fecha limite',
					'post_content' => '<p>' . self::MARKER . ' Noticia de borde sin datos oficiales.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_fecha_evento' => '2026-01-01' ),
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-borrador',
					'post_title'   => self::MARKER . ' Actualidad incompleta',
					'post_content' => '<p>' . self::MARKER . ' Borrador deliberado.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'draft',
				),
				array(
					'post_name'    => 'demo-labm-actualidad-encuentro',
					'post_title'   => self::MARKER . ' Encuentro amistoso',
					'post_content' => '<p>' . self::MARKER . ' Cronica de muestra sin datos oficiales.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-formacion',
					'post_title'   => self::MARKER . ' Jornada de formacion',
					'post_content' => '<p>' . self::MARKER . ' Actividad pedagogica ficticia.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Formacion' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-convocatoria',
					'post_title'   => self::MARKER . ' Convocatoria abierta',
					'post_content' => '<p>' . self::MARKER . ' Convocatoria completamente ficticia.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Eventos' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-resultados',
					'post_title'   => self::MARKER . ' Resultados demostrativos',
					'post_content' => '<p>' . self::MARKER . ' Marcadores inventados para probar paginacion.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-agenda',
					'post_title'   => self::MARKER . ' Agenda ficticia',
					'post_content' => '<p>' . self::MARKER . ' Fechas de ejemplo no vinculantes.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Eventos' ) ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-privada',
					'post_title'   => self::MARKER . ' Seleccion privada',
					'post_content' => '<p>' . self::MARKER . ' Estado privado para pruebas.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'private',
					'terms'        => array( 'labm_modalidad' => array( 'Piso' ) ),
				),
				array(
					'post_name'    => 'demo-labm-club-frontera',
					'post_title'   => self::MARKER . ' Club Frontera',
					'post_content' => '<p>' . self::MARKER . ' Club completamente ficticio.</p>',
					'post_type'    => 'labm_club',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_ciudad' => 'Medellin ficticio' ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-piso',
					'post_title'   => self::MARKER . ' Seleccion Piso',
					'post_content' => '<p>' . self::MARKER . ' Plantel de piso ficticio.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_modalidad' => array( 'Piso' ) ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-playa',
					'post_title'   => self::MARKER . ' Seleccion Playa',
					'post_content' => '<p>' . self::MARKER . ' Plantel de playa ficticio.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_modalidad' => array( 'Playa' ) ),
				),
				array(
					'post_name'    => 'demo-labm-integrante-vacante',
					'post_title'   => self::MARKER . ' Vacante de ejemplo',
					'post_content' => '<p>' . self::MARKER . ' Sin persona real asociada.</p>',
					'post_type'    => 'labm_integrante',
					'post_status'  => 'draft',
					'meta'         => array( 'labm_cargo' => 'Cargo ficticio' ),
				),
				array(
					'post_name'    => 'demo-labm-horario-limite',
					'post_title'   => self::MARKER . ' Horario limite',
					'post_content' => '<p>' . self::MARKER . ' Horario nocturno de prueba.</p>',
					'post_type'    => 'labm_horario',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_inicio' => '23:59' ),
				),
			),
			self::home_news_fixtures(),
			self::home_allies_fixtures()
		);

		foreach ( $fixtures as $fixture ) {
			$existing = get_page_by_path( $fixture['post_name'], OBJECT, $fixture['post_type'] );
			if ( $existing ) {
				clean_post_cache( $existing->ID );
				$existing = get_post( $existing->ID );
			}
			$post = array_intersect_key(
				$fixture,
				array_flip( array( 'post_name', 'post_title', 'post_excerpt', 'post_content', 'post_type', 'post_status', 'post_date', 'menu_order' ) )
			);
			if ( $existing && 0 === strpos( $existing->post_title, self::MARKER ) ) {
				$post['ID'] = $existing->ID;
				$result     = wp_update_post( wp_slash( $post ), true );
			} elseif ( ! $existing ) {
				$result = wp_insert_post( wp_slash( $post ), true );
			} else {
				WP_CLI::warning( "Se preservo contenido ajeno con slug {$fixture['post_name']}." );
				continue;
			}

			if ( is_wp_error( $result ) ) {
				WP_CLI::error( $result->get_error_message() );
			}

			if ( ! empty( $fixture['meta'] ) ) {
				foreach ( $fixture['meta'] as $key => $value ) {
					if ( 'labm_demo_image' === $key && ! is_file( get_theme_file_path( $value ) ) ) {
						delete_post_meta( $result, $key );
						continue;
					}
					update_post_meta( $result, $key, $value );
				}
			}

			if ( ! empty( $fixture['featured_image'] ) ) {
				$attachment_id = self::ensure_demo_attachment( $fixture['featured_image'] );
				if ( $attachment_id ) {
					update_post_meta( $result, '_thumbnail_id', $attachment_id );
				}
			}

			if ( ! empty( $fixture['terms'] ) ) {
				foreach ( $fixture['terms'] as $taxonomy => $terms ) {
					$term_ids = array();
					foreach ( $terms as $term ) {
						$found = term_exists( $term, $taxonomy );
						if ( ! $found ) {
							$found = wp_insert_term( $term, $taxonomy );
						}
						if ( ! is_wp_error( $found ) ) {
							$term_ids[] = (int) $found['term_id'];
						}
					}
					wp_set_post_terms( $result, $term_ids, $taxonomy, false );
				}
			}
		}

		WP_CLI::success( self::MARKER . ' Fixtures cargados de forma idempotente.' );
	}
}
