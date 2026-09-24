## Tarea 3.5 — Estado accesible de envío

- **RED:** la prueba `tests/e2e/contact.spec.ts::Contacto anuncia el envío y evita solicitudes repetidas mientras procesa` fue descubierta y falló antes de incorporar el marcado y el script.
- **GREEN:** se añadieron el marcado accesible y `wp-content/themes/labm/assets/contact.js`; la respuesta HTTP local confirma los marcadores del formulario, estado y script. La ejecución Playwright verde quedó bloqueada en el contenedor y requiere repetición.
- **TRIANGULATE:** `tests/e2e/contact.spec.ts::Contacto asocia errores, conserva teclado y no revela la configuración interna` ahora verifica que el formulario PRG de error vuelve con `aria-busy="false"` y el botón habilitado.
- **REFACTOR:** el script se carga únicamente en la página Contacto y no modifica el POST ni la respuesta del backend.

## Tarea 3.6 — Aviso seguro de fallo de entrega

- **RED:** test `tests/php/DocumentContactTest.php::test_contact_renders_a_safe_delivery_error_separate_from_field_validation` falló porque el estado `delivery` mostraba «Revisa los campos marcados e inténtalo de nuevo.»
- **GREEN:** `wp-content/themes/labm/functions.php` reconoce `delivery` en el estado PRG y renderiza un único aviso seguro, sin lista de campos ni detalles SMTP; el test pasa.
- **TRIANGULATE:** el test verifica simultáneamente el mensaje de entrega, la ausencia del aviso de validación, de la sigla SMTP y de listas de campos.
- **REFACTOR:** se reutiliza el estado opaco y de un solo uso existente; no se altera el POST ni el contrato del transporte.

## Tarea 3.9 â€” Logo incrustado para correo

- **RED:** `tests/php/DocumentContactTest.php::test_contact_email_embeds_its_logo_without_affecting_other_messages` falló porque `labm_core_embed_contact_logo()` no existía; además, la plantilla usaba una URL absoluta que Gmail no podía alcanzar desde el entorno local.
- **GREEN:** `class-labm-documents-contact.php` referencia `cid:labm-contact-logo` en los correos reales y el hook `phpmailer_init` adjunta el JPEG local con PHPMailer; las pruebas de plantilla, CID y vista previa pasan.
- **TRIANGULATE:** se comprueba que la imagen se adjunta una sola vez, que un correo ajeno no recibe adjuntos y que la vista previa administrativa conserva la URL navegable del logo.
- **REFACTOR:** la detección se limita al CID estable de Contacto; si el archivo no está disponible o el adjunto falla, permanece la marca textual y no se registra ni expone información sensible.
