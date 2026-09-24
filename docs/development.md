# Desarrollo local

## Ciclo de vida

- Validar: `./scripts/validate-env.ps1`
- Iniciar: `docker compose up -d --wait`
- Preparar WordPress: `./scripts/bootstrap.ps1`
- Inspeccionar: `docker compose ps` y `docker compose logs --tail 100 wordpress db`
- Detener conservando datos: `docker compose stop`
- Reiniciar: `docker compose start` y `docker compose up -d --wait`

## Diagnostico

Un servicio no saludable debe aparecer en `docker compose ps`. Capture `docker compose logs --tail 100 <servicio>` sin publicar credenciales. El bootstrap es idempotente: puede ejecutarse dos veces y solo actualiza fixtures marcados.

## Reset local

El reset elimina base de datos, core y uploads locales. Requiere confirmacion humana explicita; este proyecto no incluye un reset automatico silencioso. Tras confirmar y respaldar lo necesario, el operador puede ejecutar `docker compose down --volumes` manualmente y volver a ejecutar bootstrap.

Nunca use reset para diagnostico rutinario. No se realiza commit, push ni publicacion desde estos scripts.

## Contenido compartido entre desarrolladores

La base de datos de contenido y `uploads` viajan por Git dentro de un paquete canónico sanitizado; usuarios, sesiones y respaldos locales no se versionan. Use `git pull` y `./scripts/content-sync.ps1 -Action Begin -ConfirmReplace` antes de editar. Al terminar, ejecute `Push` e incluya `content-sync/canonical.zip` y `content-sync/latest.json` en el commit. El mecanismo bloquea versiones obsoletas, crea respaldo antes de importar y valida checksums.

Consulte [sincronización de contenido](content-sync.md) para preparar ambos equipos, consolidar los estados iniciales y recuperar un respaldo.

## SMTP de Contacto (prueba controlada)

LABM Core conserva `wp_mail()` y activa SMTP solo si la configuracion hibrida esta completa. En **LABM > SMTP**, un administrador con capacidad para editar las opciones del tema puede guardar los valores no sensibles: servidor, puerto, cifrado, correo y nombre remitente, y destinatario de Contacto. Para Gmail de prueba use `smtp.gmail.com`, puerto `587`, cifrado `TLS` y un remitente que corresponda con la cuenta autenticada.

La contrasena de aplicacion no tiene campo en el panel y nunca se guarda en WordPress. El operador debe anadir una contrasena de aplicacion nueva, solamente en el `wp-config.php` existente de cada entorno, antes de la linea que carga la configuracion de WordPress:

```php
define( 'LABM_SMTP_PASSWORD', 'contrasena-de-aplicacion-del-entorno' );
```

No incluya esa linea en Git, archivos `.env` versionados, exportaciones de la base de datos ni tickets. El panel solo muestra si la contrasena esta disponible; no puede mostrarla ni recuperarla.

Guarde primero los valores no secretos y use **Enviar prueba** una sola vez. La prueba siempre se dirige al destinatario ya guardado, usa una solicitud POST con nonce y muestra un resultado generico. Revise la entrega real y, desde un mensaje del formulario, confirme que el remitente es la cuenta autenticada y que `Reply-To` corresponde al correo del remitente del formulario.

En Hostinger se configura el mismo valor `LABM_SMTP_PASSWORD` directamente en el `wp-config.php` ya utilizado por esa instalacion; el despliegue de codigo no traslada secretos. Para rotar la clave, cree una nueva contrasena de aplicacion en Gmail, reemplace unicamente la constante del servidor y revoque la anterior. Para volver al transporte previo, retire la constante o deje incompleta la configuracion SMTP; los valores no secretos pueden conservarse o eliminarse desde WordPress.

## Rollback seguro

Antes de promover cambios de esquema, medios o configuración, genere y verifique respaldos de base de datos y uploads. Si la validación falla, detenga la promoción, restaure ambos respaldos y repita los gates. La eliminación de volúmenes requiere confirmación humana explícita.

## Administración de documentos PDF

En `wp-admin`, cree o edite un Documento y abra el panel documental del editor de bloques; el editor clásico ofrece un metabox equivalente. Seleccione o suba un PDF mediante la biblioteca de medios. Los controles permiten reemplazarlo o quitar su asociación sin borrar el adjunto de la biblioteca, y muestran nombre, tamaño, estado y límite efectivo. Use el teclado para recorrer los controles; los errores se anuncian y permiten llevar el foco al campo que requiere corrección.

Para publicar o actualizar se exige título no vacío y un PDF válido. El límite es `min(30 MB, wp_max_upload_size())`; PHP verifica acceso al adjunto, tipo MIME, extensión, legibilidad, firma PDF y tamaño. La validación del servidor es autoritativa para REST y el formulario clásico: un rechazo conserva los valores previamente persistidos sin aplicar cambios parciales. Reparar un PDF inexistente o inválido es obligatorio antes de guardar el documento.

La «Fecha del documento» es opcional: puede quedar vacía o contener una fecha calendario real guardada como `Y-m-d`, sin sustituirse por la fecha de publicación o carga. Seleccione un solo tipo existente entre Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro. Si falta la selección, el guardado válido utiliza Documento general.

Administradores y editores con permisos del Documento pueden editarlo y asignar tipos existentes mediante `assign_labm_documento_types`. Solo los administradores con `manage_labm_documento_types` pueden crear, renombrar o retirar tipos. La selección requiere acceso de edición al adjunto; el formulario clásico comprueba nonce y los canales administrativos requieren autenticación y capacidades válidas.

Los IDs numéricos históricos almacenados como texto siguen siendo compatibles. La siembra de los nueve tipos está versionada y es idempotente; no migra masivamente publicaciones, no elimina asociaciones y no despublica documentos. Un histórico sin tipo muestra Documento general como fallback administrativo sin persistirlo hasta un guardado válido. El alcance de esta mejora es exclusivamente administrativo; catálogo, plantillas y estilos públicos conservan su comportamiento.

Para revertir, respalde base de datos y uploads, restaure la versión anterior del plugin `labm-core` con su registro de metadatos, taxonomía y capacidades, y retire la carga del módulo `class-labm-document-admin.php` y del asset `admin-documento.js` con esa versión. Conserve metadatos, asociaciones, adjuntos y términos; no use una limpieza de datos ni un reset como reversión. Valide la edición con la versión restaurada antes de promoverla.

La evidencia del cambio combina integración 134/134, cobertura 82.53 %, PHPStan y Compose/unit correctos, y `document-admin.spec.ts` con desktop 9/9 y móvil/tablet 18/18. Permanecen diez warnings PHPCS de archivos ajenos al cambio, documentados como deuda externa. `artifacts/gate/summary.json` corresponde al gate completo histórico fallido y no representa una ejecución completa posterior en verde.

Al verificar, reutilice evidencia válida cuando no haya cambios de código que la invaliden. Defina límites de tiempo para ejecuciones nuevas; si una prueba entra en un ciclo prolongado o deja de progresar, deténgala y use una alternativa focal acotada, registrando el bloqueo. Evite repetir suites ya verdes sin una causa nueva.
