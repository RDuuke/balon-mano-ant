<?php

use PHPUnit\Framework\TestCase;

final class InfrastructureTest extends TestCase {
	public function test_component_sources_exist(): void {
		self::assertFileExists( LABM_TEST_ROOT . '/wp-content/themes/labm/theme.json' );
		self::assertFileExists( LABM_TEST_ROOT . '/wp-content/plugins/labm-core/labm-core.php' );
	}
}

