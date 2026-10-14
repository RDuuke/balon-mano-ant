# Llave aplicación correo Liga BalonMano
[REDACTED]


# Ajustes del manual.

- No hacer referencia al localhost:8080. Reemplazar por el dominio balonmanoantioquia.com.
- Quitar texto aclaratorio (resaltado en verde) del item 02 "ENCUENTRA CADA TAREA"
- Corregir imagen del item 04A "ACTIVA LOS CAMPOS PERSONALIZADOS"
- Preguntar para que sirve el campo custom labm_demo_image de los items de Actualidad. En el manual hace referencia a un campo llamado "lamb_demo_fecha" que no existe

## Curaduría de uso final — observaciones por sección

### General / índice de la cartilla

- **Observación:** La página 04A, **Activa los campos personalizados**, no aparece en el índice de la página 02.
  **Por qué:** Una persona que no vea el panel no tiene una referencia directa para encontrar el procedimiento de activación; el salto de 04 a 05 hace parecer que la página 04A no existe.
- **Observación:** La cartilla remite al archivo `manual-usuario.md`, pero no indica que debe consultarse dentro de la carpeta `docs/manual-usuario`.
  **Por qué:** Si la cartilla se entrega como PDF separado, el nombre del archivo no basta para localizar el documento complementario.

### 03 — Entra y ubícate

- **Observación:** La instrucción de acceso ya usa el dominio institucional, pero no explica qué hacer si el usuario recibe una pantalla de idioma distinto al de la captura.
  **Por qué:** Los botones pueden aparecer como `Log In` o `Acceder`; sin esa equivalencia, la persona puede interpretar que está en una pantalla incorrecta.

### 04 — Una publicación, cuatro momentos

- **Observación:** `Añadir… / Add Post` se presenta como una acción general.
  **Por qué:** El texto exacto cambia según la colección: `Añadir Entrada de actualidad`, `Añadir Selección`, `Añadir Documento`, `Añadir Integrante`, etc. Conviene indicar que se debe usar el botón de la colección en la que se está trabajando.
- **Observación:** `Save / Guardar`, `Update / Actualizar` y `Publish / Publicar` aparecen juntos.
  **Por qué:** Para una persona nueva no queda completamente claro cuál conserva un borrador, cuál modifica una publicación existente y cuál la hace pública.

### 04A — Activa los campos personalizados

- **Observación:** Después de activar el panel, la cartilla dice `Add New / Añadir nuevo`, pero el panel nativo muestra `Add Custom Field`, `Enter new`, `Name`, `Value` y `Update`.
  **Por qué:** La persona puede buscar un botón con un texto que no existe en su pantalla. El flujo debería indicar: pulsar `Enter new`, escribir la clave en `Name`, escribir el valor en `Value` y pulsar `Add Custom Field`; para un campo existente, usar `Update`.
- **Observación:** La página muestra la activación de Preferencias, pero no muestra el panel inmediatamente después de recargar.
  **Por qué:** El usuario puede activar correctamente la opción y aun así no saber dónde debe escribir la clave técnica.
- **Observación:** La lista de campos no indica cuáles son obligatorios para producir un resultado visible y cuáles son complementarios.
  **Por qué:** Se puede guardar un campo correcto y esperar un cambio público que depende de otra condición, como la imagen destacada, el estado publicado o una taxonomía.

### 05 — Publica en Actualidad

- **Observación:** `labm_demo_image` puede aparecer entre los campos personalizados de contenidos de demostración, pero no es un campo editorial para la fecha ni para la imagen destacada.
  **Por qué:** El nombre puede hacer pensar que debe completarse manualmente. Es un metadato técnico de respaldo de fixtures; la fecha del evento se registra en `labm_fecha_evento` y la imagen editorial se asigna como imagen destacada.
- **Observación:** La cartilla indica que se debe completar `Categorías`, pero no muestra cómo elegir una categoría válida.
  **Por qué:** `Categorías` clasifica la publicación, mientras `labm_fecha_evento` ordena el evento destacado; son decisiones distintas y el usuario puede confundirlas.
- **Observación:** El formato `AAAA-MM-DD` se indica, pero no se aclara que la fecha debe ser una fecha calendario válida.
  **Por qué:** Un valor incompleto o una fecha imposible puede no producir el resultado esperado en el bloque destacado.

### 06 — Selecciones: Piso y Playa

- **Observación:** `Modalidades`, `Categorías` y `labm_modalidad_detalle` aparecen en la misma sección sin un ejemplo de su relación.
  **Por qué:** La modalidad `Piso` o `Playa` determina el listado y el estado activo; la categoría clasifica el contenido y `labm_modalidad_detalle` solo añade contexto. Editar el campo personalizado no cambia el listado.
- **Observación:** No se aclara si una publicación puede tener las dos modalidades.
  **Por qué:** Para un registro que pertenece a una sola modalidad, asignar ambas puede hacerlo aparecer en más de un listado y contradecir la instrucción de elegir una.

### 07–08 — Documentos y PDF

- **Observación:** Se pide seleccionar un `Tipo de documento`, pero la cartilla no lista las opciones disponibles.
  **Por qué:** El usuario no sabe qué opción escoger. Las opciones sembradas por el sitio son: Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro.
- **Observación:** La página menciona una captura local con límite de 2 MB y también un límite efectivo de hasta 30 MB.
  **Por qué:** Puede interpretarse que ambos límites aplican al mismo tiempo. Debe priorizarse el límite que muestre la instalación actual y el menor valor entre WordPress y LABM.
- **Observación:** Después de elegir un PDF no se indica que en la biblioteca hay que confirmar con `Usar este PDF`.
  **Por qué:** Seleccionar el archivo no necesariamente lo asocia todavía al documento; falta la confirmación del modal.
- **Observación:** `Quitar PDF`, `Borrador` y `Papelera` se explican en páginas distintas.
  **Por qué:** Son acciones diferentes: quitar desvincula el archivo, borrador retira la publicación sin eliminarla y papelera mueve el registro a recuperación.

### 09 — Inicio: crea una diapositiva

- **Observación:** Se mencionan `labm_destino_url` y `labm_cta_texto`, pero no se aclara qué ocurre si solo se completa uno.
  **Por qué:** El usuario puede crear una diapositiva con botón sin destino, o destino sin etiqueta, y no saber por qué el enlace no aparece correctamente.
- **Observación:** La instrucción “revisa el destino” no indica que debe probarse la URL desde la portada pública.
  **Por qué:** Una URL guardada no garantiza que el botón navegue al destino esperado.

### 10 — Inicio: aliados y clubes

- **Observación:** La página reúne `Aliados Oficiales` y `Clubes` en una misma secuencia, pero la imagen solo muestra el editor de aliados.
  **Por qué:** El usuario puede intentar crear un club desde el editor clásico de aliados o esperar que ambos registros compartan los mismos campos.
- **Observación:** `Order / Orden` se menciona sin indicar si está en el editor o en la edición rápida del listado.
  **Por qué:** La ubicación depende de la vista y de los permisos de la cuenta; se necesita una ruta alternativa para encontrarlo.

### 11 — Nosotros: publica integrantes

- **Observación:** Se indican Comité, Entrenadores y Representantes, pero no se explica que son opciones existentes de `Grupos de integrantes` y no texto libre.
  **Por qué:** Crear un grupo con una variación de nombre puede impedir que el integrante aparezca en los filtros públicos.
- **Observación:** Se pide completar `labm_cargo`, pero no se indica qué valor debe tener el campo para que sea visible en la tarjeta.
  **Por qué:** El usuario puede dejar el cargo en el título o en el cuerpo y pensar que eso reemplaza el metadato que la plantilla consulta.

### 12 — Actualiza los textos institucionales

- **Observación:** Se pide confirmar el `Slug / Identificador`, pero no se señala dónde se visualiza dentro del editor.
  **Por qué:** Un usuario nuevo puede editar el título correcto y cambiar accidentalmente el slug, desconectando el bloque institucional de la plantilla.
- **Observación:** Los identificadores se enumeran juntos, sin asociar cada uno con su ubicación pública.
  **Por qué:** Es necesario distinguir `banner-nosotros`, `mision-nosotros`, `vision-nosotros` y `banner-documentos` antes de editar para no actualizar el bloque equivocado.

### 13 — Contacto y pie de página

- **Observación:** Se indica que Correo y redes se comparten con Contacto, pero no se muestra una comprobación concreta del resultado en ambas vistas.
  **Por qué:** El usuario puede guardar correctamente el footer y no verificar que el cambio se refleje también en Contacto.
- **Observación:** Se advierte que LABM → SMTP envía correo, pero la cartilla no separa visualmente esa configuración de los datos editoriales del footer.
  **Por qué:** Una persona puede intentar corregir un correo público desde SMTP o pulsar una prueba de envío durante una edición rutinaria.

### 14 — Imágenes, logos y galerías

- **Observación:** Se explica la diferencia entre imagen destacada y galería, pero no se indica cuándo debe usarse una u otra en cada colección.
  **Por qué:** La imagen destacada alimenta tarjetas y listados; la galería pertenece al contenido y no la reemplaza.
- **Observación:** La instrucción de revisar el texto alternativo no indica quién debe definirlo ni qué debe describir.
  **Por qué:** Un texto genérico o el nombre del archivo no ayuda a la accesibilidad ni a identificar la imagen en caso de carga fallida.

### 15 — Publica un club asociado

- **Observación:** Se solicita clasificar el club en `Modalidades`, pero no se repite que la ciudad se registra aparte en `labm_ciudad`.
  **Por qué:** El usuario puede escribir la ciudad solo en la descripción y no completar el dato que consume la tarjeta pública.
- **Observación:** Se indica que la portada muestra una selección limitada, pero no se aclara que publicar correctamente no garantiza aparecer de inmediato.
  **Por qué:** El usuario puede interpretar que la publicación falló cuando el club queda fuera del cupo visible de la portada.

### 16 — Retira y recupera contenido

- **Observación:** Se menciona `Trash / Papelera`, pero no se indica dónde se accede a la papelera de cada colección.
  **Por qué:** La papelera es contextual al tipo de contenido; buscarla en el menú principal puede hacer pensar que el registro se perdió.
- **Observación:** Se recomienda cambiar a borrador o usar papelera, pero no se indica cuándo conservar los metadatos y la imagen asociada.
  **Por qué:** Retirar una publicación no debería implicar borrar sus PDF o imágenes compartidas.

### 17 — Tu revisión antes de publicar

- **Observación:** “Selecciones y modalidad activos en la interna” usa una expresión visual que no está definida.
  **Por qué:** El usuario necesita saber que debe comprobar el resaltado del menú padre `Selecciones` y del enlace `Piso` o `Playa`, no solo que la página cargue.
- **Observación:** La lista no incluye una comprobación explícita de campos personalizados visibles y actualizados.
  **Por qué:** Un campo puede estar guardado con una clave incorrecta y la publicación puede verse completa en el editor, pero no alimentar la plantilla pública.