<?php
/**
 * Title: Inicio LABM
 * Slug: labm/inicio
 * Categories: featured
 * Inserter: no
 *
 * @package LABM
 */

/* data-labm-section="slider" */
echo wp_kses_post( labm_theme_render_home_slider() );
?>
<!-- wp:group {"tagName":"section","className":"labm-home-section labm-home-presentation","layout":{"type":"constrained"}} -->
<section class="wp-block-group labm-home-section labm-home-presentation" data-labm-section="presentacion"><div class="labm-home-presentation__content"><p class="labm-home-eyebrow"><?php esc_html_e( 'Nuestra Liga', 'labm' ); ?></p><!-- wp:heading {"level":1} --><h1 class="wp-block-heading"><?php esc_html_e( 'Formamos deportistas. Fortalecemos territorio.', 'labm' ); ?></h1><!-- /wp:heading -->
<!-- wp:paragraph --><p><?php esc_html_e( 'La Liga articula clubes, atletas y procesos deportivos para hacer crecer el balonmano en Antioquia. Construimos una comunidad que compite, aprende y representa al territorio.', 'labm' ); ?></p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/nosotros/"><?php esc_html_e( 'Conoce quiénes somos', 'labm' ); ?></a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><aside class="labm-home-presentation__stat" aria-label="<?php esc_attr_e( 'Una comunidad que juega unida', 'labm' ); ?>"><strong>01</strong><span><?php esc_html_e( 'Una comunidad que juega unida', 'labm' ); ?></span></aside></section>
<!-- /wp:group -->
<?php
/* data-labm-section="clubes" */
echo wp_kses_post( labm_theme_render_home_clubs() );
/* data-labm-section="evento" */
echo wp_kses_post( labm_theme_render_home_event() );
/* data-labm-section="actualidad" */
echo wp_kses_post( labm_theme_render_home_news() );
?>
<!-- wp:group {"tagName":"section","className":"labm-home-section labm-home-join","layout":{"type":"constrained"}} -->
<section class="wp-block-group labm-home-section labm-home-join" data-labm-section="vinculacion"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading"><?php esc_html_e( 'Haz parte del balonmano antioqueño', 'labm' ); ?></h2><!-- /wp:heading -->
<!-- wp:paragraph --><p><?php esc_html_e( 'Conoce nuestros clubes y encuentra una comunidad para entrenar y competir.', 'labm' ); ?></p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contacto/"><?php esc_html_e( 'Quiero vincularme', 'labm' ); ?></a></div><!-- /wp:button --></div><!-- /wp:buttons --></section>
<!-- /wp:group -->
<?php
/* data-labm-section="aliados" */
echo wp_kses_post( labm_theme_render_home_allies() );
