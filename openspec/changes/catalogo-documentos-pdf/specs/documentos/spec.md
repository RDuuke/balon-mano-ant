# Especificación: Catálogo de documentos PDF

## ADDED Requirements

### Requirement: Gestión editorial de documentos

El sistema MUST permitir a administradores y editores crear, editar y publicar un documento con título y un PDF asociado. Los metadatos administrativos MAY conservarse para ordenación, trazabilidad y seguridad, pero no SHALL ser obligatorios ni visibles en el catálogo. Solo documentos completos y publicados SHALL ser visibles públicamente.

#### Scenario: Publicación válida

- DADO un editor autorizado y un documento con título y PDF válido.
- CUANDO lo publica desde WordPress.
- ENTONCES el documento aparece en el catálogo con su título y acciones PDF.

#### Scenario: Dato obligatorio incompleto

- DADO un documento sin título o PDF asociado.
- CUANDO se intenta publicar.
- ENTONCES el sistema rechaza la publicación e informa el dato requerido sin exponerlo.

#### Scenario: Usuario no autorizado

- DADO un visitante o suscriptor.
- CUANDO intenta crear o editar un documento.
- ENTONCES WordPress deniega la operación y no cambia contenido ni adjuntos.

### Requirement: Integridad, visualización y descarga del PDF

El sistema MUST aceptar únicamente PDF legibles, dentro del límite configurado, con cabecera y MIME PDF válidos. SHALL generar enlaces públicos solo para adjuntos válidos. «Ver PDF» SHALL usar la URL directa del adjunto con `target="_blank"` y `rel="noopener"`; `noreferrer` MAY añadirse si la política de privacidad lo requiere. La visualización o descarga de respaldo corresponde al navegador o sistema operativo. «Descargar» SHALL solicitar la descarga directa con el mecanismo seguro compatible con WordPress y el navegador.

#### Scenario: PDF válido

- DADO un PDF válido asociado a un documento publicado.
- CUANDO se renderiza el catálogo.
- ENTONCES ofrece «Ver PDF» en una nueva pestaña segura y «Descargar» el mismo recurso.

#### Scenario: Archivo no PDF o excedido

- DADO un archivo que no cumple tipo, cabecera o tamaño.
- CUANDO un editor intenta asociarlo.
- ENTONCES el sistema lo rechaza y no muestra enlace público.

#### Scenario: Adjunto alterado

- DADO un documento publicado cuyo adjunto ya no tiene MIME PDF válido.
- CUANDO se consulta el catálogo.
- ENTONCES el documento no expone URL ni ruta interna del archivo.

### Requirement: Lista pública paginada

La página Documentos MUST listar exclusivamente el título y las acciones PDF de documentos publicados, por fecha de publicación descendente, con 10 documentos por página. No SHALL mostrar buscador, filtros ni metadatos editoriales. El paginador SHALL señalar la página activa, navegar mediante anterior/siguiente/números y conservar solo el contexto de página.

#### Scenario: Resultados con varias páginas

- DADO más de 10 documentos publicados y válidos.
- CUANDO el visitante abre una página de resultados.
- ENTONCES ve hasta 10 títulos y controles anterior/siguiente/números con la página activa.

#### Scenario: Catálogo sin resultados

- DADO que no hay documentos publicados y válidos.
- CUANDO el visitante abre Documentos.
- ENTONCES ve el estado vacío de referencia sin controles de búsqueda ni filtros.

#### Scenario: Página inválida o contenido no publicado

- DADO un número de página inválido, o borradores, privados o adjuntos inválidos.
- CUANDO se solicita el catálogo.
- ENTONCES normaliza la página segura y no expone contenido ni metadatos no públicos.

### Requirement: Importación inicial repetible

El sistema MUST importar los PDF de `docs/legal-documents` mediante una operación administrativa repetible, con identificador estable por archivo. SHALL crear o actualizar solo los registros de esa importación y conservar sus metadatos internos cuando estén disponibles.

#### Scenario: Primera importación

- DADO los archivos locales y títulos editoriales definidos.
- CUANDO un administrador ejecuta la importación.
- ENTONCES cada PDF válido queda como adjunto y documento publicado una sola vez.

#### Scenario: Reejecución

- DADO una importación previa con los mismos identificadores.
- CUANDO se ejecuta nuevamente.
- ENTONCES actualiza los registros propios sin duplicar documentos ni adjuntos.

#### Scenario: Archivo local faltante o inválido

- DADO una entrada cuyo archivo no existe o no es válido.
- CUANDO inicia la importación.
- ENTONCES registra un error accionable y deja intactos los documentos ya válidos.
