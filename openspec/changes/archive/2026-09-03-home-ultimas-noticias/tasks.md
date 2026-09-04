# Tasks: Últimas noticias en la portada

## Phase 1: Foundation

- [x] 1.1 RED: ampliar `tests/php/FixturesDomainTest.php` para exigir seis slugs demo únicos, categoría común, fechas, rutas locales y recarga idempotente.
- [x] 1.2 GREEN: añadir en `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` las seis noticias ficticias deterministas y preservar conflictos ajenos.
- [x] 1.3 REFACTOR: centralizar en `class-labm-fixtures-command.php` los datos repetidos sin alterar los fixtures existentes.

## Phase 2: Core Implementation

- [x] 2.1 RED: añadir en `tests/php/HomePresentationTest.php` casos de límite, orden fecha/ID, exclusión de estados y separación respecto a eventos.
- [x] 2.2 GREEN: crear en `wp-content/themes/labm/functions.php` consultas específicas para evento y noticias que no compartan resultados.
- [x] 2.3 RED: cubrir en `HomePresentationTest.php` una destacada, tres laterales, colección parcial/vacía y metadatos semánticos.
- [x] 2.4 RED: cubrir en `HomePresentationTest.php` CTA válido/ausente y prioridad miniatura→meta permitida→fallback ante rutas inseguras.
- [x] 2.5 GREEN: sustituir en `functions.php` el render genérico por encabezado, CTA opcional, destacada y lista lateral escapadas.
- [x] 2.6 REFACTOR: extraer en `functions.php` helpers pequeños para medio y metadatos, conservando alt, dimensiones y URLs seguras.

## Phase 3: Integration

- [x] 3.1 RED: ampliar `tests/e2e/home.spec.ts` para exigir estructura 1+3, CTA, enlaces y orden de foco con fixtures cargados.
- [x] 3.2 RED: añadir en `home.spec.ts` mediciones sin overflow a 320/768/1024/1440 px y comprobación axe WCAG 2.2 AA.
- [x] 3.3 GREEN: añadir en `wp-content/themes/labm/style.css` estilos BEM móviles para encabezado, destacada, lista, medios y foco visible.
- [x] 3.4 GREEN: completar en `style.css` tarjetas horizontales desde 768 px y grid 3/2 desde 1024 px con `min-width: 0`.
- [x] 3.5 REFACTOR: verificar en `style.css` que selectores de actualidad no alteran `.labm-featured-event` ni `.labm-card-grid` global.

## Phase 4: Testing

- [x] 4.1 Ejecutar PHPUnit focal para `FixturesDomainTest.php` y `HomePresentationTest.php`; corregir hasta verde sin relajar aserciones.
- [x] 4.2 Cargar dos veces los fixtures en WordPress y verificar seis noticias demo, categoría única, aislamiento del evento y ausencia de duplicados.
- [x] 4.3 Ejecutar `tests/e2e/home.spec.ts` y revisar visualmente la sección en 320/768/1024/1440 px contra la referencia.
- [x] 4.4 Ejecutar PHPCS, PHPStan y el gate local aplicable; resolver errores dentro de los seis archivos del cambio.

## Phase 5: Cleanup

- [x] 5.1 Revisar el diff para retirar marcado, selectores o datos demo redundantes y confirmar que no se modificó el archivo de actualidad.
- [x] 5.2 Verificar LF y ausencia de CRLF en cada archivo modificado; normalizar únicamente los archivos dentro del alcance.
- [x] 5.3 Registrar resultados, comandos y cualquier advertencia visual en el progreso de APPLY sin marcar tareas no ejecutadas.

## Phase 6: Remediacion contractual y de contenido

- [x] 6.1 TEST: cubrir colecciones de una a tres noticias, coleccion vacia y jerarquia parcial sin duplicados ni marcado incompleto.
- [x] 6.2 TEST: cubrir ausencia del CTA en el render y prioridad miniatura -> meta permitida -> fallback con categoria ausente.
- [x] 6.3 TEST: verificar a 320 px el orden secuencial de teclado, foco visible y ausencia de scroll horizontal.
- [x] 6.4 CONTENT: retirar exclusivamente los directorios temporales `labm-club-import` y `labm-event-import` de uploads.
- [x] 6.5 CONTENT: restaurar la base de sincronizacion `20260831T004739066Z-rduuqe-RDUUQE` y regenerar el paquete canonico mediante Push normal.
- [x] 6.6 VALIDATE: comprobar pruebas focales, contenido y checksum del ZIP, ausencia de PHP/artefactos temporales, medios preservados, estado local y LF.
- [x] 6.7 RECORD: actualizar progreso, tareas, estado y log de ejecucion con evidencia honesta.
- [x] 6.8 TEST: invalidar un activo demo antes de cargar fixtures y confirmar que la noticia sigue publicada, sin asociación rota y con fallback seguro.
