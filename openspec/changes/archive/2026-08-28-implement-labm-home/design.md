# Diseño: Implementar el Home LABM

## Enfoque técnico

`labm-core` ampliará el dominio editorial con slides y aliados; clubes y actualidad seguirán usando sus tipos existentes. El tema compondrá la portada desde el patrón `labm/inicio`, consultará exclusivamente publicaciones públicas mediante helpers pequeños y ofrecerá estados vacíos seguros. CSS trasladará los tokens del mockup y JavaScript progresivo controlará slider y marquee; sin JavaScript, todo contenido seguirá visible y navegable.

## Decisiones de arquitectura

### Decisión: Contenido de slider y aliados en el plugin

| Opción | Tradeoff | Decisión |
|---|---|---|
| CPT editoriales en `labm-core` | Más registro y permisos; persisten al cambiar de tema | Elegida |
| Datos fijos en el patrón | Simple; exige código para editar y se pierde con el tema | Descartada |
| Plugin externo | Menos código propio; dependencia y marcado no controlado | Descartada |

Razón: replica el patrón actual de contenido, capacidades, REST y fixtures, y cumple edición sin código. `labm_slide` y `labm_aliado` serán administrables pero no tendrán archivo público. Usarán título, extracto/contenido, imagen destacada, orden y metadatos saneados para destino y CTA.

### Decisión: Renderizado dinámico desde el tema

| Opción | Tradeoff | Decisión |
|---|---|---|
| Helpers de render dentro del patrón existente | Compatible con la arquitectura actual; requiere escape cuidadoso | Elegida |
| Bloques dinámicos nuevos | Mejor editor visual; eleva el alcance y pruebas | Descartada |
| HTML estático | Predecible; no refleja publicaciones | Descartada |

Razón: `patterns/inicio.php` ya es la entrada de Inicio y `functions.php` concentra consultas/render público. Cada sección tendrá una consulta acotada a `publish`; si el plugin está inactivo o no hay resultados, se omitirá sin huecos. El `main` de portada permitirá ancho completo y cada sección limitará internamente su contenido.

### Decisión: Mejora progresiva para movimiento

| Opción | Tradeoff | Decisión |
|---|---|---|
| CSS + JavaScript sin dependencia | Control accesible y carga pequeña; exige contratos propios | Elegida |
| Solo CSS | No resuelve estado, pausa y controles del slider | Descartada |
| Librería de carrusel | Funciones listas; dependencia innecesaria | Descartada |

Razón: el slider expondrá anterior, siguiente, indicadores, pausa y anuncio de estado. El marquee conservará una lista semántica original; una copia exclusivamente visual será `aria-hidden` e inerte. Foco, pausa solicitada y `prefers-reduced-motion` detendrán toda animación.

### Decisión: Compatibilidad explícita con Selecciones

| Opción | Tradeoff | Decisión |
|---|---|---|
| Retirar modalidades solo de Inicio | Mantiene contratos y rutas existentes | Elegida |
| Eliminar CPT o taxonomía | Simplifica portada; rompe dominio y alcance | Descartada |

Razón: se reemplazará el orden legado de `labm_theme_home_sections`, pero no se modificarán `labm_seleccion`, `labm_modalidad`, sus consultas ni sus rutas.

## Flujo de datos

```text
Editor -> labm-core (CPT/meta/capacidades) -> WordPress
                                              |
Visitante -> front-page -> patrón Inicio -> consultas publish
                                      -> HTML semántico -> CSS/JS progresivo
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Crear | Registrar contenido, metadatos, validación y capacidades de slides/aliados. |
| `wp-content/plugins/labm-core/labm-core.php` | Modificar | Cargar el módulo de portada. |
| `wp-content/plugins/labm-core/includes/class-labm-domain.php` | Modificar | Integrar capacidades/versionado de los tipos nuevos. |
| `wp-content/plugins/labm-core/includes/class-labm-fixtures-command.php` | Modificar | Añadir fixtures ficticios idempotentes. |
| `wp-content/themes/labm/{functions.php,patterns/inicio.php,templates/front-page.html}` | Modificar | Consultar, renderizar y componer las secciones. |
| `wp-content/themes/labm/parts/{header,footer}.html` | Modificar | Aplicar barra, navegación, CTA y pie aprobados. |
| `wp-content/themes/labm/{theme.json,style.css}` | Modificar | Tokens, responsive, foco y estados sin movimiento. |
| `wp-content/themes/labm/assets/home.js` | Crear | Controles, pausa y estado de slider/marquee. |
| `tests/php/`, `tests/e2e/` | Modificar | Cubrir dominio, composición, seguridad, responsive y accesibilidad. |

## Interfaces y contratos

- Tipos: `labm_slide` y `labm_aliado`, editables por Administrador/Editor, visibles por REST y no consultables mediante archivo público.
- Metadatos REST: URLs saneadas y texto de CTA; autorización mediante capacidad `edit_post`.
- HTML: regiones identificables `data-labm-slider` y `data-labm-allies`, botones nativos con nombres accesibles y contenido duplicado excluido del árbol accesible.
- Consultas: `post_status=publish`, orden editorial estable y límites explícitos.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| PHP | Registro, permisos, saneado, consultas, fallbacks y secciones | PHPUnit/WordPress con fixtures públicos, privados e inválidos. |
| Estática | PHP, WPCS, PHPStan y tokens | Gate existente del repositorio. |
| E2E | Orden, ausencia Piso/Playa/horarios/escenarios, rutas conservadas, teclado, pausa y anchos objetivo | Playwright, axe y emulación de movimiento reducido. |

## Migración y despliegue

No se transforma contenido existente. Se incrementará la versión de capacidades para conceder las nuevas capacidades a Administrador y Editor; los fixtures solo administrarán entradas marcadas como ficticias. El despliegue es reversible retirando módulo, activos y composición sin borrar publicaciones creadas.

## Preguntas abiertas

Ninguna bloqueante; textos, medios y destinos finales podrán reemplazar los fixtures desde WordPress.
