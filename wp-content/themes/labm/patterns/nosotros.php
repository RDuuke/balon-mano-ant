<?php
/**
 * Title: Nosotros LABM
 * Slug: labm/nosotros
 * Categories: text
 * Inserter: no
 *
 * @package LABM
 */
?>
<?php echo labm_theme_render_about_banner(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper de renderizado seguro. ?>
<?php echo labm_theme_render_about_purpose(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper de renderizado seguro. ?>
<?php echo labm_theme_render_about_team(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper de renderizado seguro. ?>
<?php echo wp_kses_post( labm_theme_render_join_cta() ); ?>
