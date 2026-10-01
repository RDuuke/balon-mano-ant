# FIX intento 1: selecciones-backend

status: warning

## Resultado APPLY correctivo

Selecciones conserva sus 14 pruebas y 431 aserciones aprobadas. Se reparó la causa del fallo de carga de medios: las pruebas PHP ejecutadas como root creaban `uploads/2026/10` con propietario root y permisos 755, impidiendo escribir al proceso web uid 33. Los runners de cobertura y de integración sin cobertura ahora ejecutan PHP con `--user 33:33`. Se corrigió el propietario de la carpeta mensual existente y se comprobó una carga PDF real, limpiando inmediatamente el archivo.

No se ejecutó el gate global en APPLY. La siguiente VERIFY debe ejecutar el gate completo; los contratos globales ajenos identificados siguen pendientes.

## Estado inicial

- STATUS read-only: VERIFY; 10 tareas completas, cero pendientes, dependencias presentes, aprobación previa vigente, informe failed; FIX autorizado 1/2.
- Skill APPLY y contrato de persistencia locales leídos; reglas de proyecto inyectadas por el orquestador.
- Se preservaron diseño/exportaciones y el flujo pausado detalle-actualidad.

## Evidencia RED/GREEN

- RED real antes de editar runners: `docker compose exec -T --user 33:33 wordpress test -w /var/www/html/wp-content/uploads/2026/10`, salida 1.
- Diagnóstico `stat`: uploads y 2026 www-data:www-data 755; 2026/10 root:root 755. `docker/coverage/Dockerfile` conserva USER root para construir PCOV; el runner no especificaba usuario al ejecutar PHP sobre uploads compartido.
- GREEN: mismo `test -w`, salida 0 tras reparación acotada de propietario.
- GREEN carga real: `artifacts/fix-selecciones-upload.php` ejecutado como uid 33 contra WordPress devuelve `exists=yes owner=33 cleaned=yes`, salida 0. El fichero temporal no se exportó.
- GREEN focal: PHPUnit con `--user 33:33 --filter DomainModelTest`, 14 pruebas, 431 aserciones, salida 0.
- Parse sintáctico PowerShell de coverage.ps1/gate.ps1 sin errores.

## Persistencia consolidada

- Antes de reemplazar runtime: respaldo oficial `.content-sync/backups/backup-manual-20261001T194334048Z-rduuqe-RDUUQE.zip`, SHA-256 `e356ed353f205c5bbdc75a6219c07c156c8a3dd2c54dc08adce087b5141bb655`.
- Auditoría de todos los archivos/hashes del canónico y respaldo, diff SQL por tablas/columnas en `artifacts/fix-selecciones-audit.json`. Runtime añadía 45 posts reconocibles de pruebas: Acta valida/segura, Adjuntos paginados, revisiones y autodrafts; ocho eliminaciones correspondían a demos recreados/autodraft. Sin cambio de contenido de Selecciones. Único cambio post_content: galería de noticia demo recargada con medios de pruebas. Modificaciones theme_mods solo cambian timestamp de sidebars; resto de opciones caché/cron/transitorios y marcador de sincronización.
- `Pull` omitiría limpieza por versiones iguales. Se utilizó proceso oficial `Restore -Backup content-sync/canonical.zip -ConfirmReplace`, con respaldo adicional automático `backup-pre-restore-20261001T194633526Z-rduuqe-RDUUQE.zip`. No se reemplazaron tablas de cuentas. Se conservó el paquete aprobado que ya contiene reparación de permisos.
- Después de carga real y limpieza, `scripts/content-sync.ps1 -Action Push` completo y Status confirma versiones local/canónica iguales.
- Nueva versión: `20261001T194755441Z-rduuqe-RDUUQE`.
- SHA-256: `ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e`.
- Paquete: 95928698 bytes; 152 archivos con hash/tamaño válidos; manifest/pointer/versión concordantes.
- SQL sin CREATE/INSERT de wp_users/wp_usermeta, sin fixtures Selección temporales/selection-test ni archivo PDF de comprobación. ZIP sin .env/wp-config.php y SQL sin claves serializadas password/secret/api_key. El manifest excluye wp_users/wp_usermeta.

## Agrupación de bloqueos globales y alcance

- Nosotros: HEAD ya renderiza cuatro cards y oculta con `hidden` las ajenas al grupo. La prueba PHP cuenta atributos del DOM completo y espera dos; es discrepancia del contrato de prueba, no evidencia de filtrado roto. No se alteró producción ni assertion.
- Detalle Actualidad: plantilla HEAD usa labm_actualidad_body y hero; pruebas exigen wp:post-content y labm_actualidad_detalle. Reconciliación corresponde al flujo pausado detalle-actualidad. No se revirtieron cambios ni se alteraron assertions.
- Portada: tests antiguos esperan fallback selección; la prueba de fixture actual espera antioquia, como la implementación. Documentar/reconciliar contrato en su dominio antes de cambiar assertions.
- Upload fixture: filtra globalmente upload_dir y espera fallo específicamente arco-comun, aunque la colección importa antes hero-seleccion. Prueba acoplada al orden de importación; sin modificar en este intento.
- Documentos: fallo min-height ya reproducido en baseline HEAD durante VERIFY. Permanece fuera del alcance Selecciones.
- PHPCS/PHPStan: errores de documentos-contact, fixtures-command y functions.php fuera del diff productivo de Selecciones; no se maquilló el gate ni se suprimieron reglas.
- Cobertura previa 73.47%: Clover registra domain 52/248, SMTP 56/156, documents 310/396, admin 251/344. Bloque nuevo Selecciones 8/9 = 88.89%, y déficit global requiere campañas de pruebas de ramas existentes. No se amplió alcance indiscriminadamente ni se cambió el umbral.

## Diff y siguiente paso

- Cambios correctivos en runners: coverage.ps1 +1/-1 y gate.ps1 +1/-1, cuatro líneas revisables; pointer +5/-5 y un ZIP binario.
- Sin nuevo cambio de especificación de Selecciones; 10/10 tareas originales completas.
- Revisión previamente aprobada conservada; review_budget configuración max_diff_lines 0, sin rutas sensibles.
- VERIFY completa pendiente; se prevé que los bloqueos globales ajenos permanezcan. ARCHIVE continúa prohibido mientras VERIFY failed.
