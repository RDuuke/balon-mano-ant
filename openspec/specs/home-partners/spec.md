# Especificación: Aliados oficiales del Home

## Requisitos

### Requisito: Contenido exclusivo de logos
El sistema MUST presentar en «Aliados Oficiales» solo logos, sin artículos, enlaces, texto visible ni controles de reproducción o velocidad.

#### Escenario: Logos disponibles
- DADO que existen aliados aptos para mostrarse
- CUANDO se presenta la sección
- ENTONCES cada elemento es un logo sin enlace, texto ni control

#### Escenario: Límite de contenido
- DADO que un aliado conserva descripción o URL legada
- CUANDO se presenta su logo
- ENTONCES esos datos no aparecen ni convierten la imagen en enlace

#### Escenario: Datos no representables
- DADO que un registro no aporta una imagen válida
- CUANDO se construye la sección
- ENTONCES el registro se omite sin mostrar contenido sustituto ni roto

### Requisito: Movimiento marquee accesible
El sistema MUST desplazar los logos automática y continuamente a velocidad fija, sin pausa manual; MUST mostrarlos estáticos ante movimiento reducido.

#### Escenario: Movimiento estándar
- DADO que no se solicita movimiento reducido
- CUANDO la sección está visible
- ENTONCES los logos avanzan continuamente con una velocidad fija

#### Escenario: Movimiento reducido
- DADO que el dispositivo comunica movimiento reducido
- CUANDO se presenta la sección
- ENTONCES todos los logos disponibles permanecen visibles sin animación

#### Escenario: Interacción inexistente
- DADO que la marquee está en movimiento
- CUANDO la persona navega con puntero, teclado o tacto
- ENTONCES no existe pausa ni ajuste de velocidad

### Requisito: Selección y orden estable
El sistema MUST mostrar como máximo 12 aliados publicados que tengan título e imagen válidos, ordenados primero por `menu_order` y con desempate estable por título.

#### Escenario: Colección válida
- DADO que hay hasta 12 aliados publicados y válidos
- CUANDO se carga la sección
- ENTONCES aparecen todos en el orden definido

#### Escenario: Más de doce aliados
- DADO que hay más de 12 aliados publicados y válidos
- CUANDO se carga la sección
- ENTONCES aparecen únicamente los primeros 12 según el orden estable

#### Escenario: Publicación inválida
- DADO que un aliado está en borrador o carece de título o imagen válida
- CUANDO se obtiene la colección pública
- ENTONCES dicho aliado no aparece y los restantes conservan su orden

### Requisito: Administración de aliados
El sistema SHALL administrar `labm_aliado` en «Aliados Oficiales» usando título como nombre y texto alternativo, imagen destacada como logo y `menu_order` como posición; MUST impedir publicar sin título o logo.

#### Escenario: Alta válida
- DADO que una persona autorizada aporta título, imagen y orden
- CUANDO publica el aliado
- ENTONCES el registro queda disponible para la colección pública

#### Escenario: Ciclo editorial
- DADO que existe un aliado
- CUANDO una persona autorizada lo edita, envía a papelera, restaura o elimina
- ENTONCES la acción administrativa solicitada se refleja en su disponibilidad

#### Escenario: Publicación incompleta
- DADO que falta el título o el logo
- CUANDO se intenta publicar el aliado
- ENTONCES el sistema rechaza la publicación e informa qué dato falta

### Requisito: Compatibilidad y adaptación visual
El sistema MUST conservar datos legados, adaptar la sección entre 320 y 1440 píxeles sin deformar logos y proporcionar logos demo ficticios y originales.

#### Escenario: Distintos tamaños de pantalla
- DADO un ancho entre 320 y 1440 píxeles
- CUANDO se presenta la sección
- ENTONCES los logos mantienen su proporción y no provocan desbordamiento horizontal

#### Escenario: Datos legados
- DADO que un aliado contiene URL, cuerpo o extracto legado
- CUANDO se guarda o presenta el aliado
- ENTONCES esos datos se ignoran en la sección y permanecen almacenados

#### Escenario: Recursos demo no válidos
- DADO que un logo demo coincide con una marca real o no puede cargarse
- CUANDO se valida el contenido de demostración
- ENTONCES el recurso se rechaza y no aparece en la colección pública
