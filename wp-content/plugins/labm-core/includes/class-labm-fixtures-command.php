<?php
/**
 * Fixtures ficticios para desarrollo local.
 *
 * @package LABM_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Comando idempotente de fixtures. */
class LABM_Fixtures_Command {
	/** Marcador obligatorio para contenido administrado por fixtures. */
	const MARKER = '[DEMO LABM — FICTICIO]';

	/**
	 * Noticias ficticias para probar la composicion editorial de portada.
	 *
	 * @return array
	 */
	private static function home_news_fixtures() {
		$definitions = array(
			array( 'resultado', 'Un resultado que celebra todo el balonmano antioqueño', 'La comunidad acompaña una jornada ficticia de celebración deportiva.', '2026-08-27 16:00:00' ),
			array( 'convocatoria', 'Proceso deportivo abierto', 'Convocatoria demostrativa para nuevos procesos deportivos.', '2026-08-27 15:00:00' ),
			array( 'clubes', 'La Liga fortalece sus clubes', 'Encuentro institucional ficticio para probar contenido editorial.', '2026-08-27 14:00:00' ),
			array( 'calendario', 'Próxima fecha del calendario', 'Programación inventada para validar el acceso a la actualidad.', '2026-08-27 13:00:00' ),
			array( 'formacion', 'Jornada técnica para entrenadores', 'Actividad pedagógica ficticia para la comunidad deportiva.', '2026-08-27 12:00:00' ),
			array( 'seleccion', 'Encuentro de selecciones departamentales', 'Noticia de demostración sin resultados ni personas reales.', '2026-08-27 11:00:00' ),
		);
		$images      = array(
			'assets/images/hero-balonmano-antioquia-v1.png',
			'assets/images/hero-balonmano-seleccion-v1.png',
		);

		return array_map(
			static function ( $definition, $index ) use ( $images ) {
				$image = $images[ $index % count( $images ) ];
				return array(
					'post_name'    => 'demo-labm-noticia-' . $definition[0],
					'post_title'   => self::MARKER . ' ' . $definition[1],
					'post_excerpt' => self::MARKER . ' ' . $definition[2],
					'post_content' => self::rich_news_content( $definition[2] ),
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'post_date'    => $definition[3],
					'meta'         => array( 'labm_demo_image' => $image ),
					'terms'        => array( 'labm_categoria' => array( 'Noticias demo' ) ),
					'featured_image'     => 'convocatoria' === $definition[0] ? '' : $image,
					'featured_image_alt' => 'Imagen ficticia para la noticia demo ' . $definition[0],
					'clear_featured_image' => 'convocatoria' === $definition[0],
					'gallery_images'      => 'resultado' === $definition[0]
						? array(
							array( 'path' => $images[0], 'alt' => 'Equipo ficticio durante una jornada de balonmano' ),
							array( 'path' => $images[1], 'alt' => 'Seleccion ficticia de balonmano en actividad' ),
						)
						: array(),
				);
			},
			$definitions,
			array_keys( $definitions )
		);
	}

	/** Construye contenido Gutenberg enriquecido para las noticias ficticias de portada. */
	private static function rich_news_content( $summary ) {
		return "<!-- wp:heading {\"level\":2} -->\n<h2>Una experiencia editorial de demostracion</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>" . self::MARKER . ' ' . esc_html( $summary ) . "</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>Este contenido es ficticio y permite revisar la lectura completa de una noticia.</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Informacion editorial de ejemplo.</li><li>Contenido preparado para pruebas locales.</li></ul>\n<!-- /wp:list -->";
	}

	/** Anexa una galeria nativa de Gutenberg con adjuntos y alternativas disponibles. */
	private static function gallery_block_content( $content, $attachments ) {
		$images = array();
		foreach ( $attachments as $attachment_id ) {
			$url = wp_get_attachment_image_url( $attachment_id, 'large' );
			if ( ! $url ) {
				continue;
			}
			$images[] = '<!-- wp:image {"id":' . (int) $attachment_id . ',"sizeSlug":"large","linkDestination":"media"} -->' . "\n"
				. '<figure class="wp-block-image size-large"><a href="' . esc_url( $url ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) . '" class="wp-image-' . (int) $attachment_id . '"/></a></figure>' . "\n"
				. '<!-- /wp:image -->';
		}
		if ( empty( $images ) ) {
			return $content;
		}

		return $content . "\n\n<!-- wp:gallery {\"columns\":2,\"linkTo\":\"media\"} -->\n<figure class=\"wp-block-gallery has-nested-images columns-2 is-cropped\">\n" . implode( "\n", $images ) . "\n</figure>\n<!-- /wp:gallery -->";
	}

	/**
	 * Aliados ficticios compuestos exclusivamente por logos ordenados.
	 *
	 * @return array
	 */
	private static function home_allies_fixtures() {
		$definitions = array(
			array( 'arco-comun', 'Arco Comun' ),
			array( 'brote-activo', 'Brote Activo' ),
			array( 'cumbre-viva', 'Cumbre Viva' ),
			array( 'mosaico-unido', 'Mosaico Unido' ),
			array( 'rio-dinamico', 'Rio Dinamico' ),
			array( 'sol-abierto', 'Sol Abierto' ),
		);

		return array_map(
			static function ( $definition, $order ) {
				return array(
					'post_name'      => 'demo-labm-aliado-' . $definition[0],
					'post_title'     => self::MARKER . ' ' . $definition[1],
					'post_content'   => '',
					'post_excerpt'   => '',
					'post_type'      => 'labm_aliado',
					'post_status'    => 'publish',
					'menu_order'     => $order,
					'featured_image' => 'assets/images/aliados-demo/' . $definition[0] . '.png',
				);
			},
			$definitions,
			array_keys( $definitions )
		);
	}

	/**
	 * Artículo ficticio para el banner estático de Nosotros.
	 *
	 * @return array
	 */
	private static function about_banner_fixture() {
		return array(
			array(
				'post_name'      => 'banner-nosotros',
				'post_title'     => self::MARKER . ' Somos la Liga',
				'post_excerpt'   => 'Trabajamos por el desarrollo integral del balonmano antioqueño, articulando clubes, deportistas y comunidad.',
				'post_content'   => '<p>' . self::MARKER . ' Trabajamos por el desarrollo integral del balonmano antioqueño, articulando clubes, deportistas y comunidad.</p>',
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'featured_image' => 'assets/images/hero-balonmano-seleccion-v1.png',
			),
		);
	}

	/**
	 * Artículo ficticio para el encabezado editable de Documentos.
	 *
	 * @return array
	 */
	private static function documents_banner_fixture() {
		return array(
			array(
				'post_name'    => 'banner-documentos',
				'post_title'   => self::MARKER . ' Documentos',
				'post_excerpt' => 'Resoluciones, circulares y archivos públicos de la Liga.',
				'post_content' => '<p>' . self::MARKER . ' Resoluciones, circulares y archivos públicos de la Liga.</p>',
				'post_type'    => 'post',
				'post_status'  => 'publish',
			),
		);
	}

	/**
	 * Artículos ficticios independientes para Misión y Visión.
	 *
	 * @return array
	 */
	private static function about_purpose_fixtures() {
		return array(
			array(
				'post_name'    => 'mision-nosotros',
				'post_title'   => self::MARKER . ' Misión',
				'post_excerpt' => 'Promovemos el desarrollo integral del balonmano antioqueño mediante procesos deportivos, formativos y comunitarios.',
				'post_content' => '<p>' . self::MARKER . ' Promovemos el desarrollo integral del balonmano antioqueño mediante procesos deportivos, formativos y comunitarios.</p>',
				'post_type'    => 'post',
				'post_status'  => 'publish',
			),
			array(
				'post_name'    => 'vision-nosotros',
				'post_title'   => self::MARKER . ' Visión',
				'post_excerpt' => 'Ser un referente nacional por la solidez de nuestros clubes, la formación deportiva y el impacto positivo en la comunidad.',
				'post_content' => '<p>' . self::MARKER . ' Ser un referente nacional por la solidez de nuestros clubes, la formación deportiva y el impacto positivo en la comunidad.</p>',
				'post_type'    => 'post',
				'post_status'  => 'publish',
			),
		);
	}

	/**
	 * Integrantes ficticios para la sección editorial de Nosotros.
	 *
	 * @return array
	 */
	private static function about_team_fixtures() {
		$definitions = array(
			array( 'andres-montoya', 'Andrés Montoya', 'Director técnico', 'Comité', 'entrenador-principal.png' ),
			array( 'daniel-restrepo', 'Daniel Restrepo', 'Preparador físico', 'Entrenadores', 'preparador-fisico.png' ),
			array( 'valentina-rios', 'Valentina Ríos', 'Entrenadora juvenil', 'Entrenadores', 'entrenadora-juvenil.png' ),
			array( 'mateo-giraldo', 'Mateo Giraldo', 'Representante comunitario', 'Representantes', 'representante-comunitario.png' ),
		);

		return array_map(
			static function ( $definition, $order ) {
				return array(
					'post_name'      => 'demo-labm-integrante-' . $definition[0],
					'post_title'     => self::MARKER . ' ' . $definition[1],
					'post_content'   => '<p>' . self::MARKER . ' Perfil ficticio para demostración editorial.</p>',
					'post_type'      => 'labm_integrante',
					'post_status'    => 'publish',
					'menu_order'     => $order,
					'meta'           => array( 'labm_cargo' => $definition[2] ),
					'terms'          => array( 'labm_grupo_integrante' => array( $definition[3] ) ),
					'featured_image' => 'assets/images/quienes-demo/' . $definition[4],
				);
			},
			$definitions,
			array_keys( $definitions )
		);
	}

	/**
	 * Importa o reutiliza un logo demo como adjunto de WordPress.
	 *
	 * @param string $relative_path Ruta relativa dentro del tema.
	 * @return int
	 */
	private static function ensure_demo_image_attachment( $relative_path, $alt = '', $prefix = 'demo-labm-image-' ) {
		$slug     = sanitize_title( pathinfo( $relative_path, PATHINFO_FILENAME ) );
		$existing = get_page_by_path( $prefix . $slug, OBJECT, 'attachment' );
		if ( $existing && 'image/png' === get_post_mime_type( $existing ) && is_file( get_attached_file( $existing->ID ) ) ) {
			if ( '' !== $alt ) {
				update_post_meta( $existing->ID, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
			}
			return (int) $existing->ID;
		}
		if ( $existing ) {
			wp_delete_attachment( $existing->ID, true );
		}

		$source = get_theme_file_path( $relative_path );
		if ( ! is_file( $source ) ) {
			return 0;
		}
		$upload = wp_upload_bits( basename( $source ), null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- activo local controlado.
		if ( ! empty( $upload['error'] ) ) {
			WP_CLI::error( sprintf( 'No se pudo importar el logo demo %1$s: %2$s', basename( $source ), $upload['error'] ) );
		}
		$attachment_id = wp_insert_attachment(
			array(
				'post_name'      => $prefix . $slug,
				'post_title'     => self::MARKER . ' Imagen ' . $slug,
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/png',
			),
			$upload['file'],
			0,
			true
		);
		if ( is_wp_error( $attachment_id ) ) {
			WP_CLI::error( sprintf( 'No se pudo registrar el logo demo %1$s: %2$s', basename( $source ), $attachment_id->get_error_message() ) );
		}
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		if ( '' !== $alt ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
		}
		return (int) $attachment_id;
	}

	/** Importa o reutiliza un logo demo como adjunto de WordPress. */
	private static function ensure_demo_attachment( $relative_path ) {
		return self::ensure_demo_image_attachment( $relative_path, '', 'demo-labm-logo-' );
	}

	/**
	 * Mapa revisable de documentos legales iniciales. Los títulos provienen de sus nombres y son editables en WordPress.
	 *
	 * @return array<string, string>
	 */
	public static function legal_document_definitions() {
		return array(
			'1. INFORME DE GESTION 2025.pdf'      => 'Informe de gestión 2025',
			'2. ESTADOS FINANCIEROS 2025.pdf'     => 'Estados financieros 2025',
			'3. Reconocimiento INDEPORTES.pdf'    => 'Reconocimiento INDEPORTES',
			'4. Certexistencia2026.pdf'           => 'Certificado de existencia 2026',
			'5. Estatutos integrados finales.pdf' => 'Estatutos integrados finales',
			'6. Actas ExtraOrdinaria2026.pdf'     => 'Actas extraordinarias 2026',
			'7. DictamenRevFiscal.pdf'            => 'Dictamen de revisoría fiscal',
			'8. Cer Antecedentes.pdf'             => 'Certificado de antecedentes',
			'9. Cer Requisitos.pdf'               => 'Certificado de requisitos',
			'10. Cer Cargos.pdf'                  => 'Certificado de cargos',
			'11. DeclaracionRenta2025.pdf'        => 'Declaración de renta 2025',
		);
	}

	/**
	 * Envía a papelera los documentos de esta importación que ya no tienen archivo fuente.
	 *
	 * No elimina adjuntos ni toca documentos creados manualmente.
	 *
	 * @param string[] $current_keys Claves de origen que siguen presentes y son válidas.
	 * @param string   $source_prefix Prefijo exclusivo del importador a reconciliar.
	 * @return int[] IDs enviados a papelera.
	 */
	public static function reconcile_legal_documents( $current_keys, $source_prefix = 'legal-document:' ) {
		$documents = get_posts(
			array(
				'post_type'      => 'labm_documento',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => 'labm_document_source_key',
						'value'   => $source_prefix,
						'compare' => 'LIKE',
					),
				),
			)
		);
		$trashed   = array();
		foreach ( $documents as $document_id ) {
			$key = (string) get_post_meta( $document_id, 'labm_document_source_key', true );
			if ( in_array( $key, $current_keys, true ) || 'trash' === get_post_status( $document_id ) ) {
				continue;
			}
			if ( wp_trash_post( $document_id ) ) {
				$trashed[] = (int) $document_id;
			}
		}
		return $trashed;
	}

	/**
	 * Importa los documentos legales desde una ruta explícita sin duplicar contenido propio.
	 *
	 * ## EXAMPLES
	 *
	 *     wp labm fixtures legal-documents --source=/app/docs/legal-documents
	 *
	 * @param array $args Argumentos posicionales.
	 * @param array $assoc_args Argumentos nombrados.
	 */
	public function legal_documents( $args, $assoc_args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$source = isset( $assoc_args['source'] ) ? (string) $assoc_args['source'] : getcwd() . '/docs/legal-documents';
		if ( ! is_dir( $source ) ) {
			WP_CLI::error( 'La ruta de documentos no existe. Indica --source=/ruta/a/legal-documents.' );
		}
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$imported     = 0;
		$current_keys = array();
		foreach ( self::legal_document_definitions() as $filename => $title ) {
			$path   = trailingslashit( $source ) . $filename;
			$valid  = labm_core_validate_pdf_file( $path, 30 * MB_IN_BYTES );
			$key    = 'legal-document:' . hash( 'sha256', $filename );
			$exists = get_posts(
				array(
					'post_type'      => 'labm_documento',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'meta_key'       => 'labm_document_source_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Clave estable del importador.
					'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Clave estable del importador.
				)
			);
			if ( is_wp_error( $valid ) ) {
				WP_CLI::warning( sprintf( 'Se omitió %1$s: %2$s', $filename, $valid->get_error_message() ) );
				continue;
			}
			$current_keys[] = $key;
			$attachments = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => 1,
					'meta_key'       => 'labm_document_source_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Clave estable del importador.
					'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Clave estable del importador.
				)
			);
			$attachment  = $attachments ? (int) $attachments[0]->ID : 0;
			if ( ! $attachment ) {
				$upload = wp_upload_bits( $filename, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Fuente local validada por operación administrativa.
				if ( ! empty( $upload['error'] ) ) {
					WP_CLI::warning( sprintf( 'No se pudo copiar %1$s: %2$s', $filename, $upload['error'] ) );
					continue;
				}
				$attachment = wp_insert_attachment(
					array(
						'post_title'     => $title,
						'post_status'    => 'inherit',
						'post_mime_type' => 'application/pdf',
					),
					$upload['file']
				);
				wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $upload['file'] ) );
				update_post_meta( $attachment, 'labm_document_source_key', $key );
			}
			$document_data = array(
				'post_type'   => 'labm_documento',
				'post_status' => 'publish',
				'post_title'  => $title,
			);
			$document_id   = $exists ? wp_update_post( array_merge( array( 'ID' => (int) $exists[0]->ID ), $document_data ), true ) : wp_insert_post( $document_data, true );
			if ( is_wp_error( $document_id ) ) {
				WP_CLI::warning( sprintf( 'No se pudo guardar %1$s: %2$s', $filename, $document_id->get_error_message() ) );
				continue;
			}
			update_post_meta( $document_id, 'labm_documento_pdf_id', $attachment );
			update_post_meta( $document_id, 'labm_document_source_key', $key );
			++$imported;
		}
		$trashed = self::reconcile_legal_documents( $current_keys );
		WP_CLI::success( sprintf( 'Documentos legales importados o actualizados: %1$d. Enviados a papelera por fuente ausente: %2$d.', $imported, count( $trashed ) ) );
	}

	/**
	 * Carga o actualiza exclusivamente paginas ficticias con slug estable.
	 *
	 * ## EXAMPLES
	 *
	 *     wp labm fixtures load
	 *
	 * @param array $args Argumentos posicionales.
	 * @param array $assoc_args Argumentos nombrados.
	 */
	public function load( $args, $assoc_args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$fixtures = array_merge(
			array(
				array(
					'post_name'    => 'demo-labm-slide-bienvenida',
					'post_title'   => self::MARKER . ' Slide editorial en borrador',
					'post_content' => '<p>' . self::MARKER . ' Slide editorial de demostracion.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'draft',
					'meta'         => array(
						'labm_cta_texto'   => 'Conocer la Liga',
						'labm_destino_url' => '/nosotros/',
					),
				),
				array(
					'post_name'    => 'demo-labm-slide-publicado-uno',
					'post_title'   => self::MARKER . ' Destacado publicado uno',
					'post_content' => '<p>' . self::MARKER . ' Primer slide publico para recorridos de navegador.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'publish',
					'meta'         => array(
						'labm_cta_texto'   => 'Conocer la Liga',
						'labm_destino_url' => '/nosotros/',
					),
				),
				array(
					'post_name'    => 'demo-labm-slide-publicado-dos',
					'post_title'   => self::MARKER . ' Destacado publicado dos',
					'post_content' => '<p>' . self::MARKER . ' Segundo slide publico para controles e indicadores.</p>',
					'post_type'    => 'labm_slide',
					'post_status'  => 'publish',
					'meta'         => array(
						'labm_cta_texto'   => 'Ver actualidad',
						'labm_destino_url' => '/actualidad/',
					),
				),
				array(
					'post_name'    => 'demo-labm-inicio',
					'post_title'   => self::MARKER . ' Inicio',
					'post_content' => '<p>' . self::MARKER . ' Contenido de ejemplo sin datos reales.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'demo-labm-nosotros',
					'post_title'   => self::MARKER . ' Nosotros',
					'post_content' => '<p>' . self::MARKER . ' Informacion institucional ficticia.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'nosotros',
					'post_title'   => self::MARKER . ' Nosotros',
					'post_content' => '<p>' . self::MARKER . ' Pagina institucional de demostracion.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'documentos',
					'post_title'   => self::MARKER . ' Documentos',
					'post_content' => '<p>' . self::MARKER . ' Página pública de documentos.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'contacto',
					'post_title'   => self::MARKER . ' Contacto',
					'post_content' => '<p>' . self::MARKER . ' Página pública de contacto.</p>',
					'post_type'    => 'page',
					'post_status'  => 'publish',
				),
				array(
					'post_name'    => 'demo-labm-actualidad-limite',
					'post_title'   => self::MARKER . ' Evento en fecha limite',
					'post_excerpt' => self::MARKER . ' Evento ficticio para revisar la fecha destacada y su lectura completa.',
					'post_content' => self::rich_news_content( 'Evento ficticio para revisar la fecha destacada y su lectura completa.' ),
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_fecha_evento' => '2026-01-01' ),
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
					'featured_image'     => 'assets/images/hero-balonmano-seleccion-v1.png',
					'featured_image_alt' => 'Imagen ficticia para el evento demo',
				),
				array(
					'post_name'    => 'demo-labm-actualidad-borrador',
					'post_title'   => self::MARKER . ' Actualidad incompleta',
					'post_content' => '<p>' . self::MARKER . ' Borrador deliberado.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'draft',
				),
				array(
					'post_name'    => 'demo-labm-actualidad-encuentro',
					'post_title'   => self::MARKER . ' Encuentro amistoso',
					'post_content' => '<p>' . self::MARKER . ' Cronica de muestra sin datos oficiales.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-formacion',
					'post_title'   => self::MARKER . ' Jornada de formacion',
					'post_content' => '<p>' . self::MARKER . ' Actividad pedagogica ficticia.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Formacion' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-convocatoria',
					'post_title'   => self::MARKER . ' Convocatoria abierta',
					'post_content' => '<p>' . self::MARKER . ' Convocatoria completamente ficticia.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Eventos' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-resultados',
					'post_title'   => self::MARKER . ' Resultados demostrativos',
					'post_content' => '<p>' . self::MARKER . ' Marcadores inventados para probar paginacion.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Noticias' ) ),
				),
				array(
					'post_name'    => 'demo-labm-actualidad-agenda',
					'post_title'   => self::MARKER . ' Agenda ficticia',
					'post_content' => '<p>' . self::MARKER . ' Fechas de ejemplo no vinculantes.</p>',
					'post_type'    => 'labm_actualidad',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_categoria' => array( 'Eventos' ) ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-privada',
					'post_title'   => self::MARKER . ' Seleccion privada',
					'post_content' => '<p>' . self::MARKER . ' Estado privado para pruebas.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'private',
					'terms'        => array( 'labm_modalidad' => array( 'Piso' ) ),
				),
				array(
					'post_name'    => 'demo-labm-club-frontera',
					'post_title'   => self::MARKER . ' Club Frontera',
					'post_content' => '<p>' . self::MARKER . ' Club completamente ficticio.</p>',
					'post_type'    => 'labm_club',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_ciudad' => 'Medellin ficticio' ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-piso',
					'post_title'   => self::MARKER . ' Seleccion Piso',
					'post_content' => '<p>' . self::MARKER . ' Plantel de piso ficticio.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_modalidad' => array( 'Piso' ) ),
				),
				array(
					'post_name'    => 'demo-labm-seleccion-playa',
					'post_title'   => self::MARKER . ' Seleccion Playa',
					'post_content' => '<p>' . self::MARKER . ' Plantel de playa ficticio.</p>',
					'post_type'    => 'labm_seleccion',
					'post_status'  => 'publish',
					'terms'        => array( 'labm_modalidad' => array( 'Playa' ) ),
				),
				array(
					'post_name'    => 'demo-labm-integrante-vacante',
					'post_title'   => self::MARKER . ' Vacante de ejemplo',
					'post_content' => '<p>' . self::MARKER . ' Sin persona real asociada.</p>',
					'post_type'    => 'labm_integrante',
					'post_status'  => 'draft',
					'meta'         => array( 'labm_cargo' => 'Cargo ficticio' ),
				),
				array(
					'post_name'    => 'demo-labm-horario-limite',
					'post_title'   => self::MARKER . ' Horario limite',
					'post_content' => '<p>' . self::MARKER . ' Horario nocturno de prueba.</p>',
					'post_type'    => 'labm_horario',
					'post_status'  => 'publish',
					'meta'         => array( 'labm_inicio' => '23:59' ),
				),
			),
			self::home_news_fixtures(),
			self::home_allies_fixtures(),
			self::about_banner_fixture(),
			self::documents_banner_fixture(),
			self::about_purpose_fixtures(),
			self::about_team_fixtures()
		);

		foreach ( $fixtures as $fixture ) {
			$existing = get_page_by_path( $fixture['post_name'], OBJECT, $fixture['post_type'] );
			if ( $existing ) {
				clean_post_cache( $existing->ID );
				$existing = get_post( $existing->ID );
			}
			$post = array_intersect_key(
				$fixture,
				array_flip( array( 'post_name', 'post_title', 'post_excerpt', 'post_content', 'post_type', 'post_status', 'post_date', 'menu_order' ) )
			);
			if ( $existing && 0 === strpos( $existing->post_title, self::MARKER ) ) {
				$post['ID'] = $existing->ID;
				$result     = wp_update_post( wp_slash( $post ), true );
			} elseif ( ! $existing ) {
				$result = wp_insert_post( wp_slash( $post ), true );
			} else {
				WP_CLI::warning( "Se preservo contenido ajeno con slug {$fixture['post_name']}." );
				continue;
			}

			if ( is_wp_error( $result ) ) {
				WP_CLI::error( $result->get_error_message() );
			}

			if ( ! empty( $fixture['meta'] ) ) {
				foreach ( $fixture['meta'] as $key => $value ) {
					if ( 'labm_demo_image' === $key && ! is_file( get_theme_file_path( $value ) ) ) {
						delete_post_meta( $result, $key );
						continue;
					}
					update_post_meta( $result, $key, $value );
				}
			}

			if ( ! empty( $fixture['featured_image'] ) ) {
				$attachment_id = self::ensure_demo_image_attachment( $fixture['featured_image'], $fixture['featured_image_alt'] ?? '' );
				if ( $attachment_id ) {
					update_post_meta( $result, '_thumbnail_id', $attachment_id );
				}
			}
			if ( ! empty( $fixture['clear_featured_image'] ) ) {
				delete_post_meta( $result, '_thumbnail_id' );
			}

			if ( ! empty( $fixture['gallery_images'] ) ) {
				$gallery_attachments = array();
				foreach ( $fixture['gallery_images'] as $gallery_image ) {
					$attachment_id = self::ensure_demo_image_attachment( $gallery_image['path'], $gallery_image['alt'] ?? '' );
					if ( $attachment_id ) {
						$gallery_attachments[] = $attachment_id;
					}
				}
				$gallery_content = self::gallery_block_content( $fixture['post_content'], $gallery_attachments );
				$gallery_result  = wp_update_post(
					wp_slash(
						array(
							'ID'           => $result,
							'post_content' => $gallery_content,
						)
					),
					true
				);
				if ( is_wp_error( $gallery_result ) ) {
					WP_CLI::error( $gallery_result->get_error_message() );
				}
			}

			if ( ! empty( $fixture['terms'] ) ) {
				foreach ( $fixture['terms'] as $taxonomy => $terms ) {
					$term_ids = array();
					foreach ( $terms as $term ) {
						$found = term_exists( $term, $taxonomy );
						if ( ! $found ) {
							$found = wp_insert_term( $term, $taxonomy );
						}
						if ( ! is_wp_error( $found ) ) {
							$term_ids[] = (int) $found['term_id'];
						}
					}
					wp_set_post_terms( $result, $term_ids, $taxonomy, false );
				}
			}
		}

		WP_CLI::success( self::MARKER . ' Fixtures cargados de forma idempotente.' );
	}
}
