# Avance APPLY: selecciones-backend

## Tarea 1.1 — Respaldo oficial
- Baseline: fuentes, pruebas y content-sync limpios al comenzar APPLY. Se preservaron exports Pencil y detalle-actualidad apartado. Artefactos previos de planificación medidos contra su contenido al iniciar APPLY.
- Docker detenido: se inició `docker compose up -d --wait`.
- `scripts/content-sync.ps1 -Action Status`: local/canónico `20260926T164029330Z-User-DESKTOP-UOCJQQ8`.
- SHA-256 inicial: `5e5276232669147ffca009c36ac07e7ea197d88fec24b457245743cfbbe421e5`.
- `scripts/content-sync.ps1 -Action Backup`: `.content-sync/backups/backup-manual-20261001T005615954Z-rduuqe-RDUUQE.zip` antes de mutar roles.
- SHA-256 respaldo: `de3558d1e25bc3a79a6ac57871322b23bcc6c401c75e4d518115e71910b09cee`.

## Tarea 1.2 — Regresión de permisos
- **RED:** test `tests/php/DomainModelTest.php::test_current_version_repairs_selection_capabilities_additively` falla con: `administrator:edit_labm_seleccion; Failed asserting that false is true` (1 prueba, 1 aserción, salida 1).
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php`; test pasa (1 prueba, 314 aserciones, salida 0).
- **TRIANGULATE:** preservación de capacidades ajenas, marcador vigente, restricciones de Suscriptor, rechazo REST de creación e idempotencia sin escrituras.
- **REFACTOR:** restauración exacta de roles/opción/usuario en finally y limpieza de listener.

## Tarea 2.1 — Reparación aditiva
- **RED:** regresión auténtica de 1.2 ejecutada antes de escribir código productivo.
- **GREEN:** concede solo capacidades faltantes de Selecciones a Editor/Administrador antes del guard; conserva marcador y migración global.

## Tarea 2.2 — Revisión
- **RED/GREEN:** ciclo de 1.2/2.1 y suite final verde.
- **REFACTOR:** bloque mínimo de 15 líneas, sin helper adicional necesario; omisión segura de CPT/roles inexistentes y conservación de capacidades compartidas. PHPCS/PHPStan focal pasan.

## Tareas 3.1, 3.2 y 3.3 — Contratos existentes
- REST editorial con Editor, términos nuevos por Administrador, imagen y datos públicos, clasificación vacía y rechazo anónimo sin alterar términos/asignaciones.
- Metadato: lectura pública, escritura autorizada, eliminación de HTML/script, vaciado y rechazo por Suscriptor/anónimo preservando valor.
- Privacidad: colección acotada a IDs temporales, solo publicado, colección vacía y rechazo directo de privados/borradores sin campos editoriales.
- No se inventó RED para contratos previamente correctos. Un fallo inicial de imagen era del fixture incompleto; se añadieron metadatos sin escribir archivos físicos.
- Limpieza de posts, adjuntos, términos y usuarios propios mediante tearDown y restauración de usuario. El borrador de la prueba antigua ya existía en el backup.

## Tarea 3.4 — Calidad focal
- PHPUnit focal final: 14 pruebas, 431 aserciones, salida 0.
- PHPCS focal: salida 0. PHPStan focal: salida 0, sin errores.
- LF verificado para todos los textos modificados por APPLY. ZIP excluido por ser binario.
- Comandos (montaje `${PWD}:/work:ro`, volumen `labm_composer_vendor:/work/vendor`, directorio `/work`, servicio WordPress con `WP_TESTS_RUNTIME_ROOT=/var/www/html`): `php vendor/bin/phpunit -c phpunit.integration.xml.dist --filter test_current_version_repairs_selection_capabilities_additively` (RED/GREEN), luego `--filter DomainModelTest` (focal final).
- Docker composer:2.8 con montaje `${PWD}:/app` y vendor: `composer exec phpcs -- wp-content/plugins/labm-core/includes/class-labm-domain.php`; `composer exec phpstan -- analyse wp-content/plugins/labm-core/includes/class-labm-domain.php --no-progress`.
- Gate completo/cobertura/navegador pendientes de VERIFY después de revisión.

## Tarea 4.1 — Persistencia
- Reparación runtime: `docker compose run --rm -T wp-cli eval "labm_core_ensure_capabilities(); echo LABM_CORE_CAPABILITIES_VERSION;"`; marcador `6`.
- `scripts/content-sync.ps1 -Action Push` tras limpiar fixtures.
- Versión final `20261001T010421989Z-rduuqe-RDUUQE`; SHA-256 `9ea0eb693bbcef3899f7d0342dd3985add5acc70c755cb92f84cf7694f563096`.
- Paquete 95928737 bytes; hash ZIP, versión manifest/pointer y tamaños/hashes de 152 archivos validados.
- Manifest excluye `wp_users`/`wp_usermeta`; SQL sin esas tablas/datos ni fixtures nuevos, sin valores serializados de password/secret/api_key. ZIP sin .env/wp-config.php; SMTP usa secreto externo a WordPress.

## Tarea 4.2 — Diff propio y gate
- `max_diff_lines=0` desactiva por defecto el gate en la skill; aquí se respeta el gate explícito del diseño/tarea 4.2/orquestador antes de VERIFY.
- Fuente productiva: +15/-0. Pruebas: +180/-0. Pointer canónico: +5/-5. Un ZIP binario actualizado.
- Rutas sensibles configuradas: ninguna. Persistencia modificada: canonical.zip/latest.json.
- Pendiente medición final de artefactos APPLY.

## Tarea FIX-1 — Runtime de cargas y runner de pruebas

- **RED:** test operativo `docker compose exec -T --user 33:33 wordpress test -w /var/www/html/wp-content/uploads/2026/10` falla con: `salida 1; propietario root:root, permisos 755`.
- **GREEN:** implementación en `scripts/coverage.ps1` y `scripts/gate.ps1`, ejecutando PHP como uid 33; reparación acotada owner mensual; mismo test salida 0 y carga real `artifacts/fix-selecciones-upload.php` pasa con propietario 33 y archivo limpiado.
- **TRIANGULATE:** suite focal Selecciones 14 pruebas, 431 aserciones, salida 0 con uid 33.
- Consolidación oficial y riesgos globales documentados en `fix-report-1.md`. Aprobación previa conservada; no se ejecuta gate global en APPLY.

## Tarea FIX-2 — Clover nuevo y escribible

- **RED:** test operativo `uid33 test -w clover.xml` falla con: `salida 1, Clover root:root644` antes de modificar código.
- **GREEN:** implementación en `scripts/coverage.ps1`: archivo único por ejecución, histórico separado y promoción desde host; runner focal genera Clover nuevo timestamp1790885360 sin error de escritura, 14 pruebas/431 aserciones aprobadas.
- **TRIANGULATE:** Clover registra31/2775 líneas; el umbral80% intacto rechaza1.12% focal. Ausencia de informe nuevo provoca fallo explícito sin reutilizar histórico.
- Auditoría/restauración oficial y límites documentados en `fix-report-2.md`; aprobación previa conservada, siguiente VERIFY completa, sin FIX3 automático.
