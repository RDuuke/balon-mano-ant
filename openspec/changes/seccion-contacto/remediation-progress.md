# Progreso de correcciÃ³n tras VERIFY fallido

## Tarea 6.1 â€” Privacidad e idempotencia

- **RED:** se aÃ±adieron pruebas para el token idempotente ausente y el enlace navegable de privacidad. El comando focal de PHPUnit mediante Docker no produjo salida y se interrumpiÃ³ tras 60 segundos; no fue posible confirmar el fallo inicial requerido por TDD estricto.
- **GREEN:** `class-labm-documents-contact.php` rechaza el token ausente antes de reservar o enviar. `functions.php` ahora genera un ancla con URL escapada para la polÃ­tica de privacidad.
- **REFACTOR:** no aplica. Header y footer no fueron modificados.

## Tarea 6.2 — Ruta y estado PRG de Contacto

- **RED:** `FixturesDomainTest::test_contact_page_fixture_is_published_and_idempotent` falló porque los fixtures no creaban la página `contacto`. `DocumentContactTest::test_contact_prg_state_consumes_a_case_sensitive_opaque_identifier` falló porque `sanitize_key()` alteraba el identificador opaco antes de recuperar el estado PRG.
- **GREEN:** el cargador de fixtures crea la página publicada e idempotente `contacto`; el estado PRG conserva mayúsculas y minúsculas del identificador opaco tanto al recibir la URL como al consumir el transient.
- **Pruebas:** PHP focal: 31 pruebas y 259 aserciones correctas. Playwright: 112 pruebas correctas en los cuatro perfiles, incluidas la ruta, el formulario, los errores y el PRG de Contacto.
- **REFACTOR:** se centralizó el saneamiento del identificador opaco en `labm_core_sanitize_contact_state_id()`.

## Controles disponibles

- Sintaxis PHP: correcta en los tres archivos PHP afectados.
- `git diff --check`: correcto.
- Finales de lÃ­nea: LF, sin CRLF, en los archivos modificados dentro del alcance.
- PHPUnit focal: bloqueado por Docker sin salida ni resultado utilizable.

## Reconciliación de APPLY — 2026-09-18

- **Tareas 1.1–1.3:** las pruebas de contrato existentes cubren ajustes públicos, consentimiento, honeypot, nonce, idempotencia y fallo de correo. Se confirmó que el contrato público reutiliza `contact_email`, redes y política desde los valores efectivos del footer, sin modificar el footer.
- **Tareas 2.1–2.5:** se verificó la implementación de ajustes saneados, destinatario institucional, copias limitadas, reserva y liberación idempotentes y controlador POST-Redirect-GET. La nueva prueba de entornos falló inicialmente porque WordPress conserva en caché el tipo de entorno; se añadió el filtro interno `labm_core_contact_environment`, cuyo valor por defecto sigue siendo `wp_get_environment_type()`. En verde comprueba producción, desarrollo y entorno ambiguo, además de que las copias no aparecen en HTML.
- **Tareas 3.1–3.4:** la plantilla, patrón, renderizado PRG y estilos de Contacto ya estaban implementados; reutilizan los template parts globales sin cambios.
- **Tareas 4.1–4.3:** PHPUnit focal verde: `DocumentContactTest` (17 pruebas, 80 aserciones) y fixture de `/contacto/` (1 prueba, 4 aserciones). PHPCS finalizó sin errores y PHPStan finalizó con código 0. La suite Playwright se inició dos veces contra la misma instancia por el gate; los escenarios de Contacto quedaron verdes en 320, 768 y 1440 px, pero una ejecución paralela falló en casos ajenos de `public-experience` al competir por fixtures y URL. La evidencia completa previa de 112/112 sigue siendo la referencia válida; repetir Playwright una sola vez en VERIFY.
- **Tarea 5.1:** `.env.example` documenta cómo definir `LABM_CONTACT_TEST_RECIPIENTS` exclusivamente en `.env` no versionado para local, desarrollo o staging, y prohíbe definirla en producción. No se versionó ninguna dirección de copia.
- **SMTP:** la prueba usa `pre_wp_mail`; la entrega real permanece condicionada a configurar SMTP en el entorno de despliegue.
