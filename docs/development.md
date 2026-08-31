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

## Rollback seguro

Antes de promover cambios de esquema, medios o configuración, genere y verifique respaldos de base de datos y uploads. Si la validación falla, detenga la promoción, restaure ambos respaldos y repita los gates. La eliminación de volúmenes requiere confirmación humana explícita.

