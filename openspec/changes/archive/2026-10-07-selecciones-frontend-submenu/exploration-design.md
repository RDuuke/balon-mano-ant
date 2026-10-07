# Exploración de diseño: Selecciones y submenú

Fecha: 2026-10-06. Alcance delegado: EXPLORE, exclusivamente diseño. Estándares del proyecto inyectados. Fuente: `C:/Users/rduuqe/Documents/BalonManoAnt/design/labm-wordpress-mockup.pen`.

## Método y acceso

MCP Pencil funcional: `get_app_state` confirmó el documento activo; `read_skill` permitió leer `execute.md` y `pen-schema.md`. Inspección mediante `execute` con `Get`, `GetVariables`, `Print` y `TakeScreenshot`, sin mutaciones ni exportaciones. No se leyó el archivo .pen como texto, no se navegó por la web y no se exploró código. Una respuesta extensa quedó truncada; se repitieron lecturas focalizadas de archivo, menú y detalle para recuperar evidencia. Coordenadas y dimensiones siguientes son bounds resueltos por MCP; las posiciones de hijos son relativas al padre.

Se inspeccionaron renders MCP de `yRBhE`, `coPGt`, `ImSRL` y `fo7wU`: submenú desktop, menú móvil abierto, listado Piso y listado Playa. Los renders corroboran etiquetas, colores, filas y jerarquía; no prueban interactividad. No se guardaron imágenes adicionales, conforme al alcance de un único artefacto.

## Inventario observado

| ID | Nombre exacto del frame | Dimensiones | Evidencia |
| --- | --- | --- | --- |
| `neIe6` | Selecciones — Desktop | 1440 × 1930 | Página de Piso, hero y tres publicaciones; submenú visible |
| `p1GT2` | Archivo de Selecciones — Desktop | 1440 × 1640 | Archivo de Playa, miga y tres publicaciones |
| `coPGt` | Selecciones — Mobile menú abierto | 390 × 854 | Menú general y sección Selecciones expandidos; hero Piso debajo |
| `LXU5I` | Detalle de actualidad — Desktop | 1440 × 2858 | Referencia editorial de Actualidad, no detalle explícito de Selecciones |
| `xyLwq` | Inicio — Mobile | 390 × 4766 | Referencia del botón para abrir menú móvil |

El inventario completo de raíces tiene once frames: los cinco anteriores, Componentes LABM (`seX1X`), Inicio — Desktop (`siSiK`), Nosotros — Desktop (`BU8oD`), Actualidad — Desktop (`Nrclx`), Documentos — Desktop (`g0s58`) y Contacto — Desktop (`XZyRt`). No existe frame dedicado de detalle de Selecciones, listado completo móvil de Selecciones ni archivo Playa móvil.

Las alturas de raíz dejan espacio adicional frente al final de sus hijos: en Piso el footer `QTENd` termina en y=1918 y en Playa `tsQrR` termina en y=1618. No convertir automáticamente ese espacio residual en requisito de frontend.

## Navegación y submenú observados

Desktop: `zPW15` Navegación Selecciones mide 1440 × 88, en y=40; barra institucional `DXtJL` mide 1440 × 40. Contenedor `HvDy8`: 1200 × 88, x=120. `E1DHwu` agrupa Inicio, Nosotros, Actualidad, Selecciones, Documentos y Contacto, con gap 28. `UoQq3`, cuyo nombre es «Enlace Selecciones⌄», contiene exactamente «Selecciones  ⌃», Inter 14/700 y color `$primary-dark`. En Inicio el nodo `GboGC` muestra «Selecciones⌄». Esto evidencia estados gráficos cerrado/abierto, no el mecanismo de apertura. En Archivo `p4hUC` dice «Selecciones» sin chevron; hay una inconsistencia entre cabeceras. En Piso `qSyIe` Inicio también usa color activo y peso 700: no copiar esa doble selección como semántica confirmada.

`yRBhE` «Menú Selecciones integrado» es hijo de `y3XkBV` Hero Selecciones, no de la navegación. Bounds: x=770, y=0, 286 × 122 dentro del hero, que empieza en y=128 de página. Rótulo `w5l5TD`: «SELECCIONES», Inter 11/700, muted. Opciones:

| Etiqueta exacta | Texto ID | Fondo ID y medida | Icono ID |
| --- | --- | --- | --- |
| BALONMANO PISO | `H3swWG` | `X8QRa`, 286 × 43, y=36, primary | `E6oNAA`, arrow-up-right, 14 × 14 |
| BALONMANO PLAYA | `YCm3F` | `BhoNb`, 286 × 42, y=79, surface | `L570wU`, arrow-up-right, 14 × 14 |

Textos Inter 12/700, x=16; iconos x=252. El render confirma Piso resaltado en verde. No se debe añadir «Ver todas» o categorías de edad al submenú como si estuvieran diseñadas.

Móvil: `Wqt9i` Navegación Mobile abierta mide 390 × 72, padding horizontal 20. `v2Uyf` Cerrar menú Mobile: 38 × 38, fondo ink; `tPNYC`: icono x de 20 × 20. `OJS3y` Menú Mobile expandido: 390 × 460, y=72, padding [16,20], layout vertical. Orden: Inicio (`Yo8D6`), Nosotros (`dxxfd`), Actualidad (`Y5iAVL`), Selecciones (`G0pFYo`), Documentos (`QAFpg`), Contacto (`Wqer0`). Filas generales: 350 × 48.

`G0pFYo` Selecciones Mobile expandido: 350 × 140, surface-soft, padding [12,14], gap 8. Cabecera `n0Zl5`: 322 × 30; `i3ktw` «SELECCIONES», Inter 16/700, primary-dark; `c0PJ7`: chevron-up 18 × 18. Opciones `dvTzK` y `HJyeS`: 322 × 34, padding [0,8]. Etiquetas exactas «Balonmano Piso» (`rp20t`) y «Balonmano Playa» (`TaEj4`), Inter 14/700, ink; flechas `YfC9t` y `FnJHX`, arrow-up-right 16 × 16. El menú desplaza visualmente el hero `Jtesa` a y=532; el render muestra panel blanco en el flujo y hero negro debajo, sin overlay oscuro. No hay estado móvil colapsado de Selecciones.

Referencia de apertura: Inicio móvil tiene `B2sh75` Navegación móvil de 390 × 76 bajo barra institucional de 36 px; botón `YxJXZ` Abrir menú móvil 48 × 48, icono `AXCad` menu 24 × 24. No hay un flow conectado que relacione este botón con `coPGt`.

Una consulta global `Get(n => n.href ? ... : undefined)` devolvió `[]`. Se revisaron también `metadata` y `context`: las anotaciones encontradas son créditos Unsplash; no se observaron destinos, transiciones o conexiones de prototipo. Las flechas diagonales no prueban apertura en otra pestaña. La miga `i9lEi` contiene «INICIO  /  SELECCIONES  /  BALONMANO PLAYA», pero es texto, sin href.

## Listados y contenido observado

Piso: hero `y3XkBV`, 1440 × 520, fondo ink, sin imagen fill aunque conserve crédito Unsplash HorseRat. `TlTri`: «SELECCIÓN ANTIOQUIA · PISO», Barlow Condensed 72/700; `S0gWe`: «REPRESENTAMOS A ANTIOQUIA», Inter 13/700 primary. `V87ez`: descripción Inter 18. Introducción `Q1tm53`: 1440 × 130, surface-soft; `BgwOb`: «PARTICIPACIONES Y REGISTRO FOTOGRÁFICO», 34/700.

Listado `ImSRL`: 1440 × 700, padding [34,120], gap 14. `YD1HP` «BALONMANO DE PISO» 40/700; `g2tklc` «MODALIDAD 01»; `AwBxx` «3 participaciones registradas». `A8GEV`: publicaciones verticales 1200 × 542, gap 12. Cada fila mide aproximadamente 1200 × 172.67, padding 12, gap 18, borde line de 1 px. Portada 220 × 148.67; contenido 938 × 100. Tipo Inter 11/700, título Barlow Condensed 28/700, metadatos Inter 14, acción Inter 12/700.

| Fila ID | Título ID y texto de demostración | Acción ID |
| --- | --- | --- |
| `D06v6` | `JZrXI`: Nacional de Selecciones Juvenil | `l9gzSs` |
| `QeodX` | `Ty62m`: Torneo Nacional Interligas | `lslE2` |
| `m5CHHn` | `sQfXb`: Festival Nacional de Balonmano | `safsE` |

Las tres acciones dicen exactamente «VER PUBLICACIÓN  →». Metadatos `CEhny`, `rs6qn`, `In6T7` ilustran categoría, lugar, año y resultado. No constituyen datos oficiales ni deben incorporarse como registros reales.

Playa: `uhXyv`, encabezado 1440 × 230, ink; `UmCpF` «SELECCIÓN ANTIOQUIA · PLAYA», Barlow Condensed 58/700; descripción `y0a7K` Inter 17. Introducción `ziEez`: 1440 × 120 surface-soft. `fo7wU`: 1440 × 700; `r8RDHv` «EVENTOS Y PARTICIPACIONES» 38/700; `vvXqr` «BALONMANO PLAYA»; `b4j4R` «3 participaciones registradas». `k1yYZ`: 1200 × 558. Filas de 1200 × 178, gap 12 entre filas, padding 12, separación interna 18 y borde line 1; portadas 220 × 154.

| Fila ID | Título ID y texto de demostración | Acción ID |
| --- | --- | --- |
| `Et2ez` | `D3opMy`: Circuito Nacional de Playa | `VFt6I` |
| `rV2Hf` | `LPe9k`: Copa Nacional de Clubes y Selecciones | `Z2fgO` |
| `GQnUp` | `rfrS4`: Festival Nacional de Playa | `TDFhC` |

Todas dicen «VER PUBLICACIÓN  →». `MAwsh`, `t6cG5` y `Gcm9j` ilustran categoría, lugar, año y resultado. Los renders muestran tres filas con fotografía izquierda y texto derecha, sin filtros ni paginación dentro de estos listados.

## Assets y estilos auditables

Variables observadas: primary `#AECD25`, primary-dark `#789614`, ink `#000000`, surface `#FFFFFF`, surface-soft `#F3F6E8`, text `#202020`, muted `#686868`, line `#DDE3CC`; font-heading Barlow Condensed; font-body Inter. Los fondos de menú usan estas variables. El logotipo es placeholder textual LABM: `amM5q` desktop y `heWYU` móvil, no un asset oficial validado.

| Portada ID | Ruta base observada de imagen Unsplash |
| --- | --- |
| `ATY5A`, `dW48j` | https://images.unsplash.com/photo-1655715890182-f3673285a06c |
| `p1n9F`, `lIlLg` | https://images.unsplash.com/photo-1741291875703-a2353c2c2946 |
| `elYND` | https://images.unsplash.com/photo-1783973566204-10c2519d78cb |
| `S3exoW` | https://images.unsplash.com/photo-1659081439066-1097244cb710 |

Estos fills son `type:image`, `mode:fill`, enabled=true; las URLs leídas incluyen parámetros crop=entropy, cs=tinysrgb, fit=max, fm=jpg, ixid, ixlib=rb-4.1.0, q=80, w=1080. Se conserva aquí la ruta base para identificación, no como descarga o exportación. Reutilizar medios oficiales editables del backend cuando se implemente; las imágenes son referencias visuales. No se verificó disponibilidad externa ni licencia adicional.

## Detalle: evidencia disponible y límites

`LXU5I` es explícitamente Detalle de actualidad — Desktop. `a9jkwS` cabecera 1440 × 550; `EkNVY` «LOGRO · 27 AGOSTO 2026 · CONTENIDO DEMO»; `SBHJi` título 64; `yffaZ` ubicación y fecha administrables. `JXeNu` imagen principal 1440 × 600. `m7MbC` sección editorial 1440 × 650; `qUuQ6` columna de 760 px; `K7QOq` entradilla 30; `HZAAH` cuerpo 17 que menciona encabezados, listas, enlaces, citas, imágenes y bloques enriquecidos. `O8Thcf` lateral 250 × 320; `lNsLe` «Facebook / WhatsApp / Copiar enlace» como líneas de texto. `X4DRT` galería 1440 × 490 con `QLx4F`, `JINzf`, `w1QeZD`, imágenes 380 × 330. Es una referencia reutilizable propuesta para publicaciones, no evidencia de que Selecciones tenga ese detalle aprobado o conectado. No se observó lightbox ni estado de ampliación.

## Propuesta de implementación, distinta de lo observado

1. Reutilizar backend Selecciones ya archivado y contenido WordPress/Gutenberg editable conforme al contexto delegado; este informe no inspeccionó ni verificó ese backend. Resolver destinos usando sus enlaces reales, sin inventar slugs ni datos deportivos. Asociar conceptualmente «Balonmano Piso» con el listado Piso y «Balonmano Playa» con el archivo Playa; la correspondencia se infiere de etiquetas/títulos, no de un prototipo conectado.
2. Desktop: botón de disclosure para Selecciones con `aria-expanded` y `aria-controls`, sublista de enlaces normales. Abrir/cerrar con clic, Enter o Espacio; Tab recorre enlaces; Escape cierra y devuelve foco al disparador. Cerrar al salir de la navegación o clic exterior. Hover podría complementar, sin ser el único acceso. No aplicar roles de menú de aplicación por defecto.
3. Móvil: botón de apertura/cierre con nombre accesible y estado; Selecciones como disclosure independiente dentro del panel. Touch abre la sección sin navegar; tocar Piso/Playa navega. Mantener menú en flujo como el render observado, salvo decisión posterior explícita. Escape cierra primero la sección o el panel según foco, restaurando foco al disparador pertinente; ocultos no deben recibir foco. No proponer focus trap salvo que se decida convertirlo en modal.
4. Las medidas móviles observadas (cabecera 30, opciones 34 y cerrar 38 px) requieren aumentar las áreas interactivas a una propuesta de 44 px como mínimo manteniendo jerarquía; no atribuir ese cambio al mockup. Definir indicador de foco visible, estado actual coherente y funcionamiento con teclado y touch en implementación.
5. Adaptar filas a columna en pantallas estrechas, portada fluida, títulos y metadatos con salto de línea; conservar orden portada/tipo/título/datos/acción. Es una propuesta responsive porque no existe listado móvil completo. Breakpoint, alturas móviles y anchos tablet quedan por definir a partir del frontend existente.
6. «VER PUBLICACIÓN» debe usar el permalink real. Puede reutilizarse el patrón editorial de Actualidad para el detalle de Selecciones si lo permite el backend, con bloques Gutenberg y galería editable, sin inventar contenido. Contadores deben derivarse del contenido. No convertir los tres ejemplos del mockup en requisito de cantidad fija.

## Gaps y riesgos delimitados

- No hay href ni flows explícitos: destinos y transición listado-detalle deben resolverse contra backend, no bloquear la reproducción visual del menú y listados.
- Faltan detalle específico, detalle móvil, archivo móvil, listado móvil completo, estado vacío y paginación de Selecciones. Reutilización editorial y adaptación responsive son propuestas pendientes de concretar; no falta toda la evidencia de diseño.
- Cabeceras desktop inconsistentes (chevron y resaltado); menú móvil abierto y cerrado usan alturas/logos distintos. Homogeneizar con el componente existente es decisión de implementación, sin fingir estados equivalentes diseñados.
- Fotos Unsplash, logos placeholder y resultados demo requieren medios/datos reales editables. El hero Piso conserva crédito de foto, pero su fill actual es negro.
- No se observaron estados de foco, comportamiento keyboard/touch, cierre exterior, animación, sticky header, apertura en otra pestaña ni galería modal. La propuesta anterior completa estos vacíos sin presentarlos como evidencia.

Este input no modifica runtime, base de datos ni uploads; no procede content-sync. No se ejecutaron código, pruebas, builds u otras fases, ni se modificaron status o execution-log. Próximo uso recomendado: consolidación del orquestador y PROPOSE. Verificación futura centrada en alcance afectado y comprobaciones pendientes o invalidadas, sin full gate por defecto.
