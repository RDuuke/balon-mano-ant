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

/** Registra el estilo publico del tema. */
function labm_theme_enqueue_public_style() {
	wp_enqueue_style( 'labm-site', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
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
