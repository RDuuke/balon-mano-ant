# Delta: experiencia pública

## MODIFIED Requirements

### Requirement: Filtros accesibles y composición responsive
La sección de integrantes MUST mostrar únicamente el grupo seleccionado como contenido perceptible y operable, identificar su filtro activo y funcionar sin JavaScript. MUST excluir datos restringidos; la presencia de contenido público no seleccionado en la página MAY variar sin alterar estas garantías.

#### Scenario: Grupo publicado seleccionado
- DADO integrantes públicos de varios grupos
- CUANDO se activa Entrenadores con o sin JavaScript
- ENTONCES solo sus integrantes resultan perceptibles y operables y el filtro activo es identificable.

#### Scenario: Grupo válido vacío
- DADO un grupo sin integrantes publicables
- CUANDO se selecciona
- ENTONCES aparece un estado vacío comprensible y los demás filtros permanecen operables.

#### Scenario: Filtro inválido o contenido restringido
- DADO un filtro desconocido e integrantes privados
- CUANDO una persona anónima consulta la vista
- ENTONCES recibe la colección predeterminada sin datos restringidos ni errores técnicos.

## ADDED Requirements

### Requirement: Contraste verificable en las vistas afectadas
Las vistas públicas afectadas MUST conservar foco perceptible y contraste WCAG 2.2 AA: al menos 4.5:1 para texto normal, 3:1 para texto grande y componentes sujetos al criterio no textual. La comprobación MUST incluir los fondos y estados usados a 320, 768, 1024, 1200 y 1440 px; MUST NOT atribuir cumplimiento integral al sitio sin auditoría integral.

#### Scenario: Colores y estados habituales
- DADO texto, enlaces, controles y foco en sus fondos habituales
- CUANDO se comprueba su contraste en los anchos objetivo
- ENTONCES cumplen el umbral aplicable y permiten identificar las acciones.

#### Scenario: Fondo alternativo o interacción
- DADO un componente sobre fondo claro alternativo o con foco
- CUANDO se comprueba el estado mostrado
- ENTONCES mantiene el contraste exigido sin depender exclusivamente del color.

#### Scenario: Contraste insuficiente
- DADO una combinación que incumple el umbral aplicable
- CUANDO se ejecuta la comprobación
- ENTONCES falla e identifica componente, estado, fondo y relación observada.
