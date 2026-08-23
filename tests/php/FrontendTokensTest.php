<?php

use PHPUnit\Framework\TestCase;

final class FrontendTokensTest extends TestCase {
	public function test_responsive_design_tokens_are_declared_in_theme_json(): void {
		$theme_json = json_decode(
			(string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/theme.json' ),
			true,
			512,
			JSON_THROW_ON_ERROR
		);
		$tokens = $theme_json['settings']['custom']['labm'] ?? array();
		self::assertSame( 'clamp(.5rem, 1vw, .75rem)', $tokens['spacing']['small'] ?? null );
		self::assertSame( 'clamp(1rem, 2vw, 1.5rem)', $tokens['spacing']['medium'] ?? null );
		self::assertSame( 'clamp(1.5rem, 4vw, 3rem)', $tokens['spacing']['large'] ?? null );
		self::assertSame( '.75rem', $tokens['radius'] ?? null );
	}

	public function test_css_contains_focus_and_reduced_motion_contracts(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/wp-content/themes/labm/style.css' );
		self::assertStringContainsString( ':focus-visible', $css );
		self::assertStringContainsString( 'prefers-reduced-motion: reduce', $css );
		self::assertStringContainsString( 'minmax(min(100%', $css );
	}
}
