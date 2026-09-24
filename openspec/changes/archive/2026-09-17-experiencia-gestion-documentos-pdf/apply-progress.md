# Progreso de APPLY: Experiencia administrativa para documentos PDF

## Tarea 1.1 — Builders y fixtures administrativos

- **GREEN (referencia de ejecución agrupada existente):** el test builder pertenece a DocumentContactTest.php, cuya corrida35/35 y264 aserciones está registrada en tarea4.1. No se afirma una ejecución granular adicional.

- **RED:** test `tests/php/DocumentContactTest.php::test_document_admin_fixture_builders_cover_validation_inputs` falla con: `Falta implementar el contrato administrativo labm_core_document_admin_validate_state().`

## Tarea 1.2 — Contrato de validación V1–V4

- **GREEN (referencia de ejecución agrupada existente):** los casos V1–V4 pertenecen a la corrida DocumentContactTest.php35/35 registrada en4.1; las correcciones6.1/6.2 agregan evidencia real del adaptador clásico y REST parcial. No se reconstruye un RED nuevo.

- **RED:** tests `tests/php/DocumentContactTest.php::test_document_admin_*` descubren los 12 escenarios V1–V4 y fallan por ausencia de `labm_core_document_admin_validate_state()`, `labm_core_document_admin_save_state()` y `labm_core_document_admin_effective_max_bytes()`.

## Tarea 1.3 — Contrato de clasificación C1–C4

- **GREEN (referencia de ejecución agrupada existente):** los casos C1–C4 pertenecen a la corrida DocumentContactTest.php35/35 registrada en4.1; backend6.2 registra3/3 y51 aserciones para fixtures reforzados. Referencia compartida, no corrida individual inventada.

- **RED:** tests `tests/php/DocumentContactTest.php::test_document_admin_*` descubren los 12 escenarios C1–C4 y fallan por capacidades, siembra, fallback y persistencia atómica todavía ausentes.

## Tarea 1.4 — Contrato E2E A1–A3

- **GREEN (referencia de ejecución agrupada existente):** la corrida desktop9/9 registrada en4.2 incluye los nueve casos A1–A3; la recuperación móvil/tablet18/18 y ejecuciones del usuarioE1–E5 refuerzan cobertura. No se afirma otra ejecución granular.

- **RED:** test `tests/e2e/document-admin.spec.ts` ejecuta 9 escenarios y todos fallan porque aún no existen los controles, estados, mensajes y manejo de foco de la experiencia administrativa.

> GREEN y REFACTOR quedan deliberadamente pendientes para las tareas 2.x–4.x; este lote autorizado solo establece el contrato RED.

## Tarea 2.1 — Metadatos REST e históricos

- **RED:** test `tests/php/DocumentContactTest.php::test_document_admin_accepts_historical_text_attachment_id` fallaba porque no existía el estado efectivo compatible con ID textual ni el esquema entero REST.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php` y `wp-content/plugins/labm-core/includes/class-labm-document-admin.php`; la prueba focal pasa.
- **REFACTOR:** esquema REST explícito para PDF entero no negativo y fecha opcional `Y-m-d`; PHPCS y PHPStan pasan.

## Tarea 2.2 — Tipo único, capacidades y siembra

- **RED:** tests `tests/php/DocumentContactTest.php::test_document_admin_assigns_exactly_one_existing_type`, `test_document_admin_type_seed_is_idempotent` y casos C1–C3 fallaban por vocabulario y capacidades ausentes.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php`; los nueve términos, el fallback y las capacidades separadas pasan la suite focal.
- **REFACTOR:** versión de vocabulario y siembra idempotente separadas de la migración de documentos; PHPCS y PHPStan pasan.

## Tarea 2.3 — Estado efectivo y validador compartido

- **RED:** tests V1–V4 fallaban por ausencia de validación de título, fecha, permisos, MIME, extensión, lectura, firma y límite efectivo.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-document-admin.php`; los contratos de validación pasan.
- **REFACTOR:** errores estructurados por campo y límite centralizado en `min(30 MB, wp_max_upload_size())`; PHPCS y PHPStan pasan.

## Tarea 2.4 — Guardados REST y clásico sin mutación parcial

- **RED:** tests `tests/php/DocumentContactTest.php::test_document_admin_rejected_combination_preserves_all_fields` y casos de autorización/atomicidad fallaban por ausencia del guardado compartido.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-document-admin.php` y carga PHP en `wp-content/plugins/labm-core/labm-core.php`; 35 pruebas de documento/contacto pasan con 264 aserciones.
- **REFACTOR:** validación previa común, nonce clásico, hooks REST y persistencia sin borrar adjuntos; PHPCS y PHPStan pasan.

## Tarea 3.1 — Carga administrativa localizada

- **RED:** los nueve escenarios A1–A3 no encontraban controles porque el adaptador administrativo no existía ni se cargaba.
- **GREEN:** `labm-core.php` y `class-labm-document-admin.php` cargan Media Library y el script solo en pantallas `post`/`post-new` de `labm_documento`, con estado, términos, límite y textos localizados.
- **REFACTOR:** la condición usa `get_current_screen()` y no encola assets en otros CPT ni en frontend; no fue necesario CSS.

## Tarea 3.2 — Panel de bloques y Media Library

- **RED:** A1.1, A1.3 y A3.1 fallaban por ausencia del selector PDF, el manejo de errores y el flujo de teclado.
- **GREEN:** A1.1, A1.3 y A3.1 pasan en `desktop-1024`; el panel abre la Media Library filtrada a PDF, permite seleccionar/reemplazar/quitar y muestra estado, nombre, peso, límite, fecha y tipo sin exponer ID ni URL.
- **REFACTOR:** se reutilizan funciones cliente de formato, selección y validación inmediata; la validación PHP sigue siendo autoritativa.

## Tarea 3.3 — Metabox clásico equivalente

- **RED:** A3.2 no encontraba controles equivalentes; el servidor tampoco conservaba entradas fallidas para reintento.
- **GREEN:** el metabox contiene nonce, selector Media Library, resumen, fecha y tipo único, y recupera valores fallidos mediante transient del usuario; el guardado sigue pasando 35 pruebas PHP (264 aserciones).
- **REFACTOR:** la interfaz clásica comparte configuración, textos y adaptador JS con bloques; su ejecución visual formal queda pendiente de 4.2 porque el entorno no tiene Classic Editor activo.

## Tarea 3.4 — Errores, anuncios y foco

- **RED:** A1.3 y A3.1 fallaban por ausencia de alertas, restauración de foco y operación por teclado.
- **GREEN:** A1.3 y A3.1 pasan; los errores usan `role=alert`, región viva y foco correctivo, y Media Library devuelve el foco al control que la abrió.
- **REFACTOR:** PHPCS, PHPStan y sintaxis JS pasan; una comprobación focal temporal de axe sobre `[data-labm-document-admin]` pasa sin violaciones. La normalización del caso A3.3 y del resto de precondiciones/encoding del spec Playwright permanece deliberadamente en 4.2.

> Estado E2E del lote: 3/9 verdes (A1.1, A1.3, A3.1). A1.2 y A2.1 parten de un documento nuevo sin adjunto; A2.2/A2.3 y A3.3 contienen selectores mojibake o dependientes del idioma; A3.2 requiere activar realmente el editor clásico. No se forzó la UI para satisfacer esas precondiciones y 4.2 permanece sin marcar.

## Tarea 4.1 — Contrato PHP consolidado

- **RED:** los 24 escenarios V1–V4/C1–C4 fallaban originalmente por ausencia del validador/guardado compartido; el foco E2E A2.1 descubrió además que WordPress 7.1 entrega `stdClass` en creación REST y que retirar `custom-fields` impedía persistir los metadatos registrados.
- **GREEN:** `tests/php/DocumentContactTest.php` pasa 35/35 con 264 aserciones; la creación REST real crea un documento con PDF, fecha y tipo que luego lee Gutenberg.
- **REFACTOR:** `labm_core_document_admin_rest_input()` admite el objeto preparado público de WordPress, se conserva `custom-fields` como contrato de REST/Gutenberg y se oculta su panel nativo; cliente y servidor no duplican firma, MIME, permisos ni atomicidad.

## Tarea 4.2 — E2E de bloques, clásico y accesibilidad

- **RED:** `tests/e2e/document-admin.spec.ts` tenía 6/9 fallos por fixtures ausentes, selectores mojibake/idioma y fallback clásico no determinista; el primer foco optimizado también expuso los dos fallos REST de producto descritos en 4.1.
- **GREEN:** corrida final única `document-admin.spec.ts --project=desktop-1024 --max-failures=1`: 9/9 pasan en 29.6 s, incluidos Media Library, reemplazo/retiro, datos/límite, teclado, fallback clásico, anuncio, foco y axe sin violaciones.
- **TRIANGULATE:** focos previos pasan para A1 (3/3), A2.1, A2.2–A2.3 (2/2), A3.1/A3.3 (2/2) y A3.2; un `EPERM` al crear worker se clasificó como infraestructura y el mismo foco pasó fuera del sandbox.
- **REFACTOR:** una autenticación por worker mediante `storageState`, fixture PDF/documento por REST con limpieza, fallback clásico mediante MU-plugin exclusivo de Compose, axe una vez, video desactivado y trazas solo en primer reintento.

## Intento de tareas 4.3–4.4 — Gate completo optimizado

- **Optimización:** `scripts/gate.ps1` acepta `-SkipCoverage`; por defecto ejecuta `php-coverage` como única evidencia de integración y conserva `wordpress-integration` cuando coverage se desactiva.
- **Ejecución única:** `& 'scripts/gate.ps1' -IncludeBrowser` ejecutó `compose-config` y `composer-test` correctamente, y se detuvo en `php-coverage` sin reintento.
- **Fallo:** PHPUnit integración devolvió código no cero; produjo `artifacts/coverage/clover.xml` con 78.4% (1633/2083), inferior al umbral de 80%. La excepción de coverage impidió persistir su salida en `artifacts/gate/php-coverage.log`, por lo que el caso fallido concreto no quedó disponible.
- **Pendiente:** 4.3 y 4.4 no se marcan. PHPCS, PHPStan y `browser-portable` no llegaron a ejecutarse en este intento.

## Segundo intento de tareas 4.3–4.4 — Cobertura dirigida y gate aislado

- **Pruebas dirigidas:** se añadieron exactamente dos casos a `DocumentContactTest.php`. El primero construye usuario, PDF válido, documento, metadatos, tipo y `$GLOBALS['post']`, y verifica configuración/estado seguro (nombre, peso, estado, nonce y términos, sin URL ni ruta). El segundo usa `rest_do_request` para creación y actualización reales y verifica `stdClass`, persistencia de PDF/fecha/tipo y fallback. Ambos casos pasan dentro de la corrida de integración; no figuran entre sus cuatro fallos.
- **Wrapper:** `scripts/gate.ps1` ejecuta `coverage.ps1` mediante `Invoke-Gate -IsolatedScript`, por lo que conserva stdout, stderr, código de salida y `artifacts/gate/php-coverage.log` incluso al fallar. No se modificó `coverage.ps1`.
- **Ejecución única:** `& 'scripts/gate.ps1' -IncludeBrowser` se ejecutó exactamente una vez, sin preflight, suites focales, comandos separados ni reintento. Integración se ejecutó una sola vez dentro de coverage: 134 pruebas, 1071 aserciones, 4 fallos, 19.012 s.
- **Cobertura:** el Clover fresco registra 1719/2083 líneas, **82.53%**, 52 líneas por encima del mínimo de 80%. La capa queda roja por los cuatro fallos PHPUnit, no por el umbral.
- **Fallos PHPUnit:** dos casos de PDF ilegible dependen de `chmod 000`, que sigue siendo legible para el usuario privilegiado del contenedor; un caso público espera una llamada de catálogo sin el argumento de paginación actual; y un caso de consulta vacía hereda fixtures legales preexistentes. Los dos primeros del contrato administrativo son un problema de portabilidad del fixture; los otros dos están fuera del alcance administrativo de este cambio.
- **Calidad:** `compose-config` y `composer-test` pasan; PHPStan pasa sin errores. PHPCS falla (exit 2): detecta 366 errores/44 avisos al aplicar reglas PHP al JavaScript administrativo y un error PHP corregible de alineación de docblock en `class-labm-document-admin.php:284`.
- **Navegador:** 122 pruebas pasan, 2 fallan y 16 no se ejecutan en 2.9 min. Los dos fallos ocurren en la preparación A1.1 de `mobile-320` y `tablet-768` porque el panel no se hace visible en 5 s; desktop/wide, incluidos los casos administrativos ejecutados, pasan. Se clasifica como aislamiento/concurrencia o espera del fixture multi-proyecto, pendiente de corrección.
- **Resultado:** el gate termina con exit 1. Por contrato no hubo reintento y 4.3/4.4 permanecen pendientes; los artefactos frescos están en `artifacts/gate/` y `artifacts/coverage/clover.xml`.

## Recuperación rápida de tareas 4.3–4.4

- **Fixture ilegible (prueba):** la variante deja una referencia de adjunto cuyo archivo ya no existe, en vez de depender de `chmod 000`; conserva la validación real de un PDF no disponible y funciona también con usuarios privilegiados.
- **Catálogo (pruebas/aislamiento):** `PublicExperienceTest` reconoce la firma vigente con página actual. `VerifyCorrectivesTest` pone temporalmente en borrador los documentos publicados, restaura cada estado en `finally` y verifica el contrato vigente del catálogo simple. No se cambió código, HTML ni comportamiento público.
- **PHPCS:** se excluyó únicamente `assets/js/*` del ruleset PHP y se corrigió la alineación del docblock REST. El análisis PHP completo permanece activo.
- **E2E:** el nombre del PDF ahora incluye el proyecto y la apertura espera la configuración localizada, abre la barra documental y expande el panel mediante las APIs del editor. La invocación de recuperación se serializó con `--workers=1`.
- **Integración, única corrida:** PASS, 134/134 pruebas y 1087 aserciones en 15.506 s.
- **Lint, única corrida:** FAIL, sin errores del JS ni del docblock corregido. Quedan únicamente diez warnings preexistentes: siete en `class-labm-documents-contact.php` y tres en `class-labm-fixtures-command.php`; Composer devuelve exit 2 por warnings.
- **Playwright móvil/tablet, única invocación:** no llegó a ejecutar casos por infraestructura local (`sh` no reconocido y binario Playwright no resuelto por pnpm). No hubo reintento. La estabilización E2E queda sin evidencia fresca.
- **Evidencia combinada:** se reutilizan coverage 82.53%, PHPStan PASS, Compose/unit PASS y desktop/wide previos. Como lint sigue rojo y móvil/tablet no se ejecutó, 4.3 y 4.4 permanecen pendientes. `artifacts/gate/summary.json` continúa siendo el resumen rojo histórico del gate anterior y no representa esta recuperación parcial.

## Último intento focal de tareas 4.3–4.4

- **Deuda PHPCS externa:** los diez warnings restantes —siete en `class-labm-documents-contact.php` y tres en `class-labm-fixtures-command.php`— provienen del flujo anterior y no fueron introducidos por `experiencia-gestion-documentos-pdf`. Se documentan como deuda externa; no se modificaron esos archivos ni se relajaron reglas globales. El alcance PHP de este cambio quedó sin errores PHPCS en la corrida anterior.
- **Playwright directo, única invocación:** se invocó Node directamente sobre la CLI de `@playwright/test`, con `NODE_PATH`, `document-admin.spec.ts`, proyectos `mobile-320`/`tablet-768` y `--workers=1`, sin pnpm, sh, wrapper ni gate. La CLI terminó antes de descubrir pruebas porque Node no resolvió `playwright/lib/program` desde el árbol pnpm (`MODULE_NOT_FOUND`, Node 24.18.0).
- **Sin reintento:** no se volvió a ejecutar Playwright ni ninguna otra capa. La evidencia móvil/tablet sigue ausente; por ello 4.3/4.4 permanecen pendientes aunque integración, cobertura, PHPStan, Compose/unit, desktop/wide y PHPCS propio estén verdes.
- **Resumen histórico:** `artifacts/gate/summary.json` permanece rojo y corresponde al gate completo anterior; no es un resultado fresco de este intento focal.

## Alternativa focal en runner contenedor

- **Invocación autorizada:** se intentó preparar el runner Linux definido por `scripts/browser-gate.ps1` para ejecutar únicamente `document-admin.spec.ts`, proyectos `mobile-320`/`tablet-768`, `--workers=1`.
- **Bloqueo de infraestructura:** la primera operación de preparación falló con `permission denied while trying to connect to the docker API at npipe:////./pipe/dockerDesktopLinuxEngine`. Playwright no llegó a invocarse, no se ejecutó ningún spec/proyecto y la URL de WordPress no alcanzó a modificarse.
- **Sin reintento:** no se usó nuevamente Docker ni el host roto. 4.3/4.4 continúan pendientes por ausencia de evidencia móvil/tablet; el resto de evidencia combinada permanece válido.
- **Deuda externa:** se mantiene la clasificación de los diez warnings PHPCS heredados (7 `documents-contact`, 3 `fixtures-command`) y `artifacts/gate/summary.json` continúa etiquetado como histórico rojo.

## Cierre combinado de tareas 4.3–4.4

- **Playwright contenedor:** el único reintento autorizado fuera del sandbox ejecutó exclusivamente `document-admin.spec.ts` en `mobile-320` y `tablet-768`, serializado con `--workers=1`: **18/18 pasan en 2.2 min**. Incluye Media Library, reemplazo/retiro, metadatos, límites, teclado, foco, editor clásico y axe.
- **Infraestructura restaurada:** el runner devolvió `home` y `siteurl` a `http://localhost:8080` en `finally` y terminó con exit 0.
- **Evidencia combinada 4.3:** integración 134/134 (1087 aserciones), coverage 82.53% (1719/2083), PHPStan PASS, Compose/unit PASS y PHPCS sin errores en los archivos del alcance. Los diez warnings restantes pertenecen a deuda externa documentada y no se modificaron ni ocultaron.
- **Evidencia combinada 4.4:** `document-admin.spec.ts` pasa 9/9 en desktop previo y 18/18 frescos en móvil/tablet; el gate previo aportó 122 casos verdes y aisló fallos de infraestructura/regresión. El `summary.json` del gate completo se conserva deliberadamente como **histórico rojo**, no como estado final agregado.
- **Resultado:** 4.3 y 4.4 se marcan completas. El cierre documental 5.1–5.2 permanece fuera de este lote.

## Tarea 5.1 — Documentación administrativa

- **RED:** comprobación focal de `docs/development.md` falla: falta la sección `## Administración de documentos PDF` y su contrato operativo.
- **GREEN:** documentación añadida con ambos editores, biblioteca, límite efectivo, permisos, validación atómica, fecha, tipo único, históricos y reversión preservando datos. Comprobación textual focal satisfactoria; no se modificó código productivo.
- **REFACTOR:** se explicita la evidencia combinada y la deuda externa, diferenciando el resumen histórico rojo; no se repiten suites ya verdes. Las comprobaciones documentales sustituyen pruebas de comportamiento para estas tareas de cierre sin código.

## Tarea 5.2 — LF y preservación del alcance público

- **RED:** comprobación focal del checklist detecta que el cierre LF/alcance no estaba registrado (`5.2` pendiente). No se inventó una regresión de finales de línea: los archivos focales ya eran LF.
- **GREEN:** comprobación acotada de 24 archivos de implementación/documentación y artefactos del cambio: ningún carácter CR; ocho comprobaciones textuales de documentación y checklist 18/18 satisfechos. `git diff HEAD` para tema público y `class-labm-documents-contact.php` vacío; ningún archivo de catálogo, plantilla o estilo público modificado por este lote. Los ajustes anteriores de tests públicos son aislamiento de pruebas, no cambios de frontend.
- **REFACTOR:** verificación limitada al alcance vigente; no se normalizaron archivos ajenos ni se ejecutaron pruebas largas.

## Reconciliación de estado y presupuesto

`flow-nea-apply/SKILL.md` Step 4.5 indica explícitamente que `max_diff_lines: 0` y `sensitive_paths: []` deshabilitan el gate. Esa es la configuración actual. El log anterior interpretó 0 como presupuesto excedido sin cifra ni paths configurados; se corrige esa inconsistencia, no se elimina un gate activo. La instrucción del usuario de retomar autoriza este cierre ordinario. `awaiting_approval` pasa a false; APPLY permanece como fase actual, sin tareas pendientes y sin ejecutar VERIFY. No hay nuevas pruebas de comportamiento porque el lote solo cambia documentación y estado; las evidencias verdes previas se conservan. Si una ejecución posterior no progresa o entra en ciclo prolongado, debe detenerse con timeout y sustituirse por una comprobación focal acotada.

## Tarea 6.1 — Corrección focal del guardado clásico (intento 1)

- **RED:** tests `tests/php/DocumentContactTest.php::test_document_admin_classic_adapter_rejects_nonce_before_real_update` y `::test_document_admin_classic_adapter_persists_real_update` fallan en WordPress real: array en vez de WP_Error y PDF guardado 0 en vez de ID del fixture. PHPUnit: 2 tests, 16 aserciones, 2 fallos, 0.495 s, exit 1. No se escribió código productivo antes de este RED.
- **GREEN:** `class-labm-document-admin.php` compara el método normalizado con `post` y usa `wp_insert_post_empty_content`, hook soportado, para validar antes de escribir. PHPUnit focal: 2/2, 23 aserciones, 0.440 s, exit 0; nonce inválido bloqueado sin mutación y POST válido persiste título/PDF/fecha/tipo.
- **TRIANGULATE:** se añaden rechazo atómico de fecha imposible y API programática sin contexto clásico permitida; 2/2 tests, 27 aserciones, 0.544 s, exit 0. El guard solo se activa para POST labm_documento con action editpost o nonce documental. No se afirma cobertura HTTP completa ni se modifica la protección estándar del formulario WordPress.
- **REFACTOR:** PHPCS focal detectó cuatro warnings nuevos; alineación y nombre del parámetro corregidos. Reejecución PHPCS del único módulo: exit 0, sin errores ni warnings. Los diez warnings externos anteriores permanecen intactos. Artefactos e implementación de este lote verificados LF.

El error específico por campo se conserva en el estado fallido; WordPress devuelve `empty_content` al bloquear mediante el filtro soportado. El diseño se reconcilia con esta decisión mediante DESIGN-FIX. `verify-focused.php` ajusta su expectativa al contrato del hook real y conserva una aserción separada del error específico del adaptador. El informe VERIFY anterior permanece histórico failed hasta una nueva verificación; no se convierte automáticamente a verde. Los gaps históricos de formato TDD no se rellenan con evidencia inventada. Ninguna suite amplia se repitió; ejecuciones PHP limitadas a 30 s.

## Lote parcial 6.2–6.3 — Evidencia adicional (intento 2)

- **Backend:** se refuerzan fixtures/aserciones de tipo múltiple/desconocido con PDF válido, tipo anterior no vacío y códigos específicos; acceso a adjunto aislado con editor autorizado a crear Documentos pero sin edit_others_posts y PDF de otro autor. Ambos casos pasan. No hubo cambios productivos ni RED funcional inventado para ampliaciones de evidencia.
- **Histórico:** publicado con archivo perdido, siembra y rechazo REST conservan título, fecha, asociación y publicación. La reparación REST enviando solo meta falla con `labm_document_title_required` aunque el título persistido existe. Última ejecución focal: 1 test, 15 aserciones, 1 fallo, 0.235 s, exit 1. El primer ensayo usó el helper interno que asigna draft por defecto; se corrigió el canal de la prueba a REST real y se capturó el mensaje exacto. No se ocultó el fallo ni se modificó implementación para continuar.
- **UI:** añadidos E1–E5 en document-admin.spec.ts. Tanda seleccionó E1–E4 desktop, workers=1, retries=0, timeout=45000. Login beforeAll terminó en chrome-error://chromewebdata/ por URL WordPress localhost inaccesible desde contenedor: 0 PASS, E1 no ejecutó cuerpo, E2–E4 omitidos; E5 no ejecutado. Sin fixtures creados. No hay evidencia verde nueva de estos casos, ni regresión productiva demostrada por este fallo de infraestructura.
- **Cierre:** usuario pidió reducir consumo y orquestador ordenó terminar tanda sin nuevas ejecuciones. Se preservan pruebas nuevas y pendientes 6.2–6.3. Próxima acción concreta: conservar título efectivo en REST parcial (RED actual existente), GREEN focal y luego runner UI con home/siteurl accesibles restaurados en finally. No repetir suites completas. Archival sigue bloqueado; verificar LF únicamente en archivos del lote.

## Tarea 6.2 — Título efectivo REST y evidencia backend

- **RED:** ejecución previa real de `tests/php/DocumentContactTest.php::test_document_admin_invalid_published_history_is_preserved_until_repaired` falló al reparar con solo meta: HTTP 400 `labm_document_title_required` pese título persistido; 1 test, 15 aserciones, 0.235 s. Esta evidencia existe antes del cambio productivo y no se repitió innecesariamente.
- **GREEN:** `labm_core_document_admin_rest_input()` incluye post_title únicamente si la solicitud envía title; lo ausente se resuelve con estado persistido, y lo explícitamente vacío sigue validándose. Focal backend ejecutado: 3/3 tests, 51 aserciones, 0.862 s, exit 0, sin warnings.
- **TRIANGULATE:** reparación meta-only conserva título/publicación/fecha y cambia PDF; title explícito vacío con cambio de fecha se rechaza sin mutación. También pasan tipos múltiples/desconocidos con PDF válido y código específico/asociación previa conservada, y adjunto inaccesible aislado de permisos de creación del Documento.
- **REFACTOR:** PHPCS solo módulo modificado exit 0; seis archivos del lote verificados LF. Ninguna suite completa ni UI ejecutada; timeout PHP 30 s. Pendiente únicamente 6.3 y re-VERIFY posterior; no se transforma el reporte anterior en evidencia actual verde.

## Tarea 6.3 — Smoke UI bloqueado por mantenimiento

- **Resultado:** el único smoke E2 desktop con beforeAll login real falla antes de crear fixtures; WordPress muestra `Briefly unavailable for scheduled maintenance. Check back in a minute.` y permanece en wp-login.php. No se ejecutaron cuerpos E1–E5 ni se demostró defecto UX. workers1, retries0, timeout30000; exit1. No se repitió la tanda ni se instaló una alternativa.
- **Harness:** E4 ajustado a PDF auténtico sobre límite, distinguiendo rechazo nativo de carga y rechazo editorial al seleccionar. Cambio solo de prueba; pendiente ejecución real.
- **Cleanup:** runner terminado; docker ps confirmó únicamente WordPress y DB healthy, sin browser runner. No fixtures creados. home/siteurl originales restaurados y comprobados: http://localhost:8080 ambos. Confirmación independiente PHP timeout10s y cleanup del agente exit0.
- **Estado:** 6.3 sigue pendiente por entorno en mantenimiento. 6.2 backend permanece GREEN 3/3 y 51 aserciones. Próxima tanda: recuperar entorno de mantenimiento, smoke login y E1–E5 focales; después re-VERIFY y ARCHIVE solo si el contrato lo permite. No más operaciones en esta tanda. LF del test y artefactos propios comprobado.

## Tarea 6.3 — Cierre con ejecuciones del usuario

- **RED:** usuario detectó fallos de espera #message tras redirect Gutenberg, idioma de tabs/errores y frame oculto duplicado; no se afirma regresión productiva. Se corrigieron únicamente selectores/esperas del propio test.
- **GREEN:** usuario ejecutó E1 PASS5.1s, E2 PASS2.4s, E3 PASS1.0s, E4 PASS1.9s y E5 PASS2.3s. E1 valida POST HTTP y persistencia; E5 acciones Space/Enter y foco. Agente no ejecutó pruebas en este cierre.
- **REFACTOR:** task6.3 completa; resultados atribuidos explícitamente al usuario. Ediciones apply_patch LF, sin comando nuevo de inspección LF ni normalización ajena. Los seis gaps TDD históricos permanecen advertencias de formato, sin fabricar historia.

## Tarea 4.3 — Referencias individuales a evidencia existente

- **RED (histórico existente):** sección Intento de tareas4.3–4.4 registra gate detenido en coverage78.4%, inferior a80%; Segundo intento registra134 tests con4 fallos reales. No es evidencia nueva ni RED retrospectivo fabricado.
- **GREEN (combinado existente):** sección Cierre combinado de tareas4.3–4.4 registra integración134/134,1087 aserciones, coverage82.53%, PHPStan/Compose/unit PASS y PHPCS propio sin errores; los10 warnings externos están separados. Tarea6.2 registra PHPCS focal exit0 posterior. No se afirma un gate completo fresco verde.

## Tarea 4.4 — Referencias individuales a evidencia existente

- **RED (histórico existente):** Segundo intento de tareas4.3–4.4 registra gate exit1 con2 fallos navegador y16 casos no ejecutados, además de fallos PHPUnit. Son ejecuciones reales anteriores, no casos inventados.
- **GREEN (combinado existente):** Cierre combinado de tareas4.3–4.4 registra recuperación integración134/134 y18/18 móvil/tablet, complementando122 casos verdes del gate previo y desktop9/9. El criterio de esta tarea era ejecutar y documentar fallos distinguiendo infraestructura; las pruebas recuperadas y documentación están presentes. El proceso del gate completo histórico sigue rojo y no se presenta como una nueva corrida PASS.

Reconciliación documental: estas referencias eliminan la ausencia de secciones/entradas individuales sin modificar ejecuciones ni resultados. No se añade una excepción de TDD ni se crea evidencia granular. Los warnings externos y limitaciones de ejecución agrupada se conservan como contexto ajeno al cumplimiento funcional del cambio.
