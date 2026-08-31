# Exploración: sitio WordPress de la Liga Antioqueña de Balonmano

## Resumen ejecutivo

El cambio es una funcionalidad nueva y transversal: el repositorio no contiene todavía WordPress, PHP, dependencias, infraestructura de desarrollo ni pruebas. La única fuente funcional disponible es `website-liga-antioquena-wordpress.md`; los dos PDF de identidad citados allí no están presentes en el repositorio. La solución deberá iniciar una base reproducible y separar la presentación en un tema propio de la funcionalidad persistente en un plugin propio.

La alternativa más coherente es un tema de bloques compatible con Gutenberg, acompañado por un plugin `labm-core` para tipos de contenido, taxonomías, metadatos, capacidades, documentos PDF y lógica transversal. Esta separación evita que los datos y permisos desaparezcan al cambiar de tema, reduce el acoplamiento con constructores visuales y permite probar la lógica de dominio.

## Clasificación y dominios afectados

- Tipo de cambio: nueva plataforma institucional.
- Dominios públicos: navegación, inicio, nosotros, actualidad, selecciones, documentos y contacto.
- Dominios editoriales: páginas, integrantes, clubes, horarios, participaciones, eventos/noticias, documentos y configuración institucional.
- Dominios técnicos: entorno local reproducible, tema, plugin, activos de marca, seguridad, privacidad, correo, SEO, accesibilidad, rendimiento, datos demo, pruebas y documentación operativa.

## Estado real del repositorio

- En la raíz solo existen `AGENTS.md`, `website-liga-antioquena-wordpress.md`, `.codex/` y `openspec/`.
- No hay repositorio Git inicializado en esta carpeta.
- No hay instalación de WordPress, `composer.json`, `package.json`, contenedores, tema, plugin ni infraestructura de pruebas.
- `openspec/config.yaml` declara WordPress con tema propio y plugin persistente, Gutenberg, WordPress Coding Standards, WCAG 2.2 AA y contenido en español.
- La API oficial de versiones de WordPress reportó 7.1 como línea estable el 22 de agosto de 2026. La versión definitiva y la versión de PHP deben fijarse después de conocer el hosting objetivo; no conviene deducirlas solo del mínimo técnico aceptado por WordPress.
- No se encontraron `Logo liga - Logo liga.pdf` ni `AVAL ANTIOQUIA MENORES PARA SELECCIÓN COLOMBIA-1.pdf`. Por tanto, todavía no se puede verificar el verde aproximado `#AECD25`, extraer el logo ni evaluar la calidad de SVG/PNG.

## Inspección de la referencia pública

Se consultaron la portada y el índice público de páginas de <https://balonmanobogota.com/>. La referencia actualmente usa WordPress con Astra y Elementor; también expone recursos de Formidable Forms y All in One SEO. Su índice confirma páginas para Inicio, Acerca de nosotros, Eventos, Selección, Balonmano Piso, Balonmano Playa y Contacto, además de páginas individuales de participaciones o logros. La portada combina destacados, horarios por modalidad, escenario/mapa y redes sociales.

La referencia sirve para validar la arquitectura de información, pero no debe heredarse técnicamente ni copiarse editorialmente. En particular, presenta señales de deuda que la solución LABM debe evitar: dependencia de un page builder, páginas de participaciones sin jerarquía clara, metadatos genéricos de una plantilla y texto de copyright no institucional. La sección Documentos requerida para LABM es adicional y no aparece en el índice consultado.

## Opciones consideradas

### Opción A — tema de bloques propio y plugin de dominio propio (recomendada)

- Tema con `theme.json`, plantillas, partes de plantilla, patrones y variaciones de estilo.
- Gutenberg nativo para páginas y composición editorial.
- Plugin para CPT, taxonomías, metadatos registrados, capacidades, consultas, validaciones PDF y demás lógica que debe sobrevivir al tema.
- Ventajas: menor acoplamiento, buena experiencia editorial, compatibilidad con el editor del sitio, pruebas del dominio y control de accesibilidad/rendimiento.
- Costes: exige diseñar patrones y controles editoriales con cuidado; los campos repetibles complejos pueden necesitar bloques propios o una interfaz administrativa pequeña.

### Opción B — tema clásico propio más campos estructurados

- Plantillas PHP convencionales y campos registrados por el plugin, eventualmente apoyados en una dependencia mantenida.
- Ventajas: flujo de renderizado muy explícito y menor novedad técnica para equipos acostumbrados a temas clásicos.
- Costes: experiencia editorial menos flexible, más plantillas específicas y riesgo de duplicar una capa de campos si una dependencia externa se vuelve central.

### Opción C — tema comercial o genérico con constructor visual

- Ventajas: prototipado inicial rápido.
- Costes: mayor dependencia de terceros, marcado y assets más pesados, migración costosa, menor control de accesibilidad y posible desaparición de funcionalidad con el tema. No se recomienda para la arquitectura objetivo declarada.

## Arquitectura preliminar recomendada

### Entorno y estructura

- Proveer un entorno reproducible con contenedores o `wp-env`, base de datos aislada, configuración por variables de entorno y archivo de ejemplo sin secretos.
- Versionar únicamente la personalización y configuración reproducible; no modificar ni copiar el núcleo de WordPress como código propio.
- Organizar al menos un tema `labm` y un plugin `labm-core`, con espacios de nombres o prefijos consistentes para evitar colisiones.
- Incorporar desde el inicio herramientas para WordPress Coding Standards, pruebas PHP, pruebas de navegador y auditorías de accesibilidad.

La elección exacta entre Docker Compose y `wp-env` debe cerrarse en diseño según el entorno del equipo y el despliegue. Docker Compose ofrece mayor control sobre versiones y servicios; `wp-env` reduce configuración para desarrollo de plugins y temas, pero añade Node/Docker como prerrequisitos y puede divergir del hosting.

### Modelo de contenido candidato

| Contenido | Ubicación sugerida | Observación |
| --- | --- | --- |
| Páginas institucionales | `page` + patrones/bloques | Aprovecha revisiones, vista previa y Gutenberg. |
| Noticias | `post` + categorías | Usa comportamiento nativo y feeds. |
| Eventos | CPT `labm_evento` o un modelo unificado de actualidad | Debe distinguir fecha del evento de fecha de publicación; decidir en diseño cómo ofrecer un listado conjunto. |
| Documentos | CPT `labm_documento` | PDF como adjunto, fecha, referencia, destacado y capacidades propias. |
| Categorías de documentos | taxonomía `labm_categoria_documento` | Necesaria para filtro y administración. |
| Participaciones/selecciones | CPT `labm_participacion` + taxonomía de modalidad | La taxonomía debe admitir Piso, Playa y futuras ramas sin código. |
| Clubes | CPT `labm_club` | Logo, URL, descripción y orden. |
| Integrantes | CPT `labm_integrante` + taxonomías/roles | Foto, cargo, grupo, biografía, contactos opcionales y orden. |
| Horarios | bloque estructurado o entidad propia | La decisión depende del volumen, reutilización y necesidad de consulta. |
| Datos institucionales | opciones registradas o ajustes del sitio | Dirección, correo, teléfono, redes y mapa, sin valores inventados. |

Los slugs públicos y nombres internos finales deben acordarse en diseño; conviene prefijar identificadores técnicos aunque los slugs visibles sean legibles.

### Documentos PDF

La lógica debe vivir en el plugin. El flujo de guardado debe comprobar capacidad y nonce, validar adjunto, extensión y MIME real mediante APIs de WordPress, sanitizar metadatos y escapar toda salida. La consulta pública debe combinar texto, categoría y año, conservar filtros al paginar y ordenar por fecha documental descendente. `Ver PDF` y `Descargar` necesitan acciones semánticamente distintas, sin exponer rutas internas.

La eliminación del registro debe desvincularse de la eliminación física del adjunto. Una política explícita debe comprobar referencias antes de borrar el archivo y requerir confirmación cuando corresponda.

### Contacto y privacidad

Hay dos rutas viables: implementar un formulario pequeño dentro de `labm-core`, o integrar un plugin de formularios mantenido. La implementación propia ofrece control y pruebas, pero incorpora responsabilidades de entrega, antispam, privacidad, rate limiting y observabilidad. Un plugin reduce esa superficie, pero debe evaluarse por mantenimiento, accesibilidad, dependencia y licenciamiento. La decisión requiere conocer SMTP, retención y consentimiento; mientras tanto, el diseño no debe asumir almacenamiento indefinido.

### Tema, accesibilidad y rendimiento

- Definir tokens de color, tipografía, espaciado, radios y ancho de contenido en `theme.json`; verificar la paleta cuando lleguen los activos originales.
- Crear encabezado, navegación móvil accesible, enlace de salto, pie institucional y patrones de las páginas principales.
- Reservar dimensiones de medios, usar imágenes responsive y evitar carruseles automáticos; si se mantiene un carrusel, debe tener controles, pausa y comportamiento compatible con movimiento reducido.
- Diseñar estados de foco, error, éxito, carga y vacío desde el sistema visual, no como correcciones posteriores.
- No usar texto blanco pequeño sobre el verde lima sin comprobar contraste; usar la variante oscura cuando sea necesario.

### Pruebas y evidencia

- PHPUnit con el entorno de pruebas de WordPress para registros, metadatos, capacidades, validación PDF, consultas combinadas y contacto.
- Pruebas de integración para publicación/borradores y políticas de adjuntos.
- Pruebas de navegador para rutas, navegación por teclado, formularios, filtros, paginación y acciones de PDF.
- Auditorías automatizadas con axe/Lighthouse y QA manual en 320, 768, 1024 y 1440 px.
- Fixtures exclusivamente ficticios y claramente marcados; ningún dato personal de los PDF de referencia debe entrar en datos demo.

## Decisiones que corresponden a PROPOSE/DESIGN

1. Entorno local (`wp-env`, Docker Compose u otra opción compatible con el equipo).
2. Versiones fijadas de WordPress, PHP, base de datos y Node, condicionadas por hosting.
3. Separación entre noticias y eventos o modelo unificado de actualidad.
4. Representación de horarios y de secciones repetibles de la portada.
5. Formulario propio frente a plugin mantenido, incluida retención y antispam.
6. Estrategia de correo, analítica, consentimiento, SEO y mapas por ambiente.
7. Política exacta de borrado y reutilización de adjuntos PDF.
8. Alcance de bloques personalizados frente a patrones y bloques nativos.

## Riesgos y bloqueos

- Los activos de marca citados no están disponibles; bloquean la verificación cromática y la preparación del logo, aunque no bloquean propuesta y diseño iniciales.
- Hosting, PHP, dominio, SMTP y restricciones de subida son desconocidos; pueden cambiar la matriz de versiones y el entorno de despliegue.
- Faltan contenido editorial, integrantes, clubes, horarios y datos de contacto vigentes; solo deben usarse placeholders explícitos.
- El alcance combina CMS, catálogo documental, formularios, accesibilidad, SEO y pruebas desde cero; requiere dividir la implementación en lotes verificables.
- La validación MIME no elimina por sí sola todo riesgo de archivos; deben conservarse las validaciones nativas, capacidades y controles del hosting.
- Un listado combinado de noticias y eventos puede complicar ordenación, paginación y filtros si se modelan como tipos distintos.
- Los objetivos Lighthouse dependen del entorno, contenido y servicios externos; las condiciones de medición deben quedar fijadas.
- La ausencia de Git reduce trazabilidad y recuperación durante la implementación; conviene inicializar control de versiones antes de APPLY, con autorización del usuario.

## Próximo paso

Crear `proposal.md` para delimitar el MVP, los módulos afectados, exclusiones, estrategia de reversión y decisiones que se trasladarán a SPEC y DESIGN. No se implementó código en esta fase.
