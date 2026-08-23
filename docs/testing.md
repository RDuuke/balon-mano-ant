# Estrategia de pruebas

## Gate del Lote 1

1. Contratos: `./tests/contract/Test-Lote1.ps1 -Task 1.1` (repita por tarea).
2. Compose: `docker compose config --quiet`.
3. Smoke: ejecute los cuatro scripts de `tests/smoke/` con servicios saludables.
4. PHP: `composer test`, integracion con el runtime WordPress, `composer lint` y `composer analyse` dentro de los contenedores documentados por `scripts/gate.ps1`.
5. Navegador: `pnpm install --frozen-lockfile`, `pnpm exec playwright install chromium`, `pnpm run test:e2e`.
6. Baseline: `pnpm run lighthouse`; los umbrales finales quedan reservados para el sitio completo.

`./scripts/gate.ps1 -IncludeBrowser` registra version, salida y `ExitCode` en `artifacts/`, que esta ignorado. Una herramienta ausente queda **NO EJECUTADA** y hace fallar el gate.

## Validacion desde entorno limpio

En un clon nuevo, copie `.env.example`, valide, levante Docker, ejecute bootstrap dos veces y luego todos los gates. La persistencia se prueba reiniciando sin borrar volumenes. Un reset requiere confirmacion y no forma parte del gate normal.

La auditoria automatica axe complementa, pero no reemplaza, la revision manual WCAG 2.2 AA. Los viewports base son 320, 768, 1024 y 1440 px.
