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
	$fallback_images = array(
		'assets/images/hero-balonmano-antioquia-v1.png',
		'assets/images/hero-balonmano-seleccion-v1.png',
	);
	ob_start();
	?>
	<section class="labm-home-slider" data-labm-section="slider" data-labm-slider aria-label="<?php esc_attr_e( 'Destacados', 'labm' ); ?>">
		<div class="labm-home-slider__items">
			<?php
			foreach ( $posts as $index => $post ) :
				$thumbnail = get_the_post_thumbnail( $post, 'full', array( 'loading' => 0 === $index ? 'eager' : 'lazy' ) );
				if ( '' === $thumbnail ) {
					$fallback  = $fallback_images[ $index % count( $fallback_images ) ];
					$thumbnail = sprintf(
						'<img src="%1$s" alt="" loading="%2$s" width="%3$d" height="%4$d">',
						esc_url( get_theme_file_uri( $fallback ) ),
						0 === $index ? 'eager' : 'lazy',
						0 === $index ? 1536 : 1366,
						0 === $index ? 864 : 768
					);
				}
				?>
				<article class="labm-home-slider__slide" data-labm-slide tabindex="0" <?php echo 0 === $index ? '' : 'hidden'; ?>>
					<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress genera o sanea el marcado. ?>
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
			<div class="labm-home-slider__controls">
				<button type="button" data-labm-slider-prev aria-label="<?php esc_attr_e( 'Anterior', 'labm' ); ?>"></button>
				<button type="button" data-labm-slider-pause data-label-pause="<?php esc_attr_e( 'Pausar', 'labm' ); ?>" data-label-resume="<?php esc_attr_e( 'Reanudar', 'labm' ); ?>" aria-label="<?php esc_attr_e( 'Pausar', 'labm' ); ?>" aria-pressed="false"><span class="screen-reader-text" data-labm-pause-label><?php esc_html_e( 'Pausar', 'labm' ); ?></span></button>
				<button type="button" data-labm-slider-next aria-label="<?php esc_attr_e( 'Siguiente', 'labm' ); ?>"></button>
			</div>
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
		$thumbnail = 'clubes' === $section ? get_the_post_thumbnail(
			$post,
			'medium',
			array(
				'class'   => 'labm-card__logo',
				'loading' => 'lazy',
				'alt'     => get_the_title( $post ),
			)
		) : '';
		?>
		<article class="labm-card<?php echo 'clubes' === $section ? ' labm-card--club' : ''; ?>">
			<?php if ( '' !== $thumbnail ) : ?>
				<a class="labm-card__logo-link" href="<?php echo esc_url( get_permalink( $post ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post ) ); ?>"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress genera el marcado. ?></a>
			<?php endif; ?>
			<h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3>
			<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 24 ) ); ?></p>
		</article>
	<?php endforeach; ?>
	</div></section>
	<?php
	return (string) ob_get_clean();
}

/** Renderiza clubes publicados. */
function labm_theme_render_home_clubs() {

	return labm_theme_render_home_cards( 'labm_club', 6, 'clubes', __( 'Clubes asociados', 'labm' ) );
}

/** Renderiza el evento editorial destacado. */
function labm_theme_home_event_query() {

	return new WP_Query(
		array(
			'post_type'      => 'labm_actualidad',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => 'labm_fecha_evento', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_compare'   => 'EXISTS',
			'orderby'        => array(
				'meta_value' => 'ASC', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'ID'         => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);
}

/** Renderiza el evento editorial destacado. */
function labm_theme_render_home_event() {

	$posts = post_type_exists( 'labm_actualidad' ) ? labm_theme_home_event_query()->posts : array();
	if ( empty( $posts ) ) {
		return '';
	}
	$post       = $posts[0];
	$event_date = get_post_meta( $post->ID, 'labm_fecha_evento', true );
	$timestamp  = $event_date ? strtotime( $event_date ) : false;
	$thumbnail  = get_the_post_thumbnail(
		$post,
		'full',
		array(
			'class'   => 'labm-featured-event__image',
			'loading' => 'lazy',
			'alt'     => get_the_title( $post ),
		)
	);
	ob_start();
	?>
	<section class="labm-featured-event" data-labm-section="evento">
		<div class="labm-featured-event__media"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress genera el marcado. ?></div>
		<div class="labm-featured-event__content">
			<p class="labm-featured-event__eyebrow"><?php esc_html_e( 'Evento destacado', 'labm' ); ?></p>
			<?php if ( $timestamp ) : ?>
				<time class="labm-featured-event__date" datetime="<?php echo esc_attr( $event_date ); ?>"><strong><?php echo esc_html( wp_date( 'd', $timestamp ) ); ?></strong><span><?php echo esc_html( wp_date( 'M', $timestamp ) ); ?><br><?php echo esc_html( wp_date( 'Y', $timestamp ) ); ?></span></time>
			<?php endif; ?>
			<h2><?php echo esc_html( get_the_title( $post ) ); ?></h2>
			<p class="labm-featured-event__summary"><?php echo esc_html( $post->post_excerpt ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 20 ) ); ?></p>
			<a class="labm-featured-event__cta" href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php esc_html_e( 'Ver evento', 'labm' ); ?></a>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/** Renderiza actualidad publicada. */
function labm_theme_home_news_query() {

	return new WP_Query(
		array(
			'post_type'      => 'labm_actualidad',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'orderby'        => array(
				'date' => 'DESC',
				'ID'   => 'DESC',
			),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => 'labm_fecha_evento',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'labm_fecha_evento',
					'value'   => '',
					'compare' => '=',
				),
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Devuelve la URL segura del archivo de noticias.
 *
 * @param string $post_type Tipo de contenido.
 * @return string
 */
function labm_theme_home_news_archive_url( $post_type = 'labm_actualidad' ) {

	if ( ! post_type_exists( $post_type ) ) {
		return '';
	}
	$url = get_post_type_archive_link( $post_type );
	return $url ? esc_url_raw( $url ) : '';
}

/**
 * Compone el medio seguro de una noticia de portada.
 *
 * @param WP_Post $post Publicacion.
 * @param bool    $featured Indica la pieza destacada.
 * @return string
 */
function labm_theme_home_news_media( $post, $featured = false ) {

	$class     = $featured ? 'labm-home-news__featured-image' : 'labm-home-news__side-image';
	$thumbnail = get_the_post_thumbnail(
		$post,
		$featured ? 'large' : 'medium_large',
		array(
			'class'   => $class,
			'loading' => $featured ? 'eager' : 'lazy',
		)
	);
	if ( '' !== $thumbnail ) {
		return $thumbnail;
	}

	$allowed = array(
		'assets/images/hero-balonmano-antioquia-v1.png',
		'assets/images/hero-balonmano-seleccion-v1.png',
	);
	$path    = get_post_meta( $post->ID, 'labm_demo_image', true );
	if ( ! in_array( $path, $allowed, true ) ) {
		$path = $allowed[ $featured ? 0 : 1 ];
	}

	return sprintf(
		'<img class="%1$s" src="%2$s" alt="" loading="%3$s" width="%4$d" height="%5$d">',
		esc_attr( $class ),
		esc_url( get_theme_file_uri( $path ) ),
		$featured ? 'eager' : 'lazy',
		$featured ? 1536 : 1366,
		$featured ? 864 : 768
	);
}

/**
 * Compone categoria y fecha de una noticia.
 *
 * @param WP_Post $post Publicacion.
 * @return string
 */
function labm_theme_home_news_meta( $post ) {

	$terms    = wp_get_post_terms( $post->ID, 'labm_categoria', array( 'fields' => 'names' ) );
	$category = ! is_wp_error( $terms ) && ! empty( $terms ) ? $terms[0] : '';
	$date     = get_post_datetime( $post );
	$parts    = array();
	if ( $category ) {
		$parts[] = '<span>' . esc_html( $category ) . '</span>';
	}
	if ( $date ) {
		$parts[] = '<time datetime="' . esc_attr( $date->format( DATE_W3C ) ) . '">' . esc_html( wp_date( 'j M Y', $date->getTimestamp() ) ) . '</time>';
	}
	return implode( '<span aria-hidden="true"> · </span>', $parts );
}

/**
 * Oculta el marcador tecnico de fixtures en el titulo visible.
 *
 * @param WP_Post $post Publicacion.
 * @return string
 */
function labm_theme_home_news_title( $post ) {

	$title = get_the_title( $post );
	$clean = preg_replace( '/^\[DEMO LABM — FICTICIO\]\s*/u', '', $title );
	return is_string( $clean ) && '' !== $clean ? $clean : $title;
}

/** Renderiza actualidad publicada. */
function labm_theme_render_home_news() {

	if ( ! post_type_exists( 'labm_actualidad' ) ) {
		return '';
	}
	$posts = labm_theme_home_news_query()->posts;
	if ( empty( $posts ) ) {
		return '';
	}
	$featured    = array_shift( $posts );
	$archive_url = labm_theme_home_news_archive_url();
	ob_start();
	?>
	<section class="labm-home-section labm-home-news" data-labm-section="actualidad">
		<header class="labm-home-news__header">
			<h2><?php esc_html_e( 'Últimas noticias', 'labm' ); ?></h2>
			<?php if ( $archive_url ) : ?>
				<a class="labm-home-news__archive" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Ver toda la actualidad', 'labm' ); ?> <span aria-hidden="true">→</span></a>
			<?php endif; ?>
		</header>
		<div class="labm-home-news__layout">
			<article class="labm-home-news__featured">
				<a class="labm-home-news__article-link" href="<?php echo esc_url( get_permalink( $featured ) ); ?>">
					<?php echo labm_theme_home_news_media( $featured, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper seguro. ?>
					<span class="labm-home-news__featured-overlay">
						<span class="labm-home-news__meta"><?php echo wp_kses_post( labm_theme_home_news_meta( $featured ) ); ?></span>
						<h3><?php echo esc_html( labm_theme_home_news_title( $featured ) ); ?></h3>
					</span>
				</a>
			</article>
			<?php if ( ! empty( $posts ) ) : ?>
				<div class="labm-home-news__side-list">
					<?php foreach ( $posts as $post ) : ?>
						<article class="labm-home-news__side-card">
							<a class="labm-home-news__article-link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
								<span class="labm-home-news__side-media"><?php echo labm_theme_home_news_media( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper seguro. ?></span>
								<span class="labm-home-news__side-content">
									<span class="labm-home-news__meta"><?php echo wp_kses_post( labm_theme_home_news_meta( $post ) ); ?></span>
									<h3><?php echo esc_html( labm_theme_home_news_title( $post ) ); ?></h3>
								</span>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Renderiza aliados con una lista semantica unica y una copia solo visual.
 *
 * @param string $post_type Tipo de contenido.
 * @return string
 */
function labm_theme_render_home_allies( $post_type = 'labm_aliado' ) {

	if ( ! post_type_exists( $post_type ) ) {
		return '';
	}
	$query = new WP_Query(
		array(
			'post_type'      => sanitize_key( $post_type ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
				'ID'         => 'ASC',
			),
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	$logos = array();
	foreach ( $query->posts as $post ) {
		$title         = trim( wp_strip_all_tags( get_the_title( $post ) ) );
		$attachment_id = get_post_thumbnail_id( $post->ID );
		$image         = $attachment_id ? wp_get_attachment_image_src( $attachment_id, 'medium' ) : false;
		if ( '' === $title || ! $image ) {
			continue;
		}
		$logos[] = array(
			'post'  => $post,
			'title' => $title,
		);
		if ( 12 === count( $logos ) ) {
			break;
		}
	}
	if ( empty( $logos ) ) {
		return '';
	}
	$list = static function () use ( $logos ) {
		foreach ( $logos as $logo ) {
			$clean_title = preg_replace( '/^\[DEMO LABM[^\]]*\]\s*/u', '', $logo['title'] );
			$title       = null === $clean_title || '' === $clean_title ? $logo['title'] : $clean_title;
			echo '<li class="labm-allies__item">';
			echo get_the_post_thumbnail(
				$logo['post'],
				'medium',
				array(
					'alt'     => sanitize_text_field( $title ),
					'loading' => 'lazy',
					'class'   => 'labm-allies__logo',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</li>';
		}
	};
	ob_start();
	?>
	<section class="labm-home-section labm-allies" data-labm-section="aliados" data-labm-allies>
		<h2><?php esc_html_e( 'Aliados Oficiales', 'labm' ); ?></h2>
		<div class="labm-allies__viewport">
			<div class="labm-allies__track">
				<ul class="labm-allies__list"><?php $list(); ?></ul>
				<ul class="labm-allies__list labm-allies__replica" aria-hidden="true" inert><?php $list(); ?></ul>
			</div>
		</div>
	</section>
	
	<?php
	return (string) ob_get_clean();
}

/**
 * Permite el atributo booleano inert en la réplica no accesible del marquee.
 *
 * @param array  $tags Etiquetas y atributos permitidos.
 * @param string $context Contexto de saneado.
 * @return array
 */
function labm_theme_allow_inert_attribute( $tags, $context ) {
	if ( 'post' === $context && isset( $tags['ul'] ) ) {
		$tags['ul']['inert'] = true;
	}
	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'labm_theme_allow_inert_attribute', 10, 2 );

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
