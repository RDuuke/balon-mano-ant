# CI/CD de Hostinger

El workflow `.github/workflows/quality.yml` conserva los gates locales para solicitudes de extracción y despliega solo desde un `push` exitoso a `main`. WordPress ya debe estar instalado de forma manual en `domains/palevioletred-salamander-919245.hostingersite.com/public_html`; este flujo nunca instala ni reemplaza el núcleo, `wp-config.php` ni los medios remotos fuera de la primera migración canónica.

## Secretos de GitHub

Configure estos secretos de Actions en el repositorio:

- `FTP_SERVER`, `FTP_USERNAME` y `FTP_PASSWORD`: host/IP, usuario y contraseña de la cuenta FTP con acceso a `/public_html/`. El flujo usa FTP por el puerto `21`.
- `DB_HOST`, `DB_NAME`, `DB_USERNAME` y `DB_PASSWORD`: MySQL remoto de la instalación. `DB_HOST` admite `host` o `host:puerto`.
- `WP_USER` y `WP_PASSWORD`: usuario administrador exclusivo para el smoke autenticado; no se modifica ni se usa para tareas administrativas.

No se debe crear `WP_PRODUCTION_URL`: la URL temporal está fijada en el workflow como `http://palevioletred-salamander-919245.hostingersite.com`. Los secretos no se imprimen, no se añaden a artefactos y no deben estar en archivos versionados.

## Primera ejecución

1. Confirme en Hostinger que WordPress vacío funciona en `http://palevioletred-salamander-919245.hostingersite.com` y que `wp-json/` es público.
2. Autorice el acceso MySQL remoto desde GitHub Actions. El preflight aborta antes de importar si no puede conectarse, no identifica un único prefijo WordPress o la versión major.minor no coincide con `content-sync/canonical.zip`.
3. Confirme en hPanel que los datos de Cuentas FTP permiten conectarse a la IP de `FTP_SERVER` por el puerto `21`. Haga merge a `main`. Tras `quality`, el job `deploy-code` actualiza únicamente `domains/palevioletred-salamander-919245.hostingersite.com/public_html/wp-content/themes/labm/` y `domains/palevioletred-salamander-919245.hostingersite.com/public_html/wp-content/plugins/labm-core/`, sin limpieza remota.
4. El job `bootstrap-content` valida `canonical.zip`, crea un respaldo de la base de datos, importa el contenido sin `users` ni `usermeta`, y sube los `uploads` del paquete. Solo después adapta URLs mediante WP-CLI y guarda `labm_content_sync_version`.
5. El job `smoke` inicia sesión con cookie temporal y exige acceso a `wp-admin/profile.php`. No muestra la respuesta, cookies ni credenciales.

El proceso es idempotente: si existe `labm_content_sync_version`, la migración y transferencia de medios se omiten en los siguientes despliegues. Si cualquier paso falla antes del marcador, el siguiente intento vuelve a requerir revisión; no se marca una migración parcial como correcta.

## Recuperación

Cuando una migración se inicia, Actions conserva durante 14 días el artefacto privado `hostinger-bootstrap-<run id>` con `database-before-import.sql.gz` e informe redactado. No se restaura automáticamente: descargue el artefacto, restaure la base de datos por un canal seguro de Hostinger y confirme la causa antes de reintentar. El FTP de código no borra archivos; para revertirlo, despliegue una revisión anterior del tema y plugin.

## Dominio definitivo y HTTPS

Cuando el dominio definitivo esté listo, actualice la variable `WP_PRODUCTION_URL` del workflow y ejecute una migración controlada de URL con WP-CLI. No realice reemplazos SQL manuales: las opciones y metadatos de WordPress pueden contener datos serializados.
