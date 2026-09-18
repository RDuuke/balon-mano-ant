# Exploración: experiencia de gestión de documentos PDF

## Alcance

Esta exploración cubre exclusivamente el back office de WordPress para `Documentos > Añadir/Editar`. No incluye cambios del catálogo público, paginación, iconos, metadatos visibles ni estilos del frontend.

## Estado actual comprobado

- `labm_documento` ya existe como CPT público, usa capacidades propias, REST y la taxonomía jerárquica `labm_documento_categoria`.
- El PDF se guarda en `labm_documento_pdf_id` y la fecha en `labm_documento_fecha`; ambos metadatos están expuestos por REST. El ID del PDF se registra actualmente como `string`, aunque se sanea con `absint`.
- No existe un panel, metabox ni script administrativo para escoger el PDF. El plugin no tiene carpeta de assets administrativos ni usa `wp.media`.
- El flujo actual obliga a recurrir a campos personalizados y conocer el ID del adjunto, una interacción inadecuada para usuarios con poca experiencia técnica.
- `labm_core_validate_publishable()` solo comprueba presencia de título e ID; no verifica que el ID corresponda a un adjunto PDF válido y no está conectado al guardado REST ni al guardado administrativo de `labm_documento`.
- La validación real disponible (`labm_core_validate_pdf_file()`) comprueba legibilidad, tamaño y firma `%PDF-`, pero el flujo editorial no la invoca. El importador WP-CLI sí la usa con un límite de 30 MB.
- Existe un patrón de validación reutilizable para Slides/Aliados: filtro `rest_pre_insert_*`, degradación a borrador en editor clásico, nonce, capacidad y aviso administrativo.
- Los documentos existentes, incluidos los importados, ya contienen IDs numéricos de adjunto y pueden hidratar una nueva interfaz sin migrar el dato base.

## Alternativas

### A. Panel propio del editor de bloques + Media Library, con fallback clásico

Un panel «Datos del documento» carga `wp.media`, muestra controles explícitos para seleccionar/subir, reemplazar y quitar el PDF, y persiste el ID internamente por REST. Un metabox equivalente cubre el editor clásico. Ambos consumen las mismas funciones PHP de validación.

**Ventajas:** no expone IDs, usa patrones nativos de WordPress, ofrece feedback inmediato y permite bloqueo accesible antes de guardar. **Costo:** añade un asset JS administrativo y requiere pruebas de los dos canales de guardado.

### B. Reutilizar «Imagen destacada» como PDF

Reduce código, pero la semántica y los textos son incorrectos, WordPress espera imágenes en varios puntos y se mezclaría con una característica visual no aplicable.

### C. Mantener campos personalizados y añadir instrucciones

Es la alternativa más barata, pero conserva el problema principal: exige comprender IDs y metadatos internos, y facilita errores silenciosos.

## Recomendación preliminar

Elegir la alternativa A y concentrar la lógica en una capa de dominio compartida. La interfaz mejora la experiencia, pero el servidor sigue siendo la autoridad y debe rechazar cualquier publicación inválida aunque el cliente sea omitido.

### Flujo editorial propuesto

1. El editor escribe el título.
2. En «Archivo PDF», pulsa «Seleccionar o subir PDF» y usa la Media Library filtrada a PDF.
3. Tras elegirlo, ve nombre de archivo, tamaño legible y estado textual «PDF válido» o el error accionable. Nunca ve el ID.
4. Puede «Reemplazar PDF» o «Quitar PDF». Reemplazar no elimina automáticamente el adjunto anterior de la biblioteca.
5. Elige opcionalmente la «Fecha del documento» y un «Tipo de documento».
6. Publicar o actualizar queda bloqueado si falta título, falta PDF o el adjunto es inválido. El error aparece en un aviso accesible y junto al control afectado.

## Contratos recomendados

### PDF

- Mantener `labm_documento_pdf_id`, corrigiendo su esquema REST a `integer`. Los valores numéricos existentes en `postmeta` siguen siendo compatibles.
- Aceptar únicamente un post de tipo `attachment` cuyo MIME declarado sea `application/pdf`, archivo local legible, tamaño dentro del límite y firma `%PDF-` válida.
- Definir un único límite compartido. El antecedente actual es 30 MB; conviene usar `min(30 MB, wp_max_upload_size())` y mostrarlo antes de abrir el selector.
- Calcular nombre y peso desde el adjunto/archivo en cada carga. No persistir el peso, porque se volvería obsoleto si se reemplaza el archivo.
- Usar `wp_prepare_attachment_for_js()` o el modelo de `wp.media` para el feedback visual, y repetir toda validación en PHP antes de guardar.

### Fecha

- Etiqueta inequívoca: «Fecha del documento», no fecha de publicación ni fecha de carga.
- Control nativo de fecha que presente el formato local del navegador y persista `Y-m-d` en `labm_documento_fecha`.
- La fecha es opcional porque el requisito obligatorio vigente menciona título y PDF. Vacía significa «Sin fecha definida» en administración; no se sustituye silenciosamente por la fecha de publicación.
- Si se suministra, debe ser una fecha calendario real; una fecha inválida bloquea el guardado con un mensaje asociado al campo.

### Tipo de documento

- Reutilizar `labm_documento_categoria` para no duplicar datos ni romper registros existentes, pero renombrar sus etiquetas administrativas a «Tipos de documento» / «Tipo de documento».
- Mantener un solo término por documento mediante el panel propio, ocultando el selector taxonómico genérico en esta pantalla.
- Sembrar idempotentemente un vocabulario inicial administrable: Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro.
- Reservar la gestión del vocabulario a administradores; editores seleccionan términos existentes. Los términos anteriores no se eliminan ni renombran automáticamente.
- Usar «Documento general» como valor por defecto para nuevos registros y como fallback explícito de registros sin término. No inferir el tipo desde el nombre del archivo salvo en una migración revisada manualmente.

## Validación y accesibilidad

- Registrar un validador único que resuelva el estado efectivo del documento, incluso en actualizaciones REST parciales: título, meta existente/nueva, adjunto y fecha.
- En REST, usar `rest_pre_insert_labm_documento` y devolver `WP_Error` con estado 400 y mensajes por campo antes de mutar el post.
- En el editor de bloques, bloquear el guardado/publicación con el store del editor mientras el formulario sea inválido; mostrar avisos con texto, no solo color, y una región `aria-live="polite"`. El bloqueo cliente es ayuda, no control de seguridad.
- Para editor clásico, usar nonce, `current_user_can()` y el patrón existente de conservar como borrador + redirect seguro + `admin_notices`.
- No aceptar IDs enviados sin comprobar capacidad para editar el documento y acceso al adjunto. Sanear el entero, la fecha y el término en servidor.
- Etiquetar todos los controles, enlazar ayuda/error mediante `aria-describedby`, conservar foco visible y permitir operar Media Library, reemplazo y eliminación solo con teclado.

## Compatibilidad y migración

- No se requiere cambio de tabla: WordPress ya almacena meta y taxonomías.
- Los IDs guardados como cadenas numéricas se leen como enteros después de corregir el esquema REST; debe probarse la transición antes de desplegar.
- Los documentos existentes con PDF válido aparecen inmediatamente en el panel con nombre y peso.
- Los publicados que carezcan de fecha o tipo no se despublican. Se muestra el fallback administrativo y se normalizan solo cuando un editor los guarde explícitamente.
- Un documento existente con adjunto ausente o inválido conserva su estado durante el despliegue, pero cualquier intento posterior de actualizar/publicar exige corregir el PDF. No se ejecuta una despublicación masiva.
- El importador debe seguir usando la misma validación central y puede asignar tipos/fechas mediante un mapa explícito; no debe depender de la interfaz JS.

## Áreas probablemente afectadas en fases posteriores

- `wp-content/plugins/labm-core/includes/class-labm-domain.php`: esquema de meta, etiquetas de taxonomía, vocabulario y validación del documento.
- `wp-content/plugins/labm-core/includes/class-labm-documents-contact.php` o un módulo administrativo nuevo: validación compartida de adjuntos y guardado.
- Nuevo asset JS administrativo y su carga limitada a `post.php`/`post-new.php` de `labm_documento`.
- `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php`: reutilización de validación y, si se aprueba, mapa explícito de tipo/fecha.
- `tests/php/DocumentContactTest.php` y nuevas pruebas de administración/navegador.

No se prevén cambios de tema ni del render público para este alcance.

## Estrategia de pruebas

- PHPUnit: meta integer, permisos, fecha ISO, términos por defecto, archivo ausente, MIME falso, firma inválida, límite y adjunto válido.
- REST: creación y actualización parcial; publicación rechazada sin título/PDF o con PDF inválido; borrador permitido; error sin mutación.
- Editor clásico: nonce, capacidad, degradación segura a borrador y aviso accionable.
- Playwright en wp-admin: seleccionar/subir, recargar con estado hidratado, reemplazar, quitar, nombre/peso, bloqueo/desbloqueo y operación completa por teclado.
- Regresión: documentos antiguos con ID numérico en string, sin fecha/tipo, importados por WP-CLI y adjuntos compartidos.
- Calidad: WPCS, PHPStan, cobertura, axe/WCAG del panel y verificación LF.

## Riesgos y preguntas para PROPOSE

- El API exacto del panel del editor de bloques debe confirmarse contra WordPress 6.8 para evitar depender de componentes obsoletos.
- La validación cliente y servidor puede divergir; debe existir una sola definición PHP de validez y pruebas contractuales del JS.
- Bloquear la actualización de un documento ya publicado e inválido puede sorprender al editor, por lo que el mensaje debe explicar cómo repararlo sin perder cambios.
- Debe confirmarse si 30 MB es el límite editorial definitivo o si se adopta otro valor menor que el límite del servidor.
- Debe acordarse quién puede administrar el vocabulario y si «Documento general» debe asignarse automáticamente a todos los registros históricos o solo al guardarlos.

