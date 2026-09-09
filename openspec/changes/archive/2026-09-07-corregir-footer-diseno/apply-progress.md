# Progreso de APPLY

## Tarea 1.1 — Pie global según el diseño aprobado

- **RED:** test `tests/php/FrontendTokensTest.php::test_footer_matches_approved_information_architecture` falla con: `falta labm-footer__main en el pie provisional`.
- **GREEN:** implementación en `wp-content/themes/labm/parts/footer.html` y `wp-content/themes/labm/style.css`; test pasa.
- **REFACTOR:** estructura semántica, cuadrícula responsive y estilos encapsulados en clases `labm-footer__*`.

## Tarea 1.2 — Reutilizar el ancho wide del tema

- **RED:** test `tests/php/FrontendTokensTest.php::test_footer_matches_approved_information_architecture` falla con: `los contenedores main y legal no incluyen alignwide`.
- **GREEN:** implementación en `wp-content/themes/labm/parts/footer.html`; test pasa con 13 aserciones.
- **REFACTOR:** ambos contenedores reutilizan `wideSize: 1200px` mediante la alineación nativa de Gutenberg, sin introducir otro ancho.

## Tarea 1.3 — Publicar canales de contacto autorizados

- **RED:** test `tests/php/FrontendTokensTest.php::test_footer_matches_approved_information_architecture` falla con: `falta el enlace mailto y permanecen placeholders`.
- **GREEN:** implementación en `wp-content/themes/labm/parts/footer.html`; test pasa con 17 aserciones.
- **TRIANGULATE:** el mismo contrato cubre ausencia de teléfono inventado, placeholders y `target="_blank"`.
- **REFACTOR:** correo, Facebook e Instagram usan nombres de enlace claros y conservan la navegación en la misma pestaña.

## Tarea 1.4 — Actualizar el correo institucional

- **RED:** test `tests/php/FrontendTokensTest.php::test_footer_matches_approved_information_architecture` falla con: `falta info@balonmanoantioquia.com`.
- **GREEN:** implementación en `wp-content/themes/labm/parts/footer.html`; test pasa con 18 aserciones.
- **TRIANGULATE:** el contrato confirma que `antioquiabalonmano@gmail.com` ya no aparece.
- **REFACTOR:** se modificaron exclusivamente el texto visible y el destino `mailto:` del correo.

## Tarea 5.1 — Contratos funcionales del footer

- **RED:** test `tests/php/FooterSettingsTest.php` falla con: `cuatro funciones de configuración inexistentes`.
- **GREEN:** implementación en `class-labm-footer-settings.php`; cuatro pruebas pasan.
- **TRIANGULATE:** cubre defaults, saneado, campos vacíos, permisos y salida manipulada.

## Tarea 5.2 — Template part sin literales

- **RED:** test `FrontendTokensTest::test_footer_matches_approved_information_architecture` falla con: `falta [labm_footer]`.
- **GREEN:** implementación en `parts/footer.html`; el contrato pasa.

## Tarea 6.1 — Opción, esquema y saneado

- **RED:** test `FooterSettingsTest::test_footer_settings_are_sanitized_by_field_type` falla con: `función inexistente`.
- **GREEN:** implementación en `class-labm-footer-settings.php`; textos, textarea, correo y URLs se sanean por tipo.
- **REFACTOR:** el esquema cerrado cubre 27 campos y los defaults equivalen al footer aprobado.

## Tarea 6.2 — Panel y permisos

- **RED:** test `FooterSettingsTest::test_footer_admin_contract_uses_theme_management_capability` falla con: `options.php exige manage_options`.
- **GREEN:** panel LABM y filtro de capacidad implementados con `edit_theme_options`; test pasa.
- **REFACTOR:** Settings API aporta nonce y persistencia de una única opción.

## Tarea 7.1 — Render seguro y visualmente estable

- **RED:** test `FooterSettingsTest::test_footer_render_uses_saved_values_and_omits_empty_items` falla con: `falta is-layout-constrained`.
- **GREEN:** render escapado conserva main/legal alignwide, tres columnas y clases visuales; test pasa.
- **TRIANGULATE:** valores vacíos omiten nodos y una entrada manipulada no produce script.

## Tarea 7.2 — Integración única

- **RED:** test `FrontendTokensTest::test_footer_matches_approved_information_architecture` falla con: `persisten literales editoriales`.
- **GREEN:** carga desde `labm-core.php`, shortcode bloqueado y fallback vacío desde el tema; test pasa.
- **REFACTOR:** el panel es fuente de verdad y el template part solo invoca el renderizador.

## Tarea 8.1 — Calidad estática y funcional

- **RED:** `composer lint` falla con: `51 errores y 43 advertencias WPCS`.
- **GREEN:** PHPCBF y ajustes manuales dejan PHPCS limpio; PHPStan sin errores; 7 pruebas y 98 aserciones pasan.
- **REFACTOR:** documentación de parámetros y formato WPCS completados.

## Tarea 8.2 — Verificación de entrega

- **RED:** inspección HTTP inicial no formaba parte del contrato dinámico.
- **GREEN:** portada responde 200, contiene footer dinámico y no expone el shortcode literal.
- **REFACTOR:** `git diff --check` y LF se verifican al cierre; navegador visual no disponible.
