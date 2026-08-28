# LABM WordPress

Entorno local reproducible para desarrollar el sitio de la Liga Antioqueña de Balonmano. Todo contenido inicial se marca **[DEMO LABM — FICTICIO]** y no representa información oficial.

## Inicio rapido

1. Copie `.env.example` como `.env` y cambie las credenciales locales.
2. Ejecute `./scripts/validate-env.ps1`.
3. Ejecute `./scripts/bootstrap.ps1` dos veces para comprobar idempotencia.
4. Abra la URL indicada en `WP_URL`.

Use `docker compose stop` para detener sin borrar datos y `docker compose up -d --wait` para reiniciar. Consulte [desarrollo](docs/development.md) para diagnostico y ciclo de vida, y [pruebas](docs/testing.md) para validacion desde un entorno limpio.

Los PDF presentes en `docs/` son fuentes restringidas: ningun script los lee, copia a uploads ni publica.

## Calidad, trazabilidad y rollback

El gate local `./scripts/gate.ps1 -IncludeBrowser` y el flujo de CI ejecutan las comprobaciones obligatorias y conservan sus reportes. La relación entre requisitos y pruebas está en [trazabilidad](docs/traceability.md).

El rollback requiere respaldo previo de base de datos y uploads. Nunca se borran volúmenes automáticamente: cualquier reversión destructiva exige confirmación humana y una restauración verificada.
