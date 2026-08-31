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
		$fixtures = array(
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
				'post_name'    => 'demo-labm-aliado-ejemplo',
				'post_title'   => self::MARKER . ' Aliado editorial en borrador',
				'post_content' => '<p>' . self::MARKER . ' Entidad ficticia sin vinculacion oficial.</p>',
				'post_type'    => 'labm_aliado',
				'post_status'  => 'draft',
				'meta'         => array( 'labm_destino_url' => 'https://example.org/' ),
			),
			array(
				'post_name'    => 'demo-labm-aliado-publicado-uno',
				'post_title'   => self::MARKER . ' Aliado publicado uno',
				'post_content' => '<p>' . self::MARKER . ' Primer aliado publico para recorridos de navegador.</p>',
				'post_type'    => 'labm_aliado',
				'post_status'  => 'publish',
				'meta'         => array( 'labm_destino_url' => 'https://example.org/aliado-uno' ),
			),
			array(
				'post_name'    => 'demo-labm-aliado-publicado-dos',
				'post_title'   => self::MARKER . ' Aliado publicado dos',
				'post_content' => '<p>' . self::MARKER . ' Segundo aliado publico para continuidad visual.</p>',
				'post_type'    => 'labm_aliado',
				'post_status'  => 'publish',
				'meta'         => array( 'labm_destino_url' => 'https://example.org/aliado-dos' ),
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
		);

		foreach ( $fixtures as $fixture ) {
			$existing = get_page_by_path( $fixture['post_name'], OBJECT, $fixture['post_type'] );
			if ( $existing ) {
				clean_post_cache( $existing->ID );
				$existing = get_post( $existing->ID );
			}
			$post = array_intersect_key(
				$fixture,
				array_flip( array( 'post_name', 'post_title', 'post_content', 'post_type', 'post_status' ) )
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
					update_post_meta( $result, $key, $value );
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
