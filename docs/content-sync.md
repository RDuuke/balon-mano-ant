# Sincronización privada de contenido WordPress

El código y el contenido canónico se comparten por el mismo repositorio Git. Los artículos, páginas, menús, taxonomías, opciones y medios viajan en `content-sync/canonical.zip`, acompañado por `content-sync/latest.json`. Cada paquete contiene un dump lógico de WordPress, `wp-content/uploads`, un manifiesto y checksums SHA-256.

## Preparación de ambos desarrolladores

1. Ambos desarrolladores deben trabajar sobre el mismo repositorio y rama acordada.
2. En el `.env` local agregue un identificador distinto por desarrollador:

   ```dotenv
   CONTENT_SYNC_OWNER=nombre-del-desarrollador
   ```

3. Levante el entorno con `docker compose up -d --wait` y ejecute el bootstrap normalmente.
4. Compruebe el estado sin mostrar credenciales:

   ```powershell
   ./scripts/content-sync.ps1 -Action Status
   ```

El repositorio contiene únicamente el paquete canónico actual y su puntero. Los respaldos automáticos permanecen localmente en `.content-sync/backups/` y nunca se versionan. Las tablas de usuarios y metadatos de usuario se excluyen del dump para no subir contraseñas hash, sesiones ni correos locales.

## Flujo diario recomendado

Antes de editar contenido, ejecute `git pull` e importe la última versión:

```powershell
./scripts/content-sync.ps1 -Action Begin -ConfirmReplace
```

`Begin` valida el paquete completo, comprueba que el entorno no esté obsoleto, crea un respaldo local y solo entonces reemplaza la base de datos y `uploads`. Si ya está actualizado no importa nada. El equipo debe coordinar un único editor de contenido a la vez.

En una instalación nueva, la versión local aparece como `unversioned`: `Begin -ConfirmReplace` permite esa primera importación después de crear el respaldo. A partir de ahí, cualquier divergencia de versión se bloquea.

Al terminar de crear o modificar artículos e imágenes, genere el paquete canónico:

```powershell
./scripts/content-sync.ps1 -Action Push
```

Después incluya `content-sync/canonical.zip` y `content-sync/latest.json` en el commit y haga `git push`. El otro desarrollador ejecuta `git pull` y luego `Begin -ConfirmReplace` antes de continuar.

Para actualizar un entorno sin reservar la edición:

```powershell
./scripts/content-sync.ps1 -Action Pull -ConfirmReplace
```

## Salvaguardas

- `Push` bloquea una publicación basada en una versión antigua. Si dos personas publican desde la misma base, Git rechazará el segundo push o marcará conflicto en `canonical.zip`/`latest.json`; no intente fusionar esos archivos.
- `Pull` e `Restore` exigen `-ConfirmReplace`, validan estructura, versión de WordPress, tamaño y SHA-256 antes de modificar datos.
- Cada importación genera primero un ZIP recuperable con base de datos y medios.
- Las URLs del origen se sustituyen con `wp search-replace`, que preserva datos serializados, y luego se fijan `home` y `siteurl` al `WP_URL` local.
- Los archivos internos del volumen MariaDB nunca se copian.
- Los respaldos, `.env` y el volumen activo de `uploads` están excluidos de Git. Solo se versiona el paquete canónico sanitizado, que excluye las tablas de usuarios y metadatos de usuario.
- No se leen ni copian los PDF de `docs/`.

El repositorio no implementa un bloqueo remoto en tiempo real. Antes de editar, coordinen quién será el escritor; el control de versión evita sobrescribir silenciosamente un paquete más nuevo.

## Respaldo y recuperación

Crear un respaldo manual:

```powershell
./scripts/content-sync.ps1 -Action Backup
```

Restaurar uno requiere confirmación explícita y crea otro respaldo antes de reemplazarlo:

```powershell
./scripts/content-sync.ps1 -Action Restore -Backup .content-sync/backups/backup-manual-<id>.zip -ConfirmReplace
```

Los respaldos se conservan hasta que ambos desarrolladores aprueben el estado sincronizado. Después pueden eliminarse manualmente; el script no borra respaldos ni volúmenes automáticamente.

## Consolidación inicial de los dos entornos divergentes

1. En ambos equipos ejecute `-Action Backup` y conserve los dos ZIP.
2. Elijan explícitamente cuál instalación será la base canónica.
3. En el entorno secundario exporten únicamente el contenido exclusivo mediante WXR (`wp export`) y reúnan sus medios asociados.
4. Importen selectivamente ese WXR y los medios en la base canónica. Revisen autores, slugs, taxonomías, menús, adjuntos y enlaces; no fusionen dumps SQL completos.
5. En la base ya consolidada ejecute `Push` para generar el primer paquete.
6. Agregue `content-sync/canonical.zip` y `content-sync/latest.json` al commit y publíquelo en Git.
7. En el segundo equipo ejecute `git pull` y luego `Pull -ConfirmReplace -ForceStale`. Esta opción es correcta únicamente aquí, después de conservar su respaldo divergente.
8. Comparen artículos, páginas, menús, medios y URLs en ambos entornos antes de eliminar cualquier respaldo.

## Resolución de conflictos

Si `Push` informa que la versión local es obsoleta, no use `-ForceStale` para trabajo cotidiano. Respalde, libere el bloqueo si corresponde, importe la última versión y vuelva a aplicar selectivamente los cambios. `-ForceStale` existe para la consolidación inicial o una recuperación acordada por ambos desarrolladores.

Si una importación se interrumpe, no elimine volúmenes. El mensaje indica el respaldo `pre-pull`, `pre-restore` o equivalente que debe restaurarse con `-Action Restore`.
