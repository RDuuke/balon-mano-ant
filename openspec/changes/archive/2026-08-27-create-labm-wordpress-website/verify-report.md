# Informe de verificación: Sitio WordPress de LABM

## Resumen ejecutivo

VERIFY ejecutó pruebas reales y contrastó los 60 escenarios vigentes. La funcionalidad queda demostrada por 38 pruebas PHP con 188 aserciones, 85,69 % de cobertura, 32 pruebas Playwright/axe en 320, 768, 1024 y 1440 px, todos los contratos y todos los smokes. PHPUnit unitario y PHPStan también pasan.

El resultado global es **FAILED**: 59 escenarios son `COMPLIANT` y 1 es `FAILING`. El escenario «Gate satisfactorio» falla porque WPCS rechaza CRLF en cuatro archivos PHP, el gate nativo de navegador no puede interpretar `scripts/browser-gate.sh` por CRLF y el agregador de Windows PowerShell aborta al convertir el progreso normal de Docker BuildKit por stderr en una excepción durante cobertura. No se corrigió código ni se ejecutó ARCHIVE.

Lighthouse, SEO y la garantía integral WCAG 2.2 AA permanecen **diferidos**. Este informe no los declara `COMPLIANT` y no los usa como bloqueo. La tarea 5.4, hosting, matriz productiva, SMTP real, PDF reales y datos reales permanecen fuera de alcance.

## Project Standards (auto-resolved)

- Durante NEA Flow, toda operación se ejecuta en subagentes; el agente raíz solo coordina y valida contratos.
- Los artefactos OpenSpec se escriben en español y son la fuente canónica del cambio.
- VERIFY debe ejecutar pruebas reales, no corregir código y tratar un fallo de gate como bloqueante.
- El proyecto aplica WordPress Coding Standards, pruebas PHP/WordPress, Playwright/axe y configuración sin secretos.
- Las especificaciones usan requisitos RFC 2119 y escenarios Dado/Cuando/Entonces.
- El alcance reconciliado difiere Lighthouse, SEO y la garantía integral WCAG 2.2 AA, y excluye 5.4 y dependencias productivas.

## Completitud de tareas

- Tareas marcadas completas: 28 de 28.
- Tareas incompletas: ninguna.
- Tareas implementativas con RED y GREEN: 27 de 27.
- Tarea 5.4: cierre administrativo por exclusión expresa; no requiere evidencia RED/GREEN ficticia.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| entorno-desarrollo | Arranque limpio | ✅ COMPLIANT | `Test-Http.ps1`, `Test-Database.ps1` | — |
| entorno-desarrollo | Reinicio con persistencia | ✅ COMPLIANT | `Test-Persistence.ps1` | — |
| entorno-desarrollo | Dependencia no disponible | ✅ COMPLIANT | `Test-Failure.ps1`, contratos del gate | — |
| entorno-desarrollo | Configuración desde ejemplo | ✅ COMPLIANT | `Test-Lote1.ps1 -Task 1.2`, `validate-env.ps1` | — |
| entorno-desarrollo | Personalización local | ✅ COMPLIANT | `Test-Lote1.ps1 -Task 1.2` | — |
| entorno-desarrollo | Configuración incompleta | ✅ COMPLIANT | `validate-env.ps1`, contrato 1.2 | — |
| entorno-desarrollo | Carga inicial | ✅ COMPLIANT | `Test-Fixtures.ps1`, `FixturesDomainTest` | — |
| entorno-desarrollo | Carga repetida | ✅ COMPLIANT | `Test-Fixtures.ps1`, `ClosingCoverageTest::test_fixture_command_is_idempotent_and_preserves_foreign_content` | — |
| entorno-desarrollo | Fuente sensible | ✅ COMPLIANT | `FixturesDomainTest::test_fixtures_do_not_create_pdf_attachments` | — |
| entorno-desarrollo | Estado versionable | ✅ COMPLIANT | `test-repository-hygiene.ps1`, contrato 1.1 | — |
| entorno-desarrollo | Archivos locales existentes | ✅ COMPLIANT | `Test-Persistence.ps1`, reglas de `.gitignore` | — |
| entorno-desarrollo | Secreto accidental | ✅ COMPLIANT | `test-repository-hygiene.ps1` | — |
| arquitectura-cms | Activación independiente | ✅ COMPLIANT | `Test-Components.ps1` | — |
| arquitectura-cms | Cambio de tema | ✅ COMPLIANT | `Test-DomainPersistence.ps1`, `Test-Components.ps1` | — |
| arquitectura-cms | Plugin inactivo | ✅ COMPLIANT | `PublicExperienceTest::test_theme_has_safe_fallback_when_domain_is_unavailable`, `Test-Components.ps1` | — |
| arquitectura-cms | Publicación autorizada | ✅ COMPLIANT | `VerifyCorrectivesTest::test_publicacion_autorizada_y_contenido_incompleto` | — |
| arquitectura-cms | Borrador | ✅ COMPLIANT | `DomainModelTest::test_drafts_are_excluded_from_public_queries` | — |
| arquitectura-cms | Operación no autorizada | ✅ COMPLIANT | `DomainModelTest::test_editor_and_administrator_have_domain_capabilities`, `DocumentContactTest` | — |
| arquitectura-cms | Nueva modalidad | ✅ COMPLIANT | `DomainModelTest::test_taxonomies_are_extensible_and_rest_enabled`, `FixturesDomainTest` | — |
| arquitectura-cms | Contenido sin presentación especializada | ✅ COMPLIANT | `VerifyCorrectivesTest::test_presentacion_generica_e_identificador_invalido` | — |
| arquitectura-cms | Identificador inválido | ✅ COMPLIANT | `VerifyCorrectivesTest::test_presentacion_generica_e_identificador_invalido` | — |
| arquitectura-cms | Interfaz inicial | ✅ COMPLIANT | `DomainModelTest::test_visible_strings_are_translation_ready_in_spanish`, E2E | — |
| arquitectura-cms | Texto no configurado | ✅ COMPLIANT | `PublicExperienceTest::test_theme_has_safe_fallback_when_domain_is_unavailable` | — |
| arquitectura-cms | Actualización incompatible | ✅ COMPLIANT | `VerifyCorrectivesTest::test_actualizacion_incompatible_bloquea_sin_alterar_contenido` | — |
| experiencia-publica | Navegación de escritorio | ✅ COMPLIANT | `verify-correctives.spec.ts::navegacion escritorio completa y ubicacion activa` | — |
| experiencia-publica | Navegación móvil | ✅ COMPLIANT | `verify-correctives.spec.ts::navegacion movil conserva foco visible y recorrido predecible` | — |
| experiencia-publica | Destino no disponible | ✅ COMPLIANT | `public-experience.spec.ts::3.1` | — |
| experiencia-publica | Portada completa | ✅ COMPLIANT | `VerifyCorrectivesTest::test_navegacion_completa_portada_completa_y_seccion_opcional`, `home.spec.ts` | — |
| experiencia-publica | Sección opcional oculta | ✅ COMPLIANT | `VerifyCorrectivesTest::test_navegacion_completa_portada_completa_y_seccion_opcional` | — |
| experiencia-publica | Contenido incompleto | ✅ COMPLIANT | `VerifyCorrectivesTest::test_publicacion_autorizada_y_contenido_incompleto` | — |
| experiencia-publica | Consulta publicada | ✅ COMPLIANT | `PublicExperienceTest`, `public-experience.spec.ts::3.2` | — |
| experiencia-publica | Filtro sin coincidencias | ✅ COMPLIANT | `public-experience.spec.ts::3.2 actualidad` | — |
| experiencia-publica | Contenido privado | ✅ COMPLIANT | `PublicExperienceTest::test_public_query_filters_and_excludes_non_public_content`, E2E | — |
| experiencia-publica | Recorrido accesible | ✅ COMPLIANT | `verify-correctives.spec.ts::recorrido accesible`, Playwright/axe | — |
| experiencia-publica | Cambio de tamaño | ✅ COMPLIANT | `public-experience.spec.ts::3.3` en cuatro anchos | — |
| experiencia-publica | Hallazgo o afirmación no sustentada | ✅ COMPLIANT | axe/Playwright para comprobaciones concretas y revisión directa de que no existe afirmación WCAG integral | — |
| documentos-contacto | Publicación válida | ✅ COMPLIANT | `VerifyCorrectivesTest::test_documento_publicado_consulta_combinada_paginada_y_consulta_vacia`, `DocumentContactTest` | — |
| documentos-contacto | Borrador | ✅ COMPLIANT | `DocumentContactTest::test_document_domain_has_permissions_pdf_metadata_and_private_drafts` | — |
| documentos-contacto | Archivo inválido | ✅ COMPLIANT | `DocumentContactTest::test_pdf_validation_checks_real_type_and_size_without_exposing_paths` | — |
| documentos-contacto | Consulta combinada | ✅ COMPLIANT | `VerifyCorrectivesTest::test_documento_publicado_consulta_combinada_paginada_y_consulta_vacia`, `DocumentContactTest` | — |
| documentos-contacto | Consulta vacía | ✅ COMPLIANT | `VerifyCorrectivesTest::test_documento_publicado_consulta_combinada_paginada_y_consulta_vacia` | — |
| documentos-contacto | Enlace no seguro | ✅ COMPLIANT | `DocumentContactTest::test_catalog_combines_text_category_year_and_keeps_safe_links` | — |
| documentos-contacto | Archivo exclusivo | ✅ COMPLIANT | `VerifyCorrectivesTest::test_archivo_exclusivo_sigue_politica_explicita` | — |
| documentos-contacto | Archivo compartido | ✅ COMPLIANT | `DocumentContactTest::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` | — |
| documentos-contacto | Usuario no autorizado | ✅ COMPLIANT | `DocumentContactTest::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` | — |
| documentos-contacto | Envío exitoso | ✅ COMPLIANT | `DocumentContactTest::test_contact_validates_nonce_honeypot_delivery_and_duplicate_token` | — |
| documentos-contacto | Validación accesible | ✅ COMPLIANT | `VerifyCorrectivesTest::test_validacion_accesible_y_error_entrega_sin_datos_personales` | — |
| documentos-contacto | Error de entrega | ✅ COMPLIANT | `VerifyCorrectivesTest::test_validacion_accesible_y_error_entrega_sin_datos_personales` | — |
| calidad-seguridad | Gate satisfactorio | ❌ FAILING | `scripts/gate.ps1 -IncludeBrowser`, WPCS, gate nativo de navegador | CRITICAL |
| calidad-seguridad | Ejecución parcial | ✅ COMPLIANT | contratos 1.13, 5.3 y salida real del gate | — |
| calidad-seguridad | Fallo detectado | ✅ COMPLIANT | gate real devuelve error y conserva evidencia diagnóstica | — |
| calidad-seguridad | Flujos críticos válidos | ✅ COMPLIANT | 38 pruebas PHP, 32 E2E y smokes | — |
| calidad-seguridad | Límites y estados vacíos | ✅ COMPLIANT | `PublicExperienceTest`, `VerifyCorrectivesTest`, E2E | — |
| calidad-seguridad | Entrada maliciosa o no autorizada | ✅ COMPLIANT | `DocumentContactTest`, `DomainModelTest` | — |
| calidad-seguridad | Evidencia dentro del alcance | ✅ COMPLIANT | Playwright/axe 32/32 en cuatro anchos; no se amplía la conclusión | — |
| calidad-seguridad | Auditorías diferidas | ✅ COMPLIANT | comprobación directa de specs, diseño y tareas: Lighthouse, SEO y WCAG integral están registrados como diferidos | — |
| calidad-seguridad | Declaración de cumplimiento no sustentada | ✅ COMPLIANT | comprobación directa: este informe no declara esas auditorías `COMPLIANT` | — |
| calidad-seguridad | Operación protegida | ✅ COMPLIANT | `DocumentContactTest`, `DomainModelTest` | — |
| calidad-seguridad | PDF pendiente de revisión | ✅ COMPLIANT | `FixturesDomainTest::test_fixtures_do_not_create_pdf_attachments`, `Test-Fixtures.ps1` | — |
| calidad-seguridad | Intento manipulado | ✅ COMPLIANT | `DocumentContactTest`, `VerifyCorrectivesTest` | — |

## Resumen por dominio

| Dominio | COMPLIANT | FAILING | UNTESTED | PARTIAL |
|---|---:|---:|---:|---:|
| entorno-desarrollo | 12 | 0 | 0 | 0 |
| arquitectura-cms | 12 | 0 | 0 | 0 |
| experiencia-publica | 12 | 0 | 0 | 0 |
| documentos-contacto | 12 | 0 | 0 | 0 |
| calidad-seguridad | 11 | 1 | 0 | 0 |
| **Total** | **59** | **1** | **0** | **0** |

## Ejecución real

| Comando | Resultado |
|---|---|
| `docker compose --env-file .env.example config --quiet` | PASS |
| `scripts/coverage.ps1` | PASS: 38 pruebas, 188 aserciones; 85,69 % (479/559 líneas) |
| `composer test` | PASS: 1 prueba, 2 aserciones |
| `composer lint` | FAIL: cuatro archivos PHP usan CRLF y WPCS exige LF |
| `composer analyse -- --no-progress` | PASS: PHPStan sin errores |
| `scripts/browser-gate.ps1 -Task playwright` | FAIL: `browser-gate.sh: 2: set: Illegal option -\r` |
| Playwright aislado con copia temporal LF | Primer intento 27/32 por mantenimiento transitorio; único reintento estable PASS 32/32 |
| Contratos 1.1–1.14, cierre, pnpm, correctivos e higiene | PASS |
| Smokes HTTP, DB, fallos, persistencia, fixtures, componentes y dominio | PASS |
| `scripts/gate.ps1 -IncludeBrowser` | FAIL antes de consolidar: Windows PowerShell convierte el progreso normal de BuildKit por stderr en excepción dentro de `coverage.ps1` |

No existe un comando de build independiente configurado. PHPStan funciona como análisis estático, pero no sustituye una señal de build explícita.

## Cobertura de Código

- Cobertura: **85,69 %** (479 de 559 líneas).
- Umbral: **80 %**.
- Estado: **PASS**.

## Evidencia TDD

`gates.apply.tdd` está configurado como `strict`. Las 27 tareas implementativas completadas tienen secciones con RED y GREEN en `apply-progress.md`. La tarea 5.4 es administrativa y fue excluida expresamente del alcance; no se inventa un ciclo RED/GREEN.

## Coherencia de diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Topología Compose persistente | ✅ Implementada | Compose y smokes de DB, HTTP y persistencia |
| Versiones reproducibles | ✅ Implementada | Tags/digests y lockfiles; matriz productiva fuera de alcance |
| Configuración sin secretos | ✅ Implementada | `.env.example`, validación e higiene |
| Tema y plugin independientes | ✅ Implementada | Smokes y fallback |
| Fixtures ficticios idempotentes | ✅ Implementada | Suite PHP y smoke de fixtures |
| PDF restringidos | ✅ Implementada | Cero PDF en fixtures y fuentes no publicadas |
| Rollback conservador | ✅ Implementada | Documentación y persistencia no destructiva |
| Gate reproducible | ❌ Desviado | WPCS, shell CRLF y captura de stderr impiden completar el gate nativo |

## Alcance diferido y excluido

- Lighthouse, rendimiento Lighthouse y SEO: **diferidos**, no `COMPLIANT`.
- Garantía integral WCAG 2.2 AA: **diferida**, no `COMPLIANT`.
- Se conservan como evidencia válida únicamente las comprobaciones concretas Playwright/axe y responsive ejecutadas.
- Hosting, matriz WP/PHP/DB productiva, SMTP real, PDF reales y datos reales: fuera de alcance.

## Fallos Detectados

### Tests fallidos

- `composer lint`: CRLF en `class-labm-documents-contact.php`, `class-labm-domain.php`, `labm-core.php` y `functions.php`; WPCS exige LF.
- `scripts/browser-gate.ps1 -Task playwright`: `scripts/browser-gate.sh: 2: set: Illegal option -\r` por CRLF.
- `scripts/gate.ps1 -IncludeBrowser`: aborta durante cobertura porque Windows PowerShell trata el progreso normal de Docker BuildKit por stderr como excepción.

### Errores de build

- No existe comando de build independiente configurado; PHPStan pasa, pero WPCS falla.

### Tareas incompletas

- Ninguna tarea de `tasks.md`; se requiere un APPLY correctivo por fallos de gate descubiertos en VERIFY.

## Riesgos y recomendación exacta

- **CRITICAL:** el gate documentado no termina exitosamente en Windows PowerShell.
- **CRITICAL:** WPCS falla por CRLF en cuatro archivos PHP.
- **CRITICAL:** el gate nativo Playwright falla por CRLF en `scripts/browser-gate.sh`, aunque las 32 pruebas pasan al normalizar una copia temporal.
- **WARNING:** no existe un comando de build independiente.
- **WARNING:** la skill opcional `testing` no está disponible; se usaron las suites y reglas generales del repositorio.

Regresar a APPLY y limitar el correctivo a:

1. Añadir una política versionada de finales de línea (`.gitattributes`) para PHP y shell, y renormalizar a LF los cuatro PHP reportados y `scripts/browser-gate.sh`.
2. Hacer que `scripts/gate.ps1` capture salida nativa sin convertir el progreso stderr de BuildKit en excepción durante `coverage.ps1`.
3. Añadir contratos que detecten CRLF en archivos ejecutables/PHP y ejecutar de nuevo WPCS, el gate nativo de navegador y el gate agregado completo.

## Decisión del gate

**FAILED.** El siguiente paso es **APPLY**. No procede ARCHIVE.
