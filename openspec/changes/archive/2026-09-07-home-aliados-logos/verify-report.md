# Informe de verificación: Marquee de logos de Aliados Oficiales

## Veredicto

**Estado: ⚠️ WARNING, apto funcionalmente para ARCHIVE.** Los 15 escenarios declarados están COMPLIANT y el gate integral pasa. No quedan escenarios PARTIAL, FAILING ni UNTESTED. Persisten dos advertencias no bloqueantes: la skill opcional `testing` no está instalada y un PHPCS focal sobre archivos de prueba —fuera del alcance del ruleset oficial— expone deuda estructural anterior a FIX 1/2.

## Completitud

- Tareas declaradas: 23.
- Tareas completadas: 23.
- Tareas pendientes: ninguna.

## Matriz de Validación

| Dominio | Escenario | Estado | Test asociado | Severidad |
|---------|-----------|--------|---------------|-----------|
| Contenido exclusivo | Logos disponibles | ✅ COMPLIANT | `HomePresentationTest::test_allies_render_image_only_marquee_with_stable_valid_selection`; E2E marquee | — |
| Contenido exclusivo | Límite de contenido legado | ✅ COMPLIANT | `HomeEditorialFlowsTest::test_actualizacion_de_aliado_conserva_datos_legados`; contrato PHP de salida exclusiva de imágenes | — |
| Contenido exclusivo | Datos no representables | ✅ COMPLIANT | `HomePresentationTest::test_allies_collection_filters_orders_and_limits_before_rendering`; fallback de render | — |
| Movimiento accesible | Movimiento estándar | ✅ COMPLIANT | E2E `slider conserva controles y aliados funciona como marquee solo de logos` | — |
| Movimiento accesible | Movimiento reducido | ✅ COMPLIANT | E2E `slider y aliados quedan estaticos con movimiento reducido` | — |
| Movimiento accesible | Interacción inexistente | ✅ COMPLIANT | Contrato CSS/JS y E2E marquee sin enlaces, texto ni controles | — |
| Selección y orden | Colección válida | ✅ COMPLIANT | Fixtures idempotentes y `HomePresentationTest::test_allies_collection_filters_orders_and_limits_before_rendering` | — |
| Selección y orden | Más de doce aliados | ✅ COMPLIANT | `HomePresentationTest::test_allies_collection_filters_orders_and_limits_before_rendering` | — |
| Selección y orden | Publicación inválida | ✅ COMPLIANT | Validación PHP y colección pública con borradores/imagen inválida | — |
| Administración | Alta válida | ✅ COMPLIANT | REST y guardado administrativo válido | — |
| Administración | Ciclo editorial | ✅ COMPLIANT | `HomeEditorialFlowsTest::test_editor_envia_aliado_a_papelera_y_restaura_su_disponibilidad`; flujo REST de borrado definitivo | — |
| Administración | Publicación incompleta | ✅ COMPLIANT | Rechazo REST/admin, aviso y adjunto no imagen | — |
| Compatibilidad visual | Distintos tamaños de pantalla | ✅ COMPLIANT | E2E proporción, `alt`, Axe y ausencia de desborde a 320, 768, 1024 y 1440 px | — |
| Compatibilidad visual | Datos legados | ✅ COMPLIANT | Persistencia de cuerpo, extracto y URL; render exclusivo de imágenes | — |
| Compatibilidad visual | Recursos demo no válidos | ✅ COMPLIANT | Test de fallo de carga y test de manifiesto/originalidad con seis SHA-256 exactos | — |

### Resumen por dominio

| Dominio | COMPLIANT | FAILING | UNTESTED | PARTIAL |
|---------|-----------|---------|----------|---------|
| Contenido exclusivo | 3 | 0 | 0 | 0 |
| Movimiento accesible | 3 | 0 | 0 | 0 |
| Selección y orden | 3 | 0 | 0 | 0 |
| Administración | 3 | 0 | 0 | 0 |
| Compatibilidad visual | 3 | 0 | 0 | 0 |
| **Total** | **15** | **0** | **0** | **0** |

## Verificación Independiente de FIX 1/2

### Ciclo editorial

- La prueba focal pasa y cubre `publish → trash → draft → republish` con WordPress real.
- Comprueba capacidades `delete_post` y `edit_post` del editor.
- Comprueba presencia en `labm_theme_home_allies_posts()` al publicar, ausencia en papelera, ausencia tras restaurar como borrador y recuperación tras republicar por REST con título y logo.
- El borrado definitivo continúa cubierto por `test_editor_realiza_flujo_editorial_completo_por_rest`.

### Originalidad y procedencia

- `originality-manifest.json` declara procedencia ImageGen, fecha, revisor, decisión `aprobado`, criterios y limitación.
- Los seis SHA-256 fueron recalculados independientemente y coinciden exactamente con manifiesto y prueba.
- La prueba focal valida además conjunto exacto, PNG, 800×400 y transparencia; cualquier sustitución no atestada rompe CI.
- La revisión visual independiente confirma seis símbolos diferenciados y sin texto ni similitud evidente con una marca conocida.
- Límite explícito: la atestación visual no equivale a una búsqueda mundial exhaustiva de marcas; los hashes prueban identidad e integridad del conjunto aprobado, no originalidad universal.

## Evidencia de Ejecución

| Comprobación | Resultado |
|--------------|-----------|
| Pruebas focales de FIX 1/2 | PASS: 2 pruebas, 58 aserciones |
| `scripts/gate.ps1 -IncludeBrowser` | PASS, código 0; siete componentes correctos |
| PHPUnit unitario | PASS: 1 prueba, 2 aserciones |
| PHPUnit integración WordPress | PASS: 78 pruebas, 567 aserciones |
| Cobertura PHP | PASS: 89,64 % (995/1110 líneas), umbral 80 % |
| PHPCS oficial | PASS sobre el alcance de `phpcs.xml.dist` |
| PHPStan | PASS, sin errores |
| Playwright portátil | PASS: 84 pruebas |
| Playwright del gate local | PASS: 20 pruebas |
| Smoke de fixtures | PASS tras dos cargas; contenido ajeno preservado y sin PDFs nuevos |
| Contrato de ownership de uploads | PASS: restauración recursiva a `33:33` |

## Evaluación de PHPCS Focal

El comando focal sobre `HomeContentTest.php` y `HomeEditorialFlowsTest.php` devuelve 25 errores y 1 advertencia. Los hallazgos corresponden a convenciones estructurales preexistentes —nombre de archivo/clase, PHPDoc, doble clase de prueba, ternario corto y capacidad personalizada— y no a las líneas funcionales añadidas por FIX 1/2. El gate oficial pasa porque `phpcs.xml.dist` incluye únicamente `wp-content/plugins/labm-core` y `wp-content/themes/labm/functions.php`, no `tests/php`.

Clasificación: **WARNING no bloqueante**. Conviene sanear esa deuda en un cambio separado o ampliar explícitamente el alcance oficial de PHPCS; no invalida los 15 escenarios ni el gate configurado de esta iniciativa.

## Evidencia Visual y de Accesibilidad

- Movimiento normal: dos grupos equivalentes, una fila, `labm-marquee` lineal e infinita de 24 s, desplazamiento observable y sin pausa por puntero.
- Movimiento reducido: animación desactivada, réplica oculta, primaria envolvente y todos los logos visibles.
- Responsive: sin desbordamiento y proporción natural 2:1 a 320, 768, 1024 y 1440 px.
- Accesibilidad: región nombrada, `alt` no vacío y Axe sin violaciones en el alcance probado.

## Evidencia TDD

- TDD estricto: activo.
- Tareas regulares con RED y GREEN: 23/23.
- FIX-1.1 con RED y GREEN: presente.
- FIX-1.2 con RED y GREEN: presente.
- Brechas: ninguna.

## Coherencia con el Diseño

| Decisión | Estado | Evidencia |
|----------|--------|-----------|
| CPT privado y soportes mínimos | ✅ Implementada | Registro y pruebas de soportes/capacidades |
| Validación REST/admin con nonce y capacidad | ✅ Implementada | Pruebas positivas y negativas |
| Selección ordenada con límite posterior al filtro | ✅ Implementada | `menu_order`, título, ID y máximo 12 válidos |
| Lista accesible y réplica inerte | ✅ Implementada | Grupos idénticos, `aria-hidden` e `inert` |
| Movimiento CSS fijo sin controles | ✅ Implementada | `translate3d`, 24 s lineal infinita |
| Alternativa de movimiento reducido | ✅ Implementada | Pista estática, réplica oculta y wrap |
| Seis PNG originales 800×400 | ✅ Implementada | Inspección, manifiesto, hashes, dimensiones y transparencia |

## Compatibilidad, Diff y Finales de Línea

- Datos legados permanecen almacenados y no se consumen en la sección.
- Fixtures preservan contenido compartido y no crean adjuntos PDF.
- Worktree verificado: 28 archivos cambiados, 21 de texto y 7 binarios; 1350 líneas de diff textual incluyendo archivos nuevos y artefactos OpenSpec.
- El presupuesto de revisión está desactivado (`max_diff_lines: 0`, sin paths sensibles); no requiere aprobación.
- `git diff --check`: PASS; auditoría de 21 archivos de texto: 0 secuencias CRLF.

## Riesgos Residuales

1. **WARNING no bloqueante:** falta la skill opcional `testing`; las comprobaciones requeridas se ejecutaron directamente.
2. **WARNING no bloqueante:** PHPCS focal revela deuda estructural en pruebas excluidas del ruleset oficial; el gate oficial está limpio.
3. **SUGGESTION:** una búsqueda profesional de marcas ampliaría la confianza jurídica, pero queda fuera del alcance técnico y del significado probatorio de la atestación actual.

Siguiente fase recomendada: **ARCHIVE**, después de que el orquestador presente estas advertencias. No se requiere FIX 2/2 porque no quedan escenarios PARTIAL ni fallos del gate.
