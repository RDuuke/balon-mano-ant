# Tasks: CI/CD y migración inicial a Hostinger

## Fase 1: Validación de infraestructura
- [x] 1.1 Crear `tests/contract/Test-HostingerCicd.ps1` que valide script, secretos obligatorios, rutas FTP, orden de jobs y smoke sin exposición.
- [x] 1.2 Crear fixtures temporales para ZIP seguro, checksum, manifiesto, prefijo y marcador; comprobar el checksum manipulado y las salvaguardas de `Finalize` sin acceso a Hostinger.

## Fase 2: Bootstrap
- [x] 2.1 Crear `scripts/bootstrap-hostinger-content.ps1` con fases `Preflight|Import|Finalize`, lectura exclusiva de variables de entorno y errores redactados; validar su interfaz.
- [x] 2.2 Implementar preflight: validar ZIP/puntero/checksums/rutas, versión WordPress, secretos, MySQL, prefijo único y marcador; emitir `should_migrate` y `content_version` a `GITHUB_OUTPUT`.
- [x] 2.3 Implementar Import: respaldo MySQL comprimido con credenciales fuera de argumentos, importación y renombrado de tablas sin usuarios, más informe redactado; validar con fixtures.
- [x] 2.4 Implementar Finalize: reemplazo URL serializado, `home`/`siteurl`, caché, marcador posterior y preparación de `uploads`; validar marcador, idempotencia y sus salvaguardas previas.
- [x] 2.5 Refactorizar el script sin ampliar su interfaz, eliminando temporales y preservando el informe y respaldo para artefactos.

## Fase 3: Integración
- [x] 3.1 Modificar `.github/workflows/quality.yml`: `quality → deploy-code → bootstrap-content → smoke`, solo `push` a `main`, concurrencia serial y dos FTP sin limpieza.
- [x] 3.2 Conectar fases, outputs, FTP de uploads condicionado y artefactos privados de respaldo/informe; inyectar los nueve secretos sin imprimirlos.
- [x] 3.3 Implementar smoke autenticado contra `wp-login.php` y `wp-admin/profile.php` con cookie temporal, sin trazas ni acciones administrativas.
- [x] 3.4 Ejecutar `tests/contract/Test-HostingerCicd.ps1`, corregir workflow/script y validar YAML sin desplegar.

## Fase 4: Documentación y verificación
- [x] 4.1 Crear `docs/ci-cd-hostinger.md` con provisionamiento, secretos, primera ejecución, artefacto de recuperación y cambio futuro a HTTPS; validar la documentación con el contrato.
- [x] 4.2 Ejecutar las pruebas de fixtures y contractual, `scripts/test-repository-hygiene.ps1`, `yamllint` y los gates rápidos aplicables; verificar LF en todos los archivos modificados.
- [ ] 4.3 En GitHub Actions, comprobar primer despliegue, artefactos y smoke; repetir el despliegue para evidenciar que el marcador omite la migración.
