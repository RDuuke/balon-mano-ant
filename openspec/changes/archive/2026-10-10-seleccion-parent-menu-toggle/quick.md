# Quick: Selecciones como control del submenú

## Objetivo

El padre «Selecciones» será un botón con texto y flecha, sin enlace. Abrirá sus opciones Balonmano Piso y Balonmano Playa por hover en escritorio y alternará abierto/cerrado al hacer clic o tocarlo. En las rutas `/selecciones/?modalidad=Piso` y `/selecciones/?modalidad=Playa` el padre conservará el estilo activo, aunque el submenú esté cerrado; sólo el enlace hijo correspondiente llevará `aria-current="page"`.

Se mantiene la etiqueta actual «Selecciones», las rutas hijas y la navegación de las demás secciones. Cambio de presentación y control del header compartido, sin modificar consultas, contenido, ajustes ni uploads de WordPress.

El detalle individual de una seleccion tambien mantiene el padre activo y resalta Piso o Playa segun la taxonomia del articulo, aunque la URL tenga un filtro distinto. En estas internas el hijo usa `aria-current="location"`; en el listado conserva `aria-current="page"`.

## Archivos afectados

- `wp-content/themes/labm/functions.php`: salida del shortcode de navegación; reemplazar enlace padre y botón de flecha por un único botón.
- `wp-content/themes/labm/assets/navigation.js`: sincronización de clic, hover y cierre accesible.
- `wp-content/themes/labm/style.css`: presentación del botón, estado activo y foco en escritorio/móvil.
- `tests/php/PublicExperienceTest.php`: contrato de marcado, selección efectiva y regresiones relacionadas.
- `tests/e2e/verify-correctives.spec.ts` y `tests/e2e/public-experience.spec.ts`: adaptar expectativas del enlace padre y cubrir interacción ejecutada.

## Blueprint

1. **q1 — RED:** ajustar pruebas existentes y añadir casos focales de padre sin enlace, apertura por hover/clic y activo en ambas modalidades. Ejecutarlas antes del cambio de producto y conservar fallos que prueben el requisito; TDD estricto del proyecto.
2. **q2 — Control único:** emitir botón semántico con etiqueta visible «Selecciones», flecha decorativa, `aria-controls` y `aria-expanded`. Conservar la clase de sección actual en el padre y `aria-current="page"` exclusivamente en el hijo actual. Ajustar CSS para tamaño, tipografía, verde activo y foco visible del nuevo botón.
3. **q3 — Interacción:** reutilizar `setSubmenu` para clic/touch y hover sólo con puntero fino capaz de hover en escritorio. Mantener abierto durante el recorrido al panel; al salir cerrar si el foco no permanece dentro. Enter/Espacio alternan con semántica nativa; Tab alcanza los hijos; Escape cierra y devuelve foco. Clic externo y salida de foco cierran. Evitar que hover reabra inmediatamente tras Escape o cierre por clic sin nueva entrada del puntero. El estado activo depende de la ruta, no de estar abierto. Sin JavaScript, conservar los enlaces hijos disponibles como mejora progresiva.
4. **q4 — GREEN y verificación:** ejecutar pruebas focales, análisis/formato aplicables, gate completo configurado y comprobación LF. Registrar evidencia por criterio y hashes vigentes. Tras APPLY presentar el diff concreto para el gate `review_budget` configurado en cero; la aprobación del alcance no sustituye aprobación de ese diff.

## Riesgos y reversión

- El header es compartido: actualizar las pruebas que actualmente exigen un enlace padre y comprobar Inicio/Actualidad además de ambas modalidades. La elegibilidad QUICK deriva del único comportamiento acotado, sin cambios de arquitectura.
- Hover y clic deben compartir estado accesible para evitar panel visible con `aria-expanded="false"`, cierres al pasar al hijo o navegación accidental.
- El wrapper de navegador oficial carga fixtures y cambia temporalmente URLs. Ejecutar contra entorno aislado cuando sea posible, o respaldar/restaurar el estado persistido mediante el proceso oficial. Si permanecen cambios en base de datos/uploads, ejecutar `scripts/content-sync.ps1`, verificar versión/hash y ausencia de `wp_users`/`wp_usermeta`; incluir artefactos canónicos resultantes en la entrega. No publicar fixtures de verificación como contenido editorial.
- Reversión: restaurar los tres archivos del tema y las pruebas modificadas desde la revisión anterior, sin migraciones de producto.

## Verificación

Los detalles repetibles y comandos están en [validation-plan.yaml](validation-plan.yaml).

- **c1:** en Inicio/Actualidad no existe enlace padre «Selecciones»; clic en el botón no cambia URL y alterna el panel.
- **c2:** en escritorio con puntero de hover, entrar al padre abre; recorrer un hijo mantiene abierto; salir del grupo cierra si no conserva foco interno. Clic y Escape siguen cerrando sin reapertura inmediata.
- **c3:** El detalle individual conserva padre y modalidad del articulo seleccionados con `aria-current="location"`, independientemente del querystring.  al cargar directamente Piso o Playa y tras seguir sus enlaces, el padre se ve activo con el panel abierto o cerrado; sólo el hijo correspondiente está marcado como página actual. En Inicio/Actualidad el padre no tiene estado de sección actual.
- **c4:** Enter/Espacio, Tab y Escape funcionan con foco visible, `aria-expanded` coherente y retorno al botón. En móvil de 320 px/touch funciona clic y conserva rutas hijas. Sin JavaScript los hijos permanecen accesibles. Comprobar 768, 1024 y 1440 px con proyectos existentes.
- **c5:** evidencia RED/GREEN real; pruebas focales y gate `scripts/gate.ps1 -IncludeBrowser` completos, cobertura PHP >=80 %, PHPCS/PHPStan y suite Playwright completa. No asumir PASS por código cero si el wrapper reporta herramientas o checks no ejecutados. Lighthouse es informativo para este cambio pequeño; no se amplía a rendimiento/SEO.
- **c6:** todos los archivos de texto modificados son LF, `git diff --check` pasa y el diff queda dentro de las superficies aprobadas. Se cumple el gate de revisión del diff y, cuando corresponda, la sincronización de estado persistido.
