# Tareas: Backend de fichas de Selecciones

## Coherencia y alcance
- Especificación y diseño coherentes: los 12 escenarios mantienen permisos aditivos, clasificación extensible, texto saneado y privacidad REST nativa.
- Backend de fichas existente; presentación pendiente de `selecciones-frontend`. Preservar cambios ajenos.

## Fase 1: Preparación y RED
- [x] 1.1 Revisar el procedimiento de `scripts/content-sync.ps1`, obtener estado y respaldo oficial antes de modificar roles; registrar versión/hash iniciales en `openspec/changes/selecciones-backend/apply-progress.md`.
- [x] 1.2 En `tests/php/DomainModelTest.php`, crear regresión con versión vigente y capacidades de Selecciones retiradas de Editor/Administrador; ejecutar RED auténtico, preservación, idempotencia y restricciones del Suscriptor; restaurar exactamente roles/opciones/usuario.

## Fase 2: Reparación, GREEN y REFACTOR
- [x] 2.1 En `wp-content/plugins/labm-core/includes/class-labm-domain.php`, reparar exclusivamente capacidades faltantes de Selecciones antes del retorno por versión; conservar permisos compartidos, marcador y omisión segura de CPT/roles inexistentes; ejecutar GREEN de 1.2.
- [x] 2.2 Revisar el cambio en `wp-content/plugins/labm-core/includes/class-labm-domain.php` conforme a WordPress; ejecutar nuevamente la regresión tras REFACTOR o documentar que no necesita refactorización, con RED/GREEN de 2.1 en `openspec/changes/selecciones-backend/apply-progress.md`.

## Fase 3: Contrato REST y calidad focal
- [x] 3.1 En `tests/php/DomainModelTest.php`, comprobar datos editoriales, imagen y términos REST, clasificación vacía, términos nuevos y rechazo anónimo; limpiar únicamente datos creados por pruebas.
- [x] 3.2 En `tests/php/DomainModelTest.php`, comprobar escritura/lectura autorizada de `labm_modalidad_detalle`, saneamiento, vaciado y rechazo sin alterar valor previo; restaurar usuario y datos temporales.
- [x] 3.3 En `tests/php/DomainModelTest.php`, comprobar colección solo publicada, colección vacía y rechazo de detalle privado/borrador sin datos; acotar consultas a IDs temporales y limpiar sus fixtures.
- [x] 3.4 Ejecutar pruebas focales para `tests/php/DomainModelTest.php`, análisis estático/formato aplicables y revisión LF del alcance; registrar comandos, resultados y pruebas existentes que ya pasan en `openspec/changes/selecciones-backend/apply-progress.md`, sin inventar RED para contratos previamente correctos.

## Fase 4: Persistencia y gate de revisión
- [x] 4.1 Tras limpiar pruebas, aplicar reparación al runtime y ejecutar `scripts/content-sync.ps1 -Action Push`; verificar `content-sync/canonical.zip` y `content-sync/latest.json`, versión/SHA-256 y exclusión de credenciales, `wp_users` y `wp_usermeta`; registrar evidencia.
- [x] 4.2 Registrar diff concreto del alcance, archivos, líneas y binarios en `openspec/changes/selecciones-backend/apply-progress.md`; comprobar LF y detener APPLY ante `max_diff_lines=0`, presentando revisión antes de VERIFY.

## Validación posterior autorizada
- La fase VERIFY ejecutará `scripts/gate.ps1 -IncludeBrowser`, cobertura mínima 80 %, PHPCS/PHPStan y suite Playwright; registrará resultados en `openspec/changes/selecciones-backend/verify-report.md` después de resolver el gate.
