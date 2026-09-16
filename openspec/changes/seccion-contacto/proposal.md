# Propuesta: Sección pública de Contacto

## Intención

Ofrecer una página de contacto institucional fiel al diseño aprobado y un canal seguro hacia la Liga, sin divulgar destinatarios de prueba.

## Alcance

### Incluye

- Página `/contacto/` con cabecera, datos, redes, ubicación y footer.
- Formulario accesible con nombre, apellidos, correo, teléfono opcional, asunto, mensaje, consentimiento y estados anunciados.
- Envío a `info@balonmanoantioquia.com`; copias de prueba solo desde configuración de entorno no productivo, fuera del frontend y del código versionado.
- Validación, saneamiento, antispam y pruebas focales.

### Excluye

- CRM, almacenamiento de solicitudes, adjuntos, automatizaciones externas y publicación de datos de prueba.

## Enfoque

Presentación en el tema y procesamiento en LABM Core. Centralizar datos públicos y resolver destinatarios adicionales solo desde variables de entorno no productivas. Usar APIs de WordPress para protección, validación, envío y estados accesibles.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `wp-content/themes/labm/patterns/contacto.php` | Nuevo | Página y formulario. |
| `wp-content/themes/labm/templates/page-contacto.html` | Nuevo | Plantilla de ruta pública. |
| `wp-content/themes/labm/functions.php` | Modificado | Registro del renderizado. |
| `wp-content/themes/labm/style.css` | Modificado | Diseño adaptable y foco visible. |
| `wp-content/plugins/labm-core/includes/class-labm-contact-form.php` | Nuevo | Procesamiento y destinatarios. |
| `wp-content/plugins/labm-core/labm-core.php` | Modificado | Carga del módulo. |
| `tests/php/ContactFormTest.php` | Nuevo | Pruebas de formulario y entorno. |
| `tests/e2e/contact.spec.ts` | Nuevo | Flujo y accesibilidad. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Correo no entregado | Media | Validar SMTP en entorno no productivo. |
| Spam | Media | Nonce, honeypot y límites del servidor. |
| Copias expuestas | Baja | Variables no productivas y prueba de ausencia en HTML. |

## Plan de reversión

Restaurar los archivos previos del tema y plugin; eliminar plantilla, patrón y módulo nuevos. No hay migraciones. Retirar las variables no productivas.

## Dependencias

- SMTP configurado en cada entorno.
- Variables de entorno para copias no productivas.

## Criterios de éxito

- [ ] `/contacto/` muestra datos, formulario y enlaces aprobados en escritorio y móvil.
- [ ] Los campos obligatorios y consentimiento bloquean envíos inválidos y anuncian errores.
- [ ] Un envío válido llega al correo institucional.
- [ ] Las copias de prueba solo operan fuera de producción y no aparecen en HTML ni repositorio.
- [ ] Las pruebas PHP, E2E, accesibilidad y LF focales pasan.
