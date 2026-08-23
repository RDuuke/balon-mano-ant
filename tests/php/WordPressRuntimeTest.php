<?php

use PHPUnit\Framework\TestCase;

final class WordPressRuntimeTest extends TestCase {
	public function test_wordpress_runtime_is_installed_with_labm_components(): void {
		self::assertTrue( is_blog_installed() );
		self::assertSame( 'labm', get_stylesheet() );
		self::assertTrue( is_plugin_active( 'labm-core/labm-core.php' ) );
	}

	public function test_demo_fixtures_have_stable_unique_slugs(): void {
		foreach ( array( 'demo-labm-inicio', 'demo-labm-nosotros' ) as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			self::assertInstanceOf( WP_Post::class, $page );
			self::assertStringContainsString( 'FICTICIO', $page->post_title );
		}
	}
}
