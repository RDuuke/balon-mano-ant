# Verificación: experiencia administrativa de documentos PDF

status: ok

Último cierre: 2026-09-17 23:24. E1–E5 ejecutados localmente por el usuario, todos PASS; checklist21/21 completo y matriz33 COMPLIANT. No hay fallo funcional ni tarea pendiente. Los seis gaps de formato se reconciliaron con referencias individuales a resultados RED/GREEN agrupados ya registrados, sin inventar ejecuciones. status ok; archive_ready true. No se ejecutaron comandos de pruebas por el agente en este cierre. Las secciones de ejecución inicial son historia, no pendientes actuales.

## Ejecución inicial (histórica, defecto posteriormente corregido)

Se inició Docker Desktop y los servicios existentes con `docker compose up -d --wait --wait-timeout 60`. No se repitió ninguna suite verde. Se ejecutó `verify-focused.php` contra WordPress real mediante `docker compose exec -T wordpress php -d max_execution_time=30`, enviando el script por stdin. Duración de la ejecución: 0.702 s; exit 1. Seis comprobaciones pasan antes del fallo, incluidos límite WordPress real de 5 MB y rechazo de PDF real de 6 MB. `finally` retiró únicamente sus fixtures exclusivos. No hubo ciclo prolongado.

La comprobación siguiente configura administrador autorizado, `REQUEST_METHOD=POST`, `action=editpost` y nonce documental inválido; llama `wp_update_post(..., true)` y espera `labm_document_nonce_invalid` sin cambiar el título. Falla. El código explica el defecto: `class-labm-document-admin.php:551` aplica `sanitize_key` al método, produciendo `post`, mientras la condición siguiente compara contra `POST`. El adaptador retorna antes de comprobar nonce, validar campos y preparar persistencia clásica.

Esta reproducción utiliza la API interna y no atraviesa el formulario HTTP ni su protección estándar `check_admin_referer`. Por ello **no demuestra una vulnerabilidad HTTP/CSRF**. Demuestra que la rama de validación documental propia del adaptador clásico no se activa. Se requiere una prueba directa del adaptador con usuario autorizado, nonce inválido y método POST, más un guardado válido equivalente. El test E2E clásico previo solo inspecciona controles.

Las comprobaciones de gestión REST de términos posteriores en el script no se ejecutaron, porque el fallo detuvo la ejecución. No se atribuye evidencia verde a esos pasos.

## Matriz de Validación

Referencia PHP: `tests/php/DocumentContactTest.php`; referencia navegador: `tests/e2e/document-admin.spec.ts`. COMPLIANT reutiliza tests previamente ejecutados y documentados en apply-progress; PARTIAL significa que las aserciones no cubren todo el escenario.

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| Experiencia | Selección satisfactoria | COMPLIANT | A1.1 | — |
| Experiencia | Reemplazo y retiro | COMPLIANT | E1 usuario PASS: sustituto persistido HTTP, retiro sin borrar | — |
| Experiencia | Fallo de biblioteca | COMPLIANT | E3 usuario PASS: upload real abortado y asociación intacta | — |
| Experiencia | Archivo dentro del límite | COMPLIANT | A2.1 + accepts_authentic_pdf | — |
| Experiencia | Límite menor de WordPress | COMPLIANT | A2.2 + focal real 5 MB | — |
| Experiencia | Archivo sobredimensionado | COMPLIANT | E4 usuario PASS + servidor real6MB | — |
| Experiencia | Flujo completo por teclado | COMPLIANT | E5 usuario PASS: Space/Enter selección/retiro/error/corrección, foco; A3.1 | — |
| Experiencia | Ayuda contextual | COMPLIANT | E2 usuario PASS: descripciones accesibles ambos editores | — |
| Experiencia | Error accesible | COMPLIANT | A3.3 foco/alerta/axe | — |
| Clasificación | Selección de tipo | COMPLIANT | assigns_exactly_one_existing_type | — |
| Clasificación | Nuevo sin selección | COMPLIANT | defaults_new_document_to_general_type | — |
| Clasificación | Múltiple o desconocido | COMPLIANT | 6.2 PDF válido, códigos específicos/asociación conservada | — |
| Clasificación | Administración autorizada | COMPLIANT | Focal real: REST crear/renombrar/retirar como administrador | — |
| Clasificación | Editor selecciona | COMPLIANT | editor_can_assign_existing_types + selección existente | — |
| Clasificación | Gestión no autorizada | COMPLIANT | Focal real: editor rechazado en crear/renombrar/retirar REST | — |
| Clasificación | Histórico válido | COMPLIANT | accepts_historical_text_attachment_id | — |
| Clasificación | Histórico sin tipo | COMPLIANT | general_fallback_does_not_mutate_historical_document | — |
| Clasificación | Histórico PDF inválido | COMPLIANT | 6.2 histórico publicado rechazado/reparado por REST sin perder estado | — |
| Clasificación | Corrección satisfactoria | COMPLIANT | correction_reuses_existing_document | — |
| Clasificación | Reintento sin cambios | COMPLIANT | E1 usuario PASS: conserva selección, corrige título y persiste | — |
| Clasificación | Validación rechazada | COMPLIANT | rejected_combination_preserves_all_fields | — |
| Validación | Guardado válido | COMPLIANT | REST controller + clásico real focal y PHPUnit 6.1 | — |
| Validación | Actualización parcial | COMPLIANT | builds_effective_state_for_partial_update | — |
| Validación | Dato obligatorio ausente | COMPLIANT | rejects_missing_required_data_atomically + guard clásico real 6.1 | — |
| Validación | PDF auténtico | COMPLIANT | accepts_authentic_pdf | — |
| Validación | Adjunto inaccesible | COMPLIANT | 6.2 editor autorizado a crear Documentos pero sin acceso al adjunto | — |
| Validación | Archivo inválido | COMPLIANT | rejects_all_invalid_pdf_variants + focal tamaño real | — |
| Validación | Solicitud autorizada | COMPLIANT | E1 usuario PASS login, POST clásico, lectura persistida PDF/título | — |
| Validación | Valor inesperado | COMPLIANT | rejects_unexpected_active_values | — |
| Validación | Solicitud no autorizada | COMPLIANT | Focal nonce propio y PHPUnit administrador autorizado sin mutación | — |
| Validación | Fecha válida | COMPLIANT | persists_valid_document_date + REST controller | — |
| Validación | Fecha vacía | COMPLIANT | accepts_empty_document_date | — |
| Validación | Fecha imposible | COMPLIANT | Helper + actualización clásica real en PHPUnit 6.1 | — |

Total actual:33 COMPLIANT,0 PARTIAL,0 FAILING. Se combina evidencia real previa con ejecuciones del usuario, sin afirmar una prueba asistiva integral ni añadir requisitos de diálogo OS o Tab más allá del spec.

## Coherencia de diseño

Panel de bloques, Media Library, metabox clásico, metadatos compatibles, tipo único y siembra versionada implementados. Validación REST compartida implementada. Validación atómica clásica: defecto corregido usando método post y wp_insert_post_empty_content, según DESIGN-FIX. El nonce inválido bloquea antes de mutar y el POST válido persiste; se comprueba que una llamada sin contexto clásico no activa el guard.

## Evidencia TDD

Modo strict. Las tareas1.1–1.4 conservan RED original y ahora enlazan GREEN agrupado real de4.1/4.2. Las tareas4.3/4.4 tienen secciones propias que referencian fallos reales del gate y recuperación combinada documentada; no se declara gate completo fresco verde. La falta anterior era de referencias/estructura, no de ejecuciones conocidas. Las tareas5.1/5.2 son comprobaciones documentales/checklist. No se inventa RED ni ejecución granular para corregir formato.

## Cobertura de Código

Clover existente: 1719/2083 statements, 82.53 %, umbral 80 %. Se reutiliza; no se ejecutó coverage otra vez. Integración recuperada previa: 134/134 y 1087 aserciones; PHPStan y Compose/unit PASS; desktop administrativo 9/9 y móvil/tablet 18/18. Esa evidencia no cubría el guardado clásico descubierto ahora.

## Calidad, alcance y limitaciones

Diez warnings PHPCS ajenos permanecen documentados. `artifacts/gate/summary.json` conserva el gate completo histórico rojo; no se anuncia gate completo fresco verde. No hay build de assets: JavaScript administrativo se distribuye sin compilación. No se modificaron implementación ni tests. La comprobación LF previa abarca 24 archivos del cambio; los nuevos artefactos VERIFY se comprueban antes de cerrar. El diff del tema y del catálogo PHP respecto de HEAD es vacío.

El presupuesto de revisión está deshabilitado (`max_diff_lines=0`, `sensitive_paths=[]`); se conserva la reconciliación documentada en APPLY. No representa aprobación de un límite activo.

## Fallo inicial resuelto (histórico)

### Tests fallidos
- `verify-focused.php`: nonce documental clásico inválido con usuario autorizado no produce el rechazo esperado; seis comprobaciones previas pasan y fixtures se limpian.

### Errores de build
- Ninguno observado; no existe build de assets aplicable.

### Tareas incompletas
- Ninguna pendiente en checklist; requiere corrección focal del guardado clásico y actualización de evidencia.

## Siguiente acción

Correcciones6.1/6.2 y ejecución6.3 completas. No quedan parciales, fallos funcionales ni gaps de referencias TDD individuales; documentación reconciliada con ejecuciones existentes. ARCHIVE habilitado por status ok, todavía no ejecutado. No se modifica la política de gates ni se inventa RED histórico.

## Re-verificación focal actual

`verify-focused.php` corregido ejecutó **15/15 comprobaciones, exit 0, 1.046 s**, con PHP max_execution_time=30 y limpieza de fixtures en finally. Cubre límite real 5 MB, PDF real 6 MB rechazado, error específico de nonce propio, bloqueo real previo a mutación, guardado clásico válido y gestión REST de términos: administrador crea/renombra/retira; editor recibe 403 en las tres operaciones.

La primera invocación de re-VERIFY falló después de nueve checks por un defecto del checker: rest_base vacío requería usar el nombre de la taxonomía como ruta REST por defecto. Se corrigió exclusivamente el script de validación y se repitió una vez con causa concreta. No fue un fallo productivo ni una suite amplia repetida. El error original wp_update_post ahora se espera como empty_content del hook soportado, manteniendo una aserción separada del error específico del adaptador.

Se reutiliza GREEN válido de APPLY6.1: PHPUnit2/2,27 aserciones,0.544s y PHPCS focal módulo exit0;6.2 agrega3/3,51 aserciones y E1 usuario valida HTTP clásico real. Ninguna suite amplia se repitió en el cierre documental. Las referencias TDD históricas se reconciliaron sin nuevas corridas. No se deduce protección HTTP exhaustiva de APIs internas. El resumen del gate completo sigue histórico rojo.

## Advertencias exactas de cierre

Los diez parciales anteriores están resueltos por E1–E5 y backend6.2. Ejecuciones comunicadas por usuario: E1 PASS5.1s (corrida8.8s), E2 PASS2.4s, E3 PASS1.0s (corrida3.8s), E4 PASS1.9s, E5 PASS2.3s (corrida6.4s). Son resultados del usuario, no nuevas ejecuciones del agente ni una suite completa fresca.

Los warnings externos PHPCS y summary histórico rojo ya se comunicaron y permanecen limitaciones de contexto; no son problemas actuales del alcance verificado. Las advertencias de formato se corrigieron mediante referencias reales, sin crear pruebas nuevas. VERIFY status ok satisface ahora la dependencia ARCHIVE; no se ha ejecutado archivo ni alterado reglas.
