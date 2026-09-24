# Diseño: Sección pública de Contacto

## Enfoque técnico

Se publicará `/contacto/` mediante una plantilla de bloque y un patrón del tema. El patrón renderizará cabecera, datos, enlaces y formulario; LABM Core conservará procesamiento, configuración segura y envío. La plantilla reutilizará sin cambios los template parts globales `header` y `footer`.

## Decisiones de arquitectura

### Decisión: Reutilizar el módulo de contacto existente

| Opción | Trade-off | Decisión |
|---|---|---|
| Crear un módulo nuevo | Duplica validación y pruebas | No |
| Extender `class-labm-documents-contact.php` | Mantiene un archivo mixto temporalmente | Sí |

**Justificación:** ya contiene validación, honeypot y barrera de duplicados. Se completará allí el contrato de destinatarios, consentimiento, idempotencia y controlador HTTP.

### Decisión: Separar presentación, datos públicos y entrega

| Opción | Trade-off | Decisión |
|---|---|---|
| Datos y correo en el patrón | Expondría reglas y dificulta administración | No |
| Patrón + API de Core | Requiere contrato explícito | Sí |

**Justificación:** `labm_core_get_contact_settings()` expondrá solo datos públicos saneados. Correo y redes reutilizarán valores efectivos del footer; teléfono, dirección y ubicación tendrán defaults aprobados y opción administrativa con `edit_theme_options`. Los destinatarios nunca formarán parte de esta API ni del HTML.

### Decisión: POST-Redirect-GET con estado efímero

| Opción | Trade-off | Decisión |
|---|---|---|
| Responder en la misma petición | Complica recargas y estados | No |
| `admin-post.php` y redirección segura | Requiere token de estado corto | Sí |

**Justificación:** el formulario enviará `action=labm_contact_send`, nonce, honeypot oculto y token aleatorio. El controlador validará y redirigirá a `/contacto/#formulario` con identificador opaco. Errores y confirmación se guardarán brevemente sin valores personales; el render usará `aria-describedby`, `aria-invalid` y `role=status`/`aria-live`.

### Decisión: Entrega privada con defensa en profundidad

| Opción | Trade-off | Decisión |
|---|---|---|
| Destinatarios en PHP/opciones públicas | Riesgo de filtración | No |
| Primario fijo y copias desde entorno no productivo | Exige configurar secretos por entorno | Sí |

**Justificación:** el primario será el correo institucional aprobado. Solo si `wp_get_environment_type()` es `local`, `development` o `staging`, se leerá `LABM_CONTACT_TEST_RECIPIENTS`, se validarán correos y limitarán a dos copias. Producción, tipo ambiguo o configuración inválida omitirán copias. No se persistirá ni registrará contenido.

### Decisión: Antispam e idempotencia accesibles

| Opción | Trade-off | Decisión |
|---|---|---|
| CAPTCHA visual | Obstáculo de accesibilidad | No |
| Nonce, honeypot e idempotencia del servidor | No elimina todo el spam | Sí |

**Justificación:** el honeypot no será focusable ni anunciado; nonce protege integridad. Antes de entregar se reservará atómicamente el hash del token en una opción temporal limpiada programadamente; una repetición no reenviará. Fallos de correo liberarán la reserva.

## Flujo de datos

```text
Visitante -> patrón Contacto -> POST admin-post.php
  -> nonce + honeypot + validación + reserva idempotente
  -> wp_mail(primario[, copias no productivas])
  -> estado efímero -> redirección /contacto/#formulario
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/themes/labm/templates/page-contacto.html` | Crear | Ruta y reutilización de header/footer existentes. |
| `wp-content/themes/labm/patterns/contacto.php` | Crear | Marcado semántico, formulario y estados. |
| `wp-content/themes/labm/functions.php` | Modificar | Renderizado del patrón y consumo seguro del estado. |
| `wp-content/themes/labm/style.css` | Modificar | Composición responsive, foco y mensajes. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificar | Ajustes, validación, destinatarios, entrega y controlador. |
| `tests/php/DocumentContactTest.php` | Modificar | Contratos de seguridad y entrega. |
| `tests/e2e/contact.spec.ts` | Crear | Ruta, formulario, teclado, estados, anchos y Axe. |

## Interfaces y contratos

- `labm_core_get_contact_settings(): array`: solo datos públicos saneados.
- `labm_core_process_contact( array $data ): array`: resultado sin contenido personal; exige consentimiento y conserva códigos de error seguros.
- `labm_core_contact_recipients(): array`: primario y copias autorizadas únicamente para envío interno.
- Acción pública `labm_contact_send`: acepta únicamente POST y redirige a la página de contacto.

## Estrategia de pruebas

| Capa | Qué comprobar | Enfoque |
|---|---|---|
| PHPUnit | saneado, consentimiento, nonce, honeypot, idempotencia y destinatarios | Simular `wp_mail` y tipos de entorno. |
| Playwright | contenido, formulario, PRG, teclado y mensajes | 320, 768, 1024, 1200 y 1440 px; Axe WCAG 2.2 AA. |
| Seguridad | no exponer copias ni datos técnicos | Inspeccionar HTML, artefactos versionados y fallo de entrega. |
| Calidad | estilo y LF | Pruebas focales, PHPCS/PHPStan aplicables y revisión de bytes. |

## Migración y despliegue

No requiere migración. Antes de producción se configurará SMTP y, solo fuera de producción, la variable de copias en secretos. Sin esa variable se enviará únicamente al correo institucional.

## Preguntas abiertas

- [ ] Confirmar la URL institucional definitiva para abrir la ubicación; mientras tanto se usará una URL de búsqueda segura construida desde la dirección aprobada.
