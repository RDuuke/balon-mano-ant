# Exploración: corregir el gate global

## Autorización y estado previo

El usuario eligió abrir un cambio separado para corregir el gate global. Esta fase investiga y prepara una propuesta; no autoriza implementación ni un tercer FIX de Selecciones. Antes de EXPLORE, STATUS resolvió `selecciones-backend` en VERIFY, diez tareas completas, sin aprobación pendiente ni dependencias documentales ausentes. Su resultado real es failed y bloquea ARCHIVE. Se conserva su estado en `.paused-state.yaml`; `detalle-actualidad/.paused-state.yaml` permanece intacto en APPLY con ocho tareas pendientes.

## Evidencia vigente

Fuente: `selecciones-backend/verify-report.md` y logs finales `artifacts/gate/{php-coverage,composer-lint,composer-analyse,browser-portable}.log`. No se ejecutó nuevamente el gate. PHPUnit: 167 pruebas, 1663 aserciones y siete fallos; Clover fresco 2038/2774 líneas = 73.47 %, frente a 80 % obligatorio. PHPCS: 35 errores y 68 warnings; PHPStan: un método privado sin uso. Playwright: 28 fallos, 160 aprobadas y cuatro omitidas por fallo serial. Los doce escenarios de Selecciones cumplen. HEAD consultado: `794203de722afe230f7c7eba917c2f75f8eea839`.

## Diagnóstico y clasificación

| Grupo | Evidencia directa | Clasificación y corrección candidata |
|---|---|---|
| Nosotros filtrado | `functions.php:328` conserva cuatro artículos y aplica `hidden` a grupos distintos; `PublicExperienceTest.php:67` y E2E:181 cuentan todos. La spec pública exige ver solo el grupo, sin exigir eliminación del DOM. | Contrato de prueba demasiado estructural: verificar visibilidad, grupo activo y ausencia de datos restringidos; comprobar funcionamiento sin JS antes de decidir cambiar render. |
| Fixtures uploads | `FixturesDomainTest.php:217` exige `arco-comun.png`, pero `home_news_fixtures()` incorpora medios y galerías anteriores. | Acoplamiento al orden de importación: aislar el recurso que debe fallar y conservar diagnóstico/error real y recuperación. No cambiar la expectativa al primer archivo circunstancial. |
| Documentos banner | Test:265 exige `min-height:22.3125rem`; CSS:41 usa `min-height:0;height:21.25rem`. `git show HEAD:.../style.css` confirma este CSS ya en HEAD; VERIFY documenta reproducción baseline. | Divergencia anterior a Selecciones: reconciliar dimensiones con contrato editorial vigente y probar composición/respuesta, sin imponer una literal CSS obsoleta. |
| Documentos limpiar filtros | E2E:388 usa un locator global; log:252 identifica enlace en formulario y otro en estado vacío. | Ambigüedad del test, no evidencia de defecto funcional: seleccionar región y verificar ambos destinos/operabilidad. |
| PDF administrativo | E2E:200 exige nombre literal; log:623 muestra `labm-e2e-replacement-1.pdf`, tras seleccionar por ID. | Colisión legítima del nombre de archivo y aislamiento insuficiente: comprobar adjunto elegido y nombre real obtenido; asegurar limpieza acotada/idempotente, sin aceptar cualquier PDF. |
| Actualidad listado | E2E:363 cuenta tres `article`; log muestra cuatro. Spec `openspec/specs/actualidad/spec.md:45` distingue primera destacada y restantes tarjetas sin duplicación; otro test:445 distingue 1+3. | Contrato antiguo de recuento: validar destacada+tarjetas y unicidad, filtros y privacidad, contra tamaño de página vigente. |
| Actualidad dimensiones | E2E:433 falla hero esperado120/observado556 y detalle:514 espera contenedor ausente. | Diagnóstico pendiente de reconciliación con Pencil y plantilla vigente. No declarar los valores antiguos obsoletos sin comprobar diseño. |
| Detalle Actualidad | Plantilla usa `[labm_actualidad_hero]` + `[labm_actualidad_body]`; PHP espera `wp:post-content` y `labm_actualidad_detalle`. Diseño pausado describe post-content nativo, delta exige contenido Gutenberg, galería accesible y privacidad. | Divergencia de artefactos/implementación en cambio pausado: decidir cuerpo nativo vs render actual usando pruebas de bloques/galería y orden semántico; SPEC-FIX/DESIGN-FIX en su flujo si cambia decisión. No resolver por simple eliminación de assertions. |
| Home fallback | `functions.php:668` centraliza respaldo Antioquia; PHP:249/366 exige Selección. | Reconciliar fallback compartido con contrato y diseño vigente; mantener prioridad miniatura, metadato permitido y recurso seguro, sin imagen rota. No hay baseline causal adicional. |
| Contraste | Axe log:284+: `#789614` sobre blanco ratio3.4 y sobre `#f3f6e8` ratio3.1, requerido4.5 para texto pequeño; CSS:19 define token. También Home falla axe. | Defecto real WCAG: ajustar colores de texto/usos, manteniendo identidad y validando todos los fondos y viewports; no excluir axe. |
| Calidad y cobertura | PHPCS afecta documents-contact, fixtures-command y functions.php; PHPStan fixtures-command:269 `ensure_demo_attachment()` unused. | Corregir código según WPCS; comprobar referencias antes de retirar método. Cubrir ramas reales de plugin y tema: faltan al menos182 líneas cubiertas si denominador sigue2774. Umbral, fuentes y suites intactos. |

No se atribuyen históricamente los otros fallos a HEAD o a Selecciones: solo Documentos tiene evidencia baseline documentada. El trabajo existente en functions.php, CSS, tests y fixtures pertenece a otros cambios y requiere delimitar hunks antes de APPLY.

## Alternativas

1. Reducir assertions, cobertura o suites: descartado, oculta defectos reales y contradice gates del proyecto.
2. Revertir todo el trabajo previo: descartado, pierde cambios aprobados y no demuestra causa.
3. Corrección por grupos con contratos trazables, TDD real y reconciliación de Detalle: recomendado. Preparar SPEC/DESIGN que indiquen por cada caso si cambia test, implementación o artefacto y por qué; después tareas en lotes y gate completo.

## Alcance para PROPOSE

Rutas candidatas: `tests/php/{FixturesDomainTest,PublicExperienceTest,HomePresentationTest}.php`, tests adicionales pertinentes a cobertura, `tests/e2e/{public-experience,document-admin,home}.spec.ts`, `wp-content/plugins/labm-core/includes/class-labm-{documents-contact,fixtures-command}.php`, `wp-content/themes/labm/{functions.php,style.css,templates/single-labm_actualidad.html}` y artefactos de Detalle solo mediante fase correctiva delimitada. No añadir funcionalidad editorial, migraciones, rediseño general, exclusiones de análisis ni bajar80 %. Scripts coverage/gate ya modificados se preservan; solo incluir retoques adicionales si evidencia nueva los justifica.

## Persistencia y comprobación futura

Runtime limpio restaurado y auditado por VERIFY anterior. Canónico versión `20261001T194755441Z-rduuqe-RDUUQE`, SHA256 `ffe9c63b752090ec7d53c8b3822ea70041c9e3e6eb3dfffd7f0a3188cf6e045e`, 152 archivos, sin cuentas ni credenciales. EXPLORE no toca DB/uploads. Si APPLY altera fixtures persistidos, seguir proceso oficial content-sync antes de verificar/cerrar, conservando backups y paquete sin datos de prueba.

Éxito: `scripts/gate.ps1 -IncludeBrowser` completo verde, cobertura fresca mínima80 %, PHPCS/PHPStan sin errores, Playwright sin omisiones derivadas de fallos, contratos de privacidad/visibilidad/accesibilidad íntegros y LF en archivos tocados. Después reanudar VERIFY de Selecciones; no archivarlo por el mero éxito de este nuevo cambio. Detalle conserva su propia lista pendiente y debe recibir un handoff explícito.

## Riesgos y siguiente fase

- Reconciliación de Detalle obligatoria antes de editar su plantilla/tests; hay decisiones de diseño pendientes, no una autorización para absorber su feature.
- Restaurar calidad global cruza módulos y requiere revisar alcance y reversión en propuesta; los182 renglones son un mínimo aritmético, no una garantía de esfuerzo.
- Pruebas WordPress mutan estado; limpiar entorno y gestionar content-sync evitando canonizar demos temporales.

Siguiente: PROPOSE con alcance concreto, rollback, criterios verificables y aprobación única de propuesta antes de SPEC/DESIGN/APPLY. No implementar desde esta exploración.
