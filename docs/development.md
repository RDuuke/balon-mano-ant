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

