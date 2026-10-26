# Delta para Arquitectura CMS

## ADDED Requirements

### Requirement: Configuración editorial de Nuestra Liga

El sistema SHALL ofrecer en **LABM → Inicio** una configuración editorial para el bloque Nuestra Liga. La configuración MUST incluir campos independientes para el rótulo de sección, título principal, descripción, texto del botón, URL del botón, indicador numérico y etiqueta del indicador. Solo usuarios con la capacidad `edit_theme_options` MAY consultar y modificar esta configuración.

La configuración MUST conservar un esquema cerrado, MUST persistir únicamente valores válidos y SHOULD mantener los valores actuales como predeterminados seguros. Los campos vacíos o ausentes MUST resolver a sus valores predeterminados efectivos sin eliminar la estructura pública del bloque. Los valores desconocidos no MUST modificar ni ampliar el esquema editorial.

#### Scenario: Edición autorizada de todos los campos

- GIVEN (DADO) un usuario con `edit_theme_options` y una instalación con el menú LABM disponible.
- WHEN (CUANDO) abre LABM → Inicio, modifica los siete campos con valores válidos y guarda la configuración.
- THEN (ENTONCES) el sistema confirma el guardado, conserva los valores al volver a abrir la pantalla y los deja disponibles para la presentación pública.

#### Scenario: Acceso no autorizado

- GIVEN (DADO) un usuario sin `edit_theme_options`.
- WHEN (CUANDO) intenta consultar o enviar la configuración de Nuestra Liga.
- THEN (ENTONCES) el sistema rechaza la operación, no permite modificar los valores guardados y no revela datos administrativos innecesarios.

#### Scenario: Opción ausente o campo vacío

- GIVEN (DADO) que la configuración todavía no existe o que uno o más campos están vacíos.
- WHEN (CUANDO) se obtiene la configuración efectiva para Inicio.
- THEN (ENTONCES) cada campo ausente o vacío devuelve su valor predeterminado seguro y el bloque conserva contenido utilizable.

#### Scenario: Entrada fuera del esquema

- GIVEN (DADO) una solicitud que contiene claves desconocidas, valores con formato no permitido o una URL no válida.
- WHEN (CUANDO) se intenta guardar la configuración.
- THEN (ENTONCES) el sistema ignora o normaliza lo que no pertenece al esquema, conserva solo valores válidos y no corrompe la configuración existente.

#### Scenario: Independencia de la presentación

- GIVEN (DADO) valores editoriales guardados para Nuestra Liga.
- WHEN (CUANDO) cambia la presentación activa del sitio o el componente administrativo no está disponible durante una consulta pública.
- THEN (ENTONCES) los valores persistidos no se pierden y la vista pública continúa siendo segura, usando los valores predeterminados cuando no pueda obtener la configuración administrada.

### Requirement: Documentación del flujo editorial de Nuestra Liga

La documentación de usuario SHALL explicar en español la ruta LABM → Inicio, el propósito de cada campo, el efecto de dejar un campo vacío y la forma de comprobar el resultado en Inicio. La documentación MUST describir únicamente comportamientos disponibles y SHOULD mantenerse coherente entre el manual escrito y la cartilla visual.

#### Scenario: Consulta del manual

- GIVEN (DADO) un editor que necesita actualizar Nuestra Liga.
- WHEN (CUANDO) consulta el manual de usuario.
- THEN (ENTONCES) encuentra la ruta administrativa, los siete campos, sus efectos y un procedimiento verificable para revisar la página pública.

#### Scenario: Campo vacío documentado

- GIVEN (DADO) un editor que deja vacío uno de los campos.
- WHEN (CUANDO) consulta la documentación y revisa Inicio.
- THEN (ENTONCES) comprende que se aplica el valor predeterminado seguro y observa un bloque funcional sin contenido ficticio.
