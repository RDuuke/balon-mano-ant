# Delta para Arquitectura CMS

## MODIFIED Requirements

### Requirement: Edición sin código de la portada
El sistema SHALL permitir a usuarios autorizados gestionar slides y aliados, y reutilizar contenido publicado de clubes y actualidad en Inicio, sin editar archivos del tema.

#### Scenario: Publicación autorizada
- DADO un editor con capacidades suficientes y datos válidos
- CUANDO publica un slide o aliado
- ENTONCES el elemento queda disponible para la portada con sus campos públicos.

#### Scenario: Borrador o lista vacía
- DADO elementos en borrador o ningún elemento publicable
- CUANDO un visitante abre Inicio
- ENTONCES esos elementos no aparecen y la portada conserva un estado coherente.

#### Scenario: Operación no autorizada
- DADO un usuario sin la capacidad necesaria
- CUANDO intenta crear, modificar, publicar o eliminar un elemento
- ENTONCES la operación se rechaza sin modificar datos ni exponer detalles sensibles.

### Requirement: Persistencia independiente de presentación
Los slides y aliados SHALL permanecer disponibles al cambiar de tema, y la portada MUST degradarse de forma segura cuando el componente de dominio no esté activo.

#### Scenario: Cambio de tema
- DADO contenido de portada publicado
- CUANDO se activa otra presentación compatible
- ENTONCES los datos, estados editoriales y permisos permanecen almacenados.

#### Scenario: Componente de dominio inactivo
- DADO que el componente persistente no está disponible
- CUANDO se renderiza la portada
- ENTONCES la página continúa accesible y omite las secciones dependientes sin error fatal.

#### Scenario: Dato inválido
- DADO un identificador, destino o medio que incumple las reglas editoriales
- CUANDO se intenta guardar o publicar
- ENTONCES se rechaza o normaliza con un mensaje verificable y sin corrupción de datos.
