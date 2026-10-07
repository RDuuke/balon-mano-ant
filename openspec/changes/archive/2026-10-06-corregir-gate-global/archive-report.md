# Informe ARCHIVE: corregir-gate-global

- status: ok
- Fecha: 2026-10-06T20:23:09.604513-05:00 (America/Bogota).
- skill_resolution: injected.
- Guard: 118 hashes de entradas actuales coinciden, comprobaciones requeridas PASS y sin pendientes. [Guard y hashes](archive-guard.json).
- [VERIFY final](verify-report.md); [resultado final](verify-20261006-resumed/final-result.json); [entradas verificadas](verify-20261006-resumed/final-inputs.json).
- Browser focal 18/18 PASS, cero fallos/omitidas/flaky. Unitarias, PHPCS y PHPStan PASS; integraci?n 176 pruebas/1895 aserciones; cobertura global 82,74 % ?80 %.
- Autorizaci?n SPEC-FIX: ?S?, acotar y detener la suite completa?. Excepci?n espec?fica de corregir-gate-global preservada, sin extenderla a otros cambios; suite cancelada no declarada PASS.
- Siete specs sincronizadas; requisitos no mencionados, garant?as adicionales y escenarios previos no sustituidos preservados.
- Restauraci?n oficial PASS; home/siteurl localhost:8080, cero PDF temporales y uploads id?nticos al backup seg?n VERIFY. Can?nico y metadata preservados; no cambios persistidos ni sincronizaci?n nueva en ARCHIVE.
- TDD advisory: 11 tareas sin encabezado individual RED/GREEN; observaci?n no bloqueante preservada, sin inventar resultados.
- Selecciones y Detalle conservan sus estados y pendientes; selecciones-backend sin cambios.
- Todos los artefactos hist?ricos preservados byte a byte tras mover. Rutas originales dentro de evidencias representan procedencia hist?rica; los enlaces relativos de este informe apuntan a sus ubicaciones vigentes.
- Sin pruebas nuevas, Docker, commits, despliegue ni modificaci?n de skills.
- Reintento: s?; primer intento sin mutaciones fall? por ausencia de tzdata. Timestamp resuelto con UTC?05:00 vigente de Bogot?.

## Specs sincronizadas

- `actualidad`: 1 requisitos a?adidos, 1 modificados.
- `calidad-seguridad`: 3 requisitos a?adidos, 0 modificados.
- `document-admin-experience`: 0 requisitos a?adidos, 1 modificados.
- `documentos-contacto`: 2 requisitos a?adidos, 0 modificados.
- `experiencia-publica`: 1 requisitos a?adidos, 1 modificados.
- `fixtures`: 1 requisitos a?adidos, 0 modificados.
- `home-news`: 0 requisitos a?adidos, 1 modificados.
