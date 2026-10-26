# Proposal: Administrar la sección Nuestra Liga desde WordPress

## Intent

Permitir que el equipo editorial modifique desde el panel administrativo los textos visibles del bloque **Nuestra Liga** en la página de Inicio, sin editar archivos PHP ni depender del despliegue del tema. La solución debe conservar el diseño, los enlaces, la accesibilidad y los textos actuales como valores iniciales.

## Scope

### In Scope

- Crear una pantalla administrativa de **LABM → Inicio** para editar el bloque Nuestra Liga.
- Hacer editables el rótulo de sección, título principal, descripción, texto del botón, URL del botón, indicador numérico y etiqueta del indicador.
- Guardar los valores mediante una opción de WordPress con esquema cerrado, saneamiento, valores predeterminados y control de permisos.
- Mantener el contenido actual como fallback cuando un valor esté vacío o la opción todavía no exista.
- Renderizar los valores administrados desde el patrón de Inicio, con escape contextual y validación de URLs.
- Añadir pruebas unitarias y de experiencia pública para guardado, permisos, saneamiento, fallback y renderizado.
- Actualizar el manual para explicar dónde se editan estos textos y qué efecto tiene cada campo.

### Out of Scope

- Rediseñar visualmente la sección Nuestra Liga.
- Convertir en editables otras secciones de Inicio en este cambio, como Slides, Clubes, Actualidad, aliados o el llamado a vinculación.
- Permitir HTML libre, shortcodes o bloques arbitrarios dentro de los campos.
- Cambiar el destino actual `/nosotros/` salvo que el usuario lo modifique explícitamente desde la nueva pantalla.
- Modificar contenido de la base de datos durante la planificación o crear una migración destructiva.

## Approach

Se ampliará `labm-core` con un esquema de configuración editorial para la presentación de Inicio, siguiendo el patrón existente de **Footer LABM**: funciones para defaults, lectura efectiva, saneamiento, registro de la opción, menú administrativo y formulario protegido por la Settings API. La pantalla se ubicará bajo el menú superior **LABM**, con la capacidad `edit_theme_options`, consistente con la configuración administrativa ya existente.

El patrón `labm/inicio` consultará la configuración efectiva mediante una función pública del plugin. Si el plugin no está activo, si la opción no existe o si algún campo está vacío, el tema conservará el texto actual mediante fallbacks seguros. Los campos de texto se escaparán al renderizar y la URL del botón se limitará a protocolos seguros (`http`, `https` y rutas internas válidas).

La primera versión tratará el bloque como una unidad editorial identificable, pero usará claves separadas para que en el futuro puedan administrarse otras secciones sin volver a introducir textos rígidos en el tema.

## Affected Areas

| Area | Impact | Description |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Modified | Defaults, esquema, sanitización, Settings API, pantalla LABM → Inicio y lector de configuración pública. |
| `wp-content/themes/labm/patterns/inicio.php` | Modified | Sustituir textos rígidos de Nuestra Liga por valores administrables con fallback. |
| `tests/php/HomeContentTest.php` | Modified | Contratos de opción, permisos, defaults y saneamiento. |
| `tests/php/HomePresentationTest.php` | Modified | Render público del bloque, fallback y URL segura. |
| `tests/php/PublicExperienceTest.php` | Modified | Verificación de textos, enlace y accesibilidad en Inicio. |
| `docs/manual-usuario/manual-usuario.md` | Modified | Instrucciones para editar el bloque y descripción de cada campo. |
| `docs/manual-usuario/cartilla-manual-usuario-labm.html` | Modified | Reflejar la ruta administrativa y los pasos de uso en la cartilla visual. |
| `docs/manual-usuario/cartilla-manual-usuario-labm.pdf` | Regenerated | Entrega visual actualizada después de validar el HTML. |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| Un valor vacío elimina accidentalmente una parte del bloque | Media | Mostrar ayuda contextual, conservar fallbacks y validar que el guardado no produzca una salida vacía no intencionada. |
| Un editor sin permisos modifica configuración global | Baja | Proteger menú, formulario y Settings API con `edit_theme_options` y nonce nativo. |
| Una URL inválida rompe el botón | Media | Saneamiento con `esc_url_raw`, permitir rutas internas y ocultar el botón si la URL queda vacía o inválida. |
| El cambio de opción no aparece por caché | Baja | Verificar Inicio después de guardar y documentar la recarga; no introducir una caché nueva en este cambio. |
| El texto administrable pierde la jerarquía visual o la accesibilidad | Baja | Mantener las etiquetas semánticas y clases CSS actuales; añadir pruebas de HTML y revisión visual responsive. |

## Rollback Plan

Restaurar `class-labm-home-content.php`, `patterns/inicio.php`, las pruebas y los archivos del manual a sus versiones anteriores. El tema debe volver a utilizar los textos rígidos actuales mediante el fallback. La opción `labm_home_settings` puede conservarse sin efecto o eliminarse únicamente mediante una operación administrativa explícita y reversible; no se requiere borrar publicaciones ni adjuntos. Si se necesita retirar los valores guardados, exportar primero la opción y después eliminarla con una herramienta controlada de WordPress.

## Dependencies

- Plugin `labm-core` activo y menú administrativo **LABM** disponible.
- Capacidad WordPress `edit_theme_options` para el rol responsable de editar configuraciones globales.
- Tema de bloques LABM y patrón `labm/inicio`.
- Infraestructura existente de PHPUnit, análisis estático, pruebas de navegador y sincronización de contenido cuando se modifique el estado persistido.

## Success Criteria

- [ ] Un usuario autorizado puede abrir **LABM → Inicio**, modificar cada campo de Nuestra Liga y guardar sin editar código.
- [ ] Un usuario sin `edit_theme_options` no puede ver ni enviar la pantalla de configuración.
- [ ] Los valores guardados aparecen en Inicio conservando las clases, jerarquía, enlace y estilos actuales.
- [ ] Una opción inexistente o un campo vacío conserva un valor predeterminado seguro y no deja una sección rota.
- [ ] Las URLs inválidas no se imprimen como enlaces inseguros y el botón se oculta cuando no existe un destino válido.
- [ ] Las pruebas focales, análisis estático, revisión visual y verificación de finales de línea LF pasan.
- [ ] El manual explica la ruta **LABM → Inicio**, cada campo, el efecto de dejarlo vacío y la forma de comprobar el resultado público.
