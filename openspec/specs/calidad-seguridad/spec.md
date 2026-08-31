# Especificación completa: Calidad y seguridad

## Requisitos

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

### Requirement: Gate automatizado del primer incremento
El primer incremento MUST producir evidencia reproducible de smoke de infraestructura, activación independiente, fixtures, pruebas PHP/WordPress y validaciones de estándares y análisis estático.

#### Scenario: Gate satisfactorio
- DADO un entorno limpio configurado desde el ejemplo
- CUANDO se ejecuta el conjunto documentado del primer incremento
- ENTONCES todas las comprobaciones pasan y su salida identifica versión, comando y resultado.

#### Scenario: Ejecución parcial
- DADO que una herramienta no está disponible
- CUANDO se intenta ejecutar el gate
- ENTONCES la comprobación queda como no ejecutada, con causa y pasos exactos de reproducción, nunca como aprobada.

#### Scenario: Fallo detectado
- DADO que una comprobación devuelve error
- CUANDO se evalúa el gate
- ENTONCES la entrega no se marca completa y la evidencia identifica el caso fallido.

### Requirement: Pruebas funcionales del sitio completo
La verificación MUST cubrir permisos, consultas, publicación y rechazo de PDF, contacto, rutas públicas, navegación, filtros, paginación y estados de error mediante pruebas unitarias, integración y E2E según corresponda.

#### Scenario: Flujos críticos válidos
- DADO fixtures ficticios representativos
- CUANDO se ejecutan los flujos automatizados críticos
- ENTONCES cada operación autorizada produce el resultado público y administrativo esperado.

#### Scenario: Límites y estados vacíos
- DADO datos ausentes, paginados o en valores límite
- CUANDO se ejecutan consultas y formularios
- ENTONCES los estados de borde son deterministas, usables y no filtran contenido restringido.

#### Scenario: Entrada maliciosa o no autorizada
- DADO entrada manipulada, archivo inválido o permisos insuficientes
- CUANDO se intenta una operación protegida
- ENTONCES se rechaza sin cambios indebidos, trazas públicas ni datos sensibles expuestos.

### Requirement: Alcance verificable de calidad visual y accesibilidad
La verificación de este cambio MUST conservar evidencia de las comprobaciones responsive y de accesibilidad realmente ejecutadas a 320, 768, 1024 y 1440 px; SHALL registrar Lighthouse, sus metas de rendimiento y SEO, y la garantía integral WCAG 2.2 AA como diferidos a un cambio futuro; MUST NOT presentarlos como COMPLIANT.

#### Scenario: Evidencia dentro del alcance
- DADO vistas representativas y comprobaciones ejecutadas en los anchos objetivo
- CUANDO se revisa la evidencia del cambio
- ENTONCES el informe identifica vistas, anchos, comprobaciones y resultados sin ampliar la conclusión más allá de lo demostrado.

#### Scenario: Auditorías diferidas
- DADO que Lighthouse, la validación SEO o la auditoría integral WCAG 2.2 AA no forman parte de este cambio
- CUANDO se evalúa su cierre
- ENTONCES se registran como alcance diferido con seguimiento futuro y no como resultados aprobados ni requisitos de cierre actuales.

#### Scenario: Declaración de cumplimiento no sustentada
- DADO un informe que afirma cumplimiento Lighthouse, SEO o WCAG 2.2 AA sin evidencia integral
- CUANDO se valida el resultado del cambio
- ENTONCES el gate falla hasta retirar la afirmación y declarar correctamente el alcance diferido.

### Requirement: Protección de información y procedencia
El sistema MUST aplicar mínimo privilegio, validación de entradas, escape de salidas y protección de acciones mutables; MUST NOT publicar secretos ni datos personales de los PDF sin revisión y autorización explícitas.

#### Scenario: Operación protegida
- DADO un usuario autorizado y una solicitud válida
- CUANDO ejecuta una acción mutable
- ENTONCES solo se modifican los recursos permitidos y la salida pública queda sanitizada.

#### Scenario: PDF pendiente de revisión
- DADO un archivo de referencia presente en `docs/`
- CUANDO se prepara contenido, activos o fixtures
- ENTONCES permanece sin publicar y no se extraen automáticamente datos personales.

#### Scenario: Intento manipulado
- DADO una solicitud sin autorización, integridad o formato válido
- CUANDO alcanza una operación administrativa o pública
- ENTONCES se rechaza sin revelar versiones, trazas, secretos ni datos personales.
