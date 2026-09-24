# Informe de verificación: Sección de Contacto

## Resumen ejecutivo

Estado: **failed**. La implementación contiene la ruta, el formulario y los controles declarados, y la sintaxis PHP y los finales de línea LF focales son correctos. Sin embargo, no hay evidencia de ejecución satisfactoria: Docker no devolvió resultado para PHPUnit y el ejecutable local de Playwright no está instalado. Además, las 17 tareas siguen sin marcarse como completadas y faltan pruebas explícitas para los entornos no productivos, ambiguos y de producción.

## Ejecuciones realizadas

- `php -l wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`: PASS.
- `php -l wp-content/themes/labm/functions.php`: PASS.
- `php -l wp-content/themes/labm/patterns/contacto.php`: PASS.
- `docker run --rm -v "${projectRoot}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 test -- --configuration phpunit.integration.xml.dist --filter DocumentContactTest`: sin salida ni resultado tras dos intentos; Docker no quedó utilizable para la verificación.
- `& '.\\node_modules\\.bin\\playwright.cmd' test tests/e2e/contact.spec.ts`: FAIL; no existe `node_modules/.bin/playwright.cmd`.
- `git diff --check`: PASS.
- Verificación por bytes de los archivos modificados y artefactos de este cambio: PASS, 0 archivos con CRLF.
- Búsqueda de las dos direcciones de prueba en los archivos versionados y no versionados del alcance de implementación: PASS, sin coincidencias. La variable `LABM_CONTACT_TEST_RECIPIENTS` solo se referencia como nombre de configuración y no en el frontend.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| experiencia-pública | Vista completa publicada | ❌ UNTESTED | `tests/e2e/contact.spec.ts` | CRITICAL |
| experiencia-pública | Pantalla estrecha o contenido largo | ❌ UNTESTED | `tests/e2e/contact.spec.ts` | CRITICAL |
| experiencia-pública | Ruta o recurso público no disponible | ❌ UNTESTED | — | CRITICAL |
| experiencia-pública | Datos y enlaces correctos | ❌ UNTESTED | `tests/e2e/contact.spec.ts` | CRITICAL |
| experiencia-pública | Red social no disponible | ❌ UNTESTED | — | CRITICAL |
| experiencia-pública | Destino de enlace inválido | ❌ UNTESTED | — | CRITICAL |
| experiencia-pública | Recorrido por teclado | ❌ UNTESTED | `tests/e2e/contact.spec.ts` | CRITICAL |
| experiencia-pública | Tecnología asistiva | ❌ UNTESTED | `tests/e2e/contact.spec.ts` | CRITICAL |
| experiencia-pública | Control sin nombre o contraste insuficiente | ❌ UNTESTED | `tests/e2e/contact.spec.ts` con Axe | CRITICAL |
| documentos-contacto | Envío válido en producción | ❌ UNTESTED | `tests/php/DocumentContactTest.php` parcial, no ejecutada | CRITICAL |
| documentos-contacto | Envío no productivo con copias configuradas | ❌ UNTESTED | — | CRITICAL |
| documentos-contacto | Falla de entrega o configuración inválida | ❌ UNTESTED | `tests/php/DocumentContactTest.php` parcial, no ejecutada | CRITICAL |
| documentos-contacto | Validación de campos y consentimiento | ❌ UNTESTED | `tests/php/DocumentContactTest.php`, no ejecutada | CRITICAL |
| documentos-contacto | Datos incompletos o no válidos | ❌ UNTESTED | PHP y E2E parciales, no ejecutadas | CRITICAL |
| documentos-contacto | Solicitud automatizada o manipulada | ❌ UNTESTED | `tests/php/DocumentContactTest.php`, no ejecutada | CRITICAL |
| calidad-seguridad | Flujo público satisfactorio | ❌ UNTESTED | PHP y E2E no ejecutadas | CRITICAL |
| calidad-seguridad | Anchos y controles representativos | ❌ UNTESTED | `tests/e2e/contact.spec.ts`, no ejecutada | CRITICAL |
| calidad-seguridad | Regresión de seguridad o accesibilidad | ❌ UNTESTED | PHP y E2E parciales, no ejecutadas | CRITICAL |
| calidad-seguridad | Producción sin copias de prueba | ❌ UNTESTED | No existe prueba que fije el entorno `production` | CRITICAL |
| calidad-seguridad | Desarrollo con configuración segura | ❌ UNTESTED | No existe prueba de destinatarios desde entorno no productivo | CRITICAL |
| calidad-seguridad | Entorno o configuración ambiguos | ❌ UNTESTED | — | CRITICAL |

## Coherencia con el diseño

- ⚠️ Parcial: plantilla `page-contacto.html` reutiliza los template parts `header` y `footer`; el diff no modifica ningún archivo de header o footer.
- ⚠️ Parcial: el contrato público incorpora correo, teléfono, dirección, redes y URL de Maps; no hay prueba ejecutada que lo demuestre.
- ⚠️ Parcial: el controlador usa POST, nonce, honeypot, estado transitorio y redirección PRG; no hay prueba ejecutada de este flujo.
- ⚠️ Parcial: las copias se leen de `LABM_CONTACT_TEST_RECIPIENTS` solo para `local`, `development` o `staging`, con un máximo de dos. La ausencia de las direcciones de prueba en el código y frontend fue comprobada estáticamente, pero faltan pruebas de producción, no producción y entorno ambiguo.
- ⚠️ Parcial: la idempotencia no rechaza un token ausente: `labm_core_contact_reserve_token( '' )` devuelve cadena vacía y el procesamiento puede continuar. Falta una prueba y corrección para que la integridad/idempotencia sea obligatoria también ante una petición manipulada.
- ⚠️ Parcial: el enlace de privacidad se construye dentro de `esc_html__()` y se imprime mediante `printf`; el ancla queda codificada como texto, por lo que no cumple el requisito de enlazar la política de privacidad.

## Tareas incompletas

Las tareas `1.1` a `5.2` permanecen sin marcar como completadas en `tasks.md`. Con TDD estricto activo, tampoco existe evidencia RED/GREEN para cada una; `apply-progress.md` solo documenta 1.1, 2.1, 3.1 y 4.1.

## Fallos Detectados

### Tests fallidos
- `tests/e2e/contact.spec.ts`: no se puede ejecutar porque falta `node_modules/.bin/playwright.cmd`.
- `tests/php/DocumentContactTest.php`: Docker no devolvió una ejecución utilizable para PHPUnit.

### Errores de build
- `wp-content/themes/labm/functions.php:179`: el enlace de política de privacidad se codifica como texto y no es navegable.
- `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php:382`: un token idempotente ausente permite continuar el envío.

### Tareas incompletas
- `1.1–5.2`: las 17 tareas siguen pendientes en `tasks.md`.

## Riesgos

- CRITICAL: no existe prueba ejecutada que respalde ninguno de los escenarios de la especificación.
- CRITICAL: falta cobertura específica de destinatarios de prueba en producción, no producción y entorno ambiguo.
- CRITICAL: dos incumplimientos detectados estáticamente en enlace de privacidad e idempotencia ante token ausente.
- WARNING: no se pudo ejecutar PHPCS, PHPStan, cobertura ni la suite Playwright por la indisponibilidad de Docker y dependencias JavaScript locales.

## Recomendación

Volver a **APPLY**: corregir los dos incumplimientos, añadir la cobertura faltante, instalar o restaurar las dependencias de pruebas y ejecutar PHPUnit, PHPCS, PHPStan y Playwright antes de una nueva verificación.
