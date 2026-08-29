# Exploración: implementación del Home LABM

## Resumen

El cambio implementará la portada aprobada sobre un tema WordPress de bloques (`labm`) y conservará la funcionalidad de dominio en `labm-core`. La portada actual es un patrón Gutenberg mínimo y todavía muestra modalidades Piso/Playa dentro del Home; no existen aún un slider, contenido de portada completo ni la sección de Aliados Oficiales. Las modalidades seguirán disponibles en la ruta de Selecciones: la eliminación solicitada aplica únicamente al Home.

## Solicitud y dominio

- Tipo de cambio: ampliación de experiencia pública y presentación del tema.
- Dominio principal: plantilla/patrón de Inicio, partes de encabezado y pie, estilos y comportamiento frontend.
- Dominios relacionados: CPT y taxonomías existentes para actualidad, selecciones y clubes; pruebas E2E y PHPUnit.
- Fuente visual: `design/labm-wordpress-mockup.pen`, inspeccionada exclusivamente mediante MCP de Pencil.

## Estado actual encontrado

### Tema y plantilla

- `wp-content/themes/labm/templates/front-page.html` compone `header`, un `<main>` restringido con el patrón `labm/inicio` y `footer`.
- `wp-content/themes/labm/patterns/inicio.php` solo renderiza un hero textual, un botón de actualidad y dos columnas de modalidades. El texto del hero menciona explícitamente Piso y Playa.
- `wp-content/themes/labm/parts/header.html` tiene título del sitio y navegación Gutenberg con Inicio, Nosotros, Actualidad, Selecciones, Documentos y Contacto. No hay barra institucional superior ni CTA Vincúlate.
- `wp-content/themes/labm/parts/footer.html` solo muestra el texto demostrativo `LABM — sitio demostrativo.`.
- `wp-content/themes/labm/functions.php` encola `style.css`, registra shortcodes de listados y mantiene el helper `labm_theme_home_sections`, cuyo conjunto actual es `modalidades`, `actualidad` y `contacto`. No hay lógica de slider, clubes, aliados o portada dinámica.
- `wp-content/themes/labm/style.css` usa tokens y colores antiguos azul/amarillo (`#123a66`, `#f4c542` indirectamente), hero azul y sombra azulada. Sí contiene foco visible y una regla `prefers-reduced-motion`.
- `wp-content/themes/labm/theme.json` declara la paleta azul LABM, amarillo LABM y blanco; no refleja la paleta aprobada del documento (`#AECD25`, `#789614`, negro, blanco, `#F3F6E8`, `#202020`).

### Plugin y modelo de contenido

- `wp-content/plugins/labm-core/labm-core.php` carga el dominio persistente y el comando de fixtures.
- `includes/class-labm-domain.php` registra CPT para actualidad, selecciones, clubes, integrantes, horarios y documentos; las taxonomías de modalidad y categoría siguen siendo necesarias para las páginas de Selecciones.
- La existencia del CPT `labm_horario` no implica que deba eliminarse: el requisito aprobado elimina únicamente su bloque de presentación en el Home.
- No se encontró un CPT o metadato de aliados oficiales. Será necesario decidir si la primera entrega usa un bloque/patrón editable o agrega un modelo persistente en el plugin; la necesidad de administración sin código favorece una solución persistente y desacoplada del tema.

### Pruebas y restricciones

- `tests/e2e/home.spec.ts` comprueba título, `main h1` y axe; deberá ampliarse para verificar el slider y la ausencia de los bloques eliminados.
- `tests/e2e/public-experience.spec.ts` valida navegación, rutas de Selecciones Piso/Playa, responsive, axe y movimiento reducido. Las rutas de Selecciones deben conservarse.
- `tests/php/PublicExperienceTest.php` verifica patrones/plantillas, consultas publicadas y filtros Piso/Playa. No debe reinterpretarse como requisito de eliminar modalidades del dominio.
- `tests/php/FrontendTokensTest.php` protege tokens de espaciado, radio, foco y movimiento reducido; los cambios visuales deberán mantener esos contratos o actualizar los tests de forma coherente.
- `tests/php/VerifyCorrectivesTest.php` todavía espera que `labm_theme_home_sections` devuelva `modalidades` cuando está configurado. Ese helper parece legado respecto al Home aprobado y debe revisarse con cuidado antes de eliminar o redefinirlo.
- Las pruebas se ejecutan mediante la infraestructura existente de PHPUnit/Playwright y no se ejecutaron durante esta exploración.

## Inspección del mockup aprobado

El archivo `.pen` fue leído mediante MCP de Pencil, sin accederlo con lectura de texto. La pantalla `Inicio — Desktop` (`1440 × 4118`) contiene, en orden:

1. Barra institucional.
2. Navegación principal.
3. Slider principal (`1440 × 620`).
4. Presentación de la Liga.
5. Clubes asociados.
6. Evento destacado.
7. Últimas noticias.
8. CTA de vinculación.
9. Aliados Oficiales (`1440 × 330`).
10. Footer.

El mockup incluye también una pantalla móvil `Inicio — Mobile` y componentes reutilizables para botón, tarjeta de noticia, filtro y logo de aliado. La sección Aliados Oficiales documenta una pausa del movimiento y una nota para `prefers-reduced-motion`. El Home inspeccionado no contiene Horarios, Escenarios deportivos ni una sección separada de Piso/Playa.

## Requisitos de implementación derivados

- El slider debe quedar inmediatamente debajo del menú principal, ser administrable, tener controles y estados accesibles, y respetar movimiento reducido.
- El patrón de Inicio no debe renderizar Piso/Playa, horarios ni escenarios; tampoco debe dejar espacios vacíos.
- La navegación y las plantillas de Selecciones deben seguir exponiendo Piso y Playa fuera del Home.
- Debe agregarse la sección Aliados Oficiales antes del footer, con logos en carrusel continuo de derecha a izquierda. La implementación debe evitar duplicación perceptible al reiniciar el ciclo y debe ofrecer pausa/foco accesible; con movimiento reducido debe quedar estática.
- El Home debe incorporar presentación, clubes, evento destacado, actualidad, CTA y el nuevo footer visual del mockup, usando contenido demo inequívoco mientras no exista contenido editorial real.
- La paleta y tipografías del mockup deben trasladarse a `theme.json`/CSS sin usar texto blanco pequeño sobre verde lima cuando no alcance contraste.
- La estructura debe conservar el patrón de bloques, las cadenas traducibles, escape de salida y compatibilidad cuando el plugin no esté activo.

## Opciones consideradas

### Opción A: patrón estático ampliado

Ampliar `patterns/inicio.php` y `parts/footer.html` con bloques Gutenberg y datos demo. Es la opción de menor complejidad, pero no satisface bien la administración sin código de slider, clubes y aliados, ni permite consultar contenido editorial real sin editar el patrón.

### Opción B: bloques/patrones con datos de WordPress y comportamiento ligero del tema

Mantener la composición en bloques, usar consultas de CPT para actualidad y clubes, añadir un modelo editable para slides/aliados en `labm-core` y un script pequeño del tema para controles/carruseles. Es la opción recomendada porque separa contenido persistente y presentación, reutiliza el dominio existente y permite pruebas de publicación, ausencia y filtros.

### Opción C: dependencia de un plugin externo de slider/carousel

Reduce código propio, pero añade dependencia de mantenimiento, puede introducir marcado inaccesible y contradice la preferencia del proyecto por Gutenberg y dependencias frontend mínimas. No se recomienda.

## Archivos candidatos a afectar en fases posteriores

- `wp-content/themes/labm/patterns/inicio.php`
- `wp-content/themes/labm/parts/header.html`
- `wp-content/themes/labm/parts/footer.html`
- `wp-content/themes/labm/functions.php`
- `wp-content/themes/labm/style.css`
- `wp-content/themes/labm/theme.json`
- `wp-content/plugins/labm-core/labm-core.php`
- `wp-content/plugins/labm-core/includes/class-labm-domain.php` y posiblemente un módulo nuevo para slides/aliados
- `tests/e2e/home.spec.ts`, `tests/e2e/public-experience.spec.ts`
- tests PHP de experiencia, tokens, fixtures y dominio si se añade contenido persistente

## Riesgos y preguntas para PROPOSE

- No hay actualmente un modelo de contenido para slides o aliados; debe definirse su esquema, capacidades, fixtures y fallback cuando no haya publicaciones.
- El carrusel infinito requiere duplicar visualmente una secuencia o usar una animación equivalente; hay que evitar desborde horizontal, foco en clones y problemas con lectores de pantalla.
- La configuración de bloques del encabezado puede generar markup y controles móviles propios de WordPress; los tests deben validar nombres accesibles sin depender de una estructura frágil.
- El mockup usa contenido demo y placeholders de logo; la implementación debe conservar la distinción entre demo y contenido oficial.
- La paleta actual en `theme.json` y CSS contradice la fuente visual aprobada, por lo que el cambio afecta tokens y puede impactar capturas o aserciones existentes.
- El helper `labm_theme_home_sections` y sus tests representan una versión anterior del Home; se requiere decidir si se mantiene como compatibilidad o se reemplaza por una configuración alineada con el nuevo orden.

## Recomendación

Avanzar a PROPOSE con la Opción B. Definir primero el modelo editorial mínimo para slides y aliados, el contrato de renderizado del Home y los estados accesibles de ambos carruseles. Mantener intactos los CPT, taxonomía y filtros de Selecciones Piso/Playa; retirar únicamente su presentación de la portada.
