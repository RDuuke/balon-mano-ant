# Informe ARCHIVE: selecciones-backend

- status: ok
- Fecha: 2026-10-06T20:26:59.9577215-05:00 (America/Bogota).
- skill_resolution: injected.
- Guard: 114 hashes de inputs y 8 hashes de evidencia vigente coinciden. [Guard](archive-guard.json).
- [VERIFY final](verify-report.md), [resultado](verify-result.json), [evidencia reutilizada](verify-reuse-evidence.json).
- 12/12 escenarios COMPLIANT, 10/10 tareas completas, sin comprobaciones pendientes.
- Evidencia acumulada aceptada: integración 176 pruebas/1895 aserciones; cobertura global 82,74 %; unitarias, PHPCS y PHPStan PASS. [Integración del gate archivado](../2026-10-06-corregir-gate-global/verify-20261006-resumed/integration-coverage.log).
- Handoff del gate focal aprobado resuelve la obligación histórica de gate completo; no se atribuye PASS a la suite cancelada ni 18 pruebas propias de Selecciones.
- Cuatro requisitos de Selecciones añadidos a arquitectura-cms; contenido previo íntegro preservado.
- Restore oficial PASS y canónico preservado según evidencia. Esta fase no modifica runtime, DB, uploads, usuarios ni paquete canónico y no requiere nueva sincronización.
- TDD advisory no bloqueante: 1.1, 3.1, 3.2, 3.3, 4.1, 4.2. Skill testing opcional ausente; no bloqueante.
- Artefactos históricos conservados byte a byte al mover; sus rutas originales describen procedencia. Enlaces relativos de este informe vigentes.
- Archivo corregir-gate-global preservado. Sin pruebas, gate, Docker, commits, push, despliegue ni cambios en skills.
