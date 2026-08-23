<?php

use PHPUnit\Framework\TestCase;

final class FixturesDomainTest extends TestCase {
	public function test_domain_fixtures_cover_public_draft_private_and_edge_states(): void {
		$expected = array(
			'demo-labm-actualidad-limite'   => array( 'labm_actualidad', 'publish' ),
			'demo-labm-actualidad-borrador' => array( 'labm_actualidad', 'draft' ),
			'demo-labm-seleccion-privada'   => array( 'labm_seleccion', 'private' ),
			'demo-labm-club-frontera'        => array( 'labm_club', 'publish' ),
			'demo-labm-integrante-vacante'   => array( 'labm_integrante', 'draft' ),
			'demo-labm-horario-limite'       => array( 'labm_horario', 'publish' ),
		);

		foreach ( $expected as $slug => $definition ) {
			$post = get_page_by_path( $slug, OBJECT, $definition[0] );
			self::assertInstanceOf( WP_Post::class, $post, $slug );
			self::assertSame( $definition[1], $post->post_status, $slug );
			self::assertStringContainsString( 'FICTICIO', $post->post_title, $slug );
		}
	}

	public function test_fixture_terms_and_metadata_are_present(): void {
		$actualidad = get_page_by_path( 'demo-labm-actualidad-limite', OBJECT, 'labm_actualidad' );
		$seleccion  = get_page_by_path( 'demo-labm-seleccion-privada', OBJECT, 'labm_seleccion' );
		self::assertSame( '2026-01-01', get_post_meta( $actualidad->ID, 'labm_fecha_evento', true ) );
		self::assertSame( array( 'Noticias' ), wp_get_post_terms( $actualidad->ID, 'labm_categoria', array( 'fields' => 'names' ) ) );
		self::assertSame( array( 'Piso' ), wp_get_post_terms( $seleccion->ID, 'labm_modalidad', array( 'fields' => 'names' ) ) );
	}

	public function test_fixtures_do_not_create_pdf_attachments(): void {
		$query = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'application/pdf',
			)
		);
		self::assertSame( 0, $query->post_count );
	}

	public function test_each_domain_fixture_slug_is_unique(): void {
		$fixtures = array(
			'labm_actualidad' => array( 'demo-labm-actualidad-limite', 'demo-labm-actualidad-borrador' ),
			'labm_seleccion'  => array( 'demo-labm-seleccion-privada' ),
			'labm_club'       => array( 'demo-labm-club-frontera' ),
			'labm_integrante' => array( 'demo-labm-integrante-vacante' ),
			'labm_horario'    => array( 'demo-labm-horario-limite' ),
		);
		foreach ( $fixtures as $post_type => $slugs ) {
			foreach ( $slugs as $slug ) {
				$posts = get_posts(
					array(
						'name'           => $slug,
						'post_type'      => $post_type,
						'post_status'    => array( 'publish', 'draft', 'private' ),
						'posts_per_page' => -1,
					)
				);
				self::assertCount( 1, $posts, $slug );
			}
		}
	}
}
