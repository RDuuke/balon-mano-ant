# Progreso APPLY: Lote 1

## Tarea C.1 â€” escenarios funcionales del VERIFY

- **RED:** test `tests/contract/Test-VerifyCorrectives.ps1::functional` falla con: `Falta la suite ejecutable de escenarios correctivos` y `Falta evidencia E2E de navegacion escritorio/movil y recorrido accesible`.
- **GREEN:** implementaciÃ³n en `tests/php/VerifyCorrectivesTest.php`, `tests/e2e/verify-correctives.spec.ts`, `labm-core` y tema `labm`; integraciÃ³n WordPress pasa 38 pruebas/188 aserciones y Playwright pasa 32/32.
- **TRIANGULATE:** se cubren runtime incompatible sin mutaciÃ³n, publicaciÃ³n incompleta, presentaciÃ³n genÃ©rica, catÃ¡logo vacÃ­o/paginado, adjunto exclusivo, fallo de entrega sin cuerpo personal, secciÃ³n opcional y navegaciÃ³n mÃ³vil con retorno de foco.
- **REFACTOR:** asociaciones accesibles y marcado de ubicaciÃ³n activa se encapsulan en helpers; WPCS permanece verde.

## Tarea C.2 â€” gate Playwright portable

- **RED:** test `tests/contract/Test-VerifyCorrectives.ps1::toolchain` falla con: `Falta el gate portable de navegador` y `El gate agregado aun depende del pnpm/Node del host`.
- **GREEN:** implementaciÃ³n en `docker/browser/Dockerfile`, `scripts/browser-gate.ps1`, `scripts/browser-gate.sh` y `scripts/gate.ps1`; Node 22.13.1, pnpm 11.17.0 y dependencias Chromium quedan aislados y el gate Playwright pasa 32/32.
- **TRIANGULATE:** el primer bootstrap detectÃ³ una firma Corepack incompatible y se reemplazÃ³ por una instalaciÃ³n fijada durante el build; una segunda construcciÃ³n reutiliza la capa cacheada.
- **REFACTOR:** las URLs WordPress se cambian temporalmente a `host.docker.internal` y se restauran en `finally`; el gate no depende de Node 20.12.2 del host.

## DecisiÃ³n de alcance â€” calidad diferida

- El usuario difiriÃ³ Lighthouse, SEO y la garantÃ­a WCAG 2.2 AA a un cambio futuro. No se ejecutÃ³ trabajo adicional para cerrarlos ni se clasifican como COMPLIANT.
- Existe contradicciÃ³n normativa con `specs/calidad-seguridad/spec.md` y `specs/experiencia-publica/spec.md`, que mantienen MUST/SHALL para auditorÃ­a, umbrales y WCAG AA. Este APPLY no modifica specs fuera de fase; se recomienda regresar formalmente a SPEC antes de un nuevo VERIFY.

## Tarea 5.3 — CI limpio y bloqueante

- **RED:** test `tests/contract/Test-Lote5-Cierre.ps1::5.3` falla con: `falta .github/workflows/quality.yml`; una triangulación posterior detecta que `scripts/gate.ps1` no admitía ejecución embebida para diagnóstico local.
- **GREEN:** implementación en `.github/workflows/quality.yml` y `scripts/gate.ps1`; el contrato confirma instalación desde lockfile, gate agregado, conservación de reportes y ausencia de fallos silenciosos.
- **TRIANGULATE:** el gate local ejecuta Compose, PHPUnit unitario, integración WordPress, WPCS y PHPStan con PASS; Playwright y Lighthouse quedan honestamente `NO EJECUTADA` al faltar el comando `pnpm` en este entorno.
- **REFACTOR:** el agregador conserva comportamiento como archivo y admite ejecución embebida para diagnósticos con políticas de PowerShell restrictivas.

## Tarea 5.5 — Documentación, rollback y trazabilidad

- **RED:** test `tests/contract/Test-Lote5-Cierre.ps1::5.5` falla por ausencia de `docs/traceability.md` y de rollback explícito en README y guías.
- **GREEN:** implementación en `README.md`, `docs/development.md`, `docs/testing.md` y `docs/traceability.md`; el contrato de cierre pasa.
- **TRIANGULATE:** `tests/contract/Test-PnpmToolchain.ps1` pasa y `scripts/test-repository-hygiene.ps1` devuelve `PASS higiene del repositorio`.
- **REFACTOR:** la trazabilidad separa evidencia automatizada de decisiones externas pendientes y exige respaldo/restauración verificados antes de cualquier rollback destructivo.

## Tarea 4.1 — RED de documentos seguros

- **RED:** test `tests/php/DocumentContactTest.php::test_document_domain_has_permissions_pdf_metadata_and_private_drafts` y casos documentales asociados fallan con: `labm_documento no registrado; funciones de PDF, catálogo y adjuntos indefinidas` (27 pruebas, 1 fallo y 4 errores).
- **GREEN:** pruebas contractuales incorporadas a `phpunit.integration.xml.dist`; el conjunto documental pasa dentro de 27 pruebas y 138 aserciones.
- **TRIANGULATE:** test `tests/php/DocumentContactTest.php::test_shared_attachment_is_not_deleted_and_unauthorized_user_changes_nothing` cubre usuario no autorizado y adjunto compartido.
- **REFACTOR:** fixtures de prueba identificados y limpiados entre casos para evitar contaminación del runtime persistente.

## Tarea 4.2 — Documentos y catálogo

- **RED:** test `tests/php/DocumentContactTest.php::test_catalog_combines_text_category_year_and_keeps_safe_links` falla por tipo, taxonomía y funciones de catálogo ausentes.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php` y `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; registro, metadatos REST, permisos, PDF real/tamaño, consulta combinada, URL segura y política de adjuntos pasan.
- **TRIANGULATE:** se cubren borradores excluidos, año/categoría/texto combinados, ausencia de URL y archivo compartido conservado.
- **REFACTOR:** consultas justifican avisos WPCS puntuales y `docs/*.pdf` permanece sin lectura, copia, extracción ni publicación automática.

## Tarea 4.3 — Contacto privado y resiliente

- **RED:** test `tests/php/DocumentContactTest.php::test_contact_validates_nonce_honeypot_delivery_and_duplicate_token` falla con: `Call to undefined function labm_core_process_contact()`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php`; nonce, campos obligatorios, correo, honeypot, SMTP simulado mediante `pre_wp_mail` y token antirrepetición pasan.
- **TRIANGULATE:** el caso cubre correo inválido, honeypot poblado y segundo envío del mismo token sin duplicar correo.
- **REFACTOR:** no se persiste el cuerpo ni los datos personales; solo se conserva temporalmente un hash del token de idempotencia.

## Gates finales del Lote 4

- PHPUnit de integración: 27 pruebas y 138 aserciones, código 0.
- WPCS: sin errores ni avisos, código 0.
- PHPStan: sin errores, código 0.

## Tarea 1.1 — Higiene del repositorio

- **RED:** test `tests/contract/Test-Lote1.ps1::1.1` falla con: `Falta .gitignore`.
- **GREEN:** implementación en `.gitignore` y `scripts/test-repository-hygiene.ps1`; contrato y prueba de higiene pasan.
- **REFACTOR:** la detección evita imprimir rutas o contenido sensible.

## Tarea 1.2 — Entorno ficticio validado

- **RED:** test `tests/contract/Test-Lote1.ps1::1.2` falla con: `Falta .env.example`.
- **GREEN:** implementación en `.env.example`, `.env` ignorado y `scripts/validate-env.ps1`; el ejemplo pasa y un archivo ausente falla sin revelar valores.

## Tarea 1.3 — Docker Compose reproducible

- **RED:** test `tests/contract/Test-Lote1.ps1::1.3` falla con: `Falta compose.yaml`.
- **GREEN:** implementación en `compose.yaml`; `docker compose --env-file .env.example config --quiet` pasa y las tres imágenes usan tag y digest.
- **TRIANGULATE:** `db` y `wordpress` alcanzan estado saludable mediante `docker compose up -d --wait`.

## Tarea 1.4 — Smoke de infraestructura

- **RED:** test `tests/contract/Test-Lote1.ps1::1.4` falla con: `Falta tests/smoke/Test-Http.ps1`.
- **GREEN:** implementación en `tests/smoke/`; HTTP, DB, fallos visibles y persistencia no destructiva pasan.
- **TRIANGULATE:** el primer smoke de persistencia falla con `El marcador no persistio`; se cambia a una opción idempotente y pasa tras reiniciar ambos servicios.

## Tarea 1.5 — Tema de bloques mínimo

- **RED:** test `tests/contract/Test-Lote1.ps1::1.5` falla con: `Falta wp-content/themes/labm/theme.json`.
- **GREEN:** implementación en `wp-content/themes/labm/`; el tema se activa de forma aislada y la portada responde HTTP 200.

## Tarea 1.6 — Plugin persistente mínimo

- **RED:** test `tests/contract/Test-Lote1.ps1::1.6` falla con: `Falta wp-content/plugins/labm-core/labm-core.php`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/`; activación aislada y persistencia al cambiar de tema pasan sin errores fatales.

## Tarea 1.7 — Fallback seguro

- **RED:** test `tests/contract/Test-Lote1.ps1::1.7` falla con: `Falta wp-content/themes/labm/functions.php`.
- **GREEN:** implementación en `wp-content/themes/labm/functions.php`; con `labm-core` desactivado la portada responde HTTP 200 y el plugin puede reactivarse.
- **REFACTOR:** el tema consulta `function_exists` y usa una cadena traducible y escapada.

## Tarea 1.8 — Fixtures idempotentes

- **RED:** test `tests/contract/Test-Lote1.ps1::1.8` falla con: `Falta wp-content/plugins/labm-core/includes/class-fixtures-command.php`.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; dos cargas conservan un registro por slug, preservan contenido ajeno y mantienen cero adjuntos PDF.
- **TRIANGULATE:** test `tests/smoke/Test-Fixtures.ps1::fixtures` cubre repetición, contenido editorial ajeno y MIME PDF.

## Tarea 1.9 — Bootstrap idempotente

- **RED:** test `tests/contract/Test-Lote1.ps1::1.9` falla con: `Falta scripts/bootstrap.ps1`; además, el usuario observa el asistente de instalación.
- **GREEN:** implementación en `scripts/bootstrap.ps1`; dos ejecuciones terminan con código 0, `wp core is-installed` devuelve 0 y `home/siteurl` son `http://localhost:8080`.
- **TRIANGULATE:** test `tests/smoke/Test-Http.ps1::installer` rechaza `install.php` y el texto del asistente; la portada instalada pasa.

## Tarea 1.10 — Gates PHP y WordPress

- **RED:** test `tests/contract/Test-Lote1.ps1::1.10` falla con: `Falta composer.json`; la primera integración falla porque Composer no incluye `mysqli`, y el agregador detecta mezcla de suites.
- **GREEN:** implementación en `composer.json`, `composer.lock`, configuraciones y `tests/php/`; PHPUnit unitario pasa 1 prueba/2 aserciones, integración WordPress pasa 2 pruebas/7 aserciones, WPCS y PHPStan pasan.
- **REFACTOR:** unitarias e integración usan suites separadas y la integración ejecuta PHP 8.3 con `mysqli`.

## Tarea 1.11 — Playwright y axe

- **RED:** test `tests/contract/Test-Lote1.ps1::1.11` falla con: `Falta package.json`; luego Playwright detecta selector ambiguo y axe detecta una lista de navegación inválida.
- **GREEN:** implementación vigente en `package.json`, `pnpm-lock.yaml`, `playwright.config.ts` y `tests/e2e/home.spec.ts`; pasan 4 pruebas en 320, 768, 1024 y 1440.
- **REFACTOR:** la navegación usa enlaces explícitos con estructura de lista válida.

## Tarea 1.12 — Lighthouse baseline

- **RED:** test `tests/contract/Test-Lote1.ps1::1.12` falla con: `Falta lighthouserc.json`; el primer gate falla por aserciones finales prematuras y por `EPERM` al limpiar temporales en Windows.
- **GREEN:** implementación en `lighthouserc.json` y `scripts/lighthouse.ps1`; reporte validado con Performance 100, Accessibility 100, Best Practices 100 y SEO 82, sin imponer umbrales finales.
- **REFACTOR:** el wrapper solo tolera el `EPERM` de limpieza si existe un reporte completo sin `runtimeError`; cualquier otro fallo conserva código no exitoso.

## Tarea 1.13 — Gate agregado honesto

- **RED:** test `tests/contract/Test-Lote1.ps1::1.13` falla con: `Falta scripts/gate.ps1`; una ejecución intermedia registra Lighthouse `FAIL`.
- **GREEN:** implementación en `scripts/gate.ps1`; el resumen final registra PASS para Compose, PHPUnit, integración WordPress, WPCS, PHPStan, Playwright y Lighthouse.
- **TRIANGULATE:** una herramienta ausente queda `NO EJECUTADA` y hace fallar el gate por contrato.

## Tarea 1.14 — Documentación operativa

- **RED:** test `tests/contract/Test-Lote1.ps1::1.14` falla con: `Falta README.md`.
- **GREEN:** implementación en `README.md`, `docs/development.md` y `docs/testing.md`; el contrato cubre ciclo de vida, diagnóstico, pruebas, confirmación de reset y entorno limpio.

## Tarea PNPM — Migración exclusiva del toolchain JS

- **RED:** test `tests/contract/Test-PnpmToolchain.ps1::gestor_exclusivo` falla con: `packageManager debe fijar una version concreta de pnpm`.
- **GREEN:** implementación en `package.json`, `pnpm-lock.yaml`, scripts, gates, documentación y contratos; `pnpm install --frozen-lockfile` y el contrato pnpm pasan sin referencias operativas obsoletas.
- **TRIANGULATE:** `pnpm run test:e2e` pasa 4/4 viewports y `pnpm run lighthouse` valida baseline 100/100/100/82.
- **REFACTOR:** se valida y retira exclusivamente `C:\Users\rduuqe\Documents\BalonManoAnt\node_modules` generado por npm antes de reconstruirlo con pnpm; `package-lock.json` queda eliminado.

## Tarea 2.1 — RED del modelo de dominio

- **RED:** test `tests/php/DomainModelTest.php::modelo_dominio` y `tests/php/FixturesDomainTest.php::fixtures_dominio` falla con 11 fallos: cinco CPT ausentes, taxonomías/metadatos/capacidades/i18n sin registrar y fixtures inexistentes.
- **GREEN:** implementación posterior en `labm-core`; la suite completa pasa 16 pruebas y 82 aserciones.
- **TRIANGULATE:** las pruebas cubren usuario suscriptor sin capacidades, fecha ISO inválida y borrador excluido de consulta pública.

## Tarea 2.2 — Dominio persistente en labm-core

- **RED:** test `tests/php/DomainModelTest.php::capacidades` falla después del primer registro porque los nombres de capacidades no conservan el prefijo `labm_`.
- **GREEN:** implementación en `wp-content/plugins/labm-core/includes/class-labm-domain.php`; cinco CPT, dos taxonomías, metadatos REST, validación, capacidades e i18n pasan.
- **TRIANGULATE:** test `tests/smoke/Test-DomainPersistence.ps1::cambio_tema` conserva contenido y metadatos al activar Twenty Twenty-Five y restaurar LABM.
- **REFACTOR:** capacidades idempotentes versionadas y separación del dominio respecto del tema.

## Tarea 2.3 — Fixtures de dominio y bordes

- **RED:** test `tests/php/FixturesDomainTest.php::estados_y_terminos` falla porque los fixtures de dominio no existen; la primera implementación deja `Noticias` sin asignar por usar nombres en una taxonomía jerárquica.
- **GREEN:** implementación en `class-labm-fixtures-command.php`; fixtures públicos, borradores, privados, categorías, modalidades y metadatos pasan 16 pruebas/82 aserciones tras asignar IDs de término.
- **TRIANGULATE:** test `tests/smoke/Test-DomainPersistence.ps1::contenido_ajeno` preserva contenido editorial ajeno; dos cargas no duplican slugs y la suite confirma cero adjuntos PDF.
- **REFACTOR:** upsert común por slug, tipo y marcador ficticio, sin acceso a `docs/*.pdf`.

## Tarea 3.1 — Navegación, Inicio y Nosotros

- **RED:** `pnpm run test:e2e -- tests/e2e/public-experience.spec.ts --project=mobile-320` termina con código 1; `3.1 navegación global...` no encuentra el enlace Inicio en la navegación y las rutas/patrones institucionales aún no existen.
- **GREEN:** patrones `labm/inicio` y `labm/nosotros`, plantillas `front-page`, `page-nosotros` y `404`, navegación global y skip link implementados; `pnpm run test:e2e` pasa 20/20 pruebas en 320/768/1024/1440.
- **TRIANGULATE:** teclado produce un `:focus-visible`; `/nosotros/` muestra H1 y `/ruta-ficticia-ausente/` responde 404 con enlace de retorno.
- **REFACTOR:** todas las cadenas de patrones PHP son traducibles y el contenido de muestra conserva `[DEMO LABM — FICTICIO]`.

## Tarea 3.2 — Actualidad y selecciones públicas

- **RED:** integración WordPress termina con código 2: 20 pruebas, 1 error y 3 fallos por templates ausentes, slug `seleccion` en vez de `selecciones` y funciones públicas inexistentes; el E2E móvil adicional falla al no encontrar tarjetas de actualidad/selecciones.
- **GREEN:** templates de archivo/detalle, consultas solo `publish`, filtros de categoría/modalidad, tres elementos por página, navegación de páginas, vacíos y fallback seguro implementados; integración pasa 22 pruebas/115 aserciones y Playwright pasa 20/20.
- **TRIANGULATE:** Piso y Playa devuelven fixtures públicos separados; borrador y selección privada no aparecen; el filtro inexistente ofrece “Limpiar filtros” y el detalle conserva retorno al archivo.
- **REFACTOR:** el plugin mantiene CPT, taxonomías, rutas y fixtures persistentes; el tema limita su responsabilidad a consulta/renderizado y responde con fallback si el tipo no está registrado.

## Tarea 3.3 — Tokens, responsive y WCAG

- **RED:** `FrontendTokensTest::test_responsive_design_tokens_are_declared_in_theme_json` termina con código 1 (22 pruebas, 1 fallo) porque `settings.custom.labm.spacing.small` era `null`.
- **GREEN:** tokens fluidos y radio declarados en `theme.json`, layouts `minmax(min(100%, ...))`, foco visible y `prefers-reduced-motion` implementados; integración pasa 22/115 y E2E/axe pasa 20/20 en 320/768/1024/1440.
- **TRIANGULATE:** las cuatro rutas públicas no presentan desborde horizontal con movimiento reducido; capturas reales `inicio-320.png` y `actualidad-1440.png` se inspeccionaron sin desbordes, contraste defectuoso ni foco truncado visibles.
- **REFACTOR:** se retiró el contenido predeterminado de WordPress de la portada y la paginación se compactó como lista flexible.

## Gates finales del Lote 3

- `scripts/gate.ps1 -IncludeBrowser`: 7/7 PASS (Compose, PHPUnit, integración, WPCS, PHPStan, Playwright y Lighthouse), código 0.
- Smokes HTTP, DB, fallos visibles, fixtures sin PDF y activación/fallback: PASS; contrato pnpm exclusivo: PASS.
- Lighthouse baseline: Performance 100, Accessibility 100, Best Practices 100 y SEO 91; se conserva la tolerancia documentada al `EPERM` de limpieza solo después de validar el reporte.
- Auditorías informativas aceptadas: `pnpm audit` código 1 con 6 avisos (3 altos, 1 moderado, 2 bajos); Composer audit código 1 con 2 avisos altos. No se hicieron upgrades mayores.

## Intento del Lote 4 — bloqueo del gate RED

- Se añadió `tests/php/DocumentContactTest.php` al conjunto de integración para cubrir permisos, PDF real y tamaño, catálogo combinado, enlaces seguros, adjuntos compartidos, nonce, antispam, validación, SMTP simulado y no duplicación.
- El comando de integración no pudo ejecutar PHPUnit porque el motor Docker no está iniciado: `open //./pipe/docker_engine: El sistema no puede encontrar el archivo especificado`.
- Conforme al TDD estricto, no se escribió código productivo, no se confirmó RED y las tareas 4.1–4.3 permanecen pendientes.

## Reintento del Lote 4 — Docker no disponible

- El 2026-08-24 22:07 se ejecutó `docker info --format '{{.ServerVersion}}'` antes de reanudar el ciclo RED.
- PowerShell devolvió `CommandNotFoundException`: el comando `docker` no está disponible en este entorno.
- El gate RED continúa sin evidencia ejecutada; no se modificó código productivo y las tareas 4.1–4.3 permanecen pendientes.

## Reintento del Lote 5 — verificación de cierre

- El 2026-08-24 se ejecutó el gate completo con navegador. Compose, PHPUnit unitario, integración WordPress, WPCS y PHPStan terminaron en `PASS`.
- **Tarea 5.1:** permanece pendiente. La suite contiene pruebas unitarias, de integración, E2E, smoke y contrato suficientes para superar 60 comprobaciones ejecutables, pero no existe un reporte reproducible que demuestre cobertura de código mayor o igual a 80%. En TDD estricto no se marca completada sin esa evidencia.
- **Tarea 5.2:** permanece pendiente. Playwright no arrancó porque pnpm 11.17.0 requiere Node.js 22.13 o posterior y el entorno ofrece Node.js 20.12.2. Lighthouse tampoco se ejecutó; además, `lighthouserc.json` conserva umbrales de baseline en cero, no los umbrales finales 85/90. Falta también la auditoría manual WCAG 2.2 AA.
- **Tarea 5.4:** permanece pendiente por las decisiones externas ya declaradas: matriz de hosting/PHP/DB, SMTP y autorización de PDF y datos institucionales reales.
- No se modificó código productivo ni se marcaron tareas como completadas durante este reintento.

## Reintento del Lote 5 — evidencia reproducible ampliada

- **Tarea 5.1, RED:** el contrato `tests/contract/Test-Lote5-Cierre.ps1::5.1` detectó que faltaban imagen, gate y evidencia Clover de cobertura. Los runtimes existentes confirmaron `No code coverage driver available`.
- **Tarea 5.1, GREEN parcial:** se añadieron `docker/coverage/Dockerfile`, PCOV 1.0.12, delimitación del PHP propio y `scripts/coverage.ps1`; el contrato pasa y PHPUnit mantiene 27 pruebas/138 aserciones. El gate produce Clover y falla correctamente: cobertura real 23.15% (116/501 líneas), inferior al 80%. La tarea permanece pendiente.
- **Tarea 5.2, RED:** el contrato detectó umbrales Lighthouse en cero. Una ejecución real de Playwright con Node 22 aislado ejecutó 20 casos: 6 pasaron y 14 fallaron; entre los hallazgos válidos, axe informó contraste 1.62:1 entre texto blanco y fondo amarillo del botón «Ver actualidad». También hubo timeouts y navegación hacia `localhost` desde el contenedor, por lo que esos fallos de transporte no se interpretan como defectos funcionales.
- **Tarea 5.2, GREEN parcial:** Lighthouse ahora incluye Inicio, Nosotros, Actualidad y Selecciones y aplica Performance >=85 y las otras tres categorías >=90. El botón amarillo usa texto azul y la integración WordPress pasa 27/27; WPCS pasa. No pudo repetirse axe tras el arreglo porque la descarga local de Chromium agotó el espacio temporal. Lighthouse y la auditoría manual WCAG 2.2 AA siguen pendientes; la tarea no se marca completada.
- **Tarea 5.4, entradas mínimas:** se requiere proveedor/plan de hosting; versiones objetivo y soportadas de WordPress, PHP y motor/versión de base de datos; límites de memoria, subida y ejecución; dominio/TLS; estrategia, frecuencia, retención y prueba de restauración de backups; proveedor SMTP, host/puerto/cifrado, remitente, destinatarios, credenciales por secreto, SPF/DKIM/DMARC, antispam y retención; responsable nominal que autorice cada PDF y cada conjunto de datos institucionales reales, con alcance, procedencia y fecha de autorización. Sin estas entradas no procede matriz ni despliegue.

## Tarea 5.1 — cobertura y cierre de escenarios

- **RED:** el gate `scripts/coverage.ps1` ejecutó 27 pruebas y 138 aserciones, produjo Clover y falló con `Cobertura PHP 23.15% (116/501 líneas), inferior al mínimo 80%`.
- **GREEN:** se añadió `tests/php/ClosingCoverageTest.php` a `phpunit.integration.xml.dist` para invocar explícitamente registro, activación, capacidades, reglas, helpers, fixtures idempotentes y renderizado público; el gate pasa con 31 pruebas, 158 aserciones y 86.83% (435/501 líneas). Junto con 20 E2E y las suites unitarias, smoke y contrato, se superan 60 comprobaciones ejecutables.
- **TRIANGULATE:** la prueba de fixtures ejecuta dos cargas, cubre actualización idempotente y preservación de contenido editorial ajeno; los helpers cubren fechas válidas, imposibles y tipos no admitidos.
- **REFACTOR:** la medición usa la misma ruta runtime de WordPress que declara Clover; no se modificó código productivo para alterar artificialmente el porcentaje.

## Tarea 5.2 — WCAG y Lighthouse

- **RED:** Playwright ejecutó 20 casos; una primera corrida útil pasó 16 y falló cuatro navegaciones de Actualidad porque WordPress devolvía enlaces canónicos a `localhost` dentro del contenedor. Una corrida de diagnóstico confirmó `ERR_CONNECTION_REFUSED`; no se clasificó como defecto funcional. Lighthouse inicialmente falló su healthcheck al no recibir una ruta de Chrome.
- **GREEN:** con `home` y `siteurl` cambiados temporalmente a `host.docker.internal` y restaurados después, Playwright/axe pasa 20/20 en 320, 768, 1024 y 1440. Lighthouse CI audita Inicio, Nosotros, Actualidad y Selecciones: Performance 100, Accessibility 100, Best Practices 100 y SEO 91/92/91/91, superando 85/90.
- **TRIANGULATE:** las cuatro rutas se recorren con movimiento reducido, sin desborde horizontal y sin violaciones axe automáticas; los reportes HTML/JSON quedan en `artifacts/lighthouse/`.
- **REFACTOR:** las URLs locales se restauraron a `http://localhost:8080`; no se publicó contenido ni se usaron PDF o datos reales.
- **PENDIENTE:** falta una auditoría manual WCAG 2.2 AA por una persona competente. La evidencia automática no cubre todos los criterios de conformidad; 5.2 no se marca completada.

## Tarea 5.4 — matriz y despliegue

- **BLOQUEO:** continúan sin definirse hosting, matriz objetivo WordPress/PHP/DB, límites productivos, dominio/TLS, backups y restauración, SMTP y política antispam/retención. Tampoco existe autorización nominal para PDF ni datos institucionales reales.
- **EVIDENCIA:** no se ejecutó despliegue ni se accedió a fuentes restringidas. La tarea permanece sin marcar hasta recibir esas decisiones y autorizaciones.

## Cierre autorizado de las tareas 5.2 y 5.4

- **Tarea 5.2 — aprobación manual:** el 2026-08-27 el usuario confirmó que la auditoría manual fue revisada y está conforme. Esta aprobación completa la evidencia automática ya registrada: Playwright/axe 20/20 en 320, 768, 1024 y 1440, y Lighthouse por vista con Performance 100, Accessibility 100, Best Practices 100 y SEO 91/92/91/91. Se marca 5.2 completada sin repetir ni inventar una auditoría manual.
- **Tarea 5.4 — exclusión de alcance:** el 2026-08-27 el usuario declaró expresamente que queda fuera del alcance. No se validaron hosting, SMTP ni una matriz productiva WP/PHP/DB; no se ejecutó despliegue; no se usaron credenciales, PDF ni datos institucionales reales.
- **Coherencia de alcance:** la exclusión no contradice requisitos normativos del cambio. `proposal.md` declara fuera de alcance despliegue, hosting, dominio, SMTP y contenido oficial; `design.md` limita el incremento al entorno local y pospone matriz, SMTP y promoción hasta aprobar hosting. Por tanto, no se requiere regresar a PROPOSE, SPEC ni DESIGN.

## Tarea correctivo APPLY — portabilidad de gates

- **RED:** comprobación de finales de línea y captura nativa falló: CRLF en `class-labm-documents-contact.php`, `class-labm-domain.php`, `labm-core.php`, `functions.php` y `scripts/browser-gate.sh`; `gate.ps1` no aislaba stderr nativo.
- **GREEN:** los cinco archivos quedaron normalizados a LF; `scripts/gate.ps1` aísla cobertura y navegador con `System.Diagnostics.Process`, redirige stdout/stderr y conserva códigos de salida. Contratos de toolchain y sintaxis pasan; el gate alcanzó PASS en Compose, PHPUnit, integración WordPress, cobertura, WPCS y PHPStan.
- **TRIANGULATE:** el primer gate aislado confirmó cobertura PASS y detectó el argumento entrecomillado incorrectamente; se corrigió la construcción de `-Task` antes del intento final.
- **REFACTOR:** se eliminó la dependencia de `Start-Process`, incompatible con el entorno por claves duplicadas `Path/PATH`, y se mantuvo la captura OS-level sin tocar specs ni diseño.
- **PENDIENTE:** por decisión de alcance, no se repite el gate agregado ni el browser gate. El artefacto disponible conserva el fallo previo de argumento; no se ejecutó VERIFY formal ni ARCHIVE.
