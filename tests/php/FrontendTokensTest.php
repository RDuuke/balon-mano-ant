<?php
/**
 * Pruebas de contratos visuales del tema.
 *
 * @package LABM
 */

use PHPUnit\Framework\TestCase;

/** Verifica tokens, foco y movimiento reducido. */
final class FrontendTokensTest extends TestCase {
	/** La paleta y los tokens responsive coinciden con el diseño aprobado. */
	public function test_responsive_design_tokens_are_declared_in_theme_json(): void {

		$theme_json = json_decode(
			(string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/theme.json' ), // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local de fixture.
			true,
			512,
			JSON_THROW_ON_ERROR
		);
		$tokens     = $theme_json['settings']['custom']['labm'] ?? array();
		self::assertSame( 'clamp(.5rem, 1vw, .75rem)', $tokens['spacing']['small'] ?? null );
		self::assertSame( 'clamp(1rem, 2vw, 1.5rem)', $tokens['spacing']['medium'] ?? null );
		self::assertSame( 'clamp(1.5rem, 4vw, 3rem)', $tokens['spacing']['large'] ?? null );
		self::assertSame( '.75rem', $tokens['radius'] ?? null );
		$palette = array_column( $theme_json['settings']['color']['palette'] ?? array(), 'color', 'slug' );
		self::assertSame( '#AECD25', $palette['verde-labm'] ?? null );
		self::assertSame( '#789614', $palette['verde-oscuro-labm'] ?? null );
		self::assertSame( '#202020', $palette['negro-labm'] ?? null );
		self::assertSame( '#F3F6E8', $palette['neutro-labm'] ?? null );
	}

	/** CSS conserva foco, grillas fluidas y contratos de pausa. */
	public function test_css_contains_focus_and_reduced_motion_contracts(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/style.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local de fixture.
		self::assertStringContainsString( ':focus-visible', $css );
		self::assertStringContainsString( 'prefers-reduced-motion: reduce', $css );
		self::assertStringContainsString( 'minmax(min(100%', $css );
		self::assertStringContainsString( '[data-labm-paused="true"]', $css );
		self::assertStringContainsString( '.labm-allies__visual[aria-hidden="true"]', $css );
	}
}
