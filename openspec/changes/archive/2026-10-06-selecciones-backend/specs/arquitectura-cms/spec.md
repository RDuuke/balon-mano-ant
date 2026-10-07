# Delta: Arquitectura CMS — backend de Selecciones

## ADDED Requirements

### Requirement: Reparación de permisos editoriales de Selecciones
El sistema MUST asegurar que Editores y Administradores puedan crear, editar y publicar fichas de Selecciones, incluso cuando la instalación figure como actualizada pero le falten permisos de este dominio. La reparación MUST conservar permisos ajenos y MUST ser idempotente.

#### Scenario: Instalación actualizada con permisos incompletos
- DADO un Editor y un Administrador sin permisos de Selecciones y una instalación marcada como actualizada
- CUANDO se comprueba la disponibilidad de permisos editoriales
- ENTONCES ambos recuperan los permisos de crear, editar y publicar Selecciones y conservan sus demás permisos.

#### Scenario: Comprobación repetida
- DADO roles editoriales con permisos completos de Selecciones
- CUANDO se repite la comprobación
- ENTONCES sus permisos permanecen iguales y no se crean concesiones adicionales.

#### Scenario: Usuario sin rol editorial
- DADO un usuario sin autorización editorial
- CUANDO intenta crear, editar o publicar una Selección después de la reparación
- ENTONCES la operación se rechaza y sus permisos no aumentan.

### Requirement: Contrato de fichas y clasificación
Las fichas de Selecciones SHALL admitir título, contenido, extracto e imagen destacada, y SHALL poder clasificarse por modalidad y categoría. La API REST existente SHALL permitir consultar sus datos y clasificaciones; nuevas clasificaciones MUST poder gestionarse sin cambiar código.

#### Scenario: Ficha clasificada
- DADO una ficha publicada con título, contenido, extracto, imagen, modalidad y categoría
- CUANDO un consumidor consulta su representación REST
- ENTONCES recibe los datos editoriales y referencias a ambas clasificaciones.

#### Scenario: Ficha sin clasificación y clasificación nueva
- DADO una ficha sin términos asignados y un administrador autorizado
- CUANDO consulta la ficha y después crea y asigna una modalidad o categoría válida
- ENTONCES la primera consulta devuelve clasificaciones vacías y la siguiente refleja la nueva asignación.

#### Scenario: Cambio de clasificación no autorizado
- DADO un visitante sin permisos editoriales
- CUANDO intenta crear términos o modificar clasificaciones de una ficha mediante REST
- ENTONCES recibe una respuesta de rechazo y los términos y asignaciones permanecen iguales.

### Requirement: Metadato público saneado y autorizado
El detalle de modalidad SHALL estar disponible como texto en la representación REST. Su escritura MUST requerir autorización para editar la ficha y MUST sanear entradas para impedir conservar marcado ejecutable.

#### Scenario: Edición autorizada
- DADO un usuario autorizado para editar una ficha
- CUANDO guarda un detalle de modalidad válido y consulta la ficha por REST
- ENTONCES recibe el texto guardado.

#### Scenario: Texto con marcado y valor vacío
- DADO un usuario autorizado para editar una ficha
- CUANDO guarda texto con marcado ejecutable y después un texto vacío
- ENTONCES la primera lectura devuelve texto saneado sin marcado ejecutable y la segunda devuelve un valor vacío.

#### Scenario: Escritura sin autorización
- DADO un usuario sin permiso para editar una ficha con detalle de modalidad existente
- CUANDO intenta actualizar ese detalle por REST
- ENTONCES se rechaza la escritura y permanece el valor anterior.

### Requirement: Privacidad de fichas no publicadas
Las consultas públicas de Selecciones MUST exponer únicamente fichas publicadas y MUST excluir borradores y fichas privadas, también al consultar un identificador directamente por REST.

#### Scenario: Consulta pública
- DADO una ficha publicada, una privada y una en borrador
- CUANDO un visitante consulta la colección pública
- ENTONCES recibe únicamente la ficha publicada.

#### Scenario: Ninguna ficha publicada
- DADO únicamente fichas privadas o en borrador
- CUANDO un visitante consulta la colección pública
- ENTONCES recibe una colección vacía sin contenido restringido.

#### Scenario: Acceso directo a contenido restringido
- DADO una ficha privada o en borrador y un visitante sin autorización
- CUANDO consulta directamente su identificador por REST
- ENTONCES recibe una respuesta de rechazo sin título, contenido ni metadatos de la ficha.
