# Diseño: corregir el gate global

## Enfoque técnico

Lotes trazables a doce requisitos; preservar cambios anteriores y snapshots. Baseline: PHPUnit7fallos, cobertura73.47%, PHPCS35errores/68warnings, PHPStan1error, Playwright28fallos. Primero contratos autónomos, contraste y calidad; después cobertura por Clover. Dimensiones y Detalle requieren dependencias explícitas. Cierre: `scripts/gate.ps1 -IncludeBrowser` íntegro.

## Decisiones de arquitectura

| Opción | Compensación | Decisión y justificación |
|---|---|---|
| Nosotros: contar nodos o comprobar contenido perceptible | `hidden` conserva integrantes públicos para filtrado local | Conservar render y comprobar visibles, no operables ocultos, filtro activo y navegación sin JavaScript. Añadir estado vacío si un grupo seleccionado no tiene tarjetas visibles; hoy se comprueba la colección completa. |
| Fixtures: fallo global de uploads o aislamiento del recurso | Fallo global depende de medios importados antes del logo | Precargar medios disponibles, retirar únicamente el adjunto objetivo y provocar su fallo; restaurar filtro en `finally`, comprobar diagnóstico y recuperación idempotente. |
| PDF: nombre solicitado o identidad real | WordPress añade sufijos legítimos | Leer ID, URL/nombre y tamaño del adjunto creado; comprobar selección, persistencia y reintento contra esos datos, incluyendo colisión real. Restaurar asociación anterior antes de limpiar IDs propios. |
| Actualidad: total de artículos o roles editoriales | Una destacada pertenece a la página | Verificar destacada más tarjetas, IDs/enlaces únicos, orden, filtros, privacidad y paginación; no cambiar tamaño de página para satisfacer recuentos antiguos. |
| Home: respaldo literal por prueba o helper común | Hay dos recursos editoriales permitidos | Mantener `labm_theme_news_fallback_path`: destacada válida, metadato permitido y respaldo institucional Antioquia. Probar referencias inválidas/restringidas y ausencia de imagen rota; corregir código si incumple estas garantías. |
| Contraste: token compartido o colores dispersos | El verde oscuro también se usa en foco/hover | Ajustar `--labm-green-dark` tras calcular contraste sobre blanco y neutral; verificar todos sus usos/estados. Conservar verde de marca donde ya cumple. |
| Calidad/cobertura: cambios locales o configuración permisiva | Aumentar cobertura requiere ramas reales | Corregir WPCS conservando sanitización/escape; retirar `ensure_demo_attachment` solo tras confirmar ausencia de referencias. Priorizar ramas no cubiertas según Clover sin alterar fuentes, suites ni umbral80 %. |

## Flujo de datos

Consulta pública → normalización existente → consulta de publicados → render accesible → aserciones semánticas.

Carga PDF → respuesta del adjunto → selección por ID → guardado → lectura REST → restauración y limpieza acotada.

## Archivos afectados

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/themes/labm/style.css` | Modificar | Token de contraste; dimensiones solo con medidas aprobadas. |
| `wp-content/themes/labm/functions.php` | Modificar | WPCS, vacío de grupo y seguridad del fallback si falla contrato. |
| `wp-content/themes/labm/assets/about-team.js` | Modificar | Mantener el vacío al filtrar localmente y normalizar grupos del historial. |
| `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` | Modificar | WPCS manteniendo contratos de descarga/contacto. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificar | WPCS y eliminación condicionada del wrapper sin uso. |
| `tests/php/FixturesDomainTest.php` | Modificar | Fallo aislado, preservación y recuperación. |
| `tests/php/PublicExperienceTest.php`, `tests/php/HomePresentationTest.php` | Modificar | Visibilidad, composición y prioridad de medios. |
| `tests/php/DocumentContactTest.php`, `tests/php/ClosingCoverageTest.php` | Modificar | Ramas reales elegidas por evidencia de cobertura. |
| `tests/e2e/public-experience.spec.ts`, `tests/e2e/home.spec.ts` | Modificar | Regiones, unicidad, teclado y axe; dimensiones condicionadas. |
| `tests/e2e/document-admin.spec.ts` | Modificar | Identidad/nombre real, colisión y limpieza idempotente. |
| `wp-content/themes/labm/templates/single-labm_actualidad.html` | Condicionada | Solo ajustes globales definidos por handoff; feature pendiente pertenece a Detalle. |

## Interfaces y contratos

Sin API nueva ni migración. Mantener firmas públicas, shortcodes, metadatos, hooks y parámetros. Acotar «Limpiar filtros» al formulario o estado vacío y verificar ambos destinos predeterminados.

El renderer actual de Detalle usa `the_content`, recoge galerías mediante filtro temporal y lo retira en `finally`; la plantilla usa hero/body. Esto no prueba equivalencia con `wp:post-content` exigido por artefactos pausados.

## Estrategia de pruebas

| Capa | Comprobación | Enfoque |
|---|---|---|
| PHPUnit/integración | Éxito, borde, error y recuperación | TDD strict RED/GREEN por tarea; cobertura fresca≥80 %. |
| Playwright | Visibilidad, filtros, PDF, privacidad | Con/sin JS donde aplica; recursos propios con limpieza en `finally`. |
| Visual/accesibilidad | Contraste, foco, composición | Axe y mediciones aprobadas a320/768/1024/1200/1440px. |
| Calidad | PHPCS/PHPStan y LF | Gate completo; documentar warnings restantes sin ocultarlos. |

## Migración y reversión

Sin migración. Revertir solo hunks propios. Backup antes de pruebas mutables; limpiar/restaurar recursos y ejecutar content-sync oficial si cambia persistencia, verificando versión/hash y exclusión de cuentas, credenciales y demos temporales.

## Preguntas y condiciones pendientes

- [ ] Obtener referencia vigente de Documentos y medidas aprobadas por viewport antes de corregir dimensiones/expectativas.
- [ ] Medir Actualidad contra frame Pencil `Nrclx` y registrar aprobación antes de cambios dimensionales.
- [ ] Detalle debe reconciliar renderer/post-content mediante SPEC-FIX/DESIGN-FIX en su flujo y entregar contrato, medidas, artefactos y pendientes. Hasta entonces bloquear sus ajustes, manteniendo ejecutables los lotes autónomos; gate verde y cierre siguen pendientes.
