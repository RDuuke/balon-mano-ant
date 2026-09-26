# Tareas: Administración SMTP híbrida para Contacto

## Fase 1: Fundamentos
- [x] 1.1 RED: crear `tests/php/SmtpSettingsTest.php` para el esquema cerrado, saneamiento y la ausencia de contraseña en `labm_smtp_settings`.
- [x] 1.2 GREEN: crear `wp-content/plugins/labm-core/includes/class-labm-smtp-settings.php` con opción privada no REST y lectura exclusiva de `LABM_SMTP_PASSWORD`.
- [x] 1.3 REFACTOR: cargar el módulo en `wp-content/plugins/labm-core/labm-core.php` y mantener la convención de capacidad de LABM.

## Fase 2: Transporte y Contacto
- [x] 2.1 RED: probar en `tests/php/SmtpSettingsTest.php` configuración completa, ausencia segura de la constante y filtros de remitente.
- [x] 2.2 GREEN: configurar `phpmailer_init`, `wp_mail_from` y `wp_mail_from_name` solo con opción válida y contraseña externa disponible.
- [x] 2.3 RED: ampliar `tests/php/DocumentContactTest.php` para From configurado, `Reply-To`, fallo SMTP y reintento sin duplicados.
- [x] 2.4 GREEN: integrar el transporte sin alterar `labm_core_process_contact()`, sus destinatarios institucionales ni su contrato público.

## Fase 3: Panel y prueba controlada
- [x] 3.1 RED: cubrir capacidad, nonce, destino fijo y avisos sin secretos de la prueba administrativa en `tests/php/SmtpSettingsTest.php`.
- [x] 3.2 GREEN: implementar la pantalla secundaria LABM, el formulario sin contraseña y la acción `admin_post` con PRG para correo de prueba.
- [x] 3.3 REFACTOR: revisar escape de salida, saneamiento de entrada y que la contraseña no se lea desde petición, opción, REST ni log.

## Fase 4: Documentación y validación
- [x] 4.1 Documentar en `docs/development.md` los valores del panel, `LABM_SMTP_PASSWORD` en el `wp-config.php` existente, prueba, rotación y reversión sin datos reales.
- [x] 4.2 Ejecutar `SmtpSettingsTest.php` y `DocumentContactTest.php`; comprobar escenarios sin red ni contraseña real.
- [x] 4.3 Ejecutar PHPCS y PHPStan focales para los PHP modificados y corregir incumplimientos aplicables.
- [x] 4.4 Verificar que los archivos modificados usan LF y que Git, opciones, HTML y logs no contienen contraseñas ni contenido de formularios.

- [x] 3.4 Mover los destinatarios privados del footer al panel SMTP, migrar los valores existentes sin perdida y cubrir la fuente canonica.
- [x] 3.5 RED/GREEN: cubrir e implementar el estado accesible de envío en Contacto; anunciar el procesamiento dentro del botón, deshabilitarlo solo tras validación nativa correcta y conservar la etiqueta original después de la respuesta PRG, restauración desde caché o validación/error.
- [x] 3.6 RED/GREEN: distinguir en la respuesta PRG el fallo seguro de entrega del aviso y enlaces de validación de campos, sin exponer detalles SMTP.
- [x] 3.7 RED/GREEN: crear una plantilla HTML institucional compatible, con logo URL absoluto y alternativa textual, escapar todos los datos de Contacto, declarar el encabezado HTML y preservar Reply-To.
- [x] 3.8 GREEN: añadir una vista previa administrativa estática y aislada de la plantilla en LABM > SMTP, sin secretos, datos reales ni envío.
- [x] 3.9 RED/GREEN: reemplazar la URL local del logo en correos reales por un adjunto JPEG CID, acotar el hook a Contacto, mantener una alternativa textual y conservar una vista previa navegable.
- [x] 4.5 Ejecutar pruebas focales de plantilla, sintaxis, estándares aplicables y verificación LF para los archivos añadidos o modificados.

## Fase 5: Entrega controlada
- [x] 5.1 En un entorno autorizado, definir una contraseña de aplicación nueva solo en `wp-config.php`, guardar valores no secretos y ejecutar una única prueba al destinatario configurado.
- [x] 5.2 Inspeccionar la recepción y el encabezado `Reply-To`; registrar solo el resultado no sensible. Por decisión explícita, conservar la contraseña local para pruebas posteriores; antes del despliegue, Producción DEBE configurar una contraseña de aplicación nueva e independiente en su propio `wp-config.php`, sin trasladar ni revelar la clave local.

## Fase 6: Corrección de la verificación de Documentos
- [x] 6.1 RED/GREEN: actualizar `tests/php/PublicExperienceTest.php`, `tests/php/DocumentContactTest.php` y `tests/php/VerifyCorrectivesTest.php` al contrato filtrable y al mensaje vacío vigentes; ejecutar los cuatro casos y la suite PHP completa.
