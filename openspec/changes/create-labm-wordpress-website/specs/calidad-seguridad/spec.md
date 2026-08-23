# Especificación completa: Calidad y seguridad

## Requisitos

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

### Requirement: Auditoría visual, accesible y de rendimiento
El sitio completo MUST auditar vistas representativas a 320, 768, 1024 y 1440 px; SHALL cumplir WCAG 2.2 AA y SHALL alcanzar Lighthouse Performance >= 85 y Accessibility, Best Practices y SEO >= 90 bajo condiciones documentadas.

#### Scenario: Auditoría conforme
- DADO contenido y condiciones de medición controladas
- CUANDO se ejecutan auditorías automáticas y revisión manual
- ENTONCES se alcanzan los umbrales y se conserva evidencia por vista y ancho aplicable.

#### Scenario: Variación controlada
- DADO una dependencia externa deshabilitada o simulada conforme al protocolo
- CUANDO se repite Lighthouse
- ENTONCES la evidencia declara la condición y permite comparar resultados reproducibles.

#### Scenario: Umbral incumplido
- DADO una métrica inferior al objetivo o una violación WCAG 2.2 AA
- CUANDO se evalúa la liberación
- ENTONCES el gate falla e identifica URL, condición y hallazgo.

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
