# Auditoría de limpieza de código orientada al usuario

Fecha: 2026-10-15
Alcance: tema y plugin LABM, recorridos públicos y recursos que los soportan.

Última actualización documental registrada: 2026-10-19, manual y cartilla versión 1.5. Este informe conserva revisiones sucesivas: los límites de ejecución y la reversión descritos en cada entrada corresponden a esa intervención. La actualización 1.5 se detalla al final y no implica una nueva validación funcional de WordPress ni el cierre de las revisiones personales.

## Metodología

Se inspeccionaron plantillas del tema, partes globales, patrones, CSS, JavaScript, funciones PHP, pruebas PHP y Playwright, scripts y configuraciones de calidad, documentación operativa y referencias de CI. Se contrastó la revisión con los casos de experiencia pública existentes. Se consideró eliminable un elemento solo cuando la redundancia o falta de necesidad era demostrable sin depender de contenido persistido o referencias dinámicas.

La inspección fue estática. No se navegó el sitio en un navegador durante esta ejecución: el acceso al daemon Docker falló por denegación de acceso a `C:\Users\User\.docker\config.json`. Por ello, las observaciones siguientes describen rutas, contratos y pruebas del repositorio, no una certificación visual del sitio desplegado.

## Reproducción de la cartilla desde la perspectiva de usuario final

### Alcance y criterio de lectura

El 2026-10-15 se recorrió visualmente el panel WordPress autenticado con la sesión `labm_demo_admin`, contrastando las pantallas disponibles con los pasos de las secciones 1 a 14 de la cartilla (`docs/manual-usuario/cartilla-manual-usuario-labm.pdf`) y su manual asociado. La matriz registra si el recorrido pudo comprobarse en pantalla; no certifica que una publicación, ajuste o archivo se guarde correctamente. Los pasos con efectos laterales se dejan explícitamente sin ejecutar.

En **Custom Fields**, ver una clave en el selector confirma únicamente que está disponible en el panel genérico observado. No confirma un metabox dedicado para esa clave, que su uso sea apropiado en el tipo de contenido abierto, que pueda guardarse ni que la consuma una página pública. `lamb_demo_fecha` **NO EXISTE**, tal como advierte la cartilla; la clave mencionada para fecha de evento es `labm_fecha_evento`.

### Matriz de recorrido

| Sección | Recorrido para una persona editora | Estado | Evidencia observada | Paso no ejecutado / límite |
|---|---|---|---|---|
| 1. Ingresar al administrador | Entrar y reconocer el Escritorio y el menú lateral. | Reproducido parcialmente | Se observó Dashboard con sesión ya autenticada. | No se abrió el formulario de acceso ni se introdujeron credenciales. |
| 2. Encontrar la sección correcta | Ubicar el contenido por el menú administrativo. | Reproducido visualmente (registro histórico) | Menús observados: Actualidad, Selecciones, Clubes, Integrantes, Horarios, Documentos, Slides de Inicio, Aliados Oficiales, LABM/SMTP y Media. Horarios fue retirado el 2026-10-25; ver el registro de retirada al final. | No se comprobó el destino público de cada menú ni la disponibilidad según otros roles. |
| 3. Crear, revisar, publicar y actualizar | Abrir un editor y localizar campos y controles de publicación. | Reproducido parcialmente (registro histórico) | En crear Actualidad, Selección, Club, Integrante y Horario apareció **Custom Fields** con selector de claves `labm_cargo`, `labm_ciudad`, `labm_cta_texto`, `labm_demo_image`, `labm_destino_url`, `labm_documento_fecha`, `labm_documento_pdf_id`, `labm_document_source_key`, `labm_fecha_evento`, `labm_inicio`, `labm_modalidad_detalle` y `labm_test_fixture`, además de **Value** y **Add Custom Field**. Horarios y `labm_inicio` se retiraron el 2026-10-25. | No se añadieron ni actualizaron metadatos, no se guardó ni publicó una entrada ni se comprobó el resultado público. La lista no demuestra metaboxes dedicados. |
| 4. Actualidad: noticias y eventos | Abrir el editor y reconocer campos de contenido y fecha de evento. | Reproducido parcialmente | Se observó la pantalla de creación de Actualidad y el selector genérico anterior, incluida `labm_fecha_evento`. | No se diligenció, guardó ni publicó noticia; no se probó la fecha en la portada. `lamb_demo_fecha` **NO EXISTE**. |
| 5. Selecciones: Piso y Playa | Abrir Selección y localizar datos adicionales. | Reproducido parcialmente | Se observó la pantalla de creación de Selección y el selector genérico, incluida `labm_modalidad_detalle`. | No se asignó modalidad/categoría, ni se guardó o verificó un listado público. |
| 6. Documentos: publicar y sustituir PDF | Reconocer el panel para asociar, clasificar y fechar un documento. | Reproducido visualmente | Panel dedicado **Archivo y clasificación**, selector/subida de PDF, fecha, tipo y nota de que quitar el archivo de la publicación no lo elimina de la biblioteca. Tipos visibles: Acta, Actas publicas, Certificado, Circular, Circulares de prueba, Convocatoria, Documento general, Informe, Otro, Reglamento y Resolución. | No se eligió ni subió PDF, no se quitó/reemplazó archivo, ni se guardó/publicó documento. |
| 7. Inicio: slides | Reconocer el editor de slides y su orden. | Reproducido parcialmente | Slides usa editor clásico; etiquetas en inglés **Add Post**, **Featured image**, **Post Attributes/Order**; se observó el control **Order**. | No se creó ni ordenó slide, guardó, publicó o revisó el carrusel público. |
| 8. Inicio: aliados y clubes | Abrir aliados y clubes para reconocer los editores. | Reproducido parcialmente | Aliados usa editor clásico con **Add Post**, **Featured image** y **Post Attributes/Order**. La pantalla de creación de Club mostró el panel **Custom Fields** genérico. | No se cargó logo, creó aliado, guardó club ni se comprobó Inicio. La observación no confirma enlace externo para aliados. |
| 9. Nosotros: presentación, misión, visión e integrantes | Localizar Integrantes y las entradas institucionales. | Reproducido parcialmente | Se observó la creación de Integrante y el selector genérico de **Custom Fields**. | No se abrieron ni editaron Entradas institucionales, no se eligió grupo ni se guardó o comprobó la página Nosotros. |
| 10. Contacto y pie de página | Revisar ajustes de Footer y correo. | Reproducido parcialmente | Footer LABM mostró identidad, descripción, navegación 1-4, recursos 1-3, contacto, correo, Facebook, Instagram, copyright, política y **Save Changes**. SMTP mostró servidor, puerto, cifrado TLS/SSL, correo/nombre remitente, destinatarios, **Save Changes** y **Enviar prueba**. Se indicó que la contraseña se lee solo desde `LABM_SMTP_PASSWORD` en `wp-config.php`. | No se guardaron ajustes ni se envió prueba SMTP. No se comprobó el correo público. |
| 11. Biblioteca de medios y galerías | Buscar y administrar adjuntos. | Reproducido parcialmente | **Media** mostró **Add Media File**, vistas de lista/cuadrícula, filtros por tipo/fecha, **Bulk select**, **Search media**, numerosos adjuntos con nombres genéricos y una imagen demo. | No se subió, seleccionó, editó ni eliminó archivo; no se creó galería. |
| 12. Retirar y recuperar una publicación | Enviar a papelera y restaurar contenido. | No ejecutado por seguridad | No se realizaron acciones destructivas o de recuperación durante la inspección. | No se movió contenido a papelera, restauró ni eliminó permanentemente. |
| 13. Resolver problemas frecuentes | Reproducir incidencias y comprobar sus soluciones. | No ejecutado por seguridad | Se observaron pantallas normales del panel, no un flujo de error. | No se guardaron datos inválidos, provocaron fallos de envío ni alteraron estado para simular incidencias. |
| 14. Lista de revisión y límites del panel | Aplicar la lista antes/después de publicar y reconocer límites. | Reproducido parcialmente | Las pantallas permitieron constatar controles y límites indicados: secreto SMTP fuera del formulario y selectores genéricos de metadatos. | No se publicó contenido ni se hizo una verificación posterior; no se afirma que la lista operativa haya sido completada. |

### Hallazgos UX priorizados y mejoras concretas

| Prioridad | Hallazgo observado | Mejora concreta |
|---|---|---|
| Alto | El panel **Custom Fields** ofrece una lista amplia y transversal de claves técnicas, sin demostrar cuál aplica a cada tipo de contenido. El usuario puede confundir disponibilidad del selector con un campo dedicado o con un dato que se verá en el sitio. | Sustituir la entrada manual por controles dedicados por tipo, con etiqueta en español, ayuda contextual, validación de formato y vista previa del resultado; ocultar claves técnicas o restringirlas al tipo pertinente. Hasta implementarlo, la cartilla debe indicar explícitamente qué claves usar en cada tipo y que el selector por sí solo no garantiza salida pública. |
| Alto | Los editores de Slides y Aliados presentan etiquetas inglesas (**Add Post**, **Featured image**, **Post Attributes/Order**) en un flujo editorial en español. | Localizar las etiquetas del editor y añadir ayuda en español para imagen obligatoria y orden, conservando en la guía los nombres ingleses solo como referencia temporal para capturas/versiones que aún los muestren. |
| Alto | SMTP coloca **Save Changes** junto a **Enviar prueba**, acción que envía correo; la contraseña está correctamente fuera del formulario, pero depende de configuración técnica. | Separar visualmente la acción de prueba de guardar configuración, mostrar confirmación del destinatario antes del envío y un resultado explícito; incluir una ayuda breve que remita al administrador técnico para la contraseña, sin pedir ni mostrar el secreto. |
| Medio | La biblioteca contiene muchos adjuntos con nombres genéricos y una imagen de demostración, lo que dificulta reconocer archivos antes de seleccionarlos. | Establecer una convención de nombres al subir, promover búsqueda y filtros, y añadir indicaciones de título/texto alternativo; distinguir visualmente material de demostración del contenido institucional. |
| Medio | Hallazgo histórico: el menú **Horarios** podía sugerir que publicar allí tendría visibilidad pública automática. | Resuelto el 2026-10-25: se retiró el tipo del administrador y del código; se eliminaron sus tres registros locales y el metadato `labm_inicio`. |
| Medio | Footer concentra numerosas familias de ajustes y ofrece un único **Save Changes** al final, aumentando el riesgo de no advertir campos fuera de pantalla. | Agrupar controles por secciones con encabezados claros, enlaces de salto y aviso de cambios sin guardar; añadir enlaces directos para previsualizar Footer y Contacto después de guardar. |
| Bajo | En Documentos el control del archivo y su clasificación están en un panel específico, mientras el panel genérico también enumera claves con nombres relacionados (`labm_documento_fecha`, `labm_documento_pdf_id`). | Mantener una sola ruta editorial dedicada y aclarar en la cartilla que fecha y PDF se gestionan allí; evitar que las claves internas se sugieran como campos para editar manualmente. |

## Implementación de mejoras UX

### Detalle de artículos de Actualidad

- Hallazgo: el cuerpo y el bloque **Compartir** se renderizaban como elementos consecutivos del artículo. Las reglas CSS de dos columnas ya estaban definidas para `.labm-actualidad-detail-page__reading`, pero ningún elemento de la plantilla usaba esa clase.
- Corrección: se agruparon el cuerpo de Gutenberg y el shortcode de detalle dentro del contenedor semánticamente neutro previsto por la plantilla, activando la cuadrícula existente en escritorio y su apilado en pantallas estrechas. Se conservan intactos los enlaces, iconos, nombres accesibles y controles.
- Verificación: `git diff --check` limpio; los dos archivos editados usan LF sin CRLF. Revisión estática confirma que la plantilla activa la cuadrícula de dos columnas existente desde escritorio y conserva la regla de apilado a `48rem`. No fue posible comprobar el artículo demo en el servidor local: Docker no pudo leer `C:\Users\User\.docker\config.json` por acceso denegado.

### Corrección del recorte de la imagen hero de Actualidad

- Causa: `.labm-actualidad-detail__image` tenía `max-height: 38rem` y `object-fit: cover`. El límite de altura podía recortar verticalmente imágenes principales altas, especialmente cuando la proporción del archivo no coincidía con el espacio disponible.
- Solución: se eliminó el límite de altura y se mantuvo `width: 100%`, `max-width: 100%` y `height: auto`, con `object-fit: contain` como protección adicional. La imagen conserva su proporción, se muestra completa y no genera overflow en escritorio ni móvil. La plantilla `single-labm_actualidad.html` no se alteró en esta corrección; se preservan el encabezado y la composición de compartir existente.
- Verificación: inspección estática de la plantilla y de la regla CSS confirma que no hay altura fija ni `object-fit: cover` en el hero; `git diff --check` y la comprobación LF/CRLF se ejecutaron sobre los archivos modificados. La comprobación visual del artículo público queda limitada por la disponibilidad del navegador/runtime local y se documenta junto con los resultados finales.

La implementación se limitó a código y documentación. No se abrió el panel de WordPress, no se guardó contenido ni configuración, no se envió correo y no se subieron, eliminaron ni modificaron archivos de la biblioteca.

| Sugerencia | Estado | Archivos | Motivo y verificación |
|---|---|---|---|
| Campos personalizados técnicos | Parcialmente implementada | `wp-content/plugins/labm-core/includes/class-labm-domain.php`, `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Documentos oculta solo el metabox genérico porque ya tiene un panel dedicado, pero conserva el soporte `custom-fields` para no cambiar el contrato del tipo ni el registro REST. Actualidad, Selecciones e Integrantes muestran ayuda de claves con consumidor público confirmado; Clubes advierte que su metadato registrado no tiene salida pública confirmada. Slides muestra sus dos claves de portada, registradas y consumidas por el tema. Horarios y `labm_inicio` fueron retirados el 2026-10-25. No se inventaron metaboxes dedicados: su creación requiere una decisión de arquitectura y pruebas de guardado por tipo. Verificación: inspección del registro de tipos, metadatos y consumidores, más diff estático; la presentación visual debe confirmarse en el editor desplegado. |
| Consistencia de Slides y Aliados | Implementada parcialmente | `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Se añadieron etiquetas administrativas en español, traducciones localizadas de `Add Post`, `Featured image`, `Post Attributes` y `Order`, y una ayuda visible sobre imagen, logo y orden. La cobertura se limita al editor clásico observado; una eventual personalización equivalente del editor Gutenberg requiere validar la versión concreta de WordPress. Verificación: revisión del registro de labels, filtro acotado por tipo y diff estático. |
| Confirmación de SMTP “Enviar prueba” | Implementada | `wp-content/plugins/labm-core/includes/class-labm-smtp-settings.php` | La pantalla advierte que la acción envía un correo real, muestra los destinatarios efectivos y exige confirmación antes de enviar. Se conserva el nonce, la capacidad y el flujo PRG existentes. Verificación: inspección estática del formulario y del handler; no se ejecutó la acción ni se envió correo. |
| Confirmación de quitar PDF | Implementada | `wp-content/plugins/labm-core/includes/class-labm-document-admin.php`, `wp-content/plugins/labm-core/assets/js/admin-documento.js` | Las variantes Gutenberg y clásica confirman que la acción solo desvincula el PDF y que el adjunto permanece en la biblioteca. Verificación: revisión de ambas ramas JavaScript y del metabox clásico; no se seleccionó ni quitó ningún archivo. |
| Ayudas para PDF, fecha, tipo y medios | Implementada | `wp-content/plugins/labm-core/includes/class-labm-document-admin.php`, `wp-content/plugins/labm-core/assets/js/admin-documento.js` | Se aclaró que el PDF se busca o sube desde Medios, que no se introduce su ID, que la fecha es la que figura en el documento y que el tipo es una clasificación única. Verificación: comparación de textos compartidos entre editor clásico y Gutenberg. |
| Declaración CSS duplicada en Nosotros | Implementada | `wp-content/themes/labm/style.css` | Se eliminó una declaración repetida idéntica de la cuadrícula del equipo, sin alterar su regla efectiva. Verificación: diff de una línea y lint/check de formato. |
| Biblioteca de medios | Pendiente | Sin cambios de contenido o biblioteca | La convención de nombres, el texto alternativo y la distinción visual del material demo dependen de una política editorial y de contenido existente. No se modificaron adjuntos ni se añadió una operación de biblioteca fuera del alcance. |
| Horarios y Footer | Horarios retirado; Footer sin cambios | `wp-content/plugins/labm-core/includes/class-labm-domain.php`; sin cambios en Footer | Horarios fue eliminado del registro de tipos, de sus capacidades activas y de los fixtures. El agrupamiento del Footer, enlaces de salto y aviso de cambios sin guardar requieren rediseñar la pantalla de ajustes; no se alteró ese flujo sin una especificación adicional. |
| Metadatos sin consumidor o con arquitectura por decidir | Pendiente | Sin cambios de contenido ni migración | `labm_ciudad` carece de consumidor público confirmado y los valores genéricos de slides no son todavía controles editoriales dedicados. Definir/implementar esos controles, decidir cómo conservar y migrar valores existentes, o conectar una salida pública requiere decisión de producto y pruebas de compatibilidad. Se mantiene la mitigación contextual; no se cambió el manual ni se reinterpretaron valores persistidos. |

Las ayudas de claves técnicas son una mitigación temporal. La solución completa sigue pendiente de definir controles dedicados por tipo, sus consumidores públicos y la migración compatible de valores existentes; por esa razón la cartilla debe continuar explicando los límites del panel genérico.

### Seguridad, estado y validación documental

No se guardó ni publicó contenido, no se subieron ni borraron archivos, no se restauraron publicaciones y no se envió la prueba SMTP. Por tanto, esta reproducción no cambió contenido, base de datos, ajustes persistidos ni uploads de WordPress; no aplica ejecutar `scripts/content-sync.ps1` ni actualizar `content-sync/canonical.zip` o `content-sync/latest.json`.

Esta ejecución actualiza la auditoría y aplica las mejoras de interfaz descritas en la sección anterior. `php -l` pasó en los cuatro PHP modificados y `node --check` pasó en `admin-documento.js`. `git diff --check`, la revisión de espacios finales y la verificación LF pasaron en los siete archivos modificados. PHPUnit, PHPCS y PHPStan no están disponibles porque falta `vendor/autoload.php`; no se ejecutó E2E porque su flujo de Documentos crea contenido y sube archivos. No se ejecutó `content-sync`: el cambio se limita a código y documentación y no altera estado persistido de WordPress.

## Recorridos revisados

| Recorrido público | Evidencia inspeccionada | Resultado de inspección |
|---|---|---|
| Inicio | `front-page.html`, patrones, lógica PHP y pruebas de Inicio | Hay cobertura dedicada para presentación, contenido y componentes editoriales. No se identificó un recurso redundante adicional con certeza alta. |
| Nosotros | `page-nosotros.html`, `about-team.js`, CSS y pruebas públicas | Hay comprobaciones para banner, misión/visión, integrantes, filtros, teclado, grupos vacíos, responsive y axe. Se confirmó una declaración CSS duplicada para la cuadrícula del equipo. |
| Actualidad y detalle | Plantillas de archivo y detalle, `actualidad-detail.js` y pruebas públicas | Se cubren listado, detalle, compartir, copiar enlace, accesibilidad y varios anchos. No se eliminó ningún recurso. |
| Selecciones | Plantillas de archivo/detalle, navegación, CSS y pruebas públicas | Hay cobertura de filtros, menú, paginación, vacíos, teclado, responsive y uso sin JavaScript. No se eliminó ningún recurso. |
| Documentos | `page-documentos.html`, plugin, pruebas PHP y E2E | Hay lógica y pruebas para filtros, descargas, validaciones y resultados vacíos. Se conserva el código de administración que publica el catálogo. |
| Contacto | `page-contacto.html`, `contact.js`, plugin y pruebas PHP/E2E | Hay pruebas de presentación y envío del formulario. Se conserva el código que sostiene el flujo de contacto. |
| Navegación, footer y 404 | `parts/header.html`, `parts/footer.html`, `templates/404.html`, navegación JS, estilos y pruebas | Se conservaron por ser componentes compartidos o fallback directo de navegación. |
| Responsive y accesibilidad | Proyectos Playwright 320, 768, 1024 y 1440 px; axe y comprobaciones de teclado/overflow | Existe cobertura automatizada en el repositorio. No fue ejecutada en esta auditoría por indisponibilidad del entorno Docker. |

## Hallazgos y acciones

| Hallazgo | Impacto para visitantes | Acción |
|---|---|---|
| La regla `.labm-about-team__grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }` aparecía dos veces consecutivas en el mismo bloque `@media (min-width: 768px)`. | No alteraba el diseño: ambas declaraciones eran idénticas y la segunda no cambiaba la cascada. | Se retiró la copia redundante; se conserva una única declaración. |
| No se encontraron otros archivos públicos de código/configuración cuya ausencia de uso pudiera probarse con certeza alta en el alcance inspeccionado. | Evita retirar por error comportamientos de navegación, formularios, contenido, diseño adaptable o soporte editorial. | No se eliminaron otros recursos. |

## Elementos conservados

- Scripts y estilos del tema encolados desde `functions.php`, incluidos navegación, Inicio, equipo, detalle de actualidad, contacto y volver arriba.
- Plantillas, patrones y bloques usados por rutas públicas, estados vacíos y páginas de error.
- Código del plugin que registra y administra contenido consumido por las páginas públicas.
- Imágenes, fuentes, uploads, fixtures, scripts de pruebas/gates y configuraciones de despliegue o calidad.
- Recursos de `design/`, `docs/` y `openspec/`, además de `debug.log`; no se juzgaron prescindibles por falta de referencias de frontend.

## Riesgos, validación y reversión

El cambio CSS es de riesgo bajo: elimina una repetición idéntica en el mismo contexto responsive. No afecta base de datos, contenido WordPress, uploads, dependencias ni sincronización canónica.

Validaciones realizadas después de editar:

- `git diff --check`: ejecutado; sin errores.
- Búsqueda exacta de la regla en `style.css`: ejecutada; queda una aparición (línea 545).
- Revisión del diff: ejecutada; una línea CSS eliminada y este informe nuevo.
- Finales de línea de `style.css` y este informe: verificados; cero pares CRLF.
- Revisión de referencias públicas y pruebas: inspección estática realizada; no equivale a ejecutar los recorridos.
- PHP/Composer: no ejecutados; existe PHP/Composer, pero falta `vendor/bin/phpunit` (no se instalaron dependencias).
- Playwright E2E y `scripts/gate.ps1 -IncludeBrowser`: no ejecutados. `node_modules/.bin/playwright` existe, pero `docker compose ps` no pudo leer `C:\Users\User\.docker\config.json` por acceso denegado y el intento de `HEAD http://localhost:8080/` no obtuvo una respuesta HTTP utilizable.
- No se alteró base de datos, contenido, uploads ni dependencias; no aplica sincronización `content-sync`.

Para revertir únicamente esta limpieza, restaurar la declaración duplicada del equipo en `style.css` y retirar este informe mediante Git. No es necesario restaurar ni sincronizar estado persistido de WordPress.

## Análisis del manual vs. campos del panel administrativo

### Alcance y método

Se revisó `docs/manual-usuario/manual-usuario.md` de principio a fin y se extrajeron los metadatos y controles de edición que indica crear, llenar, cambiar o administrar, además de los nombres que aclara que no existen. Se contrastaron con el registro de tipos/meta del dominio, el administrador dedicado de Documentos, las pantallas de contenido de Inicio, Footer y SMTP, el renderizado del tema y las pruebas PHP/E2E administrativas. El 2026-10-15 se añadió una comprobación visual autenticada de las pantallas indicadas abajo; sus resultados actualizan las conclusiones previas basadas solo en código.

En la primera revisión no se pudo inspeccionar una sesión del panel y se dejó constancia de los errores de conexión de aquel momento. Esa limitación histórica queda superada para las pantallas visitadas el 2026-10-15. `EXISTE` significa que el control o selector fue visto en pantalla o que hay evidencia de implementación; `NO EXISTE` indica que la clave o control no apareció en el código ni en las pantallas observadas; `EXISTE CON OTRO NOMBRE/UBICACIÓN` indica que el dato se gestiona en otro control o pantalla. El selector genérico **Custom Fields** confirma que las claves enumeradas están disponibles en su selector `Name`; no demuestra que exista un metabox dedicado por clave ni que cada valor sea consumido en la vista pública.

### Evidencia visual autenticada

Fecha de inspección: 2026-10-15. Se revisaron pantallas del panel WordPress con sesión autenticada. La inspección fue de solo lectura: no se guardó ni publicó contenido, no se borró nada y no se pulsó **Enviar prueba** SMTP.

| Ruta/pantalla visitada | Resultado visual | Alcance de la evidencia |
|---|---|---|
| `/wp-admin/post-new.php?post_type=labm_actualidad` | En Gutenberg se vio el panel **Custom Fields**. El selector **Name** ofrecía `labm_cargo`, `labm_ciudad`, `labm_cta_texto`, `labm_demo_image`, `labm_destino_url`, `labm_documento_fecha`, `labm_documento_pdf_id`, `labm_document_source_key`, `labm_fecha_evento`, `labm_inicio`, `labm_modalidad_detalle` y `labm_test_fixture`. Evidencia histórica; `labm_inicio` fue retirado el 2026-10-25. | Confirma disponibilidad en el selector genérico de esta pantalla en esa fecha; no confirma controles dedicados ni consumo público de cada clave. `lamb_demo_fecha` no apareció. |
| Nuevas pantallas de Selecciones, Clubes, Integrantes, Horarios y Documentos | Se observó la misma lista en el selector **Name** de **Custom Fields**. Evidencia histórica; Horarios y `labm_inicio` fueron retirados el 2026-10-25. | Confirma disponibilidad del selector genérico en esos tipos en esa fecha; no prueba que cada clave fuera apropiada para todos esos tipos ni su comportamiento público. `lamb_demo_fecha` no apareció. |
| `/wp-admin/post-new.php?post_type=labm_documento` | Panel dedicado **Archivo y clasificación** con **Seleccionar o subir PDF**, **FECHA DEL DOCUMENTO** y **TIPO DE DOCUMENTO**. Opciones visibles: Acta, Actas publicas, Certificado, Circular, Circulares de prueba, Convocatoria, Documento general, Informe, Otro, Reglamento y Resolución. | Confirma visualmente los controles dedicados y las opciones de clasificación mostradas en esta sesión. |
| `/wp-admin/admin.php?page=labm-footer` | Pantalla **Footer LABM** con controles de identidad, descripción, encabezados, navegación 1–4, recursos 1–3, contacto, correo, Facebook, Instagram, copyright y política. | Confirma la disponibilidad visual de esas familias de ajustes en la pantalla Footer. |
| `/wp-admin/admin.php?page=labm-smtp` | Pantalla **SMTP LABM** con Servidor SMTP, Puerto, Cifrado TLS/SSL, Correo remitente, Nombre remitente, Destinatarios de Contacto, **Guardar cambios** y **Enviar prueba**. La pantalla indica que la contraseña se lee solo desde `LABM_SMTP_PASSWORD` en `wp-config.php` y no está disponible como campo. | Confirma los controles visibles; la contraseña no es editable desde el panel. No se envió la prueba SMTP ni se guardaron cambios. |

Límite de la inspección: se registró la disponibilidad y las etiquetas visibles de las rutas indicadas. No se probó guardar valores, publicar contenido, permisos con otros roles, ni la salida pública de cada metadato.

### Inventario contrastado

| Elemento que menciona el manual | Clave/meta/opción | Tipo/control descrito | Pantalla administrativa | Evidencia de implementación y pruebas | Estado |
|---|---|---|---|---|---|
| Fecha del evento de Actualidad | `labm_fecha_evento` | Metadato de fecha `AAAA-MM-DD` | Actualidad, panel genérico **Custom Fields**, selector **Name** | `class-labm-domain.php` registra la meta REST; `functions.php` la consulta para el evento de Inicio; `DomainModelTest.php` comprueba el registro. Se observó en el selector genérico. No hay control de fecha propio. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO CONFIRMADO |
| Imagen de respaldo técnica de noticias demo | `labm_demo_image` | Metadato de ruta de imagen, solo para fixtures/demos | Actualidad, panel genérico **Custom Fields**, selector **Name** | `class-labm-fixtures-command.php` escribe fixtures; `functions.php` valida/consume la ruta; pruebas de fixtures/presentación la cubren. Se observó en el selector genérico. El manual indica que no se use como campo editorial. | EXISTE EN SELECTOR GENÉRICO; META TÉCNICA, NO CAMPO EDITORIAL |
| Fecha mal escrita que el manual declara inexistente | `lamb_demo_fecha` | Supuesto campo de fecha | Actualidad, panel nativo | No aparece como clave de implementación en el código ni pruebas; la fecha implementada es `labm_fecha_evento`. | NO EXISTE |
| Texto de modalidad para tarjeta de Selecciones | `labm_modalidad_detalle` | Metadato de texto breve | Selecciones, panel genérico **Custom Fields**, selector **Name** | `class-labm-domain.php` registra meta REST; `functions.php` lo lee para la tarjeta; pruebas cubren registro/uso. Se observó en el selector genérico. No hay control dedicado. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO CONFIRMADO |
| Destino del botón de una diapositiva | `labm_destino_url` | Metadato URL | Slides de Inicio, panel genérico **Custom Fields**, selector **Name** | `class-labm-home-content.php` registra meta REST; `functions.php` la renderiza; pruebas cubren el contrato. Se observó en el selector genérico. No hay control URL propio. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO CONFIRMADO |
| Etiqueta del botón de una diapositiva | `labm_cta_texto` | Metadato de texto | Slides de Inicio, panel genérico **Custom Fields**, selector **Name** | `class-labm-home-content.php` registra meta REST; `functions.php` la renderiza; pruebas cubren la clave. Se observó en el selector genérico. No hay control de texto propio. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO CONFIRMADO |
| Ciudad del club | `labm_ciudad` | Metadato de texto | Clubes, panel genérico **Custom Fields**, selector **Name** | `class-labm-domain.php` registra meta REST; prueba el registro y sanitización. Se observó en el selector genérico. No se encontró un control dedicado ni un consumidor visible en el tema revisado. | EXISTE EN SELECTOR GENÉRICO; EFECTO PÚBLICO NO EVIDENCIADO |
| Cargo del integrante | `labm_cargo` | Metadato de texto | Integrantes, panel genérico **Custom Fields**, selector **Name** | `class-labm-domain.php` registra meta REST; `functions.php` lo muestra junto al nombre; pruebas y fixtures cubren el dato. Se observó en el selector genérico. No hay control dedicado. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO CONFIRMADO |
| PDF asociado al Documento | `labm_documento_pdf_id` (meta interna) | Selector de Medios; botones **Seleccionar o subir PDF**, **Reemplazar PDF** y **Quitar PDF** | Documentos, panel **Archivo y clasificación** | `class-labm-document-admin.php` renderiza el control y persiste la meta; `class-labm-documents-contact.php` la consume; `DocumentContactTest.php` y `tests/e2e/document-admin.spec.ts` cubren el panel. | EXISTE |
| Fecha del documento | `labm_documento_fecha` | Campo de fecha opcional | Documentos, panel **Archivo y clasificación** | El administrador renderiza `input type="date"`; se guarda/valida en `class-labm-document-admin.php`; las pruebas PHP y E2E cubren su presencia y formato. | EXISTE |
| Tipo de documento | Taxonomía `labm_documento_categoria` (no es meta) | Selector de un término | Documentos, panel **Archivo y clasificación** | `class-labm-document-admin.php` renderiza el selector; `class-labm-domain.php` registra la taxonomía; pruebas PHP/E2E lo verifican. El vocabulario es administrable por permisos. | EXISTE |
| Categorías, modalidades y grupos que el manual indica asignar | Taxonomías `labm_categoria`, `labm_modalidad`, `labm_grupo_integrante` (no son metas) | Selectores/controles de clasificación estándar de WordPress | Actualidad, Selecciones, Clubes e Integrantes, en el panel de taxonomías del editor | `class-labm-domain.php` registra las tres taxonomías con soporte de administración/REST; `DomainModelTest.php`, fixtures y `PublicExperienceTest.php` cubren los contratos y el uso de modalidad/grupos. | EXISTE |
| Identidad, descripción, encabezados, etiquetas y URLs de navegación/recursos, correo, redes, copyright y política de datos | Opción `labm_footer_settings`, subclaves: `brand_name`, `description`, `navigation_heading`, `navigation_1_label`–`navigation_4_label`, `navigation_1_url`–`navigation_4_url`, `resources_heading`, `resources_1_label`–`resources_3_label`, `resources_1_url`–`resources_3_url`, `contact_heading`, `contact_email`, `facebook_label`, `facebook_url`, `instagram_label`, `instagram_url`, `copyright`, `policy_label`, `policy_url` | Campos de texto, área de texto, email y URL | **LABM > Footer LABM** | `class-labm-footer-settings.php` define el esquema, registra la opción y genera un control por subclave; `FooterSettingsTest.php` comprueba el esquema y sanitización. | EXISTE |
| Teléfono, dirección, mapa y textos estáticos de Contacto | No hay clave/opción editable en estas pantallas | Datos/textos que el manual identifica como no editables | No disponible en **LABM > Footer LABM** | El manual indica que están definidos por el tema/plugin; `class-labm-documents-contact.php` obtiene teléfono, dirección y mapa desde la configuración implementada, pero el esquema visible del footer solo declara los campos enumerados arriba. | NO EXISTE |
| Servidor, puerto, cifrado, correo remitente, nombre remitente y destinatarios de Contacto | Opción `labm_smtp_settings`, subclaves `host`, `port`, `encryption`, `from_email`, `from_name`, `recipients` | Texto, número, selector TLS/SSL, email y área de texto | **LABM > SMTP** | `class-labm-smtp-settings.php` define los seis controles y guarda la opción; `SmtpSettingsTest.php` comprueba el esquema. El manual no detalla las etiquetas de cada campo, pero instruye administrar los datos de envío y destinatarios. | EXISTE |
| Contraseña SMTP | Constante `LABM_SMTP_PASSWORD` en configuración técnica (`wp-config.php`), no subclave de opción | Secreto sin campo de formulario | No hay campo editable en **LABM > SMTP** | `class-labm-smtp-settings.php` lee la constante y muestra solo si está disponible; el render no crea input de contraseña; `SmtpSettingsTest.php` confirma que no se almacena en la opción. El manual advierte que no se gestiona como campo editorial. | EXISTE CON OTRO NOMBRE/UBICACIÓN |
| Enviar prueba de correo | No es una clave persistente | Botón/acción de prueba | **LABM > SMTP** | `class-labm-smtp-settings.php` implementa el envío de prueba; el manual correctamente advierte que envía correo. | EXISTE |
| Orden de slides y aliados | Atributo nativo `menu_order`, no meta editorial de LABM | Control **Atributos > Orden** o edición rápida | Slides de Inicio / Aliados Oficiales | `class-labm-home-content.php` habilita `page-attributes`; el manual describe esta variación del control por editor/rol. | EXISTE CON OTRO NOMBRE/UBICACIÓN |
| Enlace externo propio de un aliado | No hay meta/opción | Campo que el manual dice que no está implementado | Aliados Oficiales | `class-labm-home-content.php` excluye `custom-fields` del tipo `labm_aliado`; el manual declara que no hay campo de enlace externo. | NO EXISTE |

### Desglose control por control

La tabla anterior resume familias de controles. Las siguientes tablas desglosan cada control editable concreto mencionado por el manual. Los controles estándar se clasifican expresamente como tales; la etiqueta no implica una inspección visual de la instalación local. Las claves y nombres se citan solo cuando aparecen en el manual o en las fuentes de implementación.

#### Acceso, editor y publicación

| Control mencionado | Tipo | Clave/nombre | Ubicación indicada | Evidencia | Estado |
|---|---|---|---|---|---|
| Usuario o correo | Autenticación estándar de WordPress | `user_login` (nombre de control observado en prueba E2E; etiqueta del manual: Username or Email Address) | Inicio de sesión `/wp-admin/` | Manual, sección 1; el login forma parte del flujo estándar de WordPress. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Contraseña de acceso | Autenticación estándar de WordPress | `user_pass` (nombre de control observado en prueba E2E; etiqueta del manual: Password) | Inicio de sesión `/wp-admin/` | Manual, sección 1; `tests/e2e/document-admin.spec.ts` utiliza el formulario estándar de login. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Nombre de la publicación | Campo estándar de título | `post_title` | Editor de la sección correspondiente | `class-labm-domain.php` registra tipos con soporte `title`; `DomainModelTest.php` comprueba ese soporte; manual, secciones 3–9. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Cuerpo/contenido | Editor estándar de contenido | `post_content` | Editor de bloques; para aliados, título e imagen en el editor clásico sin cuerpo requerido | `class-labm-domain.php` registra soporte `editor` para tipos de dominio; el manual instruye escribir y actualizar el cuerpo. El tipo aliado se define separadamente en `class-labm-home-content.php`. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Insertar párrafo, encabezado, lista, imagen u otro bloque | Inserción de bloques estándar | Bloque elegido desde `+` (sin meta LABM) | Editor de bloques | Manual, sección 3, describe el insertador `+` y tipos de bloque. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Extracto | Campo estándar de extracto | `post_excerpt` | Panel de publicación/entrada o área de extracto del editor | `class-labm-domain.php` y `class-labm-home-content.php` incluyen soporte `excerpt` en los tipos pertinentes; manual, secciones 3–9. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Imagen destacada/fotografía/logo | Campo estándar de miniatura destacada y selector de Medios | `_thumbnail_id` (dato interno WordPress; no es una clave LABM indicada al editor) | **Set featured image / Imagen destacada** en el editor | Soporte `thumbnail` registrado en `class-labm-domain.php` y `class-labm-home-content.php`; el manual lo indica para Actualidad, Slides, Aliados, Clubes e Integrantes. En Documentos explícitamente no es necesario. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Texto alternativo de una imagen | Metadato estándar del adjunto de WordPress | `alt_text` (clave estándar; no citada en el manual) | Medios, detalle del adjunto al subir o seleccionar la imagen | Manual, secciones 8 y 11, indica completar el texto alternativo; no se halló campo LABM propio para ello. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Subir o escoger un archivo de imagen/PDF | Selector/carga estándar de Medios | Adjunto de WordPress; la clave interna del PDF se especifica en la fila de Documentos | Biblioteca de Medios o selector del editor/panel Documentos | Manual, secciones 6 y 11; `class-labm-document-admin.php` integra el selector de Medios para PDF. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Galería de fotografías | Bloque estándar de galería | Bloque `core/gallery` (sin meta LABM) | Cuerpo de Actualidad o Selecciones, botón `+` > Gallery / Galería | Manual, secciones 4, 5 y 11; se almacena en el contenido de bloques, no como campo LABM independiente. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Pies de foto y orden de imágenes de la galería | Controles editoriales del bloque de galería | Atributos del bloque/adjuntos; sin clave personalizada indicada | Bloque Gallery / Galería dentro del editor | Manual, sección 11, instruye ordenar la galería y revisar pies de foto. No hay controles LABM propios. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Categorías de Actualidad | Clasificación mediante taxonomía personalizada de LABM | Taxonomía `labm_categoria` | Actualidad, panel **Categorías** | `class-labm-domain.php` registra `labm_categoria` para Actualidad y Selecciones; manual, sección 4. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Estado borrador, revisión/privado o publicado | Control estándar de estado editorial | `post_status` | Panel de publicación del editor; acciones Draft, Publish, Update | Manual, secciones 3, 4, 6–10 y 12; WordPress administra estado; la lógica de publicación de Documento se valida en `class-labm-document-admin.php`. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Fecha de publicación | Campo estándar de fecha/hora de publicación | `post_date` (clave estándar) | Panel de publicación del editor | Manual, secciones 3 y 4, diferencia la fecha de publicación de `labm_fecha_evento`; el orden de Actualidad/Selecciones considera fecha. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Vista previa | Acción estándar, no campo de datos | Botón **Preview / Vista previa** | Barra/panel de publicación | Manual, sección 3. Se registra como acción mencionada, no como campo editable ni metadato. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Título usado para buscar y abrir una publicación | Campo estándar `post_title` en listado/editor | `post_title` | Listado y editor de cada sección | Manual, secciones 3 y 12; mismo control de título descrito arriba, aquí se documenta el uso de búsqueda. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Slug/identificador del banner de Nosotros | Campo estándar de slug/permalink | `banner-nosotros` | **Posts / Entradas** > editor > Slug / Identificador | Manual, sección 9; `functions.php` resuelve la entrada institucional por ese identificador. Es slug de WordPress, no meta LABM. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Slug/identificador de Misión | Campo estándar de slug/permalink | `mision-nosotros` | **Posts / Entradas** > editor > Slug / Identificador | Manual, sección 9; `functions.php` resuelve la entrada institucional por ese identificador. Es slug de WordPress, no meta LABM. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Slug/identificador de Visión | Campo estándar de slug/permalink | `vision-nosotros` | **Posts / Entradas** > editor > Slug / Identificador | Manual, sección 9; `functions.php` resuelve la entrada institucional por ese identificador. Es slug de WordPress, no meta LABM. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Slug/identificador del encabezado de Documentos | Campo estándar de slug/permalink | `banner-documentos` | **Posts / Entradas** > editor > Slug / Identificador | Manual, sección 9; `functions.php` resuelve la entrada institucional por ese identificador. Es slug de WordPress, no meta LABM. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Título de entrada institucional | Campo estándar `post_title` | `post_title` | **Posts / Entradas**, entrada identificada por slug | Manual, sección 9, indica cambiar el título; contenido recuperado por `functions.php`. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Texto/extracto de entrada institucional | Campos estándar `post_content` y `post_excerpt` | `post_content`, `post_excerpt` | **Posts / Entradas**; extracto tiene prioridad sobre cuerpo para el bloque | Manual, sección 9; los renderizadores de `functions.php` usan el extracto y recurren al contenido cuando está vacío. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Imagen destacada del banner de Nosotros | Campo estándar de miniatura | `_thumbnail_id` | Entrada `banner-nosotros` > Imagen destacada | Manual, sección 9; `functions.php` obtiene la destacada para el bloque. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Orden de Slides de Inicio | Atributo estándar de página | `menu_order` | Slides de Inicio > **Post Attributes / Atributos** o edición rápida | Manual, sección 7; `class-labm-home-content.php` habilita `page-attributes` para slides. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Orden de Aliados Oficiales | Atributo estándar de página | `menu_order` | Aliados Oficiales > **Post Attributes > Order / Atributos > Orden** | Manual, sección 8; `class-labm-home-content.php` habilita `page-attributes` para aliados. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Preferencia para mostrar el panel Campos personalizados | Preferencia del editor por usuario, no dato editorial | Preferencia de interfaz WordPress; sin meta/opción de LABM | Editor > ⋮ > Preferences > Panels | Manual, sección 3, describe activarla una vez por usuario; no es una clave de contenido. | NO ES CAMPO PERSONALIZADO / PREFERENCIA ESTÁNDAR |
| Clave de campo personalizado genérico | Control de texto `Name` | Clave seleccionada o introducida por el editor | Panel nativo **Custom Fields / Campos personalizados** | Observado en las pantallas nuevas enumeradas en **Evidencia visual autenticada**; las claves disponibles se listan allí. | CONTROL GENÉRICO Y SELECTOR VISUALMENTE CONFIRMADOS; NO IMPLICA METABOX DEDICADO |
| Valor de campo personalizado genérico | Control de texto `Value` | Valor de la meta nombrada en `Name` | Panel nativo **Custom Fields / Campos personalizados** | El panel se observó visualmente. No se editó ni guardó ningún valor durante esta revisión. | CONTROL GENÉRICO DISPONIBLE; GUARDADO NO PROBADO |

#### Acciones editoriales y controles de Medios desglosados

| Control/acción que menciona el manual | Tipo | Clave | Ubicación indicada | Evidencia disponible | Estado |
|---|---|---|---|---|---|
| Mover publicación o Documento a la papelera | Acción estándar de WordPress, reversible | Acción de fila; estado `trash` | Listado de la sección > **Move to trash / Mover a la papelera**; también sección 12 | Manual, secciones 6 y 12; `class-labm-domain.php` registra los tipos de contenido LABM y WordPress proporciona la papelera. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Borrar permanentemente una publicación o Documento | Acción estándar de WordPress | Acción **Delete permanently / Borrar permanentemente** | Listado > Papelera > acciones del elemento | Manual, sección 12, indica expresamente esta acción y recomienda no usarla rutinariamente; la acción corresponde a WordPress. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Restaurar una publicación o Documento | Acción estándar de WordPress | Acción **Restore / Restaurar** | Listado de sección > Papelera > acción del elemento | Manual, sección 12; WordPress proporciona la restauración. No se encontró una prueba E2E específica del recorrido de restaurar desde la papelera. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Buscar archivos ya existentes para evitar duplicados | Búsqueda estándar de Medios | Consulta de búsqueda; sin meta LABM | **Media / Medios** > Biblioteca | Manual, sección 11; función de la Biblioteca de Medios de WordPress, sin implementación LABM propia. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| Añadir archivo a Medios | Acción/control estándar de carga | **Add Media File / Añadir archivo** | **Media / Medios** > Añadir archivo; también selector de medios de la publicación | Manual, sección 11; `class-labm-document-admin.php` usa `wp_enqueue_media()` para el selector estándar de Documentos. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Revisar nombre del archivo | Información del adjunto, no campo LABM | Nombre del archivo | Biblioteca de Medios o selector de medios | Manual, sección 11, pide revisar nombre y formato; `class-labm-document-admin.php` muestra el nombre del PDF asociado. | NO ES CAMPO PERSONALIZADO / REVISIÓN DE DATOS DEL MEDIO |
| Revisar formato del archivo | Información/tipo del adjunto, no campo LABM | Tipo/MIME del archivo | Biblioteca de Medios o selector de medios | Manual, sección 11; para documentos, `class-labm-document-admin.php` valida tipo y formato PDF. | NO ES CAMPO PERSONALIZADO / REVISIÓN DE DATOS DEL MEDIO |
| Abrir el insertador para añadir Galería | Acción estándar del editor de bloques | Botón `+`, sin clave/meta | Cuerpo de la publicación > insertador de bloques | Manual, secciones 3, 4, 5 y 11; el botón inserta bloques estándar. | NO ES CAMPO PERSONALIZADO / ACCIÓN ESTÁNDAR |
| Seleccionar imágenes para una Galería | Selector estándar de adjuntos múltiples | Adjuntos del bloque `core/gallery`; sin meta LABM independiente | Editor de bloques > Galería > selector de medios | Manual, secciones 4, 5 y 11; `functions.php` reconoce bloques `core/gallery`; no se encontró un selector de galería propio de LABM. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |

#### Metadatos LABM citados en el manual

| Elemento mencionado | Tipo | Clave | Ubicación indicada | Evidencia | Estado |
|---|---|---|---|---|---|
| Fecha de evento de Actualidad | Meta de fecha | `labm_fecha_evento` | Actualidad > Custom Fields > Name | Registrada para REST y consultada para Inicio; se observó en el selector genérico de Actualidad. No hay control de fecha dedicado. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO |
| Fecha errónea que el manual dice que no existe | Meta pretendida | `lamb_demo_fecha` | Actualidad > Campos personalizados (según el texto que la declara inexistente) | No aparece como clave de implementación en las fuentes buscadas. | NO EXISTE |
| Imagen de respaldo de contenido demo | Meta técnica de ruta | `labm_demo_image` | Actualidad > Custom Fields > Name | Fixture y tema la escriben/consumen; se observó en el selector genérico. No tiene input dedicado. | META TÉCNICA EN SELECTOR GENÉRICO; NO EDITAR COMO CAMPO EDITORIAL |
| Detalle breve de modalidad | Meta de texto | `labm_modalidad_detalle` | Selecciones > Custom Fields > Name | Meta REST y consumo en tarjeta; se observó en el selector genérico. Sin control dedicado. | META EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO |
| Destino de CTA de slide | Meta URL | `labm_destino_url` | Slides de Inicio > Custom Fields > Name | Meta REST y renderizado; se observó en el selector genérico de Actualidad. La pantalla específica de Slides de Inicio no se visitó. | DISPONIBLE EN SELECTOR OBSERVADO; PANTALLA ESPECÍFICA DE SLIDE NO INSPECCIONADA |
| Texto de CTA de slide | Meta de texto | `labm_cta_texto` | Slides de Inicio > Custom Fields > Name | Meta REST; se observó en el selector genérico de Actualidad. La pantalla específica de Slides de Inicio no se visitó. | DISPONIBLE EN SELECTOR OBSERVADO; PANTALLA ESPECÍFICA DE SLIDE NO INSPECCIONADA |
| Ciudad de club | Meta de texto | `labm_ciudad` | Clubes > Custom Fields > Name | Meta REST; se observó en el selector genérico de Clubes. Sin control dedicado ni consumidor público hallado. | EXISTE EN SELECTOR GENÉRICO; EFECTO PÚBLICO NO EVIDENCIADO |
| Cargo de integrante | Meta de texto | `labm_cargo` | Integrantes > Custom Fields > Name | Meta REST y renderizado en tarjeta; se observó en el selector genérico de Integrantes. Sin input dedicado. | EXISTE EN SELECTOR GENÉRICO; SIN METABOX DEDICADO |

#### Taxonomías y clasificaciones

| Clasificación que el manual pide asignar | Tipo | Clave/taxonomía | Ubicación | Evidencia | Estado |
|---|---|---|---|---|---|
| Categorías de noticias | Taxonomía personalizada | `labm_categoria` | Actualidad > **Categorías** | `class-labm-domain.php` la registra asociada a `labm_actualidad`; manual, sección 4. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Modalidad Piso o Playa de una selección | Taxonomía personalizada | `labm_modalidad` | Selecciones > **Modalidades** | `class-labm-domain.php` la registra para selecciones y clubes; manual, sección 5; pruebas de dominio/experiencia cubren su uso. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Categorías de Selecciones | Taxonomía personalizada | `labm_categoria` | Selecciones > **Categorías** | `class-labm-domain.php` la registra asociada a `labm_seleccion`; manual, sección 5. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Modalidad de Club | Taxonomía personalizada | `labm_modalidad` | Clubes > modalidad | `class-labm-domain.php` registra la taxonomía para `labm_club`; manual, sección 8; fixtures y tests la utilizan. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Grupo de integrante (Comité) | Taxonomía personalizada | `labm_grupo_integrante`, término/slug `comite` | Integrantes > **Grupos de integrantes** | `class-labm-domain.php` registra la taxonomía; manual, sección 9, cita el término y slug; `functions.php` consume esos grupos. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Grupo de integrante (Entrenadores) | Taxonomía personalizada | `labm_grupo_integrante`, término/slug `entrenadores` | Integrantes > **Grupos de integrantes** | Mismas fuentes; el manual cita el grupo y su slug. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Grupo de integrante (Representantes) | Taxonomía personalizada | `labm_grupo_integrante`, término/slug `representantes` | Integrantes > **Grupos de integrantes** | Mismas fuentes; el manual cita el grupo y su slug. | TAXONOMÍA / EXISTE EN CÓDIGO; CONTROL VISUAL NO VERIFICADO |
| Tipo de documento | Taxonomía personalizada con selector específico | `labm_documento_categoria` | Documentos > **Archivo y clasificación** > Tipo de documento | `class-labm-domain.php` registra taxonomía; `class-labm-document-admin.php` renderiza selector; pruebas PHP y E2E existentes la cubren. | TAXONOMÍA/CONTROL DEDICADO / EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |

#### Controles dedicados de Documentos

| Control mencionado | Tipo | Clave | Ubicación indicada | Evidencia | Estado |
|---|---|---|---|---|---|
| Título que identifica el PDF | Campo estándar de título | `post_title` | Documentos > editor del Documento | `class-labm-domain.php` registra soporte de título; `class-labm-document-admin.php` lo valida como obligatorio; `DocumentContactTest.php` y E2E lo ejercitan. | NO ES CAMPO PERSONALIZADO / CONTROL ESTÁNDAR |
| PDF asociado | Control dedicado de selección/carga de medio | `labm_documento_pdf_id` (meta interna; el editor no introduce el ID) | Documentos > **Archivo y clasificación** | Renderizado y guardado en `class-labm-document-admin.php`; uso público en `class-labm-documents-contact.php`; `DocumentContactTest.php` y `tests/e2e/document-admin.spec.ts`. | CONTROL DEDICADO / EXISTE EN CÓDIGO Y PRUEBAS; VISUAL LOCAL NO VERIFICADO |
| Fecha oficial del documento | Campo de fecha | `labm_documento_fecha` | Documentos > **Archivo y clasificación** > **Fecha del documento** | `class-labm-document-admin.php` renderiza y valida; tests PHP y E2E verifican. Es opcional. | CONTROL DEDICADO / EXISTE EN CÓDIGO Y PRUEBAS; VISUAL LOCAL NO VERIFICADO |
| Tipo de documento seleccionado | Selector de taxonomía, no meta | `labm_documento_categoria` | Documentos > **Archivo y clasificación** > **Tipo de documento** | Renderizado y validación en `class-labm-document-admin.php`; registro en `class-labm-domain.php`; tests PHP/E2E. | TAXONOMÍA/CONTROL DEDICADO / EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Reemplazar PDF | Acción, no campo de datos | Control **Reemplazar PDF** | Documento existente > **Archivo y clasificación** | `class-labm-document-admin.php` y JavaScript administrativo; E2E cubre reemplazo. | ACCIÓN DE PANEL / EXISTE EN CÓDIGO Y PRUEBAS; VISUAL LOCAL NO VERIFICADO |
| Quitar PDF de la publicación | Acción, no campo de datos | Control **Quitar PDF** | Documento > **Archivo y clasificación** | `class-labm-document-admin.php` implementa desvinculación; E2E comprueba que el adjunto permanezca en Medios. | ACCIÓN DE PANEL / EXISTE EN CÓDIGO Y PRUEBAS; VISUAL LOCAL NO VERIFICADO |
| Nombre, tamaño y estado del PDF | Información de solo lectura | Sin clave introducida por editor (el archivo asociado se resume en el panel) | Documento > **Archivo y clasificación** | `class-labm-document-admin.php` renderiza resumen; `tests/e2e/document-admin.spec.ts` contiene comprobaciones. | NO ES CAMPO EDITABLE / RESUMEN DE ESTADO |

#### Ajustes de Footer: una fila por opción

Todos los siguientes controles son ajustes agrupados en `labm_footer_settings`, no campos personalizados de una publicación. El manual, sección 10, instruye revisar/modificar la identidad, descripción, encabezados, etiquetas y URLs de navegación y recursos, Correo, redes y política; la evidencia exacta de control se encuentra en `class-labm-footer-settings.php` y el contrato de esquema en `FooterSettingsTest.php`.

| Control que el manual indica editar | Tipo | Clave de opción | Ubicación | Estado |
|---|---|---|---|---|
| Identidad mostrada | Texto | `labm_footer_settings[brand_name]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Descripción | Área de texto | `labm_footer_settings[description]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Encabezado de navegación | Texto | `labm_footer_settings[navigation_heading]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de navegación 1 | Texto | `labm_footer_settings[navigation_1_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de navegación 1 | URL interna | `labm_footer_settings[navigation_1_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de navegación 2 | Texto | `labm_footer_settings[navigation_2_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de navegación 2 | URL interna | `labm_footer_settings[navigation_2_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de navegación 3 | Texto | `labm_footer_settings[navigation_3_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de navegación 3 | URL interna | `labm_footer_settings[navigation_3_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de navegación 4 | Texto | `labm_footer_settings[navigation_4_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de navegación 4 | URL interna | `labm_footer_settings[navigation_4_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Encabezado de recursos | Texto | `labm_footer_settings[resources_heading]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de recurso 1 | Texto | `labm_footer_settings[resources_1_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de recurso 1 | URL interna | `labm_footer_settings[resources_1_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de recurso 2 | Texto | `labm_footer_settings[resources_2_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de recurso 2 | URL interna | `labm_footer_settings[resources_2_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de recurso 3 | Texto | `labm_footer_settings[resources_3_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de recurso 3 | URL interna | `labm_footer_settings[resources_3_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Encabezado de contacto | Texto | `labm_footer_settings[contact_heading]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Correo del Footer/Contacto | Correo electrónico | `labm_footer_settings[contact_email]` | LABM > Footer LABM > **Correo** | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de Facebook | Texto | `labm_footer_settings[facebook_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de Facebook | URL externa | `labm_footer_settings[facebook_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de Instagram | Texto | `labm_footer_settings[instagram_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de Instagram | URL externa | `labm_footer_settings[instagram_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Copyright | Texto | `labm_footer_settings[copyright]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Etiqueta de política de datos | Texto | `labm_footer_settings[policy_label]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| URL de política de datos | URL interna | `labm_footer_settings[policy_url]` | LABM > Footer LABM | AJUSTE / CONTROL DEDICADO EXISTE EN CÓDIGO; VISUAL LOCAL NO VERIFICADO |
| Teléfono de Contacto | El manual dice que no tiene campo editable en esta pantalla | Sin control/opción en el esquema Footer | No disponible en LABM > Footer LABM | `class-labm-footer-settings.php` enumera el esquema cerrado; el manual remite el cambio al responsable técnico. | NO EXISTE EN ESTA PANTALLA |
| Dirección de Contacto | El manual dice que no tiene campo editable en esta pantalla | Sin control/opción en el esquema Footer | No disponible en LABM > Footer LABM | Esquema cerrado en `class-labm-footer-settings.php`; el manual remite el cambio al responsable técnico. | NO EXISTE EN ESTA PANTALLA |
| URL del mapa | El manual dice que no tiene campo editable en esta pantalla | Sin control/opción en el esquema Footer | No disponible en LABM > Footer LABM | Esquema cerrado en `class-labm-footer-settings.php`; el manual remite el cambio al responsable técnico. | NO EXISTE EN ESTA PANTALLA |
| Textos estáticos de Contacto | El manual dice que algunos no tienen campo editable; no identifica sus etiquetas individuales | Sin clave de opción editorial confirmada | No disponible en LABM > Footer LABM | El manual, sección 10, los atribuye a tema/plugin; no se inventan nombres de campos. | NO EXISTE COMO CONTROL EDITORIAL EN ESTA PANTALLA |

#### Ajustes y acciones SMTP: una fila por control

El manual menciona **LABM > SMTP**, administrar los datos de envío y destinatarios, no mostrar/editar la contraseña y evitar **Enviar prueba** durante una revisión. Las etiquetas, tipos y subclaves siguientes proceden del esquema y render de `class-labm-smtp-settings.php`; `SmtpSettingsTest.php` cubre el esquema y la ausencia de contraseña en la opción.

| Control mencionado o comprendido en los datos de envío | Tipo | Clave | Ubicación | Estado |
|---|---|---|---|---|
| Servidor SMTP | Texto | `labm_smtp_settings[host]` | LABM > SMTP > **Servidor SMTP** | Campo observado visualmente el 2026-10-15; la clase y prueba citadas respaldan su configuración. | EXISTE; CONFIRMADO VISUALMENTE |
| Puerto SMTP | Número | `labm_smtp_settings[port]` | LABM > SMTP > **Puerto** | Campo observado visualmente el 2026-10-15; la clase y prueba citadas respaldan su configuración. | EXISTE; CONFIRMADO VISUALMENTE |
| Cifrado de conexión | Selector TLS/SSL | `labm_smtp_settings[encryption]` | LABM > SMTP > **Cifrado TLS/SSL** | Selector observado visualmente el 2026-10-15; la clase y prueba citadas respaldan su configuración. | EXISTE; CONFIRMADO VISUALMENTE |
| Correo remitente | Email | `labm_smtp_settings[from_email]` | LABM > SMTP > **Correo remitente** | Campo observado visualmente el 2026-10-15. | EXISTE; CONFIRMADO VISUALMENTE |
| Nombre remitente | Texto | `labm_smtp_settings[from_name]` | LABM > SMTP > **Nombre remitente** | Campo observado visualmente el 2026-10-15. | EXISTE; CONFIRMADO VISUALMENTE |
| Destinatarios de Contacto | Área de texto; admite correos separados por coma o salto de línea | `labm_smtp_settings[recipients]` | LABM > SMTP > **Destinatarios de Contacto** | Campo observado visualmente el 2026-10-15; `SmtpSettingsTest.php` valida el modelo de destinatarios. | EXISTE; CONFIRMADO VISUALMENTE |
| Contraseña de aplicación SMTP | No hay campo editable; secreto técnico | Constante `LABM_SMTP_PASSWORD` | Configuración técnica en `wp-config.php`, no en formulario SMTP | El código la lee y el manual indica que no se muestra como campo editorial. | EXISTE EN OTRA UBICACIÓN; NO ES CAMPO DEL PANEL |
| Enviar prueba | Botón/acción con efecto de envío, no campo de configuración | Acción `labm_smtp_test` | LABM > SMTP > **Enviar prueba** | `class-labm-smtp-settings.php` implementa el envío y el manual advierte que la acción sí envía un correo. No se pulsó. | ACCIÓN DE PANEL IMPLEMENTADA; NO EJECUTADA |

#### Otros controles que el manual declara ausentes

| Elemento | Tipo | Clave | Ubicación referida | Evidencia | Estado |
|---|---|---|---|---|---|
| Interruptor general para destacar una noticia | El manual dice explícitamente que no existe | No hay clave de meta/opción | Actualidad | El manual, sección 4, dice que el destacado depende del orden editorial y que no hay interruptor general. | NO EXISTE |
| Enlace externo de un Aliado Oficial | Campo editorial que el manual dice que no está implementado | No hay clave de meta/opción | Aliados Oficiales | `class-labm-home-content.php` configura el tipo aliado sin `custom-fields`; el manual, sección 8, declara que no hay campo de enlace externo. | NO EXISTE |
| Bandeja editorial para mensajes del formulario Contacto | Función/pantalla que el manual dice que no existe | Sin CPT/opción de bandeja descrito | Administrador de WordPress | El manual, sección 10, indica que Contacto envía correo y no guarda una bandeja editorial. | NO EXISTE |

### Exclusiones para no confundir controles

El título, cuerpo, extracto, imagen destacada, texto alternativo de Medios, estado de publicación, slug/identificador de Entradas y atributos de orden son controles estándar/editoriales de WordPress, no campos personalizados de LABM. **Modalidades**, **Categorías**, **Grupos de integrantes** y **Tipo de documento** son taxonomías/clasificaciones; el último se muestra dentro del panel dedicado de Documentos, pero no se guarda como meta. La preferencia **Campos personalizados** activa o desactiva un panel del editor, no es un dato del sitio. La contraseña SMTP vive en configuración técnica, no como control editorial.

### Discrepancias, impacto y recomendaciones

- El manual afirma que `labm_fecha_evento`, `labm_modalidad_detalle`, `labm_destino_url`, `labm_cta_texto`, `labm_ciudad` y `labm_cargo` se editan en **Campos personalizados**. La inspección autenticada confirma que esas claves aparecen en el selector `Name` del panel genérico. No hay inputs/metaboxes dedicados para cada una; tampoco se probó guardar valores ni se confirmó la salida pública de todas ellas. En particular, `labm_ciudad` no tiene consumidor público hallado. Slides de Inicio no se visitó como tipo específico: `labm_destino_url` y `labm_cta_texto` se observaron en la lista de Actualidad y la lista común de otras pantallas, no en una pantalla de slide.
- `labm_demo_image` existe como metadato técnico consumido por el tema y fixtures, pero el código no lo registra como meta visible y el manual desaconseja editarlo. No debe presentarse como campo editorial ordinario.
- `labm_ciudad` está registrado como metadato, pero no encontramos un consumidor del valor en el tema ni un control dedicado. Aunque pudiera guardarse desde el panel genérico, no hay evidencia de que complete la ficha pública del club; la instrucción puede hacer que el editor llene un dato sin efecto observable.
- La fecha y el tipo de Documento sí existen, pero están en **Archivo y clasificación**, no en el panel nativo genérico. El PDF se elige mediante el selector de Medios; el ID interno no se debe pedir al editor.
- Los ajustes del Footer y SMTP sí tienen pantallas dedicadas; algunos nombres concretos de campos (servidor, cifrado, remitente, etc.) solo quedan implícitos en la frase general del manual sobre datos SMTP.
- El manual identifica correctamente como no editables desde Footer el teléfono, dirección, mapa y ciertos textos de Contacto; los trata como solicitudes al responsable técnico, no como campos que el usuario pueda completar.
- Recomendación para una futura corrección, fuera del alcance de esta auditoría: decidir si cada metadato editorial citado tendrá un control dedicado y documentarlo en esa ubicación, o validar con editores si el panel nativo realmente lo expone en la versión desplegada y corregir las instrucciones que no correspondan. La cartilla no se modificó; las correcciones de interfaz aplicadas quedan documentadas en “Implementación de mejoras UX”.

## Evidencia y límites de esta ampliación

- Fuentes contrastadas: `docs/manual-usuario/manual-usuario.md`; `class-labm-domain.php`, `class-labm-document-admin.php`, `class-labm-home-content.php`, `class-labm-documents-contact.php`, `class-labm-footer-settings.php`, `class-labm-smtp-settings.php`; `wp-content/themes/labm/functions.php`; pruebas `DomainModelTest.php`, `HomeContentTest.php`, `DocumentContactTest.php`, `FooterSettingsTest.php`, `SmtpSettingsTest.php`, `FixturesDomainTest.php`, `HomePresentationTest.php` y `tests/e2e/document-admin.spec.ts`.
- Runtime: inspección visual autenticada completada el 2026-10-15 en las rutas listadas en **Evidencia visual autenticada**. La nota de fallo del navegador corresponde a la revisión anterior y queda superada por esta inspección. Se observó el panel, sin guardar, publicar, borrar ni enviar la prueba SMTP.
- No se ejecutaron pruebas E2E: el flujo E2E de Documentos crea contenido y sube archivos, acciones expresamente fuera del alcance permitido. Se usaron pruebas existentes mediante inspección de sus fuentes, no como ejecuciones de esta ampliación.
- No se modificaron contenido, usuarios, opciones, uploads, manual, pruebas, configuración ni `content-sync`; sí se modificaron los archivos de código indicados en “Implementación de mejoras UX”.
- Esta ampliación del informe conserva las conclusiones de la auditoría de limpieza anterior y documenta las correcciones administrativas de bajo riesgo aplicadas.

### Cartilla manual de usuario — actualización 1.3 (2026-10-15)

- Se retiró de la página 04A el listado general que mezclaba claves de distintas colecciones. La página ahora explica cómo activar el panel y dirige a cada sección para consultar el dato que le corresponde.
- Se ajustaron las secciones de Actualidad (`labm_fecha_evento`: fecha del evento, formato `AAAA-MM-DD`; la nota de que `lamb_demo_fecha` no existe queda solo en Actualidad), Selecciones (`labm_modalidad_detalle`: explicación complementaria de la tarjeta), Slides (`labm_destino_url`: destino del botón; `labm_cta_texto`: etiqueta visible), Integrantes (`labm_cargo`: función mostrada junto al nombre) y Clubes (`labm_ciudad`: referencia editorial que actualmente no se muestra en la ficha pública).
- Se aclaró que Documentos usa el panel dedicado Archivo y clasificación; Modalidades, Categorías y Grupos de integrantes son clasificaciones; Aliados usa el control estándar de orden; y Footer LABM tiene su pantalla de ajustes. No se presentan como campos personalizados.
- Se actualizaron también los apartados equivalentes del bloque visual secundario de la fuente HTML para evitar descripciones divergentes si se reutiliza.
- PDF regenerado desde `docs/manual-usuario/cartilla-manual-usuario-labm.html`. Se exportaron a PNG y revisaron visualmente las páginas 04A, 05, 06, 09, 10, 11 y 15; sin desbordamientos ni superposiciones. No se modificó contenido ni estado persistido de WordPress.
### Cartilla manual de usuario — sección 04A

- Defecto: el bloque de nota sobre la captura real excedía el alto útil de la página fija y se superponía al pie `04A`.
- Corrección: se compactaron de forma localizada la imagen, los espacios y la lista de campos de la página 04A, y se reservó margen inferior para el pie; se conservó el texto y el estilo visual.
- Verificación: PDF regenerado y páginas 04A y adyacentes renderizadas a PNG e inspeccionadas visualmente; sin desbordamiento ni superposición. `git diff --check` limpio.

### Cartilla manual de usuario — Actualidad (2026-10-15)

- En la página 05 se reemplazó la indicación general de revisar listado y detalle por pasos concretos para abrir `/actualidad/` y la página individual `/actualidad/nombre-del-articulo/`, y comprobar los datos del artículo y sus enlaces para compartir.
- PDF regenerado desde el HTML. La página 05 se renderizó y revisó visualmente para comprobar legibilidad y ausencia de desbordamientos. No se modificó contenido ni estado persistido de WordPress.

### Fecha visible de Actualidad (2026-10-15)

- Causa: la metadata compartida por el listado y el detalle formateaba siempre `post_date`, aunque el tema ya almacenaba `labm_fecha_evento` para la fecha del evento destacado.
- Corrección: `labm_theme_home_news_date()` valida estrictamente `AAAA-MM-DD` y la fecha real del calendario. Una fecha válida tiene prioridad; si falta o es inválida, usa la fecha de publicación. El día se construye en UTC como referencia neutra de calendario y `datetime` se publica como `YYYY-MM-DD`, sin desplazamiento por zona horaria. Se conserva la presentación localizada `j M Y`.
- Alcance: la metadata compartida mantiene coherentes listado y detalle. No se modificó contenido ni datos persistidos de WordPress; la regla y el fallback se añadieron a la cartilla HTML/PDF.
- Verificación: `php -l wp-content/themes/labm/functions.php` sin errores; `git diff --check` limpio; archivos de texto modificados verificados con LF. PDF regenerado desde el HTML y página 05 revisada visualmente, sin desbordamientos ni superposiciones.

### Aclaración de fecha del evento en la cartilla (2026-10-15)

- Se precisó en el paso de Actualidad que una fecha válida `AAAA-MM-DD` en `labm_fecha_evento` prevalece y se muestra en el listado y el detalle del artículo; si falta o es inválida, el sitio muestra automáticamente la fecha de publicación.
- Se retiró la repetición de esa regla en el bloque visual secundario y se conservó allí solo la referencia al evento destacado de Inicio.
- PDF regenerado desde el HTML. Página de Actualidad revisada visualmente y sin desbordamientos; `git diff --check` y finales de línea LF verificados. No se modificó contenido ni datos persistidos de WordPress.

## Manual y cartilla — adaptación para personal no técnico, versión 1.5 (2026-10-19)

### Alcance y cambios realizados

Se compararon el historial del manual, la cartilla y las secciones asignadas a Daniel y Juan. Las mejoras se concentraron en Aliados Oficiales, Clubes, Integrantes y Nosotros, con correcciones de coherencia en las reglas compartidas de Actualidad, Selecciones y Slides. Se actualizaron conjuntamente `docs/manual-usuario/manual-usuario.md`, `docs/manual-usuario/cartilla-manual-usuario-labm.html` y `docs/manual-usuario/cartilla-manual-usuario-labm.pdf`, identificados como versión 1.5 del 19 de octubre de 2026.

| Apartado | Resultado documental | Referencia en la cartilla |
|---|---|---|
| Aliados Oficiales | Se separó de Clubes. Se explican preparación del logo, creación, orden con el ejemplo 10 antes de 20, reemplazo, retiro y comprobación en Inicio. Se aclaran el límite de 12 aliados completos y la ausencia de enlace externo editable. | 10 y 10A |
| Clubes | Se detallan descripción y extracto, logo, modalidad, modificación y comprobación pública. Se documentan hasta seis clubes en Inicio, por orden y nombre. Se corrigió el manual escrito: `labm_ciudad` es opcional y no se muestra en la ficha pública. | 15 y 15A |
| Integrantes | Se añadió el ejemplo ficticio Ana Pérez / Entrenadora / Entrenadores y el recorrido para crear o actualizar `labm_cargo`, confirmar el campo y guardar también la publicación. Se explican requisitos de la tarjeta, filtros, retiro y comprobaciones si no aparece. | 11 y 11A |
| Nosotros | Se explica cómo reconocer la entrada correcta por su identificador sin modificarlo. Presentación y Misión/Visión tienen instrucciones separadas. Se muestra por qué el extracto tiene prioridad sobre el contenido y cómo comprobar el bloque público. | 12, 12A y 12B |

Se incorporaron acercamientos mediante HTML/CSS de capturas reales ya existentes, con señales numeradas; no se simularon pantallas ni guardados. Los ejemplos editoriales están identificados como ficticios. Las recomendaciones de dimensiones y peso de imágenes se distinguen del límite efectivo de carga. Se sustituyeron términos técnicos por explicaciones de las acciones y sus resultados cuando no era necesario conservar una clave exacta.

Se agregaron cinco páginas de apoyo, conservando las referencias de las secciones revisadas mediante las etiquetas 10A, 11A, 12A, 12B y 15A. El índice enlaza a las secciones. Se reemplazó la regla que ocultaba páginas a partir de la posición 19 por una clase explícita para el bloque secundario histórico, evitando que se oculten las páginas nuevas. La revisión de maquetación detectó una nota de Documentos demasiado próxima al pie; se ajustó el espacio de su captura.

### Validación y evidencia

- Exportación con Chromium desde la fuente HTML: 23 páginas visibles, sin destinos de enlace ausentes ni identificadores duplicados. La comprobación geométrica no detectó contenido invadiendo la zona del pie.
- PDF final: 23 páginas con texto, renderizadas a PNG; 25 enlaces internos con destinos dentro del documento. Se inspeccionaron visualmente las páginas ampliadas, las demás páginas modificadas y sus adyacentes, sin recortes de contenido ni superposiciones.
- Coherencia documental: versión y fecha alineadas; aclaraciones compartidas sobre fecha de Actualidad, modalidad de Selecciones y texto/enlace de Slides; lista de comprobación ajustada a las secciones que no tienen página individual.
- `git diff --check` y comprobación de LF sin CRLF en los textos modificados. Las verificaciones y los PNG de esta intervención se conservaron como evidencia local en `artifacts/manual-review-v15/` (directorio ignorado por Git); no son un requisito para consultar los documentos entregables.
- No se ejecutaron pruebas PHP ni E2E de la aplicación: esta intervención es documental. La revisión de capturas y código no demuestra que una edición se guarde o publique correctamente en el WordPress desplegado.

### Seguimiento y trabajo pendiente

Las mejoras documentales están implementadas. La revisión personal de Juan de Aliados Oficiales, Clubes, Integrantes y Nosotros sigue **no iniciada** en [el seguimiento del manual](seguimiento-revision-manual.md). No se modificaron los estados de Daniel. Preparar la guía y verificar su PDF no equivale a finalizar el recorrido de sus responsables.

Esta intervención no implementa controles dedicados para metadatos, una salida pública para `labm_ciudad`, un rediseño del Footer ni una política de medios. Esos pendientes funcionales o editoriales de las entradas anteriores siguen abiertos; la guía aclara su comportamiento actual.

No se guardó ni publicó contenido, no se modificaron opciones, usuarios o uploads y no se envió correo. No aplica `scripts/content-sync.ps1` ni actualizar el paquete canónico. Para revertir únicamente esta adaptación se deben revisar los cambios de esta intervención en el manual, HTML, PDF y sus dos documentos de seguimiento; la instrucción histórica de revertir solo una declaración CSS no corresponde a esta actualización.

## Retirada completa de Horarios (2026-10-25)

Por decisión del usuario, se retiró Horarios del administrador, del código activo y de los datos locales de WordPress. Antes de modificar la base de datos se creó el respaldo oficial `.content-sync/backups/backup-manual-20261025T171008512Z-User-DESKTOP-UOCJQQ8.zip` (99,456,498 bytes). La base de datos usa InnoDB y el respaldo contiene el dump sanitizado y los uploads; `wp_users` y `wp_usermeta` están excluidos por el proceso oficial.

La inspección previa encontró tres entradas `labm_horario`:

| ID | Título | Estado | Metadatos asociados |
|---:|---|---|---|
| 19 | `[DEMO LABM — FICTICIO] Horario limite` | Publicada | `labm_inicio=23:59`; `_edit_lock=1792944281:1` |
| 12475 | `Auto Draft` | Borrador automático | `_edit_lock=1792107463:1` |
| 12490 | `Auto Draft` | Borrador automático | `_edit_lock=1792943803:1` |

Se comprobó que esas entradas no tenían hijos, comentarios ni asignaciones taxonómicas. Primero se ocultó el tipo del panel y WP-CLI confirmó `show_ui=no` y `show_in_menu=no`. Después se eliminaron solo los posts IDs 19, 12475 y 12490 cuyo tipo era `labm_horario`, junto con las filas `wp_postmeta` cuyos `post_id` coincidían. No se borraron usuarios, otros tipos, adjuntos ni metadatos compartidos.

El plugin ya no registra `labm_horario` ni `labm_inicio`, tampoco presenta la ayuda administrativa de Horarios. Se retiró el fixture de demostración y sus expectativas de las pruebas existentes. La versión de capacidades se incrementó a 7 para retirar de los roles Administrador y Editor únicamente las capacidades del tipo Horarios; no se alteraron capacidades ajenas. El manual, esta auditoría y la propuesta de sitio señalan que la sección fue retirada.

**Sincronización canónica:** completada con `scripts/content-sync.ps1 -Action Push`. `content-sync/latest.json` y el manifiesto coinciden en la versión `20261025T171659162Z-User-DESKTOP-UOCJQQ8`; SHA-256 de `canonical.zip`: `43b72ea3a73cd5f5cc54b9c7f91e2bc9045903f9bb7884972c22711746ff980b`. El manifiesto excluye `wp_users` y `wp_usermeta`, y se confirmó que no están en `database.sql`.

## Pendiente UX: destino de la política de tratamiento de datos (2026-10-25)

En la validación del navegador, el Footer mostró el enlace **“Política de tratamiento de datos”**, pero su destino `/privacidad/` devolvió **404**. La configuración se administra en **LABM → Footer LABM**: `policy_url` controla el enlace legal y `resources_3_url` controla el recurso de privacidad; ambos apuntan actualmente a `/privacidad/`. El mismo destino se utiliza en el consentimiento del formulario de Contacto.

**Impacto para la persona visitante:** al seguir el enlace desde el Footer o el consentimiento de Contacto, no puede acceder a la política de privacidad; esto deja sin resolver una consulta importante antes de enviar sus datos personales.

**Corrección pendiente:** crear y publicar la página con el slug `privacidad`, o cambiar `policy_url` y `resources_3_url` en **LABM → Footer LABM** para que apunten a una URL válida. Después, comprobar los dos enlaces desde el sitio público. No se realizaron cambios de configuración ni de contenido al registrar este hallazgo.
