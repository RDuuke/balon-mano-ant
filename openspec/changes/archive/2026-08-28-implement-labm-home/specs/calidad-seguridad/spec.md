# Delta para Calidad y seguridad

## MODIFIED Requirements

### Requirement: Verificación automatizada de la portada
La verificación MUST cubrir composición, contenido público, permisos editoriales, estados vacíos, controles de slider y aliados, ausencia de secciones retiradas de Inicio y conservación de las rutas de Selecciones.

#### Scenario: Flujos críticos válidos
- DADO fixtures ficticios representativos
- CUANDO se ejecutan las pruebas PHP y de navegador de la portada
- ENTONCES las secciones, controles, orden y destinos entregan el resultado esperado.

#### Scenario: Estados vacíos y movimiento reducido
- DADO datos ausentes o preferencia de movimiento reducido
- CUANDO se ejecutan los recorridos automatizados
- ENTONCES no hay controles vacíos, movimiento forzado, huecos ni desbordamiento horizontal.

#### Scenario: Regresión funcional o accesible
- DADO una sección retirada visible, una ruta de Selecciones ausente o un control inaccesible
- CUANDO se evalúa el gate
- ENTONCES la verificación falla e identifica el contrato incumplido.

### Requirement: Protección del contenido de portada
Las operaciones editoriales de slides y aliados MUST aplicar autorización y validación, y toda salida pública MUST excluir contenido no publicado y datos no seguros.

#### Scenario: Contenido autorizado
- DADO un usuario autorizado y valores válidos
- CUANDO guarda y publica contenido de portada
- ENTONCES solo se modifican los recursos permitidos y la salida pública queda saneada.

#### Scenario: Valores límite
- DADO textos largos, enlaces opcionales o medios ausentes
- CUANDO se validan y presentan los datos
- ENTONCES se aplica una respuesta determinista sin romper la interfaz ni inventar información oficial.

#### Scenario: Solicitud manipulada
- DADO permisos insuficientes o una entrada con formato no permitido
- CUANDO se intenta una operación mutable
- ENTONCES se rechaza sin cambios indebidos, trazas públicas ni exposición de contenido privado.
