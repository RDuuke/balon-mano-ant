# Tasks: Catálogo público de documentos PDF

## Phase 1: Contrato RED

- [x] 1.1 Añadir en `tests/php/DocumentContactTest.php` pruebas RED para título/PDF obligatorios, PDF válido e inválido y metadatos internos no visibles.
- [x] 1.2 Añadir pruebas RED de consulta sin filtros, vacío, 10 elementos por página, página activa y normalización en `tests/php/DocumentContactTest.php`.
- [x] 1.3 Añadir pruebas RED para «Ver PDF» con URL validada, `_blank` y `noopener`, y «Descargar» seguro en `tests/php/DocumentContactTest.php`.
- [x] 1.4 Añadir en `tests/php/PublicExperienceTest.php` pruebas RED de patrón Documentos, ausencia de buscador/filtros y semántica accesible aislada.

## Phase 2: Dominio y seguridad GREEN

- [x] 2.1 Completar en `wp-content/plugins/labm-core/includes/class-labm-domain.php` la validación de publicación para título/PDF y metadatos internos opcionales.
- [x] 2.2 Completar en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` la validación de adjunto y los enlaces «Ver PDF»/«Descargar» seguros.
- [x] 2.3 Implementar en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` consulta sin filtros, orden descendente y paginación normalizada de 10 elementos.

## Phase 3: Integración pública

- [x] 3.1 Renderizar en `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` filas con solo título y acciones, estado vacío y navegación accesible.
- [x] 3.2 Integrar exclusivamente el catálogo en `wp-content/themes/labm/patterns/documentos.php`, debajo del encabezado existente.
- [x] 3.3 Añadir en `wp-content/themes/labm/style.css` selectores `labm-documents-*` responsivos para lista, vacío y paginador, sin estilos de filtros.

## Phase 4: Importación y refactor

- [ ] 4.1 Definir títulos y mapa revisable de `docs/legal-documents`; añadir prueba RED de primera carga, repetición y error parcial en `tests/php/DocumentContactTest.php`.
- [ ] 4.2 Implementar en `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` el comando administrativo idempotente con clave de origen.
- [x] 4.3 Ejecutar pruebas focales GREEN y refactorizar únicamente duplicación dentro de los archivos de alcance.

## Phase 5: Verificación

- [ ] 5.1 Ejecutar PHPUnit focal, WPCS y PHPStan sobre los archivos modificados; corregir fallos introducidos por el cambio.
- [ ] 5.2 Ejecutar Playwright de Documentos con teclado, nueva pestaña de «Ver PDF» y paginador; verificar WCAG 2.2 AA.
- [x] 5.3 Verificar LF y `git diff --check` para los archivos modificados y registrar resultados en la verificación NEA.
