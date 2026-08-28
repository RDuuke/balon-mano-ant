# Estrategia de pruebas

## Gate del Lote 1

1. Contratos: `./tests/contract/Test-Lote1.ps1 -Task 1.1` (repita por tarea).
2. Compose: `docker compose config --quiet`.
3. Smoke: ejecute los cuatro scripts de `tests/smoke/` con servicios saludables.
4. PHP: `composer test`, integracion con el runtime WordPress, `composer lint` y `composer analyse` dentro de los contenedores documentados por `scripts/gate.ps1`.
5. Navegador portable: `./scripts/browser-gate.ps1 -Task playwright` ejecuta Node 22.13.1 y pnpm 11.17.0 aislados en Docker, sin depender del Node del host.
6. Lighthouse y SEO quedan diferidos a un cambio futuro y no bloquean el gate actual. La especificaciÃ³n vigente conserva esos umbrales, por lo que sus escenarios no deben declararse conformes hasta tramitar el cambio formal correspondiente.

`./scripts/gate.ps1 -IncludeBrowser` registra version, salida y `ExitCode` en `artifacts/`, que esta ignorado. Una herramienta ausente queda **NO EJECUTADA** y hace fallar el gate.

## Validacion desde entorno limpio

En un clon nuevo, copie `.env.example`, valide, levante Docker, ejecute bootstrap dos veces y luego todos los gates. La persistencia se prueba reiniciando sin borrar volumenes. Un reset requiere confirmacion y no forma parte del gate normal.

La auditoria automatica axe complementa, pero no reemplaza, la revision manual WCAG 2.2 AA. Los viewports base son 320, 768, 1024 y 1440 px.

## CI, evidencia y rollback

`.github/workflows/quality.yml` parte de una configuración ficticia, instala dependencias desde lockfiles, inicializa WordPress y ejecuta el gate completo. Un fallo o paso no ejecutado bloquea el trabajo; los reportes de `artifacts/` se conservan incluso cuando falla.

El gate construye `docker/coverage/Dockerfile`, fijado sobre el runtime WordPress CLI, ejecuta la integración con PCOV 1.0.12 y conserva `artifacts/coverage/clover.xml`. `scripts/coverage.ps1` falla si la cobertura del PHP propio del tema y plugin es inferior a 80%. Lighthouse audita Inicio, Nosotros, Actualidad y Selecciones y exige Performance >=85 y Accessibility, Best Practices y SEO >=90.

Las comprobaciones axe, de desborde y movimiento reducido se ejecutan en 320, 768, 1024 y 1440 px. Siguen sin sustituir la auditoría manual WCAG 2.2 AA de teclado, orden y visibilidad del foco, nombres/roles/estados, reflow y zoom, contraste no detectable automáticamente, mensajes de error y tecnologías de asistencia.

Antes de una reversión, preserve base de datos, uploads y reportes. El rollback se considera aprobado solo cuando el entorno restaurado vuelve a superar los mismos gates. Consulte [trazabilidad](traceability.md) para relacionar cada requisito con su prueba.
