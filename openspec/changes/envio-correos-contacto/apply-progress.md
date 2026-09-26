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

## Tareas 1.1–4.4 — Reanudación y validación local

- **RED:** la evidencia de ejecución roja de la implementación original no está disponible en el historial; no se reconstruyó ni se inventó.
- **GREEN:** `SmtpSettingsTest.php` y `DocumentContactTest.php` se ejecutaron con WordPress sin red ni contraseña real; PHPStan focal terminó sin errores tras corregir el PHPDoc de `labm_core_render_contact_email()`.
- **REFACTOR:** PHPCS pasa para el módulo SMTP nuevo y su carga. El análisis ampliado de Contacto conserva incidencias históricas fuera de los fragmentos SMTP; no se normalizó ese archivo ajeno masivamente.
- **VALIDACIÓN:** los archivos textuales del commit de Contacto se revisaron sin CRLF; la búsqueda de `LABM_SMTP_PASSWORD` solo encontró la constante, la documentación con marcador y las comprobaciones esperadas.

## Tarea 5.1 — Prueba SMTP autorizada

- **CONFIGURACIÓN:** se guardaron exclusivamente los valores SMTP no secretos autorizados en la opción privada; la contraseña continuó fuera de WordPress, en `wp-config.php`, y no se consultó ni se registró.
- **VALIDACIÓN:** WordPress confirmó que SMTP quedó configurado y que el destinatario autorizado estaba incluido. Se ejecutó un solo envío real de prueba; `wp_mail()` devolvió aceptación del transporte.
- **REPLY-TO:** la prueba focal de Contacto comprueba el encabezado `Reply-To` del flujo de formulario y finalizó correctamente. La confirmación final en los encabezados recibidos requiere inspección de la bandeja del destinatario.

## Tarea 5.2 — Evidencia de recepción y cierre operativo

- **RECEPCIÓN:** el destinatario autorizado confirmó la llegada del único correo de prueba.
- **REPLY-TO:** el destinatario confirmó que el encabezado corresponde al remitente configurado.
- **DECISIÓN OPERATIVA:** se conserva la contraseña de aplicación actual exclusivamente en el entorno local para pruebas posteriores. No se leyó, mostró, copió ni registró su valor.
- **DESPLIEGUE:** antes de promover Producción, el operador DEBE generar y configurar una contraseña de aplicación nueva e independiente en el `wp-config.php` de Producción. La clave local no se reutiliza ni se traslada entre entornos.

## Tarea 6.1 — Contrato filtrable del catálogo de Documentos

- **RED:** Las cuatro expectativas heredadas fallaban en PHPUnit al exigir catálogo sin filtros, texto vacío anterior y URL que descartaba filtros.
- **GREEN:** Se actualizaron los tres archivos de prueba; los cuatro casos focales pasan con 31 aserciones.
- **REFACTOR:** No aplica; no se cambió código de producción ni el contrato público.
