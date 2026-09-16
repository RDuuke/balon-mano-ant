# Tareas: Sección pública de Contacto

## Fase 1: Base y contratos
- [ ] 1.1 En `tests/php/DocumentContactTest.php`, escribir pruebas RED para ajustes públicos, destinatario institucional, consentimiento, honeypot, nonce, idempotencia y fallo de correo.
- [ ] 1.2 En `tests/php/DocumentContactTest.php`, añadir casos de entorno `production`, no productivo y ambiguo: las copias proceden solo de `LABM_CONTACT_TEST_RECIPIENTS`, no se registran ni se exponen.
- [ ] 1.3 En `wp-content/plugins/labm-core/includes/class-labm-footer-settings.php`, identificar los valores efectivos de correo y redes que reutilizará el contrato público, sin alterar el footer.

## Fase 2: Procesamiento seguro
- [ ] 2.1 En `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`, implementar `labm_core_get_contact_settings()` con correo, teléfono, dirección, redes y URL de búsqueda de Maps saneados.
- [ ] 2.2 En el mismo archivo, completar `labm_core_contact_recipients()` para usar siempre el correo institucional y habilitar hasta dos copias solo fuera de producción desde la variable de entorno válida.
- [ ] 2.3 En el mismo archivo, completar `labm_core_process_contact()` con validación y saneamiento de campos, consentimiento, nonce, honeypot, reserva idempotente y liberación ante fallo.
- [ ] 2.4 En el mismo archivo, registrar `admin_post_labm_contact_send` y `admin_post_nopriv_labm_contact_send`: solo POST, PRG a `/contacto/#formulario` y estado efímero sin datos personales.
- [ ] 2.5 Ejecutar `tests/php/DocumentContactTest.php` hasta GREEN; refactorizar sin cambiar sus contratos.

## Fase 3: Página e integración
- [ ] 3.1 Crear `wp-content/themes/labm/templates/page-contacto.html` con la plantilla de `/contacto/` que reutiliza los template parts globales existentes sin modificarlos.
- [ ] 3.2 Crear `wp-content/themes/labm/patterns/contacto.php` con encabezado, datos aprobados, redes disponibles, enlace seguro de Maps y formulario semántico que consume solo ajustes públicos.
- [ ] 3.3 En `wp-content/themes/labm/functions.php`, registrar el patrón/renderizado y consumir el estado PRG con textos, asociaciones ARIA y valores seguros.
- [ ] 3.4 En `wp-content/themes/labm/style.css`, añadir composición responsive, contraste, foco visible, mensajes no dependientes del color y honeypot no enfocable.

## Fase 4: Pruebas de interfaz y seguridad
- [ ] 4.1 Crear `tests/e2e/contact.spec.ts` con RED para ruta, datos, enlaces, consentimiento, errores, confirmación PRG y ausencia de destinatarios de prueba en HTML.
- [ ] 4.2 Completar `tests/e2e/contact.spec.ts` para teclado, Axe WCAG 2.2 AA y anchos 320, 768, 1024, 1200 y 1440 px sin desborde ni solapamiento.
- [ ] 4.3 Ejecutar pruebas PHP y Playwright focales; ejecutar PHPCS/PHPStan aplicables e informar la dependencia de SMTP si bloquea una entrega real.

## Fase 6: Correcciones tras VERIFY fallido
- [x] 6.1 Corregir el enlace navegable de privacidad y rechazar el token idempotente ausente sin intentar la entrega.
- [ ] 6.2 Ejecutar las pruebas focales de las correcciones y registrar evidencia RED/GREEN antes de volver a VERIFY.

## Fase 5: Cierre
- [ ] 5.1 Documentar en la configuración no versionada la variable de copias para entornos no productivos y confirmar que producción no la define.
- [ ] 5.2 Verificar LF por bytes en cada archivo modificado dentro del alcance y corregir únicamente los que introduzcan CRLF.
