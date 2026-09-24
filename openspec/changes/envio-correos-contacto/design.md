# Diseño: Administración SMTP híbrida para Contacto

## Enfoque técnico

LABM Core conservará `wp_mail()` y configurará PHPMailer mediante `phpmailer_init` solo con una configuración completa. Una pantalla secundaria bajo el menú LABM guardará en la opción privada `labm_smtp_settings` los valores no secretos: host, puerto, cifrado, correo y nombre remitente, y destinatario. La contraseña se leerá únicamente de la constante `LABM_SMTP_PASSWORD` definida en el `wp-config.php` del entorno; no habrá campo de contraseña ni mecanismo alternativo de almacenamiento.

## Decisiones de arquitectura

### Decisión: Opción de WordPress solo para valores no secretos

| Opción | Trade-off | Decisión |
|---|---|---|
| Guardar toda la configuración en opciones | Facilita el panel, pero persiste una contraseña | No |
| Opción cerrada sin contraseña + constante del servidor | Requiere carga manual del secreto | Sí |

**Justificación:** permite ajustar Gmail y el destino desde WordPress, sin incorporar la contraseña de aplicación a copias de base de datos, exportaciones o Git.

### Decisión: Panel administrativo y prueba por POST protegido

| Opción | Trade-off | Decisión |
|---|---|---|
| Prueba desde una URL o AJAX público | Aumenta el riesgo de abuso | No |
| Acción `admin_post` con capacidad, nonce y PRG | Añade una redirección | Sí |

**Justificación:** la acción usará la misma capacidad del menú LABM, `check_admin_referer()` y una redirección con aviso genérico. La prueba usa el destinatario guardado, nunca un destino solicitado por el navegador.

### Decisión: Remitente autenticado y Reply-To separado

| Opción | Trade-off | Decisión |
|---|---|---|
| Usar el correo del formulario como From | Falla DMARC y permite suplantación | No |
| From configurado + Reply-To validado | Requiere un remitente autorizado | Sí |

**Justificación:** los filtros `wp_mail_from` y `wp_mail_from_name` establecerán el remitente SMTP; `labm_core_process_contact()` conservará el `Reply-To` validado.

## Flujo de datos

```text
Administrador → pantalla SMTP → opción no secreta
Operador Hostinger → wp-config.php → LABM_SMTP_PASSWORD
Formulario válido → wp_mail() → filtros From + phpmailer_init → SMTP
correo del formulario ───────────────────────────────────────────→ Reply-To
Prueba protegida → wp_mail() → destinatario configurado → aviso seguro
```

### Decisión: Indicador local y accesible de envío

El tema cargará un script pequeño únicamente en Contacto. Tras superar `checkValidity()` al recibir `submit`, el script marcará el formulario con `aria-busy="true"`, deshabilitará el botón y sustituirá su etiqueta por un spinner y «Enviando mensaje…», anunciado con `aria-live="polite"`. No interceptará ni modificará el POST; la respuesta PRG existente vuelve a renderizar un formulario limpio y habilitado. Al restaurar una página desde caché se restablece el botón. La validación nativa permanece habilitada y no activa el estado de envío.

### Decisión: Separar el fallo de entrega de la validación de campos

El estado PRG ya conserva las claves de error, incluida `delivery`, sin datos personales ni detalles del transporte. El tema detectará esa clave antes de renderizar el aviso de validación: para `delivery` mostrará un único mensaje seguro con `role="alert"` y `aria-live="assertive"`, sin lista de campos. Los errores de validación conservan el aviso actual y sus enlaces a los controles marcados. Esto evita atribuir al usuario un fallo de configuración o entrega sin revelar SMTP, proveedor o secretos.

### Decisión: Plantilla de correo HTML institucional y previsualización segura

Contacto sustituirá el cuerpo de texto plano por una plantilla HTML de una sola columna, construida con tablas de presentación y CSS en línea para clientes de correo. La cabecera combinará el logo oficial del tema como adjunto MIME JPEG con el Content-ID estable `labm-contact-logo` y el texto «LABM · Liga Antioqueña de Balonmano»: la imagen se referencia con `cid:labm-contact-logo`, por lo que Gmail no debe descargar una URL del entorno local o privado. El hook `phpmailer_init` añade la imagen solo cuando el cuerpo contiene ese CID, comprueba que no esté previamente adjunta y no modifica otros mensajes. Si el archivo no existe o PHPMailer rechaza el adjunto, el texto conserva la identificación. La vista previa administrativa usa la URL del tema exclusivamente en el navegador administrativo, donde sí es alcanzable. La composición empleará verde `#AECD25`, negro `#202020` y claro `#F3F6E8`; no dependerá de CSS externo, JavaScript, SVG embebido ni imágenes base64.

Los datos del formulario se sanearán antes de invocar el renderizador y se escaparán al insertarse en HTML; el mensaje conserva párrafos mediante `nl2br( esc_html() )`. La llamada a `wp_mail()` añadirá el encabezado HTML sin cambiar el `From`, ni el `Reply-To` validado existente. El panel SMTP mostrará una muestra con datos ficticios dentro de un `iframe sandbox` con `srcdoc`, accesible solo para administradores; no consulta opciones sensibles, no tiene acción de envío y no reutiliza mensajes almacenados.

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-smtp-settings.php` | Crear | Opción cerrada, saneamiento, pantalla, prueba protegida y configuración SMTP. |
| `wp-content/plugins/labm-core/labm-core.php` | Modificar | Cargar el módulo SMTP. |
| `tests/php/SmtpSettingsTest.php` | Crear | Cubrir permisos, saneamiento, secreto externo, transporte, prueba y avisos seguros. |
| `tests/php/DocumentContactTest.php` | Modificar | Comprobar From configurado, Reply-To y fallo/reintento con el transporte habilitado. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificar | Renderizar la plantilla HTML institucional y declarar el tipo de contenido del correo de Contacto. |
| `wp-content/plugins/labm-core/includes/class-labm-smtp-settings.php` | Modificar | Mostrar una vista previa administrativa estática y aislada de la plantilla. |
| `docs/development.md` | Modificar | Configuración local/Hostinger, prueba, rotación y límites de seguridad. |

Además de los cambios enumerados, el tema actualizará `functions.php` y `style.css`, añadirá `assets/contact.js` y ampliará `tests/e2e/contact.spec.ts` para cubrir el estado de envío.

## Interfaces y contratos

- `labm_smtp_settings` contiene solo las seis claves no secretas definidas por el esquema cerrado; no se expone por REST.
- `LABM_SMTP_PASSWORD` es la única fuente aceptada para la contraseña de aplicación.
- La pantalla muestra únicamente estado “configurada/no disponible” de la contraseña.
- `labm_core_process_contact(array $data): array` y los destinatarios institucionales actuales se mantienen.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| PHPUnit | Opción, permisos, nonce, secreto externo y avisos | Dobles de WordPress/PHPMailer, sin red ni secreto real. |
| PHPUnit | From, Reply-To, fallo y reintento | Interceptar `wp_mail()` y filtros. |
| PHPUnit | Plantilla HTML y logo CID | Interceptar `wp_mail()` y comprobar marca, escape de datos, encabezados HTML, `Reply-To`, CID y adjunto MIME, sin red. |
| Manual | Vista previa de plantilla | Revisar LABM > SMTP con datos ficticios; no enviar correo. |
| Manual | Prueba Gmail y encabezado Reply-To | Cuenta de prueba autorizada y contraseña temporal en `wp-config.php`. |
| Calidad | Estándares y seguridad | PHPCS, PHPStan y comprobación LF. |

## Migración y despliegue

No hay migración. Tras desplegar, un administrador guarda los valores no secretos en LABM; el operador define `LABM_SMTP_PASSWORD` en el `wp-config.php` existente de Hostinger. Se ejecuta una única prueba autorizada y se verifica entrega y `Reply-To`. La reversión desactiva SMTP retirando la constante y, si procede, elimina la opción; Contacto retorna al transporte previo.

## Migracion de destinatarios

`labm_smtp_settings.recipients` es la fuente canonica. En la primera carga, SMTP combina los destinatarios validos del campo historico `labm_footer_settings.contact_recipients` y del campo SMTP singular heredado. La operacion es idempotente, conserva la opcion de origen para recuperacion y elimina solamente la interfaz del footer. Las pruebas y Contacto consumen la lista SMTP; la prueba administrativa usa esa misma lista sin aceptar destinos del navegador.

## Preguntas abiertas

- [ ] Confirmar la capacidad administrativa definitiva si LABM deja de usar `edit_theme_options`.
- [ ] Antes de producción, confirmar remitente institucional, SPF, DKIM y DMARC.
