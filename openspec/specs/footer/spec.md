# Especificación: Footer administrable

## Requirements

### Requirement: Fuente editorial única

El sistema MUST permitir administrar todos los textos y enlaces visibles del footer desde un panel protegido de WordPress y MUST usar esa configuración como fuente de verdad.

#### Scenario: Edición autorizada
- DADO un administrador con capacidad de gestionar el tema
- CUANDO guarda una configuración válida del footer
- ENTONCES el footer público MUST reflejar cada valor guardado

#### Scenario: Campo vacío
- DADO un campo opcional vacío
- CUANDO se renderiza el footer
- ENTONCES el elemento correspondiente MUST omitirse sin romper la composición

#### Scenario: Edición no autorizada
- DADO un usuario sin la capacidad requerida
- CUANDO intenta acceder o guardar la configuración
- ENTONCES el sistema MUST denegar la operación

### Requirement: Contenido completo y seguro

El sistema MUST cubrir identidad, descripción, títulos, cada etiqueta y URL, canales de contacto, copyright y política; MUST sanear al guardar y escapar al renderizar.

#### Scenario: Defaults
- DADO que aún no existe una configuración guardada
- CUANDO se renderiza el footer
- ENTONCES MUST mostrarse el contenido equivalente al estado aprobado actual

#### Scenario: URLs y correo válidos
- DADO enlaces HTTP(S), internos y un correo válidos
- CUANDO se guardan y renderizan
- ENTONCES MUST conservar destinos seguros y etiquetas claras

#### Scenario: Entrada manipulada
- DADO texto con marcado o URL insegura
- CUANDO se procesa la configuración
- ENTONCES MUST eliminarse el marcado y rechazarse el destino inseguro

### Requirement: Composición visual estable

El sistema MUST conservar las clases y jerarquía necesarias para la composición visual responsive vigente.

#### Scenario: Configuración completa
- DADO el conjunto completo de defaults
- CUANDO se renderiza el footer
- ENTONCES MUST conservar marca, tres columnas y franja legal con ancho wide

#### Scenario: Lista parcial
- DADO que uno o más enlaces están vacíos
- CUANDO se renderiza el footer
- ENTONCES las columnas restantes MUST conservar su estructura

#### Scenario: Módulo no disponible
- DADO que el renderizador no está registrado
- CUANDO WordPress procesa el template part
- ENTONCES MUST degradar sin ejecutar código inseguro ni inventar contenido
