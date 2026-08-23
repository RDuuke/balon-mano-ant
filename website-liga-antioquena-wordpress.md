# OpenSpec - Website Liga Antioqueña de Balonmano en WordPress

## Instrucción de ejecución

Implementar un sitio web institucional en WordPress para la **Liga Antioqueña de Balonmano (LABM)** conforme a esta especificación.

Esta especificación es la fuente de verdad funcional y visual. Antes de implementar, el agente DEBE inspeccionar el repositorio, conservar las convenciones existentes y convertir este documento al flujo OpenSpec configurado en el proyecto. Si el proyecto usa el esquema `spec-driven`, crear el cambio `create-labm-wordpress-website` con `proposal.md`, `design.md`, `tasks.md` y las delta specs requeridas. Después de validar los artefactos, ejecutar la implementación con `/opsx:apply create-labm-wordpress-website`.

Si WordPress todavía no está inicializado, el agente DEBE preparar una instalación reproducible para desarrollo local y documentar sus prerrequisitos. No se deben incluir secretos, contraseñas reales ni credenciales en el repositorio.

## 1. Objetivo

Construir un sitio institucional administrable, responsive, accesible y optimizado para buscadores, inspirado en la arquitectura de información de <https://balonmanobogota.com/>, pero adaptado a la identidad y contenido de la Liga Antioqueña de Balonmano.

El sitio DEBE permitir que un administrador sin conocimientos técnicos gestione páginas, noticias/eventos, selecciones, clubes, integrantes, horarios, datos de contacto y documentos PDF desde el panel de WordPress.

## 2. Fuentes de referencia

### 2.1 Sitio de referencia funcional

Usar como referencia de secciones y navegación:

- Inicio.
- Acerca de nosotros.
- Eventos y noticias.
- Selecciones, con subsecciones Balonmano Piso y Balonmano Playa.
- Contacto.

No copiar textos, fotografías, nombres, teléfonos, direcciones ni identidad de la Liga de Balonmano de Bogotá. La referencia define estructura y capacidades, no contenido editorial.

### 2.2 Identidad visual

Usar los archivos suministrados como fuentes de marca:

- `Logo liga - Logo liga.pdf`: logo oficial de la Liga Antioqueña de Balonmano.
- `AVAL ANTIOQUIA MENORES PARA SELECCIÓN COLOMBIA-1.pdf`: referencia de papelería, colores, datos institucionales y tratamiento gráfico.

Paleta base extraída del logo:

| Token | Color | Uso |
| --- | --- | --- |
| `--color-primary` | `#AECD25` | Botones principales, enlaces activos, acentos y estados de foco |
| `--color-primary-dark` | `#789614` | Hover y contraste sobre fondos claros |
| `--color-ink` | `#000000` | Encabezados, navegación, pie y texto de alto contraste |
| `--color-surface` | `#FFFFFF` | Fondos y tarjetas |
| `--color-surface-soft` | `#F3F6E8` | Fondos alternos y secciones destacadas |
| `--color-text` | `#202020` | Texto general |

El verde lima `#AECD25` es aproximado y DEBE verificarse contra el recurso gráfico original durante la implementación. Los ajustes de contraste para accesibilidad pueden usar variantes más oscuras sin alterar el carácter de la marca.

El agente DEBE extraer o convertir el logo a un formato apto para web:

- Preferir SVG si la extracción conserva correctamente las formas.
- Proveer PNG transparente de alta resolución como fallback.
- No deformar, recolorear, recortar ni reconstruir el logo con tipografías sustitutas.
- Definir texto alternativo: `Liga Antioqueña de Balonmano`.

## 3. Alcance funcional

### 3.1 Navegación global

El menú principal DEBE incluir:

1. Inicio.
2. Nosotros.
3. Eventos y noticias.
4. Selecciones.
   - Balonmano Piso.
   - Balonmano Playa.
5. Documentos.
6. Contacto.

El encabezado DEBE contener el logo, el menú principal, un control accesible para navegación móvil y, si están configurados, enlaces a redes sociales.

El pie de página DEBE mostrar logo o nombre institucional, datos de contacto, navegación secundaria, redes sociales, política de tratamiento de datos y copyright con año dinámico.

### 3.2 Inicio

La portada DEBE permitir administrar las siguientes secciones:

- Hero o carrusel destacado con imagen, título, descripción y llamada a la acción.
- Presentación breve de la Liga.
- Clubes asociados con logo, nombre y enlace opcional.
- Evento o logro destacado.
- Últimas noticias/eventos.
- Horarios de entrenamiento separados por modalidad.
- Ubicación o escenarios deportivos, con enlace a mapa.
- Llamada a la acción de contacto o vinculación.

El administrador DEBE poder ocultar secciones sin dejar espacios vacíos ni romper el diseño.

### 3.3 Nosotros

La página DEBE incluir bloques administrables para:

- Quiénes somos.
- Misión.
- Visión.
- Comité ejecutivo.
- Entrenadores.
- Representantes de los deportistas u otros grupos institucionales configurables.

Cada integrante DEBE soportar nombre, cargo/rol, fotografía, modalidad o grupo, biografía breve y enlaces opcionales de contacto/redes.

### 3.4 Eventos y noticias

El sitio DEBE ofrecer un listado cronológico con:

- Imagen destacada.
- Título.
- Fecha de publicación o fecha del evento.
- Extracto.
- Categoría.
- Enlace al detalle.

El detalle DEBE soportar contenido enriquecido, galería, fecha, ubicación, categorías y botones para compartir cuando estén habilitados.

El listado DEBE incluir paginación y filtros por categoría. Si se implementa búsqueda, esta DEBE ser compatible con teclado y mostrar un estado vacío claro.

### 3.5 Selecciones

Deben existir páginas independientes para:

- Balonmano Piso.
- Balonmano Playa.

Cada modalidad DEBE permitir publicar participaciones, torneos, convocatorias o logros agrupados, con título, fecha o periodo, ubicación, descripción y galería de imágenes.

La taxonomía y el modelo de contenido DEBEN permitir agregar nuevas categorías o ramas sin modificar código.

### 3.6 Documentos PDF

Crear una sección pública `Documentos` y un modelo administrable desde WordPress.

Cada documento DEBE soportar:

- Título obligatorio.
- Archivo PDF obligatorio.
- Descripción o resumen opcional.
- Categoría obligatoria.
- Fecha del documento.
- Número de resolución, circular o referencia opcional.
- Estado publicado/borrador.
- Orden destacado opcional.
- Fecha de publicación gestionada por WordPress.

La vista pública DEBE:

- Mostrar una lista o cuadrícula responsive.
- Mostrar título, categoría, fecha, descripción breve y tamaño del archivo cuando esté disponible.
- Permitir buscar por título, descripción o referencia.
- Permitir filtrar por categoría y año.
- Permitir ordenar por fecha, más reciente primero de forma predeterminada.
- Incluir acciones diferenciadas `Ver PDF` y `Descargar`.
- Abrir `Ver PDF` en una nueva pestaña sin reemplazar la página actual.
- Incluir paginación configurable; valor inicial recomendado: 12 documentos por página.
- Mostrar un estado vacío comprensible cuando no existan resultados.

Desde el panel, solamente usuarios con permisos autorizados DEBEN poder crear, editar, publicar o eliminar documentos.

Validaciones mínimas:

- Aceptar únicamente archivos con MIME `application/pdf` y extensión `.pdf`.
- Rechazar archivos que no superen las validaciones nativas y de seguridad de WordPress.
- Respetar el límite de subida configurado por WordPress/PHP; no codificar un límite incompatible en el tema.
- Sanitizar metadatos y escapar toda salida.
- No exponer rutas internas del servidor.

La eliminación de un registro NO DEBE borrar silenciosamente un archivo que sea utilizado por otro contenido. Si se decide eliminar también el adjunto, la acción DEBE requerir confirmación explícita o una política documentada.

### 3.7 Contacto

La página DEBE contener:

- Formulario con nombre, apellidos, correo, teléfono opcional, asunto y mensaje.
- Datos institucionales administrables.
- Dirección o escenario deportivo.
- Enlace a Google Maps u otro proveedor configurado.
- Redes sociales.

El formulario DEBE validar campos obligatorios, mostrar estados de éxito/error, incluir protección antispam sin afectar significativamente la accesibilidad y enviar las solicitudes al correo configurado por la administración.

No almacenar mensajes indefinidamente sin una política de privacidad explícita. Si se almacenan en WordPress, definir retención y acceso por rol.

## 4. Modelo de contenido recomendado

El agente DEBE reutilizar capacidades nativas de WordPress cuando sea razonable y crear tipos de contenido/taxonomías solo cuando mejoren la administración.

| Contenido | Implementación recomendada | Campos clave |
| --- | --- | --- |
| Páginas institucionales | `page` + bloques/campos | Contenido, imagen, visibilidad de secciones |
| Noticias y eventos | `post` o CPT `evento` | Fecha, ubicación, galería, categoría |
| Documentos | CPT `documento` | PDF, fecha, referencia, destacado |
| Categorías de documentos | Taxonomía `categoria_documento` | Nombre, slug, descripción |
| Selecciones/participaciones | CPT `seleccion` o `participacion` | Modalidad, periodo, lugar, galería |
| Clubes | CPT `club` | Nombre, logo, URL, descripción |
| Integrantes | CPT `integrante` | Rol, grupo, foto, contacto, orden |
| Horarios | Bloque repetible u opciones | Modalidad, día, hora, categoría, escenario |

Si el repositorio ya define otro modelo equivalente, adaptarlo sin duplicar datos.

## 5. Requisitos de administración

### Requirement: Gestión editorial sin código

El sistema SHALL permitir que un usuario con rol Editor o Administrador actualice todo contenido visible sin editar archivos PHP, CSS o JavaScript.

#### Scenario: Editor actualiza una sección

- **GIVEN** un usuario autenticado con permisos editoriales
- **WHEN** modifica y publica el contenido de una sección
- **THEN** el cambio se refleja en la página correspondiente
- **AND** el diseño conserva su estructura responsive.

### Requirement: Vista previa y borradores

El sistema SHALL aprovechar los estados, revisiones y vista previa de WordPress.

#### Scenario: Documento en borrador

- **GIVEN** un documento guardado como borrador
- **WHEN** un visitante consulta la sección pública
- **THEN** el documento no aparece en los resultados ni es enlazado públicamente por el sitio.

## 6. Requisitos funcionales verificables

### Requirement: Navegación equivalente a la referencia

El sitio SHALL ofrecer todas las secciones definidas en el alcance y permitir acceder a ellas desde la navegación global.

#### Scenario: Navegación de escritorio

- **GIVEN** un visitante en una pantalla de escritorio
- **WHEN** usa el menú principal
- **THEN** puede acceder a Inicio, Nosotros, Eventos y noticias, ambas modalidades de Selecciones, Documentos y Contacto.

#### Scenario: Navegación móvil

- **GIVEN** un visitante en una pantalla móvil
- **WHEN** abre el control de menú y navega con teclado o lector de pantalla
- **THEN** todos los enlaces son alcanzables, el foco es visible y el menú puede cerrarse.

### Requirement: Gestión de PDF

El sistema SHALL permitir publicar documentos PDF con metadatos y mostrarlos en un catálogo público.

#### Scenario: Publicación correcta

- **GIVEN** un administrador con un PDF válido y los campos obligatorios completos
- **WHEN** publica el documento
- **THEN** el documento aparece en el listado público con título, categoría y fecha
- **AND** las acciones de ver y descargar apuntan al archivo correcto.

#### Scenario: Archivo inválido

- **GIVEN** un usuario intenta adjuntar un archivo que no es PDF
- **WHEN** guarda o publica el documento
- **THEN** el sistema rechaza la operación
- **AND** muestra un mensaje claro sin exponer información técnica sensible.

#### Scenario: Búsqueda y filtros

- **GIVEN** varios documentos publicados de diferentes categorías y años
- **WHEN** el visitante combina texto, categoría y año
- **THEN** solo se muestran documentos que cumplen todos los criterios activos
- **AND** la paginación conserva esos criterios.

### Requirement: Formulario de contacto

El sistema SHALL enviar solicitudes válidas al destinatario configurado y comunicar el resultado al visitante.

#### Scenario: Envío exitoso

- **GIVEN** un visitante completa correctamente los campos obligatorios
- **WHEN** envía el formulario y el proveedor de correo responde satisfactoriamente
- **THEN** se muestra confirmación
- **AND** no se reenvía el formulario al recargar la página.

#### Scenario: Error de entrega

- **GIVEN** un fallo en el envío
- **WHEN** el visitante intenta enviar el formulario
- **THEN** se presenta un mensaje de error accionable
- **AND** el sistema registra el detalle técnico sin mostrarlo públicamente.

## 7. Diseño y experiencia de usuario

- Diseño deportivo, moderno y sobrio, con predominio de verde, negro y blanco.
- No replicar pixel a pixel el sitio de referencia; mejorar jerarquía, consistencia y experiencia móvil.
- Componentes con espaciado, tipografía y radios consistentes mediante variables de tema.
- Imágenes optimizadas y con proporciones estables para evitar saltos de diseño.
- Estados hover, focus, active, loading, success, warning, error y empty coherentes.
- Contraste mínimo WCAG 2.2 AA; no usar verde lima con texto blanco pequeño si no alcanza contraste.
- Contenido usable desde 320 px de ancho y en pantallas grandes sin líneas de texto excesivamente largas.
- Respetar `prefers-reduced-motion`; animaciones decorativas deben ser discretas y prescindibles.

## 8. Arquitectura técnica

### 8.1 WordPress

- Usar una versión estable y soportada de WordPress y PHP compatible con el entorno de despliegue.
- Implementar la personalización en un tema propio o child theme; no modificar archivos core.
- Separar funcionalidad persistente del contenido (CPT, taxonomías, permisos y validaciones) en un plugin propio cuando corresponda, para que no desaparezca al cambiar el tema.
- Preferir Gutenberg y bloques nativos/personalizados sobre page builders con alto acoplamiento, salvo que el repositorio ya haya estandarizado uno.
- Evitar plugins abandonados, redundantes o sin mantenimiento activo.
- Todas las cadenas visibles DEBEN estar preparadas para traducción y usar español como idioma inicial.

### 8.2 Calidad de código

- Seguir WordPress Coding Standards.
- Sanitizar entradas, escapar salidas y usar nonces/capabilities en acciones administrativas.
- Usar consultas parametrizadas o APIs de WordPress; nunca concatenar entrada de usuario en SQL.
- No incluir dependencias de frontend innecesarias para funciones simples.
- Versionar assets para invalidación de caché.
- Mantener compatibilidad con los navegadores modernos acordados por el proyecto.

### 8.3 Configuración y despliegue

- Proveer `.env.example` o mecanismo equivalente sin secretos.
- Documentar instalación local, build de assets, importación de contenido demo, pruebas y despliegue.
- Proveer contenido inicial o fixtures que permitan validar todas las plantillas sin depender de producción.
- La configuración específica de dominio, correo, analítica y mapas DEBE ser reemplazable por ambiente.

## 9. Seguridad y privacidad

- Aplicar principio de mínimo privilegio a roles y capacidades.
- Proteger formularios y acciones mutables con nonce/CSRF y antispam.
- Validar tipo real y permisos de cada archivo cargado.
- Deshabilitar ejecución de archivos dentro de directorios de uploads cuando el hosting lo permita.
- No exponer versiones, trazas, errores PHP ni datos personales al público.
- Incluir política de privacidad y consentimiento requerido para tratamiento de datos del formulario.
- No incluir datos personales del PDF de aval como contenido demo ni publicarlo automáticamente.
- Mantener WordPress, tema y plugins actualizables sin perder personalizaciones.

## 10. Rendimiento, SEO y accesibilidad

- Objetivo Lighthouse en páginas públicas representativas: Performance >= 85 y Accessibility/Best Practices/SEO >= 90 en condiciones de prueba controladas.
- Usar imágenes responsive (`srcset`), lazy loading fuera del primer viewport y formatos modernos cuando sean compatibles.
- Evitar Cumulative Layout Shift reservando dimensiones de imágenes, logos y embeds.
- HTML semántico, un único `h1` por vista y jerarquía de encabezados coherente.
- URLs legibles, metadatos editables, Open Graph, sitemap XML y datos estructurados cuando apliquen.
- Formularios con etiquetas asociadas, mensajes anunciables y errores vinculados a los campos.
- Navegación completa por teclado, enlace para saltar al contenido y foco visible.
- PDFs enlazados con nombre descriptivo, tipo y tamaño cuando esté disponible.

## 11. Analítica y observabilidad

- Permitir configurar analítica sin incrustar identificadores en el código.
- Cargar scripts de seguimiento solo después del consentimiento cuando legalmente corresponda.
- Registrar fallos de formularios y errores relevantes sin almacenar cuerpos con datos personales innecesarios.
- No enviar datos a servicios externos no documentados.

## 12. Pruebas mínimas

El agente DEBE implementar y ejecutar, según el stack disponible:

- Pruebas de registro de CPT, taxonomías, metadatos y capacidades.
- Pruebas de validación de carga PDF y rechazo de tipos inválidos.
- Pruebas de consultas de documentos con búsqueda, categoría, año y paginación combinados.
- Pruebas de permisos para crear, editar, publicar y eliminar documentos.
- Pruebas del formulario de contacto: validación, éxito, error y antispam.
- Pruebas de humo de las rutas públicas principales.
- Revisión responsive en 320 px, 768 px, 1024 px y 1440 px.
- Auditoría básica de teclado, foco, contraste, textos alternativos y encabezados.
- Verificación de enlaces `Ver PDF` y `Descargar`.

No marcar una tarea como completada si la prueba correspondiente falla o no fue ejecutada. Si el entorno impide una prueba, registrar la limitación y los pasos exactos para reproducirla.

## 13. Criterios de aceptación globales

1. Todas las secciones del sitio de referencia están representadas y adaptadas a la Liga Antioqueña.
2. Existe una sección adicional de Documentos totalmente gestionable desde WordPress.
3. Un Editor o Administrador puede cargar, categorizar, publicar, buscar y retirar PDFs sin editar código.
4. El frontend usa la identidad verde-negro, el logo suministrado y cumple contraste accesible.
5. El sitio funciona correctamente en móvil, tableta y escritorio.
6. No contiene datos, imágenes ni textos copiados de la Liga de Bogotá salvo la referencia estructural autorizada.
7. No publica automáticamente información personal contenida en los PDF de referencia.
8. Las acciones administrativas respetan capacidades, nonces, sanitización y escape.
9. La documentación permite levantar el proyecto en un entorno nuevo.
10. Las pruebas y verificaciones críticas pasan antes del cierre.

## 14. Entregables

- Código fuente del tema/child theme y plugin funcional, según la arquitectura adoptada.
- Activos del logo optimizados para web y sus fuentes originales preservadas.
- Configuración reproducible de desarrollo.
- Datos demo no sensibles.
- Manual corto de administración: noticias, selecciones, clubes, integrantes, horarios, documentos y datos de contacto.
- Evidencia de pruebas automáticas y checklist de QA manual.
- Registro de decisiones técnicas y plugins utilizados, con justificación.

## 15. Plan de implementación

- [ ] Inspeccionar repositorio, versión de WordPress, tema, plugins y configuración OpenSpec.
- [ ] Crear y validar el cambio OpenSpec `create-labm-wordpress-website`.
- [ ] Definir arquitectura de tema/plugin y documentar decisiones.
- [ ] Extraer, optimizar y verificar los activos de marca.
- [ ] Implementar design tokens, layout global, encabezado, navegación y pie.
- [ ] Implementar Inicio y sus secciones administrables.
- [ ] Implementar Nosotros, integrantes y clubes.
- [ ] Implementar Eventos/Noticias y páginas de detalle.
- [ ] Implementar Selecciones de Piso y Playa.
- [ ] Implementar CPT/taxonomía/metadatos/permisos de Documentos.
- [ ] Implementar listado, búsqueda, filtros, paginación, vista y descarga de PDFs.
- [ ] Implementar Contacto, validación, antispam y entrega de correo.
- [ ] Implementar SEO, privacidad, accesibilidad y optimizaciones de rendimiento.
- [ ] Crear datos demo y manual de administración.
- [ ] Ejecutar pruebas automáticas y QA responsive/accesible.
- [ ] Corregir hallazgos, verificar criterios de aceptación y preparar despliegue.

## 16. Supuestos y decisiones pendientes

Los siguientes puntos NO bloquean la creación de la base, pero deben confirmarse antes del despliegue productivo:

- Dominio final y proveedor de hosting.
- Versión de PHP y restricciones del hosting.
- Correos destinatarios y configuración SMTP.
- Datos vigentes de dirección, teléfono, horarios y redes sociales.
- Integrantes actuales, fotografías, clubes y contenido editorial inicial.
- Política definitiva de retención para mensajes y archivos.
- Herramienta de analítica y gestor de consentimiento.
- Necesidad de migrar contenido existente.

Cuando falte un dato, usar placeholders claramente identificados en el entorno demo; nunca inventar información institucional y presentarla como real.
