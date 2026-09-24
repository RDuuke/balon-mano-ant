# Evidencia TDD de APPLY

## Tarea 1.1 — Contratos del formulario

- **RED:** las pruebas de consentimiento, destinatario institucional, ajustes públicos y liberación del token fallaban porque las funciones y el consentimiento no existían.
- **GREEN:** implementación en `tests/php/DocumentContactTest.php` y `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; la sintaxis PHP valida correctamente.
- **REFACTOR:** se separó el contrato público de los destinatarios internos.

## Tarea 2.1 — Procesamiento seguro

- **RED:** las pruebas de ajustes y consentimiento fallaban contra el procesamiento anterior.
- **GREEN:** se añadieron ajustes saneados, destinatarios por entorno, reserva idempotente, liberación ante fallo y PRG.
- **REFACTOR:** los estados PRG almacenan solamente resultado y claves de error.

## Tarea 3.1 — Ruta y formulario público

- **RED:** la ruta, plantilla y patrón Contacto no existían.
- **GREEN:** se crearon plantilla, patrón, renderizado y estilos responsive con asociaciones ARIA.

## Tarea 4.1 — Interfaz focal

- **RED:** la especificación Playwright de Contacto no tenía elementos que localizar.
- **GREEN:** se añadió `tests/e2e/contact.spec.ts` para ruta, datos, seguridad visible, teclado, Axe y anchos.
