# FIX intento 2: salida Clover

status: warning

## Resultado

Se corrigió exclusivamente `scripts/coverage.ps1`: conserva el Clover anterior con nombre histórico único, escribe un informe nuevo con GUID como uid33 y lo promueve a clover.xml desde PowerShell. Si PHPUnit no genera el archivo de esta ejecución, el runner falla sin reutilizar evidencia antigua. Si la suite falla pero genera Clover, se conserva el informe actual y se informa su métrica antes de fallar. Umbral 80% y usuario 33:33 permanecen vigentes.

## STATUS previo y alcance

VERIFY tras FIX1 failed; 10/10 tareas completas, dependencias presentes y aprobación previa vigente. Skill APPLY local releída, contrato y reglas de proyecto conservados. FIX2 autorizado exclusivamente para salida de cobertura; no se modificaron frontend, assertions, PHPCS/PHPStan ni umbrales.

## RED/GREEN real

- RED antes de editar: contenedor uid33 ejecuta `test -w /work/artifacts/coverage/clover.xml`, salida 1; `ls -ln` confirma root:root 644, 133976 bytes, fecha Oct 1 01:12.
- GREEN: runner real cargado en memoria, añadiendo únicamente `--filter DomainModelTest` a PHPUnit para comprobación focal, sin modificar selección de tests del script versionado. 14 pruebas/431 aserciones aprobadas, PCOV 1.0.12 y generación Clover `done`, sin error de escritura.
- Clover nuevo: generated `1790885360`, modificación `2026-10-01T20:09:21.167775Z`, 31/2775 líneas = 1.12% focal. La cobertura focal limitada dispara el umbral intacto 80%; salida 1 esperada del runner por umbral, no por escritura ni fallos de pruebas.
- Histórico conservado en artifacts/coverage/clover.previous.<GUID>.xml; no confundir la métrica focal con cobertura global.
- Parser sintáctico PowerShell aprobado; LF verificado en alcance modificado.

## Runtime y canónico protegido

- Backup oficial posterior focal: backup-manual-20261001T200949771Z-rduuqe-RDUUQE.zip, SHA256 `231bc84230e19a24f908cf0c52d873a0a22d98f4a0a0f6b110b4b88577be4e56`.
- Auditoría artifacts/fix-selecciones-fix2-audit.json: cero posts nuevos/eliminados, solo post_date/post_modified del borrador de prueba preexistente ID20 y cachés/opciones temporales. Sin cambios editoriales ni fichas Selecciones.
- Restore oficial del paquete canónico autorizado y respaldado para eliminar modificaciones temporales. No se exportaron fixtures ni se reemplazaron cuentas.
- Canónico preservado, sin nuevo Push: versión `20261001T194755441Z-rduuqe-RDUUQE`, SHA256 `ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e`, 152 archivos.

## Siguiente paso y límites

VERIFY debe ejecutar gate completo para producir cobertura global actual. Permanecen siete fallos PHP, PHPCS/PHPStan y contratos frontend de VERIFY previo; este FIX no pretende resolver dominios ajenos. Es el segundo y último intento automático: no iniciar FIX3. ARCHIVE prohibido mientras VERIFY failed. Aprobación previa conservada.
