# Informe de verificación: Sitio WordPress de LABM

## Resumen ejecutivo

La verificación ejecutó pruebas reales y contrastó los 60 escenarios de las especificaciones con pruebas identificables. PHPUnit de integración y cobertura pasó con 31 pruebas, 158 aserciones y 86,83 % de líneas; Playwright/axe pasó 20 de 20 casos en 320, 768, 1024 y 1440 px usando Node.js 22 en Docker. Compose, PHPUnit unitario, integración WordPress, WPCS y PHPStan también pasaron dentro del gate agregado.

El resultado global es **FAILED**: 43 escenarios son COMPLIANT, 1 es FAILING, 5 son UNTESTED y 11 son PARTIAL. Conforme al contrato de VERIFY, un escenario sin una prueba completa equivale a fallo. No se corrigió ningún hallazgo ni se ejecutó ARCHIVE.

La tarea 5.4 se considera formalmente excluida del alcance: no se intentaron despliegue, hosting, SMTP real, PDF reales ni datos institucionales reales.

## Completitud de tareas

- Tareas marcadas completas: 28.
- Tareas incompletas: ninguna.
- 5.2 incluye aprobación expresa de la revisión manual WCAG 2.2 AA, además de evidencia automatizada.
- 5.4 está cerrada administrativamente por exclusión expresa y coherente con `proposal.md` y `design.md`.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---|---|---|---|---|
| arquitectura-cms | Activación independiente | ✅ COMPLIANT | `tests/smoke/Test-Components.ps1` | — |
| arquitectura-cms | Cambio de tema | ✅ COMPLIANT | `tests/smoke/Test-DomainPersistence.ps1`, `tests/smoke/Test-Components.ps1` | — |
| arquitectura-cms | Plugin inactivo | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php::test_theme_has_safe_fallback_when_domain_is_unavailable`, `tests/smoke/Test-Components.ps1` | — |
| arquitectura-cms | Publicación autorizada | ⚠️ PARTIAL | `tests/php/DomainModelTest.php::test_editor_and_administrator_have_domain_capabilities`, `tests/php/PublicExperienceTest.php::test_public_query_filters_and_excludes_non_public_content` | WARNING |
| arquitectura-cms | Borrador | ✅ COMPLIANT | `tests/php/DomainModelTest.php::test_drafts_are_excluded_from_public_queries` | — |
| arquitectura-cms | Operación no autorizada | ✅ COMPLIANT | `tests/php/DomainModelTest.php::test_editor_and_administrator_have_domain_capabilities`, `tests/php/DocumentContactTest.php::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` | — |
| arquitectura-cms | Nueva modalidad | ✅ COMPLIANT | `tests/php/DomainModelTest.php::test_taxonomies_are_extensible_and_rest_enabled`, `tests/php/FixturesDomainTest.php::test_fixture_terms_and_metadata_are_present` | — |
| arquitectura-cms | Contenido sin presentación especializada | ⚠️ PARTIAL | `tests/php/PublicExperienceTest.php::test_theme_has_safe_fallback_when_domain_is_unavailable` | WARNING |
| arquitectura-cms | Identificador inválido | ⚠️ PARTIAL | `tests/php/DomainModelTest.php::test_metadata_is_registered_and_sanitized` | WARNING |
| arquitectura-cms | Interfaz inicial | ✅ COMPLIANT | `tests/php/DomainModelTest.php::test_visible_strings_are_translation_ready_in_spanish`, `tests/e2e/home.spec.ts` | — |
| arquitectura-cms | Texto no configurado | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php::test_theme_has_safe_fallback_when_domain_is_unavailable` | — |
| arquitectura-cms | Actualización incompatible | ❌ UNTESTED | — | CRITICAL |
| calidad-seguridad | Gate satisfactorio | ❌ FAILING | `scripts/gate.ps1 -IncludeBrowser` | CRITICAL |
| calidad-seguridad | Ejecución parcial | ✅ COMPLIANT | `tests/contract/Test-Lote1.ps1`, `tests/contract/Test-Lote5-Cierre.ps1` | — |
| calidad-seguridad | Fallo detectado | ✅ COMPLIANT | `tests/contract/Test-Lote5-Cierre.ps1` | — |
| calidad-seguridad | Flujos críticos válidos | ✅ COMPLIANT | `tests/php/*.php`, `tests/e2e/*.spec.ts`, gates ejecutados | — |
| calidad-seguridad | Límites y estados vacíos | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php`, `tests/e2e/public-experience.spec.ts` | — |
| calidad-seguridad | Entrada maliciosa o no autorizada | ✅ COMPLIANT | `tests/php/DocumentContactTest.php`, `tests/php/DomainModelTest.php` | — |
| calidad-seguridad | Auditoría conforme | ✅ COMPLIANT | Playwright/axe 20/20, Lighthouse 100/100/100 y SEO 91–92, aprobación manual 5.2 | — |
| calidad-seguridad | Variación controlada | ✅ COMPLIANT | Evidencia 5.2 en `apply-progress.md` | — |
| calidad-seguridad | Umbral incumplido | ✅ COMPLIANT | `tests/contract/Test-Lote5-Cierre.ps1::5.2`, aserciones de `lighthouserc.json` | — |
| calidad-seguridad | Operación protegida | ✅ COMPLIANT | `tests/php/DocumentContactTest.php`, `tests/php/DomainModelTest.php` | — |
| calidad-seguridad | PDF pendiente de revisión | ✅ COMPLIANT | `tests/php/FixturesDomainTest.php::test_fixtures_do_not_create_pdf_attachments`, `tests/smoke/Test-Fixtures.ps1` | — |
| calidad-seguridad | Intento manipulado | ✅ COMPLIANT | `tests/php/DocumentContactTest.php` | — |
| documentos-contacto | Publicación válida | ⚠️ PARTIAL | `tests/php/DocumentContactTest.php::test_document_domain_has_permissions_pdf_metadata_and_private_drafts` | WARNING |
| documentos-contacto | Borrador | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_document_domain_has_permissions_pdf_metadata_and_private_drafts` | — |
| documentos-contacto | Archivo inválido | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_pdf_validation_checks_real_type_and_size_without_exposing_paths` | — |
| documentos-contacto | Consulta combinada | ⚠️ PARTIAL | `tests/php/DocumentContactTest.php::test_catalog_combines_text_category_year_and_keeps_safe_links` | WARNING |
| documentos-contacto | Consulta vacía | ❌ UNTESTED | — | CRITICAL |
| documentos-contacto | Enlace no seguro | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_catalog_combines_text_category_year_and_keeps_safe_links` | — |
| documentos-contacto | Archivo exclusivo | ❌ UNTESTED | — | CRITICAL |
| documentos-contacto | Archivo compartido | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` | — |
| documentos-contacto | Usuario no autorizado | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` | — |
| documentos-contacto | Envío exitoso | ✅ COMPLIANT | `tests/php/DocumentContactTest.php::test_contact_validates_nonce_honeypot_delivery_and_duplicate_token` | — |
| documentos-contacto | Validación accesible | ⚠️ PARTIAL | `tests/php/DocumentContactTest.php::test_contact_validates_nonce_honeypot_delivery_and_duplicate_token` | WARNING |
| documentos-contacto | Error de entrega | ❌ UNTESTED | — | CRITICAL |
| entorno-desarrollo | Arranque limpio | ✅ COMPLIANT | `tests/smoke/Test-Http.ps1`, `tests/smoke/Test-Database.ps1` | — |
| entorno-desarrollo | Reinicio con persistencia | ✅ COMPLIANT | `tests/smoke/Test-Persistence.ps1` | — |
| entorno-desarrollo | Dependencia no disponible | ✅ COMPLIANT | `tests/smoke/Test-Failure.ps1` | — |
| entorno-desarrollo | Configuración desde ejemplo | ✅ COMPLIANT | `tests/contract/Test-Lote1.ps1::1.2`, `scripts/validate-env.ps1` | — |
| entorno-desarrollo | Personalización local | ✅ COMPLIANT | `tests/contract/Test-Lote1.ps1::1.2` | — |
| entorno-desarrollo | Configuración incompleta | ✅ COMPLIANT | `scripts/validate-env.ps1`, evidencia TDD 1.2 | — |
| entorno-desarrollo | Carga inicial | ✅ COMPLIANT | `tests/smoke/Test-Fixtures.ps1`, `tests/php/FixturesDomainTest.php` | — |
| entorno-desarrollo | Carga repetida | ✅ COMPLIANT | `tests/smoke/Test-Fixtures.ps1`, `tests/php/ClosingCoverageTest.php::test_fixture_command_is_idempotent_and_preserves_foreign_content` | — |
| entorno-desarrollo | Fuente sensible | ✅ COMPLIANT | `tests/php/FixturesDomainTest.php::test_fixtures_do_not_create_pdf_attachments` | — |
| entorno-desarrollo | Estado versionable | ✅ COMPLIANT | `scripts/test-repository-hygiene.ps1`, `tests/contract/Test-Lote1.ps1::1.1` | — |
| entorno-desarrollo | Archivos locales existentes | ✅ COMPLIANT | `tests/smoke/Test-Persistence.ps1`, reglas de `.gitignore` | — |
| entorno-desarrollo | Secreto accidental | ✅ COMPLIANT | `scripts/test-repository-hygiene.ps1` | — |
| experiencia-publica | Navegación de escritorio | ⚠️ PARTIAL | `tests/e2e/public-experience.spec.ts::3.1` | WARNING |
| experiencia-publica | Navegación móvil | ⚠️ PARTIAL | `tests/e2e/public-experience.spec.ts::3.1` | WARNING |
| experiencia-publica | Destino no disponible | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::3.1` | — |
| experiencia-publica | Portada completa | ⚠️ PARTIAL | `tests/e2e/home.spec.ts`, `tests/php/WordPressRuntimeTest.php` | WARNING |
| experiencia-publica | Sección opcional oculta | ❌ UNTESTED | — | CRITICAL |
| experiencia-publica | Contenido incompleto | ⚠️ PARTIAL | `tests/php/DomainModelTest.php::test_metadata_is_registered_and_sanitized` | WARNING |
| experiencia-publica | Consulta publicada | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php`, `tests/e2e/public-experience.spec.ts::3.2` | — |
| experiencia-publica | Filtro sin coincidencias | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::3.2 actualidad` | — |
| experiencia-publica | Contenido privado | ✅ COMPLIANT | `tests/php/PublicExperienceTest.php::test_public_query_filters_and_excludes_non_public_content`, E2E 3.2 | — |
| experiencia-publica | Recorrido accesible | ⚠️ PARTIAL | `tests/e2e/public-experience.spec.ts::3.1`, Playwright/axe | WARNING |
| experiencia-publica | Cambio de tamaño | ✅ COMPLIANT | `tests/e2e/public-experience.spec.ts::3.3` en cuatro anchos | — |
| experiencia-publica | Incumplimiento AA | ✅ COMPLIANT | axe automático y aprobación manual de 5.2 | — |

## Resumen por dominio

| Dominio | COMPLIANT | FAILING | UNTESTED | PARTIAL |
|---|---:|---:|---:|---:|
| arquitectura-cms | 8 | 0 | 1 | 3 |
| calidad-seguridad | 11 | 1 | 0 | 0 |
| documentos-contacto | 6 | 0 | 3 | 3 |
| entorno-desarrollo | 12 | 0 | 0 | 0 |
| experiencia-publica | 6 | 0 | 1 | 5 |
| **Total** | **43** | **1** | **5** | **11** |

## Ejecución real

| Comando | Resultado |
|---|---|
| `scripts/gate.ps1 -IncludeBrowser` | Código 1: Compose, PHPUnit unitario, integración WordPress, WPCS y PHPStan PASS; cobertura, Playwright y Lighthouse fallaron en el agregador por el runtime Node.js 20.12.2 del host, incompatible con pnpm 11.17.0. |
| `scripts/coverage.ps1` | PASS: 31 pruebas, 158 aserciones; 86,83 % (435/501 líneas). |
| Playwright 1.54.1 con Node.js 22 en Docker y dependencias del navegador | PASS: 20/20 en 18,1 s, con axe en 320, 768, 1024 y 1440 px. |
| Lighthouse CI del cierre APPLY | PASS documentado: cuatro vistas, Performance 100, Accessibility 100, Best Practices 100 y SEO 91/92/91/91. No pudo ejecutarse mediante el agregador de VERIFY por incompatibilidad del runtime local. |

No existe un comando de build o type-check independiente configurado. PHPStan y WPCS sí pasaron como validaciones estáticas dentro del gate.

## Cobertura de Código

- Cobertura: **86,83 %** (435 de 501 líneas).
- Umbral: **80 %**.
- Estado: **PASS**.

## Evidencia TDD

`gates.apply.tdd` está configurado como `strict`. Las 27 tareas implementativas completadas tienen secciones con evidencia RED y GREEN en `apply-progress.md`. La tarea 5.4 no es implementativa: fue excluida expresamente del alcance y su cierre administrativo está documentado, por lo que no se exige un ciclo RED/GREEN ficticio.

## Coherencia de diseño

| Decisión | Estado | Evidencia |
|---|---|---|
| Topología Compose con estado persistente y componentes montados | ✅ Implementada | `compose.yaml`, smokes de DB/HTTP/persistencia |
| Versiones reproducibles | ✅ Implementada | Tags/digests y lockfiles; matriz productiva permanece fuera de alcance |
| Configuración sin secretos | ✅ Implementada | `.env.example`, validación e higiene |
| Tema y plugin independientes | ✅ Implementada | Smokes de componentes y fallback |
| Fixtures ficticios idempotentes | ✅ Implementada | Pruebas de fixtures y cobertura de cierre |
| PDF restringidos | ✅ Implementada | Cero adjuntos PDF en fixtures; no se accedió a `docs/*.pdf` |
| Rollback conservador | ✅ Implementada | Documentación y persistencia no destructiva |

## Fallos Detectados

### Tests fallidos
- `scripts/gate.ps1 -IncludeBrowser`: el gate agregado devuelve código 1 porque pnpm 11.17.0 requiere Node.js 22.13 o posterior y el host ejecuta Node.js 20.12.2.
- `arquitectura-cms / Actualización incompatible`: no existe prueba asociada.
- `documentos-contacto / Consulta vacía`: no existe prueba asociada.
- `documentos-contacto / Archivo exclusivo`: no existe prueba asociada.
- `documentos-contacto / Error de entrega`: no existe prueba asociada.
- `experiencia-publica / Sección opcional oculta`: no existe prueba asociada.

### Errores de build
- No existe comando de build independiente configurado; PHPStan y WPCS pasan, pero no sustituyen esa señal contractual.

### Tareas incompletas
- Ninguna tarea de `tasks.md`; los fallos corresponden a cobertura insuficiente de escenarios y portabilidad del gate.

## Riesgos y recomendaciones

- **CRITICAL:** el gate documentado no es reproducible en el runtime actual del host aunque las suites afectadas pasen con Node.js 22 en Docker.
- **CRITICAL:** cinco escenarios carecen de prueba directa y, por contrato, son equivalentes a fallo.
- **WARNING:** once escenarios solo están cubiertos parcialmente; deben completarse sus aserciones de extremo a extremo.
- **WARNING:** no hay comando de build independiente configurado.
- **WARNING:** la skill opcional `testing` no está disponible; la verificación se realizó con las reglas generales y las pruebas del repositorio.
- **SUGGESTION:** al volver a APPLY, priorizar primero los cinco escenarios UNTESTED y la portabilidad de Node/pnpm del gate; después completar los once escenarios PARTIAL.

## Decisión del gate

**FAILED.** El cambio debe regresar a **APPLY**. No procede ARCHIVE hasta que el gate agregado pase en el entorno documentado y todos los escenarios tengan pruebas completas que pasen.
