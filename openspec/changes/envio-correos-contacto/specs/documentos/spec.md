# Especificación: Contrato del catálogo público de Documentos

## Requisitos añadidos

### Requisito: Expectativas verificables del catálogo filtrable

El catálogo público DEBE conservar los filtros presentes en la solicitud, la paginación y la exclusión de documentos sin PDF válido. Sus pruebas DEBEN comprobar ese contrato vigente y no exigir el catálogo simple retirado.

#### Escenario: Patrón recibe filtros públicos

- DADO la plantilla pública de Documentos
- CUANDO se compone el catálogo
- ENTONCES usa los filtros y la página actuales.

#### Escenario: Consulta filtrada sin coincidencias

- DADO un texto que no coincide con documentos publicados
- CUANDO se renderiza el catálogo
- ENTONCES informa que no encontró documentos y ofrece limpiar filtros.

#### Escenario: Documento o adjunto no válido

- DADO publicaciones con adjuntos inválidos
- CUANDO se consulta una página filtrada o sin filtros
- ENTONCES esos elementos no se presentan como documentos descargables.

### Requisito: Mensaje vacío consistente

El catálogo DEBE usar el mensaje vigente «No encontramos documentos» tanto para una consulta sin resultados como para una consulta sin filtros.

#### Escenario: Catálogo vacío

- DADO que no hay documentos publicados elegibles
- CUANDO se consulta la primera página sin filtros
- ENTONCES se muestra el mensaje vacío vigente sin enlace de limpieza.

#### Escenario: Filtros activos

- DADO una consulta sin resultados con filtros activos
- CUANDO se muestra el estado vacío
- ENTONCES se muestra el enlace para limpiar filtros.

#### Escenario: Solicitud no válida

- DADO valores de filtro no válidos
- CUANDO se normalizan y consulta el catálogo
- ENTONCES no se muestran documentos fuera del contrato público.
