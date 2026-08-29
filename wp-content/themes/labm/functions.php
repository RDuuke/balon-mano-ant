<?php
/**
 * Funciones del tema LABM.
 *
 * @package LABM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Renderiza una capacidad de dominio o un fallback seguro.
 *
 * @return string HTML seguro.
 */
function labm_theme_domain_summary() {
	if ( function_exists( 'labm_core_summary' ) ) {
		return labm_core_summary();
	}

	return '<p class="labm-notice">' . esc_html__( 'LABM Core no esta activo; el contenido institucional sigue disponible.', 'labm' ) . '</p>';
}

/** Registra los activos publicos del tema. */
function labm_theme_enqueue_public_style() {

	wp_enqueue_style( 'labm-site', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	if ( is_front_page() ) {
		wp_enqueue_script( 'labm-home', get_theme_file_uri( 'assets/home.js' ), array(), wp_get_theme()->get( 'Version' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'labm_theme_enqueue_public_style' );

/** Registra listados dinamicos antes de procesar las plantillas. */
function labm_theme_setup_public_experience() {
	add_shortcode( 'labm_actualidad_listado', 'labm_theme_actualidad_shortcode' );
	add_shortcode( 'labm_selecciones_listado', 'labm_theme_selecciones_shortcode' );
}
add_action( 'init', 'labm_theme_setup_public_experience' );

/** Enlace para saltar la navegacion repetida. */
function labm_theme_skip_link() {
	echo '<a class="labm-skip-link" href="#contenido-principal">' . esc_html__( 'Saltar al contenido', 'labm' ) . '</a>';
}
add_action( 'wp_body_open', 'labm_theme_skip_link' );

/**
 * Devuelve las secciones habilitadas de portada conservando su orden.
 *
 * @param array $configuration Estado de secciones.
 * @return array
 */
function labm_theme_home_sections( $configuration = array() ) {

	$sections = array();
	foreach ( array( 'slider', 'presentacion', 'clubes', 'evento', 'actualidad', 'vinculacion', 'aliados' ) as $section ) {
		if ( ! array_key_exists( $section, $configuration ) || ! empty( $configuration[ $section ] ) ) {
			$sections[] = $section;
		}
	}
	return $sections;
}

/**
 * Consulta una coleccion acotada para la portada.
 *
 * @param string $post_type Tipo de contenido.
 * @param int    $limit Limite de elementos.
 * @return WP_Query
 */
function labm_theme_home_query( $post_type, $limit ) {

	return new WP_Query(
		array(
			'post_type'      => sanitize_key( $post_type ),
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, absint( $limit ) ),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Devuelve publicaciones o una lista vacia si el tipo no esta disponible.
 *
 * @param string $post_type Tipo de contenido.
 * @param int    $limit Limite de elementos.
 * @return array
 */
function labm_theme_home_posts( $post_type, $limit ) {

	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}
	return labm_theme_home_query( $post_type, $limit )->posts;
}

/**
 * Renderiza el slider editorial.
 *
 * @param string $post_type Tipo de contenido.
 * @return string
 */
function labm_theme_render_home_slider( $post_type = 'labm_slide' ) {

	$posts = labm_theme_home_posts( $post_type, 5 );
	if ( empty( $posts ) ) {
		return '';
	}
	ob_start();
	?>
	<section class="labm-home-slider" data-labm-section="slider" data-labm-slider aria-label="<?php esc_attr_e( 'Destacados', 'labm' ); ?>">
		<div class="labm-home-slider__items">
			<?php
			foreach ( $posts as $index => $post ) :
				?>
				<article data-labm-slide <?php echo 0 === $index ? '' : 'hidden'; ?>>
					<?php echo get_the_post_thumbnail( $post, 'full', array( 'loading' => 0 === $index ? 'eager' : 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress genera el marcado. ?>
					<h2><?php echo esc_html( get_the_title( $post ) ); ?></h2>
					<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 30 ) ); ?></p>
					<?php $destination = get_post_meta( $post->ID, 'labm_destino_url', true ); ?>
					<?php
					if ( $destination ) :
						?>
						<a href="<?php echo esc_url( $destination ); ?>"><?php echo esc_html( get_post_meta( $post->ID, 'labm_cta_texto', true ) ? get_post_meta( $post->ID, 'labm_cta_texto', true ) : __( 'Conocer más', 'labm' ) ); ?></a><?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
		if ( count( $posts ) > 1 ) :
			?>
			<div class="labm-home-slider__controls"><button type="button" data-labm-slider-prev><?php esc_html_e( 'Anterior', 'labm' ); ?></button><button type="button" data-labm-slider-pause aria-pressed="false"><?php esc_html_e( 'Pausar', 'labm' ); ?></button><button type="button" data-labm-slider-next><?php esc_html_e( 'Siguiente', 'labm' ); ?></button></div>
			<div class="labm-home-slider__indicators" aria-label="<?php esc_attr_e( 'Elegir destacado', 'labm' ); ?>">
			<?php
			foreach ( $posts as $index => $post ) :
				?>
				<button type="button" data-labm-slide-to="<?php echo esc_attr( (string) $index ); ?>" aria-label="<?php /* translators: %d: numero ordinal del destacado. */ echo esc_attr( sprintf( __( 'Ir al destacado %d', 'labm' ), $index + 1 ) ); ?>" aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>"></button><?php endforeach; ?></div>
		<?php endif; ?>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Renderiza tarjetas sencillas de una coleccion editorial.
 *
 * @param string $post_type Tipo de contenido.
 * @param int    $limit Limite de elementos.
 * @param string $section Identificador de seccion.
 * @param string $heading Titulo visible.
 * @return string
 */
function labm_theme_render_home_cards( $post_type, $limit, $section, $heading ) {

	$posts = labm_theme_home_posts( $post_type, $limit );
	if ( empty( $posts ) ) {
		return '';
	}
	ob_start();
	?>
	<section class="labm-home-section labm-home-<?php echo esc_attr( $section ); ?>" data-labm-section="<?php echo esc_attr( $section ); ?>"><h2><?php echo esc_html( $heading ); ?></h2><div class="labm-card-grid">
	<?php
	foreach ( $posts as $post ) :
		?>
		<article class="labm-card"><h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3><p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 24 ) ); ?></p></article><?php endforeach; ?>
	</div></section>
	<?php
	return (string) ob_get_clean();
}

/** Renderiza clubes publicados. */
function labm_theme_render_home_clubs() {

	return labm_theme_render_home_cards( 'labm_club', 6, 'clubes', __( 'Clubes asociados', 'labm' ) );
}

/** Renderiza el evento editorial destacado. */
function labm_theme_render_home_event() {

	return labm_theme_render_home_cards( 'labm_actualidad', 1, 'evento', __( 'Evento destacado', 'labm' ) );
}

/** Renderiza actualidad publicada. */
function labm_theme_render_home_news() {

	return labm_theme_render_home_cards( 'labm_actualidad', 3, 'actualidad', __( 'Actualidad', 'labm' ) );
}

/**
 * Renderiza aliados con una lista semantica unica y una copia solo visual.
 *
 * @param string $post_type Tipo de contenido.
 * @return string
 */
function labm_theme_render_home_allies( $post_type = 'labm_aliado' ) {

	$posts = labm_theme_home_posts( $post_type, 12 );
	if ( empty( $posts ) ) {
		return '';
	}
	$list = static function ( $visual_copy = false ) use ( $posts ) {

		foreach ( $posts as $post ) {
			$url = get_post_meta( $post->ID, 'labm_destino_url', true );
			echo '<li>';
			if ( $url ) {
				echo '<a href="' . esc_url( $url ) . '"' . ( $visual_copy ? ' tabindex="-1"' : '' ) . '>';
			}
			echo get_the_post_thumbnail(
				$post,
				'medium',
				array(
					'alt'     => get_the_title( $post ),
					'loading' => 'lazy',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<span>' . esc_html( get_the_title( $post ) ) . '</span>';
			if ( $url ) {
				echo '</a>';
			}
			echo '</li>';
		}
	};
	ob_start();
	?>
	<section class="labm-home-section labm-allies" data-labm-section="aliados" data-labm-allies><h2><?php esc_html_e( 'Aliados Oficiales', 'labm' ); ?></h2><button type="button" data-labm-allies-pause aria-pressed="false"><?php esc_html_e( 'Pausar movimiento', 'labm' ); ?></button><ul class="labm-allies__list"><?php $list(); ?></ul><div class="labm-allies__visual" aria-hidden="true" inert><ul><?php $list( true ); ?></ul></div></section>
	
	<?php
	return (string) ob_get_clean();
}
/**
 * Marca semanticamente el destino activo de la navegacion global.
 *
 * @param string $content HTML del enlace.
 * @param array  $block Bloque de navegacion.
 * @return string
 */
function labm_theme_mark_current_navigation_link( $content, $block ) {
	$url          = isset( $block['attrs']['url'] ) ? (string) $block['attrs']['url'] : '';
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
	$current_path = wp_parse_url( $request_uri, PHP_URL_PATH );
	$target_path  = wp_parse_url( $url, PHP_URL_PATH );
	if ( untrailingslashit( (string) $current_path ) === untrailingslashit( (string) $target_path ) ) {
		return preg_replace( '/<a\s/', '<a aria-current="page" ', $content, 1 ) ?? $content;
	}
	return $content;
}
add_filter( 'render_block_core/navigation-link', 'labm_theme_mark_current_navigation_link', 10, 2 );

/**
 * Consulta publica, paginada y filtrada sin exponer estados no publicos.
 *
 * @param string $post_type Tipo de contenido.
 * @param array  $filters Filtros permitidos.
 * @param int    $page Pagina solicitada.
 * @param int    $per_page Elementos por pagina.
 * @return WP_Query
 */
function labm_theme_public_query( $post_type, $filters = array(), $page = 1, $per_page = 3 ) {
	$args     = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) $per_page ),
		'paged'          => max( 1, (int) $page ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	$taxonomy = 'labm_actualidad' === $post_type ? 'labm_categoria' : 'labm_modalidad';
	$key      = 'labm_actualidad' === $post_type ? 'categoria' : 'modalidad';
	if ( ! empty( $filters[ $key ] ) ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'name',
				'terms'    => sanitize_text_field( $filters[ $key ] ),
			),
		);
	}
	return new WP_Query( $args );
}

/**
 * Renderiza un listado publico con filtro, vacio y paginacion.
 *
 * @param string $post_type Tipo de contenido.
 * @param array  $filters Filtros permitidos.
 * @return string
 */
function labm_theme_render_listing( $post_type, $filters ) {
	if ( ! post_type_exists( $post_type ) ) {
		return '<p class="labm-notice">' . esc_html__( 'Esta sección no está disponible por el momento.', 'labm' ) . '</p>';
	}
	$is_news   = 'labm_actualidad' === $post_type;
	$key       = $is_news ? 'categoria' : 'modalidad';
	$taxonomy  = $is_news ? 'labm_categoria' : 'labm_modalidad';
	$data_name = $is_news ? 'actualidad' : 'selecciones';
	$page      = isset( $filters['pagina'] ) ? absint( $filters['pagina'] ) : 1;
	$selected  = isset( $filters[ $key ] ) ? sanitize_text_field( $filters[ $key ] ) : '';
	$query     = labm_theme_public_query( $post_type, array( $key => $selected ), $page );
	$terms     = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	ob_start();
	?>
	<form class="labm-filter" method="get">
		<label for="labm-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $is_news ? __( 'Categoría', 'labm' ) : __( 'Modalidad', 'labm' ) ); ?></label>
		<select id="labm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>">
			<option value=""><?php esc_html_e( 'Todas', 'labm' ); ?></option>
			<?php foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term->name ); ?>" <?php selected( $selected, $term->name ); ?>><?php echo esc_html( $term->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<button type="submit"><?php esc_html_e( 'Aplicar filtro', 'labm' ); ?></button>
	</form>
	<div class="labm-card-grid" data-labm-listado="<?php echo esc_attr( $data_name ); ?>">
		<?php foreach ( $query->posts as $post ) : ?>
			<article class="labm-card">
				<p class="labm-card__eyebrow" data-labm-modalidad><?php echo esc_html( implode( ', ', wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'names' ) ) ) ); ?></p>
				<h2><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h2>
				<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $post->post_content ), 24 ) ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
	<?php if ( ! $query->have_posts() ) : ?>
		<div class="labm-empty"><p><?php esc_html_e( 'No hay publicaciones para este filtro.', 'labm' ); ?></p><a href="<?php echo esc_url( get_post_type_archive_link( $post_type ) ); ?>"><?php esc_html_e( 'Limpiar filtros', 'labm' ); ?></a></div>
	<?php elseif ( $query->max_num_pages > 1 ) : ?>
		<nav class="labm-pagination" aria-label="<?php esc_attr_e( 'Paginación', 'labm' ); ?>">
			<?php
			echo wp_kses_post(
				paginate_links(
					array(
						'base'      => add_query_arg( 'pagina', '%#%', get_post_type_archive_link( $post_type ) ),
						'format'    => '',
						'current'   => $page,
						'total'     => $query->max_num_pages,
						'prev_text' => __( 'Página anterior', 'labm' ),
						'next_text' => __( 'Página siguiente', 'labm' ),
					)
				)
			);
			?>
		</nav>
	<?php endif; ?>
	<?php
	wp_reset_postdata();
	return (string) ob_get_clean();
}

/**
 * Renderiza el listado de actualidad.
 *
 * @return string
 */
function labm_theme_actualidad_shortcode() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtro publico de solo lectura.
	$filters = array_map( 'sanitize_text_field', wp_unslash( $_GET ) );
	return labm_theme_render_listing( 'labm_actualidad', $filters );
}

/**
 * Renderiza el listado de selecciones.
 *
 * @return string
 */
function labm_theme_selecciones_shortcode() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtro publico de solo lectura.
	$filters = array_map( 'sanitize_text_field', wp_unslash( $_GET ) );
	return labm_theme_render_listing( 'labm_seleccion', $filters );
}
