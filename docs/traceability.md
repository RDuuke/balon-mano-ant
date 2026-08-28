# Trazabilidad de requisitos y pruebas

Esta matriz relaciona cada dominio OpenSpec con evidencia automatizada. Los reportes de ejecución se generan en `artifacts/` y no se versionan.

| Dominio | Pruebas principales | Evidencia esperada |
|---|---|---|
| `entorno-desarrollo` | `tests/contract/Test-Lote1.ps1`, `tests/smoke/Test-Database.ps1`, `tests/smoke/Test-Persistence.ps1` | Compose válido, servicios saludables y datos persistentes |
| `arquitectura-cms` | `tests/php/WordPressRuntimeTest.php`, `tests/php/DomainModelTest.php`, `tests/smoke/Test-Components.ps1` | Tema y plugin independientes, permisos y metadatos persistentes |
| `experiencia-publica` | `tests/php/PublicExperienceTest.php`, `tests/php/FrontendTokensTest.php`, `tests/e2e/public-experience.spec.ts` | Rutas, filtros, privacidad, responsive, teclado y axe |
| `documentos-contacto` | `tests/php/DocumentContactTest.php` | PDF validado, catálogo seguro, adjuntos compartidos y correo simulado |
| `calidad-seguridad` | `scripts/gate.ps1`, WPCS, PHPStan, Playwright y Lighthouse | Gate completo sin pasos omitidos y reportes conservados |

## Límites de la evidencia

- La auditoría automática no reemplaza la revisión manual WCAG 2.2 AA.
- La matriz productiva WP/PHP/DB, el hosting, SMTP y los PDF reales requieren decisiones y autorización externas.
- Ningún resultado local autoriza publicación, despliegue, commit ni push.

## Rollback

El rollback conserva primero una copia de base de datos y uploads. Los cambios de código se revierten mediante una versión anterior revisada; los volúmenes solo se eliminan tras confirmación humana explícita. Si una prueba de migración falla, se detiene la promoción y se restaura el respaldo antes de reintentar.

