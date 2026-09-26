# Delta para Actualidad

## ADDED Requirements

### Requirement: Detalle editorial publicado

El sistema MUST presentar cada entrada `labm_actualidad` publicada en su ruta individual con categoría, fecha pertinente, título, imagen destacada o fallback y cuerpo enriquecido. El detalle MUST conservar una vía de regreso al archivo y MUST omitir toda ubicación no administrada.

#### Scenario: Entrada completa publicada

- DADO una entrada publicada con categoría, fecha, miniatura y contenido
- CUANDO una persona abre su enlace individual
- ENTONCES percibe hero, metadatos, medio, contenido y retorno al archivo

#### Scenario: Medio o categoría ausente

- DADO una entrada publicada sin miniatura o sin categoría
- CUANDO se muestra el detalle
- ENTONCES conserva una lectura comprensible sin imagen rota ni texto inventado

#### Scenario: Entrada no pública

- DADO una entrada en borrador o privada
- CUANDO un visitante anónimo solicita su URL
- ENTONCES el sistema no revela su contenido ni metadatos restringidos

### Requirement: Contenido y galería nativos

El sistema MUST renderizar el contenido enriquecido publicado. Un bloque `core/gallery` publicado MUST presentarse como galería accesible; si no existe, la sección de galería MUST omitirse sin huecos. El sistema MUST NOT crear un modelo de galería paralelo.

#### Scenario: Galería publicada

- DADO una entrada con un bloque `core/gallery` válido dentro de su contenido
- CUANDO se renderiza el detalle
- ENTONCES sus imágenes mantienen textos alternativos y orden editorial

#### Scenario: Sin galería

- DADO una entrada publicada sin bloque `core/gallery`
- CUANDO se renderiza el detalle
- ENTONCES no aparece un encabezado ni contenedor vacío de galería

#### Scenario: Galería no publicable

- DADO un bloque con recurso no disponible o sin datos visibles
- CUANDO se renderiza el detalle
- ENTONCES no muestra enlaces rotos ni expone recursos restringidos

### Requirement: Compartir accesible y degradable

El sistema MUST ofrecer enlaces de compartir por Facebook y WhatsApp para la URL canónica publicada. La acción “Copiar enlace” SHOULD copiar mediante capacidades del navegador cuando estén disponibles y MUST ofrecer sin JavaScript una alternativa etiquetada para seleccionar manualmente la URL.

#### Scenario: Navegador compatible

- DADO una entrada pública y un navegador con portapapeles disponible
- CUANDO la persona activa “Copiar enlace”
- ENTONCES recibe confirmación perceptible y la URL canónica queda disponible para compartir

#### Scenario: JavaScript o portapapeles no disponible

- DADO una entrada pública sin JavaScript o sin permiso de portapapeles
- CUANDO la persona llega al panel Compartir
- ENTONCES puede seleccionar la URL canónica mediante un control etiquetado

#### Scenario: URL no válida

- DADO que no se puede obtener una URL canónica segura
- CUANDO se renderiza el panel
- ENTONCES no se emiten enlaces externos ni controles de copia inoperables

### Requirement: Presentación responsive y operable

El detalle MUST conservar jerarquía semántica, foco visible, contraste y ausencia de desbordamiento horizontal entre 320 y 1440 px.

#### Scenario: Anchos objetivo

- DADO una entrada con título y contenido largos
- CUANDO se visita a 320, 768, 1024, 1200 y 1440 px
- ENTONCES no hay solapamiento, recorte ni desplazamiento horizontal global

#### Scenario: Recorrido por teclado

- DADO el detalle visible
- CUANDO una persona recorre retorno y acciones de compartir
- ENTONCES el foco, nombre y orden de los controles son perceptibles

#### Scenario: Contenido hostil

- DADO contenido o metadatos con marcado no seguro
- CUANDO se presenta el detalle
- ENTONCES se aplica el saneamiento de WordPress sin ejecutar contenido no autorizado
