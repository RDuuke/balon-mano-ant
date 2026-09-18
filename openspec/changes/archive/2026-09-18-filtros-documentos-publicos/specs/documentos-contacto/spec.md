# Delta para Documentos y contacto

## MODIFIED Requirements

### Requirement: Catálogo público filtrable y accesible

El catálogo público MUST permitir consultar documentos publicados por texto libre, categoría, año y orden; MUST conservar los criterios al cambiar de página; y MUST presentar acciones diferenciadas para ver y descargar el PDF.

#### Scenario: Consulta combinada con resultados
- DADO documentos publicados que difieren en título, categoría y fecha editorial
- CUANDO el visitante combina texto, categoría, año y un orden disponible
- ENTONCES solo se muestran coincidencias de todos los criterios en el orden elegido.

#### Scenario: Consulta sin filtros
- DADO documentos publicados válidos y ningún criterio seleccionado
- CUANDO el visitante abre el catálogo
- ENTONCES se muestran los documentos según el orden predeterminado y con paginación cuando corresponda.

#### Scenario: Filtros conservados al paginar
- DADO una consulta con texto, categoría, año y orden que ocupa varias páginas
- CUANDO el visitante navega a otra página
- ENTONCES la URL y los resultados conservan todos los criterios activos.

#### Scenario: Categoría o año sin coincidencias
- DADO criterios válidos que no coinciden con ningún documento publicado
- CUANDO el visitante procesa la consulta
- ENTONCES aparece un estado vacío que explica el resultado y ofrece limpiar los filtros.

#### Scenario: Documento sin fecha editorial
- DADO un documento publicado con PDF válido, categoría y fecha editorial ausente
- CUANDO se muestra el catálogo sin filtro de año
- ENTONCES el documento permanece visible, omite la fecha y conserva sus acciones disponibles.

#### Scenario: Documento sin fecha en filtro por año
- DADO un documento publicado cuya fecha editorial está ausente
- CUANDO el visitante aplica un año
- ENTONCES el documento no se presenta como coincidencia de ese año.

#### Scenario: Metadata pública de la tarjeta
- DADO un documento publicado con categoría, fecha editorial y PDF cuyo tamaño está disponible
- CUANDO se muestra su tarjeta
- ENTONCES presenta categoría, título, fecha y tamaño legibles, además de “Ver PDF” y “Descargar”.

#### Scenario: Código documental no disponible
- DADO un documento publicado sin código editorial definido en la metadata pública
- CUANDO se muestra su tarjeta
- ENTONCES no inventa un código ni expone identificadores internos, y presenta el resto de metadata disponible.

#### Scenario: Diseño responsive y teclado
- DADO un visitante que usa una pantalla estrecha o navegación por teclado
- CUANDO consulta filtros, tarjetas o paginación
- ENTONCES los controles se pueden usar sin desbordamiento, el foco es visible y las acciones siguen siendo operables.

#### Scenario: Archivo PDF no disponible
- DADO un documento publicado cuya referencia PDF falta, no es pública o no es válida
- CUANDO se genera el catálogo
- ENTONCES no se muestra un enlace roto ni una ruta interna, y el documento no ofrece acciones PDF inseguras.

## REMOVED Requirements

Ninguno.
