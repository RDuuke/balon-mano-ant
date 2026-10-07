# Exploración: backend de Selecciones

## Alcance revisado

Se revisaron el dominio del plugin, su registro REST y permisos, las consultas públicas existentes, fixtures y pruebas relacionadas con `labm_seleccion`. La futura vista y sus estilos pertenecen a `selecciones-frontend`.

## Estado existente

`wp-content/plugins/labm-core/includes/class-labm-domain.php` ya registra `labm_seleccion` como tipo público con archivo y slug `/selecciones/`, API REST activa, capacidades por tipo y `map_meta_cap`. El tipo admite título, contenido, extracto, imagen destacada y campos personalizados. También existen las taxonomías REST `labm_modalidad` y `labm_categoria`; `labm_modalidad_detalle` se expone como metadato de texto con saneamiento y autorización mediante `edit_post`.

El contrato disponible alcanza para publicar selecciones como contenido editorial y clasificarlas por modalidad. WordPress proporciona la persistencia y el endpoint estándar `wp/v2/labm_seleccion`; la taxonomía se expone como `wp/v2/labm_modalidad`. No se encontraron endpoints REST propios ni un modelo relacional para planteles, convocatorias, deportistas, temporadas o resultados.

Hay datos ficticios idempotentes para modalidades Piso y Playa, además de una selección privada, dentro de `class-labm-fixtures-command.php`. Las pruebas comprueban registro del CPT y taxonomías, metadato saneado, permisos de editor y administrador, publicación pública filtrada y exclusión de contenido privado.

## Límites entre backend y frontend

El tema ya contiene una consulta pública que limita resultados a `publish`, filtra por modalidad y pagina; el shortcode y las plantillas resuelven el listado. Por tanto, la capa de datos debe permanecer en `labm-core`; la presentación, navegación y comportamiento visual corresponden al cambio posterior `selecciones-frontend`. Antes de añadir endpoints, conviene confirmar que el diseño requiere datos que el CPT, términos y metadato existentes no pueden representar.

## Decisiones por resolver antes de especificar

- Confirmar si “Selecciones” son fichas editoriales simples (nombre, descripción, imagen y modalidad) o si incluyen planteles, personas, categorías, temporada, partidos o resultados.
- Si solo son fichas, reutilizar el CPT y la API REST existentes; precisar campos obligatorios, validación y cardinalidad de modalidades, si hay cambios necesarios.
- Si hay relaciones de plantel/personas, definir entidades, ownership editorial, orden, estados de publicación y representación REST antes de implementar. No inferir ese modelo desde la vista.
- Verificar cobertura de capacidades de `labm_seleccion` al endurecer permisos: `labm_core_ensure_capabilities()` concede capacidades a editor y administrador, pero su comprobación de versión no incluye explícitamente las capacidades de selecciones.

## Validación prevista para APPLY/VERIFY

Con TDD estricto, cubrir registro del dominio, esquema/saneamiento de metadatos o relaciones, autorización por rol, contrato REST y privacidad de borradores/privados con pruebas focales PHPUnit. Ejecutar después análisis estático, formato y verificación LF. La vista no forma parte de este cambio. El `review_budget.max_diff_lines=0` debe revisarse al definir el gate, porque cualquier diff de implementación excedería literalmente el límite configurado.

## Project Standards (auto-resolved)

- WordPress 7.1 y PHP 8.3; persistencia nueva o modificada en `wp-content/plugins/labm-core`.
- El frontend vive en el tema de bloques y queda fuera de este cambio.
- TDD estricto; pruebas focales, análisis estático, formato y LF para cambios de código.
- `review_budget.max_diff_lines=0`; no hay rutas sensibles declaradas.
- Sincronizar el estado canónico de WordPress antes de cerrar si el cambio altera contenido, opciones, esquema o uploads persistidos.