# Propuesta: Envío de correos del formulario de Contacto

## Intención

Asegurar y comprobar la entrega SMTP de los mensajes de `/contacto/`, que ya dispone de validación y controlador, sin versionar credenciales.

## Alcance

### Incluye

- Configuración SMTP mediante secretos para cada entorno.
- Conservar `wp_mail()`, destinatario institucional, `Reply-To`, errores seguros e idempotencia.
- Prueba de entrega controlada y guía operativa sin datos personales.

### Excluye

- Cambios de campos, diseño o ruta del formulario.
- CRM, persistencia, adjuntos, autorespuesta y marketing.

## Enfoque

Configurar un transporte SMTP mantenido o de infraestructura con host, puerto, cifrado y credenciales en secretos. LABM Core seguirá enviando mediante `wp_mail()`. Desarrollo comprobará un buzón de pruebas; staging y producción, el buzón institucional autorizado. Los registros omitirán cuerpo, correos y secretos.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `.env.example` | Modificado | Documentar límites de pruebas. |
| `compose.yaml` | Modificado | Configuración SMTP de desarrollo, si se aprueba. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificado | Contrato de entrega y errores seguros. |
| `tests/php/DocumentContactTest.php` | Modificado | Éxito, fallo y reintento sin secretos. |
| `docs/development.md` | Modificado | Prueba y diagnóstico seguros. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Secretos SMTP expuestos | Media | Secret manager, `.env` ignorado y revisión. |
| Baja entregabilidad | Media | SPF/DKIM/DMARC, remitente autorizado y prueba previa. |
| Duplicados tras fallo | Baja | Conservar idempotencia y probar reintento. |

## Plan de reversión

Restaurar `.env.example`, `compose.yaml`, módulo, pruebas y documentación; retirar secretos y desactivar SMTP. No hay migraciones ni banderas. El formulario conservará su error seguro mientras se restablece la configuración previa.

## Dependencias

- Credenciales SMTP y remitente autorizados.
- DNS SPF, DKIM y DMARC del dominio institucional.

## Criterios de éxito

- [ ] Una prueba autorizada llega al buzón previsto y conserva `Reply-To`.
- [ ] Un fallo SMTP muestra un error seguro y permite reintento sin duplicar correos.
- [ ] No hay credenciales, destinatarios de prueba ni datos personales en Git, HTML o logs.
- [ ] Pasan las pruebas PHP focales y la verificación LF.
