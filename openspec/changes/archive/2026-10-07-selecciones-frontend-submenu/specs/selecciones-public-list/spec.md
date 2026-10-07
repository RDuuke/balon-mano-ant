# Delta: listado público de Selecciones

RFC 2119: MUST obligatorio; MUST NOT prohibido.

## ADDED Requirements

### Requirement: Colección pública
MUST reutilizar backend sin ampliar REST: solo labm_seleccion publish, filtrado por labm_modalidad. /selecciones/ MUST seleccionar Piso; ?modalidad=Piso y ?modalidad=Playa MUST seleccionar modalidad. Piso/Playa MUST permanecer disponibles vacios. Terminos adicionales con publicaciones MUST ser seleccionables en listado sin ampliar submenu. Contadores MUST totalizar modalidad publica independientemente de pagina; MUST NOT contar Clubes/otros estados.

#### Scenario: 1
- Dado siete Selecciones Piso publicadas y dos Clubes con ese término
- Cuando se abre `/selecciones/`
- Entonces se selecciona Piso, el contador es siete y únicamente aparecen Selecciones Piso publicadas.

#### Scenario: 2
- Dado Playa sin publicaciones y otro término con una Selección publicada
- Cuando se consulta Playa y después el término adicional
- Entonces Playa muestra cero y vacío; el término adicional permite consultar su publicación.

#### Scenario: 3
- Dado Selecciones privadas y borradores junto a publicaciones
- Cuando una persona anónima consulta cada modalidad
- Entonces filas/opciones/contadores excluyen datos restringidos.

### Requirement: Estado de
MUST aceptar modalidad/pagina escalares validas. Modalidad desconocida, vacia o no escalar MUST resolver a Piso. Pagina ausente, no escalar, no entera positiva o desbordada MUST resolver a uno; superior al maximo MUST resolver a ultima disponible, o uno sin resultados. MUST mostrar hasta tres registros por pagina, fecha descendente y desempate estable. Paginacion MUST conservar modalidad; cambiarla MUST iniciar pagina uno. Parametros ajenos MUST NOT alterar coleccion.

#### Scenario: 4
- Dado siete publicaciones Playa, dos con igual fecha
- Cuando se recorre la paginación y se recarga
- Entonces las páginas contienen tres, tres y una filas, conservan Playa y mantienen orden sin duplicados.

#### Scenario: 5
- Dado dos páginas Piso y una petición de página nueve
- Cuando se abre la consulta y después se cambia a Playa
- Entonces se muestra la última página Piso y Playa inicia en uno.

#### Scenario: 6
- Dado modalidad desconocida o array y página array, negativa, fraccionaria o desbordada
- Cuando se solicita el listado
- Entonces los valores inválidos usan sus predeterminados sin avisos técnicos ni exposición de datos.

### Requirement: Filas editoriales
Filas MUST mostrar titulo, resumen editable (extracto o contenido publico resumido), medios/metadatos existentes. MUST NOT inventar resultados, fechas deportivas ni planteles. MUST NOT enlazar titulo/imagen/fila a fichas ni incluir VER PUBLICACION/CTA individual. MUST NOT crear rutas/plantillas de detalle. Imagen ausente/invalida MUST usar placeholder neutro sin medio roto; vacio MUST ofrecer recuperacion Piso/Playa sin datos ficticios.

#### Scenario: 7
- Dado una publicación con extracto e imagen
- Cuando se editan y recargan
- Entonces refleja cambios sin enlace individual.

#### Scenario: 8
- Dado una publicación sin extracto, imagen ni metadatos opcionales
- Cuando se presenta
- Entonces usa contenido resumido y placeholder; omite metadatos ausentes.

#### Scenario: 9
- Dado una imagen inaccesible o ninguna publicación coincidente
- Cuando se abre el listado
- Entonces no hay imagen rota; la colección vacía identifica la modalidad y permite cambiarla.

### Requirement: Fidelidad visual
MUST conservar hero negro por modalidad, introduccion clara, filas foto izquierda/texto derecha de neIe6/p1GT2, tipografia y colores LABM. Demo/CTA individuales quedan excluidos. Apilado movil, vacio/paginacion son adaptaciones aprobadas, no observadas; MUST conservar lectura, contraste y ausencia de desborde a 320, 768, 1024, 1200 y 1440 px.

#### Scenario: 10
- Dado contenido público completo a 1440 px
- Cuando se compara con los frames
- Entonces hero, introducción y filas conservan composición y jerarquía observadas.

#### Scenario: 11
- Dado el mismo listado a 320 px
- Cuando se presenta
- Entonces las filas se apilan y todos los contenidos y controles permanecen disponibles.

#### Scenario: 12
- Dado títulos largos y ampliación de texto al 200 %
- Cuando se visualiza el listado
- Entonces no aparecen recortes, superposiciones ni desborde horizontal global.
