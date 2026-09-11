# Informe de verificación: CI/CD y migración inicial a Hostinger

## Resumen ejecutivo

**Estado: warning.** Toda la verificación local aplicable aprobó: fixtures aislados, contrato, higiene, sintaxis de PowerShell, YAML, `git diff --check` y finales de línea LF. La validación de extremo a extremo continúa pendiente en GitHub Actions como tarea externa 4.3; no se usaron secretos reales, Hostinger, FTP ni MySQL remoto.

La excepción explícita de TDD solicitada por el usuario se conserva: este cambio es configuración de repositorio, por lo que se usaron fixtures y comprobaciones contractuales en lugar de una fase TDD.

## Comprobaciones ejecutadas

- `tests/contract/Test-HostingerBootstrapFixtures.ps1`: PASS. Cubre secreto ausente redactado, puntero inexistente, ZIP con traversal, checksum alterado, manifiesto válido, prefijo simulado, marcador idempotente y los dos guards de `Finalize`.
- `tests/contract/Test-HostingerCicd.ps1`: PASS. Comprueba secretos, rutas FTP, cadena de jobs, smoke y la invocación de fixtures.
- `scripts/test-repository-hygiene.ps1`: PASS.
- Analizador de PowerShell para `scripts/bootstrap-hostinger-content.ps1`: PASS.
- `yamllint` en contenedor de solo lectura, con reglas de estilo no funcionales desactivadas: PASS.
- `git diff --check` y revisión de CRLF para todos los archivos modificados del cambio: PASS.

Los fixtures usan un directorio temporal, valores de proceso ficticios, un cliente MySQL simulado y un índice REST simulado. Se eliminan al finalizar y no contactan servicios externos.

## Cobertura y límites

| Dominio | Escenario | Estado | Evidencia |
|---|---|---|---|
| Despliegue | Publicación solo tras calidad y en `main` | COMPLIANT | Contrato estático |
| Migración | Puntero, ZIP, manifiesto y checksum inválidos | COMPLIANT | Fixtures aislados |
| Migración | Marcador existente omite la migración | COMPLIANT | Fixture MySQL simulado |
| Migración | `Finalize` sin uploads o sin importación | COMPLIANT | Fixtures aislados |
| Migración | Respaldo, importación y finalización satisfactorios | PARTIAL | Implementado; pendiente de ejecución remota |
| Secretos y smoke | Secreto ausente e informe redactado | COMPLIANT | Fixture aislado |
| Secretos y smoke | Login contra WordPress remoto | UNTESTED | Tarea 4.3 |

## Pendiente externo

- **4.3 (CRITICAL):** ejecutar el workflow desde un `push` a `main` tras confirmar conectividad MySQL desde GitHub Actions.
- Verificar las transferencias FTP del tema, plugin y uploads; inspeccionar el artefacto privado de respaldo e informe; comprobar el smoke autenticado.
- Repetir el despliegue y confirmar que `should_migrate=false` evita la importación y la transferencia de uploads.

## Riesgos residuales

- Hostinger puede bloquear el acceso MySQL de runners de GitHub o exponer una versión de WordPress incompatible.
- La primera importación modifica la base de datos remota; el respaldo debe comprobarse antes de reintentar un fallo.
- La autenticación real puede diferir si el usuario `WP_USER` exige MFA, CAPTCHA, restricciones de IP o un flujo de login adicional.
