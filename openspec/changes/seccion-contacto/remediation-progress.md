# Progreso de correcciÃ³n tras VERIFY fallido

## Tarea 6.1 â€” Privacidad e idempotencia

- **RED:** se aÃ±adieron pruebas para el token idempotente ausente y el enlace navegable de privacidad. El comando focal de PHPUnit mediante Docker no produjo salida y se interrumpiÃ³ tras 60 segundos; no fue posible confirmar el fallo inicial requerido por TDD estricto.
- **GREEN:** `class-labm-documents-contact.php` rechaza el token ausente antes de reservar o enviar. `functions.php` ahora genera un ancla con URL escapada para la polÃ­tica de privacidad.
- **REFACTOR:** no aplica. Header y footer no fueron modificados.

## Controles disponibles

- Sintaxis PHP: correcta en los tres archivos PHP afectados.
- `git diff --check`: correcto.
- Finales de lÃ­nea: LF, sin CRLF, en los archivos modificados dentro del alcance.
- PHPUnit focal: bloqueado por Docker sin salida ni resultado utilizable.
