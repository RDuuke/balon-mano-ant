<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DomainModelTest extends TestCase {
	/** @return array<string> */
	public static function post_type_provider(): array {
		return array(
			'actualidad' => array( 'labm_actualidad' ),
			'seleccion'  => array( 'labm_seleccion' ),
			'club'       => array( 'labm_club' ),
			'integrante' => array( 'labm_integrante' ),
			'horario'    => array( 'labm_horario' ),
		);
	}

	#[DataProvider( 'post_type_provider' )]
	public function test_domain_post_types_are_rest_enabled_and_persistent( string $post_type ): void {
		$object = get_post_type_object( $post_type );
		self::assertInstanceOf( WP_Post_Type::class, $object );
		self::assertTrue( $object->show_in_rest );
		self::assertTrue( $object->map_meta_cap );
		self::assertTrue( post_type_supports( $post_type, 'title' ) );
	}

	public function test_taxonomies_are_extensible_and_rest_enabled(): void {
		$modalidad = get_taxonomy( 'labm_modalidad' );
		$categoria = get_taxonomy( 'labm_categoria' );
		self::assertInstanceOf( WP_Taxonomy::class, $modalidad );
		self::assertInstanceOf( WP_Taxonomy::class, $categoria );
		self::assertTrue( $modalidad->show_in_rest );
		self::assertTrue( $categoria->show_in_rest );
		self::assertContains( 'labm_seleccion', $modalidad->object_type );
		self::assertContains( 'labm_actualidad', $categoria->object_type );
	}

	public function test_metadata_is_registered_and_sanitized(): void {
		$fields = array(
			'labm_actualidad' => 'labm_fecha_evento',
			'labm_seleccion'  => 'labm_modalidad_detalle',
			'labm_club'       => 'labm_ciudad',
			'labm_integrante' => 'labm_cargo',
			'labm_horario'    => 'labm_inicio',
		);
		foreach ( $fields as $post_type => $meta_key ) {
			self::assertTrue( registered_meta_key_exists( 'post', $meta_key, $post_type ), $meta_key );
		}
		self::assertSame( 'Medellin', sanitize_meta( 'labm_ciudad', ' <b>Medellin</b> ', 'post', 'labm_club' ) );
		self::assertTrue( labm_core_validate_iso_date( '2026-02-28' ) );
		self::assertFalse( labm_core_validate_iso_date( '2026-02-30' ) );
		self::assertFalse( labm_core_validate_iso_date( 'no-es-fecha' ) );
	}

	public function test_editor_and_administrator_have_domain_capabilities(): void {
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			self::assertInstanceOf( WP_Role::class, $role );
			self::assertTrue( $role->has_cap( 'edit_labm_actualidades' ) );
			self::assertTrue( $role->has_cap( 'publish_labm_actualidades' ) );
		}
		$subscriber = get_role( 'subscriber' );
		self::assertInstanceOf( WP_Role::class, $subscriber );
		self::assertFalse( $subscriber->has_cap( 'edit_labm_actualidades' ) );
	}

	public function test_drafts_are_excluded_from_public_queries(): void {
		$post = get_page_by_path( 'prueba-dominio-borrador', OBJECT, 'labm_actualidad' );
		$data = array(
			'ID'          => $post ? $post->ID : 0,
			'post_type'   => 'labm_actualidad',
			'post_status' => 'draft',
			'post_name'   => 'prueba-dominio-borrador',
			'post_title'  => '[DEMO LABM — FICTICIO] Prueba borrador',
		);
		$post_id = wp_insert_post( wp_slash( $data ) );
		$query   = new WP_Query(
			array(
				'post_type'   => 'labm_actualidad',
				'post_status' => 'publish',
				'post__in'    => array( $post_id ),
			)
		);
		self::assertSame( 0, $query->post_count );
	}

	public function test_visible_strings_are_translation_ready_in_spanish(): void {
		self::assertNotFalse( has_action( 'init', 'labm_core_load_textdomain' ) );
		self::assertSame( 'Actualidad', get_post_type_object( 'labm_actualidad' )->labels->name );
		self::assertSame( 'Modalidades', get_taxonomy( 'labm_modalidad' )->labels->name );
	}
}
