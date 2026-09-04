## Tarea 1.1 — Soportes editoriales de aliados

- **RED:** test `tests/php/HomeContentTest.php::test_allies_only_expose_logo_catalog_editorial_supports` falla con: `Failed asserting that true is false` porque `editor` seguía habilitado.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; test pasa.
- **REFACTOR:** los soportes se calculan por tipo y se conserva sin cambios el contrato de slides.

## Tarea 1.2 — Flujos REST y administrativos

- **RED:** tests `tests/php/HomeEditorialFlowsTest.php::test_rest_valida_publicacion_de_aliados` y `test_admin_impide_publicacion_incompleta_y_muestra_aviso` fallan con: `500 is identical to 400` y `draft is identical to publish`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`; publicación válida, rechazos, aviso, capacidades, nonce, borrador y conservación legada pasan.
- **TRIANGULATE:** tests `test_admin_validation_requires_nonce_and_capability` y `test_actualizacion_de_aliado_conserva_datos_legados` cubren autorización negativa y persistencia de URL, cuerpo y extracto.

## Tarea 1.3 — Validación especializada sin recursión

- **RED:** la ejecución conjunta conservó un fallo en `HomeEditorialFlowsTest::test_editor_realiza_flujo_editorial_completo_por_rest`: el contrato anterior intentaba editar URL en aliados pese a excluir `custom-fields`.
- **GREEN:** implementación y pruebas alineadas en `wp-content/plugins/labm-core/includes/class-labm-home-content.php`, `tests/php/HomeContentTest.php` y `tests/php/HomeEditorialFlowsTest.php`; 15 tests y 96 aserciones pasan.
- **REFACTOR:** la validación administrativa usa `wp_insert_post_data`, solo transforma los datos retornados y no invoca `wp_update_post`, evitando recursión.
