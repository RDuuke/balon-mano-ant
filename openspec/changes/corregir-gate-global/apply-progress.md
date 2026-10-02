# Progreso APPLY: corregir-gate-global

## Reconciliación de Detalle y recursos visuales disponibles — 2026-10-02

- **Handoff reconciliado (6.3):** el estado pausado de `detalle-actualidad` estaba desfasado: enumera pendientes 1.2 y 3.1–3.4, pero su `tasks.md` los marca completos y `apply-progress.md` registra GREEN focal en Playwright para escritorio, 320 y 768 px, incluidos copia, modo sin JavaScript, axe y ausencia de desborde. El contrato vigente es: hero negro, medio destacado a ancho completo, `post-content` nativo, galería `core/gallery`, compartir seguro y retorno; no se admite ubicación ni una fuente de galería alternativa.
- **Contrato aplicado (6.4):** `single-labm_actualidad.html` ya respeta el orden reconciliado `hero -> media -> post-content -> detalle`. Las aserciones de `public-experience.spec.ts` ya verifican hero/medio de borde a borde y contenido nativo. No se modifica otra plantilla ni se introduce ningún contrato nuevo.
- **Recursos visuales:** `design/exports/documentos-desktop.png`, `design/exports/actualidad-desktop.png` y `design/exports/detalle-actualidad-desktop.png` están presentes después del pull. El brief fija 1440 px para los frames de escritorio, ancho editorial aproximado de 1200 px y compatibilidad mínima con 320 px. No hay evidencia de aprobación de las medidas 768/1024/1200 ni del identificador `Nrclx`; por ello 6.1–6.2 continúan pendientes y no se inventan medidas.

## Lote 3: calidad del importador de fixtures

STATUS previo read-only: APPLY,5/26 completas,21 pendientes, sin aprobacion ni dependencias bloqueantes; skill_resolution injected. Usuario aprobo el siguiente lote con ok. Se ejecutan4.1 y4.3, manteniendo4.2, navegador, dimensiones y Detalle pendientes.

## Tarea 4.1 — evidencia actual de calidad

Registro preparatorio, sin modificar comportamiento; RED conductual no aplicable a lectura/evidencia. Antes de cualquier cambio logico posterior se mantiene obligatorio el RED conductual. Este lote solo cambia formato, comentarios y un wrapper privado sin uso.

- `docker run --rm -v "${PWD}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 lint`: exit1,35errores/68warnings WPCS en documents-contact,fixtures-command y functions.php. Evidencia983553.
- `docker run --rm -v "${PWD}:/app" -v labm_composer_vendor:/app/vendor -w /app composer:2.8 analyse -- --no-progress`: exit1,un error en fixtures-command:269, metodo privado ensure_demo_attachment sin uso. Evidencia948e5f.
- Git status previo mostraba fixtures-command limpio, por tanto HEAD coincide con fuente pre-lote; no se atribuyen cambios anteriores ni se revierten archivos ajenos.

## Tarea 4.3 — refactor seguro y comprobaciones estaticas

- **RED:** comprobacion `phpcs wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` falla con8errores y23warnings antes de corregir; exit1,evidencia3cf7f4. PHPStan global falla con el wrapper privado sin uso,exit1. Ambos son RED estaticos autenticos; no se presentan como fallos conductuales PHPUnit.
- **GREEN:** formato WPCS mediante PHPCBF focal, documentacion de parametros y retiro exclusivo del wrapper privado sin referencias. `rg` en todo el repositorio encontro solo su declaracion en codigo y menciones documentales; la unica llamada productiva de reconcile_legal_documents pertenece al comando WP-CLI legal_documents.
- **TRIANGULATE:** PHPCBF corrigio24violaciones,exit1 convencional que indica modificaciones. Primer PHPCS posterior tuvo0errores/1warning meta_query,exit1; no se conto como GREEN. La consulta intencional selecciona documentos del importador por prefijo de origen durante una operacion explicita CLI. Se documenta excepcion focal del sniff SlowDBQuery, siguiendo las cuatro excepciones existentes del mismo importador por clave de origen; no se altera consulta, sanitizacion, escape, suites ni severidades globales.
- **GREEN:** PHPCS focal final exit0,sin errores ni warnings,evidencia00b40a. PHPStan global exit0,No errors,evidencia2c195b.
- **REFACTOR:** tokenizador PHP compara HEAD con archivo final descartando solo whitespace/comentarios/comas finales y el wrapper eliminado: PASS,exit0,evidenciaf62b0e. Los tokens ejecutables restantes son identicos. Una primera verificacion inline fallo por quoting de PHP en Windows, no por comportamiento; el script temporal posterior verifico correctamente y fue retirado tras ejecutar.

Comandos finales: `composer:2.8 exec phpcs -- wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`; `composer:2.8 analyse -- --no-progress`. Resumen PHPCS global posterior `composer:2.8 exec phpcs -- --report=summary` conserva27errores/45warnings en functions.php y documents-contact,exit1,evidencia8c830f. El gate global sigue pendiente y no se ejecuta en este lote.

Review budget deshabilitado,max_diff_lines0/sensitive_paths[]. Diff propio de codigo46anadidas32retiradas=78lineas. Las tareas4.1 y4.3 completas llevan el total a7/26,19pendientes. No se ejecutaron pruebas mutables ni se alteraron DB/uploads; no procede backup, Restore ni Push. Canonico/version20261001T194755441Z conservados; SHAffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e coincide latest. Snapshots Selecciones3a687d3f4b6cab321a550f7db47e27b98f22a303a22334a388ec4d48ffe8ef90 y Detalle688c1bb8db266b9ae16ce289412fdc2cd7f8977a3ad89a7f2cce5e804d7933e3 conservados. LF y git diff --check se verifican en los cinco archivos propios al cierre.

## Lote 2: prioridad y seguridad de medios de Home

STATUS previo read-only: APPLY,4/26 completas,22 pendientes, sin aprobación ni dependencias bloqueantes; skill_resolution injected. Lote acotado2.10–2.11; sin fase visual6, Detalle ni gate global.

Backup oficial previo verificado: `.content-sync/backups/backup-manual-20261001T210151939Z-rduuqe-RDUUQE.zip`,95928689bytes. Status local/canónico conserva versión20261001T194755441Z. Recursos propios: noticia pública temporal, padre privado/draft/protegido y adjunto sintético; eliminados en finally. No se exportan fixtures al canónico.

## Tarea 2.10 — RED de prioridad y referencias restringidas

- **RED:** test `tests/php/HomePresentationTest.php::test_home_news_rejects_restricted_thumbnail_references` falla porque la miniatura de padre privado expone `imagen-restringida.png`. Focal combinado con dos expectativas anteriores del respaldo:3tests/8assertions/3failures,exit1,salida9e9838. Los dos fallos restantes esperaban Selección frente al respaldo Antioquia declarado en diseño; no justifican modificar producción.
- **GREEN:** guardia en `wp-content/themes/labm/functions.php` antes de renderizar la miniatura; focal3tests/65assertions,exit0,salidad3a2d9. Test nuevo de `tests/e2e/home.spec.ts` comprueba imagen cargada realmente, título, fecha válida, enlace al detalle y respaldo editorial decorativo; ejecución navegador pendiente dentro2.11.
- **TRIANGULATE:** padre draft y publicado con contraseña en ambas posiciones; ID inexistente; metadatos vacíos, externos, javascript, traversal, inexistentes y sufijos manipulados. Ambos recursos permitidos conservados y comprobados en disco.
- **REFACTOR:** arrays/alineación WPCS propios y resolver thumbnail ID antes de get_post evita recurrir a global con ID0. Suite ampliada `--filter HomePresentationTest`:18tests/163assertions,exit0,salidaef4821.

## Tarea 2.11 — implementación parcial; navegador pendiente

- **RED:** ciclo compartido2.10, fuga de miniatura restringida observada antes de producción.
- **GREEN:** comprobar estado público efectivo del adjunto y contraseña del adjunto/padre; mantener prioridad destacada válida, medio permitido y respaldo seguro Antioquia. `labm_theme_news_fallback_path` ya cumple su whitelist y no se cambia. Aserciones antiguas actualizadas al respaldo declarado, con garantías negativas ampliadas. PHP18/163 aprobado tras refactor.
- **REFACTOR:** cambios limitados a Home; metadatos/categoría y fecha siguen comprobados. No modificar consultas, plantilla Detalle ni fallback compartido.
- **PENDIENTE:** Playwright no llegó a ejecutar. pnpm normal intentó auto-install y falló NoTTY; rerun escalado conservó NoTTY. Install CI frozen exit0 Already up to date; focal CI llegó al script y falló `playwright no se reconoce`,salida3879c3. No es RED conductual ni GREEN. Runner oficial `scripts/browser-gate.ps1` solo acepta Task; `scripts/browser-gate.sh` ejecuta suite completa sin argumentos/filtro, fuera de este lote. No se debilitan pruebas ni se cambia configuración;2.11 permanece unchecked hasta ejecutar browser.

Checks: review budget deshabilitado max_diff_lines0/sensitive_paths[]. Diff propio final122líneas; chequeo LF/diff final al cierre. Skills opcionales testing/typescript-general ausentes; se sigue convenciones existentes. Total5/26 completas;21 pendientes. Gate global y cobertura fresca siguen sin ejecutar.

## Lote 1: aislamiento y contrato de fixtures

STATUS previo: TASKS, aprobación liberada, 0/26 tareas, dependencias presentes; skill_resolution injected. Solo 1.1, 1.2, 2.4 y 2.5. Ningún cambio de producción, dimensiones, Detalle ni gate global.

## Tarea 1.1 — baseline e inventario

Evidencia preparatoria; no se inventa un RED/GREEN conductual para lecturas/backup. TDD estricto continúa para implementación.

- Runtime y canónico: `20261001T194755441Z-rduuqe-RDUUQE`, comprobados mediante `scripts/content-sync.ps1 -Action Status`.
- Hash canónico comprobado: `ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e`.
- Backup oficial previo: `.content-sync/backups/backup-manual-20261001T204835366Z-rduuqe-RDUUQE.zip`, 95928692 bytes. Snapshot anterior a cualquier prueba mutable del lote.
- Clover existente es diagnóstico histórico: 2038/2774 sentencias = 73.4679%; no constituye aceptación ni cobertura fresca de este lote. XML declara cero conditionals; se usan sentencias no cubiertas como orientación, sin inventar cobertura de ramas.
- Candidatas reales para 5.1: `class-labm-documents-contact.php` líneas153–167 (validación),240–248,382–430,657–678; para5.2: functions.php730–853, fixtures-command.php323–413 y módulos admin/footer/smtp. Se requiere leer comportamiento concreto y RED auténtico antes de cambios posteriores.
- Recursos del lote: aliado temporal propio con título `Contenido ajeno al fallo de logo`, eliminado en finally; objetivo demo existente `demo-labm-image-arco-comun`, seleccionado por thumbnail real de `demo-labm-aliado-arco-comun`. Se retira únicamente ese adjunto; restauración oficial del backup recupera IDs, archivos y asociaciones originales tras los ensayos.
- Trabajo preexistente preservado: scripts gate/cobertura, DomainModelTest, dominio, content-sync, diseño y cambios Selecciones/Detalle. No commits, borrados ni normalización ajenos.

## Tarea 1.2 — trazabilidad de los 36 escenarios

Evidencia preparatoria sin cambio de comportamiento. Mapa de requisitos directos, sin modificar specs/design.

| Dominio | Escenario | Tareas | Comprobación prevista |
|---|---|---|---|
| actualidad | Página completa filtrada | 2.8–2.9 | PHP + Playwright: roles, unicidad, orden y paginación |
| actualidad | Última página parcial | 2.8–2.9 | PHP + Playwright: roles, unicidad, orden y paginación |
| actualidad | Página inexistente o contenido privado | 2.8–2.9 | PHP + Playwright: roles, unicidad, orden y paginación |
| actualidad | Contrato reconciliado | 6.1–6.4 | Referencia/handoff reconciliados y Playwright; bloqueado hasta condiciones |
| actualidad | Medio ausente o contenido largo | 6.1–6.4 | Referencia/handoff reconciliados y Playwright; bloqueado hasta condiciones |
| actualidad | Contratos contradictorios | 6.1–6.4 | Referencia/handoff reconciliados y Playwright; bloqueado hasta condiciones |
| calidad-seguridad | Ejecución completa satisfactoria | 7.2 | Gate íntegro + Clover fresco y etapas completas |
| calidad-seguridad | Exactamente el umbral | 7.2 | Gate íntegro + Clover fresco y etapas completas |
| calidad-seguridad | Fallo, omisión o evidencia antigua | 7.2 | Gate íntegro + Clover fresco y etapas completas |
| calidad-seguridad | Prueba mutable satisfactoria | 1.1, 2.4–2.7, 5.1–5.2, 7.1 | Inventario, backup, ramas reales, finally/restauración |
| calidad-seguridad | Limpieza repetida | 1.1, 2.4–2.7, 5.1–5.2, 7.1 | Inventario, backup, ramas reales, finally/restauración |
| calidad-seguridad | Prueba interrumpida | 1.1, 2.4–2.7, 5.1–5.2, 7.1 | Inventario, backup, ramas reales, finally/restauración |
| calidad-seguridad | Estado persistido modificado | 1.2, 7.1–7.2 | Content-sync oficial + hash/versión y snapshots |
| calidad-seguridad | Cambio sin persistencia | 1.2, 7.1–7.2 | Content-sync oficial + hash/versión y snapshots |
| calidad-seguridad | Contaminación o cierre ajeno | 1.2, 7.1–7.2 | Content-sync oficial + hash/versión y snapshots |
| document-admin-experience | PDF reemplazado | 2.6–2.7 | Playwright + REST: identidad/nombre/tamaño y restauración |
| document-admin-experience | Nombre ya utilizado | 2.6–2.7 | Playwright + REST: identidad/nombre/tamaño y restauración |
| document-admin-experience | Carga fallida o selección inválida | 2.6–2.7 | Playwright + REST: identidad/nombre/tamaño y restauración |
| documentos-contacto | Formulario con resultados | 2.1–2.3 | PHP + Playwright: restablecimiento por región y teclado |
| documentos-contacto | Estado vacío con dos acciones | 2.1–2.3 | PHP + Playwright: restablecimiento por región y teclado |
| documentos-contacto | Parámetros manipulados | 2.1–2.3 | PHP + Playwright: restablecimiento por región y teclado |
| documentos-contacto | Contenido editorial representativo | 6.1–6.2 | Medidas aprobadas + Playwright: composición/texto largo |
| documentos-contacto | Texto largo | 6.1–6.2 | Medidas aprobadas + Playwright: composición/texto largo |
| documentos-contacto | Discrepancia sin referencia resuelta | 6.1–6.2 | Medidas aprobadas + Playwright: composición/texto largo |
| experiencia-publica | Grupo publicado seleccionado | 2.1–2.3 | PHP + navegador con/sin JS: visibilidad, privacidad y teclado |
| experiencia-publica | Grupo válido vacío | 2.1–2.3 | PHP + navegador con/sin JS: visibilidad, privacidad y teclado |
| experiencia-publica | Filtro inválido o contenido restringido | 2.1–2.3 | PHP + navegador con/sin JS: visibilidad, privacidad y teclado |
| experiencia-publica | Colores y estados habituales | 3.1–3.2 | Playwright: ratios/estados/fondos en cinco anchos |
| experiencia-publica | Fondo alternativo o interacción | 3.1–3.2 | Playwright: ratios/estados/fondos en cinco anchos |
| experiencia-publica | Contraste insuficiente | 3.1–3.2 | Playwright: ratios/estados/fondos en cinco anchos |
| fixtures | Medios disponibles | 2.4–2.5 | PHPUnit: fallo aislado, preservación y recuperación idempotente |
| fixtures | Carga repetida tras recuperación | 2.4–2.5 | PHPUnit: fallo aislado, preservación y recuperación idempotente |
| fixtures | Recurso previsto no disponible | 2.4–2.5 | PHPUnit: fallo aislado, preservación y recuperación idempotente |
| home-news | Imagen destacada válida | 2.10–2.11 | PHP + Playwright: prioridad, seguridad y metadatos |
| home-news | Destacada ausente | 2.10–2.11 | PHP + Playwright: prioridad, seguridad y metadatos |
| home-news | Medio inválido o restringido | 2.10–2.11 | PHP + Playwright: prioridad, seguridad y metadatos |

## Tarea 2.4 — RED del recurso aislado y recuperación

- **RED:** test `tests/php/FixturesDomainTest.php::test_home_allies_fixture_reports_upload_failures` ampliado antes de corregir aislamiento falla: el diagnóstico contiene `hero-balonmano-seleccion-v1.png`, no `arco-comun.png` (PHPUnit 1test/5assertions/1failure; salida a09855). Aserciones nuevas: preservación de contenido ajeno y recuperación idempotente.
- **GREEN:** corrección compartida con2.5: PHPUnit focal 1test/10assertions pasa (salida0df8fc).
- **REFACTOR:** reutilizar comando y alinear variables; focal ampliado 2tests/59assertions pasa (salida981cae).

## Tarea 2.5 — GREEN y refactor del aislamiento

- **RED:** mismo ciclo observado contra contrato reforzado de2.4; precarga y retiro correcto aún fallaban cuando se falseaba basedir (salida00e284). No se escribió código de producción antes de RED.
- **GREEN:** precargar disponibles, resolver adjunto por asociación real y alterar exclusivamente path de nuevas escrituras. Mantener basedir permite resolver los medios existentes. Verificar recurso previsto, asociación ausente tras fallo, ajeno idéntico, PNG recuperado, ID estable y un único adjunto.
- **REFACTOR:** remover filtro en finally, limpieza propia en finally anidado y reutilizar comando. Suite `--filter test_home_allies`: 2tests,59assertions,exit0.

Comandos focales: `docker compose run --rm -T --user 33:33 -e WP_TESTS_RUNTIME_ROOT=/var/www/html -v "${PWD}:/work:ro" -v labm_composer_vendor:/work/vendor -w /work wordpress php vendor/bin/phpunit -c phpunit.integration.xml.dist --filter test_home_allies_fixture_reports_upload_failures`; después del refactor `--filter test_home_allies`. Un primer fallo de acceso a Docker requirió escalación; no se contó como RED.

Review budget: deshabilitado por configuración max_diff_lines0/sensitive_paths[]. Diff de código propio:31añadidas/10retiradas=41líneas; no se atribuye diff histórico al lote. LF se comprueba en archivos propios. Condiciones6.1–6.4 y gate final siguen pendientes.
Restauracion oficial completada exit0: backup previo recuperado, safety backup20261001T205650450Z. No cambia persistencia final autorizada: no procede Push ni actualizar canonico. Snapshots preservados: Selecciones SHA3a687d3f4b6cab321a550f7db47e27b98f22a303a22334a388ec4d48ffe8ef90; Detalle SHA688c1bb8db266b9ae16ce289412fdc2cd7f8977a3ad89a7f2cce5e804d7933e3.

Restauración oficial del lote2 completada exit0: backup210151939Z recuperado; safetybackup-pre-restore20261001T211340111Z. Status tras restauración local/canónico20261001T194755441Z coinciden. SHA canónicoffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e coincide latest. No queda cambio persistido final; no procede Push. git diff --check aprobado. Guardia usa image_id explícito y null si0: sin fallback global involuntario.

LF comprobado en7archivos propios. Snapshots Selecciones3a687d3f4b6cab321a550f7db47e27b98f22a303a22334a388ec4d48ffe8ef90 y Detalle688c1bb8db266b9ae16ce289412fdc2cd7f8977a3ad89a7f2cce5e804d7933e3 conservan hash del lote1.

## Lote 4: calidad WPCS y entradas seguras de Contacto

STATUS previo read-only: APPLY,7/26 completas,19 pendientes, awaiting_approval false, dependencias presentes; skill_resolution injected. Usuario autorizo siguiente lote con ok. Scope exclusivo4.2; navegador2.11, dimensiones/handoff y gate integral siguen pendientes.

Backup oficial antes de pruebas mutables: `.content-sync/backups/backup-manual-20261001T213508663Z-rduuqe-RDUUQE.zip`,95928693bytes. Pruebas temporales: transiente estado Array, estados PRG generados por handler; limpieza en finally, filtros mail/redirect y error handler retirados; GET/POST/SERVER originales restaurados. Setup/teardown conserva opciones SMTP.

## Tarea 4.2 - WPCS y saneamiento explicito

- **RED:** PHPCS focal de functions.php y documents-contact falla exit1,27errores45warnings,salida42d6c0. Dos pruebas nuevas escritas antes de produccion: `tests/php/DocumentContactTest.php::test_contact_prg_rejects_array_query_without_consuming_state` falla ErrorException Array to string conversion; `::test_contact_handler_rejects_array_method_without_delivery` falla TypeError strtoupper array. PHPUnit2tests0assertions2errors,exit1,salida4e0923. Error inicial de Docker fue infraestructura y se reintento escalado; no se conto como RED.
- **GREEN:** sanitizar con `sanitize_text_field(wp_unslash(...))` antes del helper de identificador opaco y antes de strtoupper REQUEST_METHOD. Arrays se rechazan como vacio; no consumen transiente Array ni entregan correo. Focal2tests7assertions,exit0,salida883216.
- **TRIANGULATE:** misma prueba del handler ampliada con metodo ausente, GET y post; POST reconoce mayusculas/minusculas y el procesador sigue rechazando nonce invalido. Regresion incluye identificador opaco case-sensitive consumido una vez, delivery error seguro, privacidad y token ausente:6tests27assertions,exit0,salidab9ea3b.
- **REFACTOR:** PHPCBF focal corrigio49violaciones,exit1 convencional de archivo modificado, no GREEN. Docblocks faltantes y formato conservan firmas/escape/consultas. Documentadas excepciones focales: GET solo lectura para descarga publica validada/filtros/paginacion; nonce POST delegado a labm_core_process_contact que ejecuta wp_verify_nonce antes de correo; tax_query/meta_query requeridas por categoria editorial y fecha editorial distinta de post_date, siguiendo patron existente de meta_key. No cambios de ruleset, severidad, suites, umbral ni assertions debilitadas.
- **GREEN:** PHPCS focal exit0,salidaf92d3f; PHPCS global `composer:2.8 exec phpcs -- --report=summary` exit0,salida893583,ningun error/warning. PHPStan global `composer:2.8 analyse -- --no-progress` exit0 No errors,salidaea44ee.
- **REFACTOR:** comparacion tokens PHP contra copias exactas pre-lote (no HEAD, conserva guardia Home previa): PASS ambosarchivos,salida88677b,exceptuando los dos saneamientos explicitos cubiertos. Comparacion ignora comentarios/whitespace/comasfinales y normaliza whitespace HTML, por lo que no implica comprobacion visual. Primer chequeo fallaba por tokens HTML de whitespace vacio; corregido comparador, no produccion. Scripts/copias temporales retirados tras comprobar.

Restauracion oficial backup previo exit0,salida9010ac,safetybackup-pre-restore20261001T214221112Z. Status oficial posterior local/canonico20261001T194755441Z coinciden,salidac4ec3d; SHAffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e coincide latest. Sin cambio persistido final, no procede Push ni actualizar paquete. No se ejecuto gate integral ni nueva cobertura.

Review budget deshabilitado max_diff_lines0/sensitive_paths[]. Diff codigo propio contra snapshots pre-lote: functions57+/19-,documents92+/47-=215lineas; pruebas nuevas de entradas no escalares y triangulacion. LF y diff--check verificados solo siete archivos propios al cierre. Total8/26completas,18pendientes. Snapshots Selecciones/Detalle preservados sin edicion.

## Lote 5: contratos perceptibles de Nosotros y filtros por región

STATUS previo read-only: APPLY8/26 completas18pendientes, awaiting_approval false, dependencias completas, skill_resolution injected. Alcance autorizado2.1–2.3 y comprobación pendiente2.11. Configuración strict; review_budget deshabilitado max_diff_lines0/sensitive_paths[]. Backup oficial215138547Z previo a pruebas mutables. No gate integral ni cambios dimensionales/handoff.

## Tarea 2.1 — RED de grupo seleccionado vacío

- **RED:** `tests/php/PublicExperienceTest.php::test_about_team_selected_empty_group_keeps_filters` falla antes de producción: falta mensaje comprensible al ocultar temporalmente integrantes seleccionados mediante estado private; PHPUnit5tests46assertions1failure, salidaea022a. Miembros originales restaurados en finally. Prueba invalidez y exclusión de títulos restringidos. Aserciones anteriores de recuento total DOM cambiadas por artículos perceptibles/no operables hidden, conservando garantías.
- **RED:** `tests/e2e/public-experience.spec.ts::Nosotros anuncia y restablece un grupo vacío al filtrar localmente` falla: no existe role=status tras cambiar grupo; salidae8b0a1,1failure3passed. Variante editorial sólo DOM local, sin mutación persistida. Los controles teclado/privacidad conJS y sinJS y ambas regiones Documentos ya pasan; no se atribuye RED a comportamientos existentes.
- **GREEN:** PHP5tests52assertions y navegador5/5 pasan tras2.2; salida515b47 ycc51e4.
- **REFACTOR:** ciclo compartido con2.3, comprobaciones permanecen aprobadas.

## Tarea 2.2 — GREEN del vacío servidor y cliente

- **RED:** mismo contrato vacío2.1, confirmado tanto PHP como navegador antes de modificar producción. No cambio productivo previo al fallo observado.
- **GREEN:** `functions.php` cuenta integrantes del grupo seleccionado y conserva mensaje role=status con hidden sólo cuando hay visibles; conserva colección pública para filtrado local. `assets/about-team.js` actualiza visibilidad del mensaje al cambiar grupo y normaliza grupo desconocido al volver en historial. Dependencia del controlador existente imprescindible para contrato conJS; tabla Archivos afectados del diseño omite este archivo (addendum de trazabilidad sugerido antesVERIFY, sin cambiar decisiones).
- **TRIANGULATE:** vacío servidor causado por integrantes private excluye sus títulos y mantiene cuatro filtros; filtro desconocido devuelve colección predeterminada. Navegador prueba activación teclado, filtro activo, tarjetas perceptibles, grupo no seleccionado oculto y recuperación Todos con/sinJS.
- **GREEN:** PHP5tests52assertions, PHPCS functions.php exit0 (salida515b47); PHPStan global Noerrors, exit0 (salidac74bc2). Primer GREEN PHP detectó que contar todos hidden incluía ahora mensaje; aserción se acotó a artículos con atributo hidden, sin retirar garantía.

## Tarea 2.3 — REFACTOR de comprobaciones focales

- **RED:** ciclo funcional compartido2.1–2.2; error observado de estado vacío preservado en evidencia. No falla inventada del helper.
- **GREEN:** navegador focal5/5 exit0 salida cc51e4 antes del refactor.
- **REFACTOR:** helper activateLinkByKeyboard centraliza visibilidad/foco/Enter; tests distinguen limpiar en formulario y región No encontramos documentos, verifican URL limpia, texto/categoría/año/orden predeterminados, resultados publicados y privacidad. La prueba antigua reconoce título real del vacío y acota acción al formulario. Verificación conservada5/5 exit0 salida5093c4. Sin reducción de cobertura, suites, umbrales ni exclusiones.

## Tarea 2.11 — GREEN navegador de prioridad de medios Home

- **RED:** ciclo backend registrado previamente2.10; este lote completa su validación pendiente, no recrea un fallo ya corregido. No nuevo código Home.
- **GREEN:** `tests/e2e/home.spec.ts::ultimas noticias conserva medios cargados y metadatos semanticos` PASS desktop1024 dentro5/5, antes y después del refactor (cc51e4/5093c4); respeta helper común y respaldo institucional.
- **REFACTOR:** helper público ya centralizado en lote2; se conserva implementación y garantías verificadas PHP18tests163assertions previas.

Runner focal reproducible sin instalación: imagen oficial `labm-browser-gate:node-22.13.1`, volumen `labm_playwright_browsers`, CLI existente `node node_modules/.pnpm/@playwright+test@1.54.1/node_modules/@playwright/test/cli.js test tests/e2e/public-experience.spec.ts tests/e2e/home.spec.ts --grep 'Nosotros anuncia|Nosotros filtros|Documentos limpia|medios cargados' --project desktop-1024 --workers 1`, WP_URL http://host.docker.internal:8080. Home/siteurl temporalmente ajustados con WP-CLI; restaurados mediante proceso oficial. No cambios en scripts/configuración ni uso de runner global como prueba focal.

Restore oficial backup215138547Z exit0 salida3c4bff, safetybackup-pre-restore220646608Z. Status oficial posterior salida1a6b79 confirma local/canónico20261001T194755441Z coinciden. SHA256 canónicoffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e coincide latest.json. Sin cambio persistido final: no procede Push/actualizar paquete. Fixtures temporales no se exportan al canónico; no se modifican cuentas. Artefactos Selecciones y Detalle preservados sin edición. LF de los ocho archivos propios y git diff--check comprobados. Total12/26completas14pendientes; siguiente lote requiere continuación del usuario.

## Continuación APPLY bloqueada por infraestructura — 2026-10-01 21:19

STATUS read-only: APPLY, 12/26 completas y 14 pendientes; TDD estricto activo y review budget deshabilitado. Antes de ejecutar los contratos mutables 2.6–2.9, 3.1–3.2 y 5.1–5.2 se intentó preparar el runtime oficial.

- `docker compose ps`: no puede conectar al daemon (`//./pipe/docker_engine` inexistente); Docker Desktop no está disponible.
- `scripts/content-sync.ps1 -Action Backup`: bloqueado por la política de ejecución local al no estar firmado. La copia oficial previa es requisito para pruebas que crean medios/documentos.
- Se solicitó iniciar Docker Desktop en `C:\Program Files\Docker\Docker\Docker Desktop.exe`; la ruta no existe. La instalación local solo contiene `cli-plugins`.
- No se modificó código ni estado WordPress, ni se ejecutaron tests como sustituto del ciclo RED→GREEN. Se mantienen sin marcar las tareas dependientes.
- Las tareas 6.1–6.2 requieren además referencias y medidas visuales aprobadas; 6.3–6.4 siguen condicionadas al handoff reconciliado de Detalle. No se deben inferir ni tocar plantilla/aserciones asociadas.

## Continuación APPLY — 2026-10-01 21:42

- Docker ya está disponible: daemon 29.7.2; `labm-db-1` y `labm-wordpress-1` están saludables.
- Se inició el backup oficial con `scripts/content-sync.ps1 -Action Backup`, pero no quedó confirmado ni produjo un archivo nuevo en `.content-sync/backups` porque el proceso de Codex se ejecuta como `DESKTOP-JP8MFJ8\CodexSandboxOffline`.
- En esa identidad, tanto `content-sync.ps1` como `validate-env.ps1` devuelven `UnknownError`: la cadena termina en una raíz no compatible con el proveedor de confianza. La firma que el usuario comprobó como válida está confiada en su perfil interactivo, no en el almacén que usa el ejecutor.
- No se iniciaron pruebas mutables ni se marcaron tareas: 2.6–2.9, 3.1–3.2, 5.1–5.2 y 7.1–7.2 conservan el requisito de backup oficial. Las condiciones de referencia y handoff de 6.1–6.4 permanecen pendientes de evidencia reconciliada; las exportaciones presentes no sustituyen esa aprobación.
